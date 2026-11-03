<?php
/*
==========================================================================
ASSIGNMENT 7 QUESTIONS AND ANSWERS
==========================================================================

1. Explain why we are using the Db_conn class

   The Db_conn class has one job: it creates the PDO connection to the
   MySQL database and sets the connection attributes (error mode, buffered
   queries, etc). Because the connection details (host, database name,
   username and password) live only in this one class, we never repeat
   credentials in other files. If the password or database ever changes we
   update one file and the entire application keeps working. The class can
   also be stored outside the public web files, which keeps the credentials
   more secure.

2. Explain why we are using the Pdo_methods class

   The PdoMethods class wraps all of the repetitive PDO work (prepare,
   bind, execute, fetch, error handling) into four reusable methods:
   selectBinded, selectNotBinded, otherBinded and otherNotBinded. Instead
   of writing a try/catch block and prepared statement boilerplate every
   time we touch the database, a page simply passes in the SQL string and
   an array of bindings. This keeps our page code short, consistent, and
   guarantees every query goes through prepared statements.

3. Why are we storing the PDF files on the web server and not in the
   database

   The database only stores the display name and the path to the file.
   Storing the actual PDF bytes in the database would bloat the database,
   make backups and queries slow, and the file would have to be pulled
   through PHP and the database connection every time someone opened it.
   On disk, the web server (Apache) can serve the PDF directly, which is
   fast and efficient, and the database stays small and quick to query.
   The path column is all we need to build a working link.

4. The PdoMethods class extends DatabaseConn (Db_conn). Explain the
   benefits of this inheritance structure and how it promotes code
   reusability and separation of concerns.

   Inheritance lets PdoMethods build on top of Db_conn without copying
   any of its code. Each class has a single, separate concern: Db_conn
   knows how to connect, PdoMethods knows how to run queries. Any class
   that needs the database can extend or use PdoMethods and automatically
   gets the connection logic from the parent class for free. That is code
   reuse: one connection class serves every query method and every page
   of the site. It also means changes stay isolated. If the connection
   setup changes, only Db_conn is edited; if we add a new query helper,
   only PdoMethods is edited. Neither change touches the pages that use
   them.

5. In the fileUploadProc.php, the code uses prepared statements with
   bindings. Explain how this approach prevents SQL injection attacks and
   why it is considered a security best practice.

   With a prepared statement the SQL is sent to MySQL first with named
   placeholders (:fileName, :filePath) instead of user values. MySQL
   compiles the query plan from that template, so the structure of the
   query is locked in before any user data arrives. The bound values are
   then sent separately and are treated purely as data, never as SQL
   code. If a user typed something malicious like
   "x'); DROP TABLE files; --" into the form, it would simply be stored
   as a weird file name. It can never break out of the placeholder and
   change the query. Because ATTR_EMULATE_PREPARES is set to false in
   Db_conn, these are true server side prepared statements. This is a
   best practice because it removes the whole category of SQL injection
   attacks instead of trying to filter dangerous characters by hand.

==========================================================================
*/

// BRING IN THE INSTRUCTOR'S PDO CLASSES
require_once __DIR__ . "/../classes/Pdo_methods.php";

// MAXIMUM ALLOWED FILE SIZE IN BYTES (2 MB)
define("MAX_FILE_SIZE", 2000000);

/* THIS FUNCTION DOES ALL THE CHECKS AND THE UPLOAD.
IT RETURNS A MESSAGE STRING THAT IS DISPLAYED IN THE PAGE BELOW. */
function processUpload(){

	// CHECK THAT THE FORM WAS ACTUALLY SUBMITTED
	if(!isset($_POST['uploadFile'])){
		return "No form was submitted. <a href='../index.php'>Go back to the form</a>";
	}

	// 1. CHECK THAT A FILE WAS CHOSEN
	if(!isset($_FILES['pdfFile']) || $_FILES['pdfFile']['error'] == UPLOAD_ERR_NO_FILE){
		return "Error: No file was chosen. Please choose a PDF file to upload.";
	}

	// CHECK FOR ANY OTHER UPLOAD ERROR
	if($_FILES['pdfFile']['error'] != UPLOAD_ERR_OK){
		return "Error: There was a problem uploading the file. Error code: " . $_FILES['pdfFile']['error'];
	}

	// 2. CHECK THAT THE FILE IS NOT TOO LARGE
	if($_FILES['pdfFile']['size'] > MAX_FILE_SIZE){
		return "Error: The file is too large. The maximum size allowed is 2 MB.";
	}

	// 3. CHECK THAT THE FILE IS A PDF. I CHECK THE FILE EXTENSION
	// AND THE MIME TYPE THE BROWSER SENT.
	$fileExt = strtolower(pathinfo($_FILES['pdfFile']['name'], PATHINFO_EXTENSION));
	if($fileExt != "pdf" || $_FILES['pdfFile']['type'] != "application/pdf"){
		return "Error: Only PDF files are allowed.";
	}

	// CLEAN THE FILE NAME SO NO DIRECTORY TRICKS CAN BE PLAYED
	$cleanName = basename($_FILES['pdfFile']['name']);

	// MOVE THE FILE INTO THE files FOLDER
	$destination = __DIR__ . "/../files/" . $cleanName;

	if(!move_uploaded_file($_FILES['pdfFile']['tmp_name'], $destination)){
		return "Error: The file could not be moved into the files folder.";
	}

	// THE PATH THAT IS STORED IN THE DATABASE. listFiles.php SITS IN THE
	// ROOT OF THE PROJECT SO THE LINK IT BUILDS IS files/filename.pdf
	$filePath = "files/" . $cleanName;

	// GET THE DISPLAY NAME THE USER TYPED IN
	$fileName = trim($_POST['fileName']);
	if($fileName == ""){
		$fileName = $cleanName;
	}

	/* INSERT THE DISPLAY NAME AND THE PATH USING BOUND PARAMETERS.
	THE USER VALUES ARE NEVER CONCATENATED INTO THE SQL. */
	$pdo = new PdoMethods();

	$sql = "INSERT INTO files (file_name, file_path) VALUES (:fileName, :filePath)";

	$bindings = [
		[':fileName', $fileName, 'str'],
		[':filePath', $filePath, 'str']
	];

	$result = $pdo->otherBinded($sql, $bindings);

	if($result === 'error'){
		return "Error: There was a problem saving the file information to the database.";
	}

	return "Success: The file was uploaded and saved to the database.";
}

$message = processUpload();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>File Upload Result</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-4">
		<h1>File Upload Result</h1>
		<p><?php echo $message; ?></p>
		<a href="../index.php">Back to Upload Form</a> |
		<a href="../listFiles.php">Show File List</a>
	</div>
</body>
</html>
