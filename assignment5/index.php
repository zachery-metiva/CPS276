<?php
/*
 * ===================== ASSIGNMENT 5 QUESTIONS =====================
 *
 * 1. Explain the difference between creating a directory and creating a file in PHP.
 *    What PHP functions are used for each operation, and why is it important to check
 *    if a directory already exists before attempting to create it?
 *
 *    A directory is a container that holds files; a file holds the actual data.
 *    mkdir() creates a directory (see Directories.php, step 3). A file is created by
 *    opening it in write mode with fopen($path, 'w'), writing with fwrite(), and closing
 *    it with fclose() (file_put_contents() can also do all three in one call).
 *    We check with file_exists() first because mkdir() throws a warning and returns
 *    false if the folder is already there. Checking first lets us show a friendly
 *    "already exists" message and keeps us from overwriting another user's readme.txt.
 *
 * 2. Describe the flow of data from an HTML form submission to PHP processing. How does
 *    PHP access form data, and what considerations should developers keep in mind when
 *    handling user input from forms?
 *
 *    The user fills in the form and clicks Submit. The browser sends an HTTP POST
 *    request to index.php (the form's action) with the fields as name/value pairs.
 *    PHP puts those values into the $_POST superglobal, keyed by each input's name
 *    attribute, so we read $_POST['folderName'] and $_POST['fileContent']. We then
 *    pass them to the Directories class. User input can never be trusted: we check that
 *    the field is set, trim it, validate the folder name with ctype_alnum() so nobody
 *    can send something like "../../" to write outside our folder, and escape anything
 *    we echo back to the page with htmlspecialchars() to prevent XSS.
 *
 * 3. Why is it important to properly close file handles after writing to files? What
 *    problems can occur if file handles are not closed, and how does this relate to
 *    system resource management?
 *
 *    fclose() flushes any buffered data to disk and releases the handle. If a file is
 *    not closed, data may not be fully written, the file can stay locked so other
 *    processes cannot use it, and the server can run out of available file handles
 *    because every operating system limits how many files can be open at once. On a busy
 *    web server that leak adds up quickly, so we always call fclose($handle) right after
 *    fwrite().
 *
 * 4. Why did we use 777 permissions and what should we use and why.
 *
 *    777 gives read, write, and execute to the owner, the group, and everyone else. It is
 *    used in class examples because it "just works" when the web server user is
 *    different from the account that owns the folder. It is unsafe for real work because
 *    any user or process on the server could change or delete the files, or drop a
 *    malicious script into them. We use 0755 for directories (owner can write, everyone
 *    else can only read and enter) and 0644 is the normal choice for files. This follows
 *    the principle of least privilege: give only the access that is actually needed.
 *
 * 5. Explain the benefits of organizing file and directory operations into a class
 *    structure. How does this approach improve code organization, reusability, and
 *    maintainability compared to writing all operations in procedural code?
 *
 *    The Directories class keeps all of the folder and file logic in one place, while
 *    index.php only handles the form and the display. That separation makes each file
 *    shorter and easier to read. The class can be included and reused on any other page
 *    with one line (new Directories()). Settings like the base path and permissions are
 *    stored as properties, so changing them happens in one spot instead of everywhere.
 *    Bugs are easier to find and fix, and the class can be tested on its own.
 * ===================================================================
 */

require_once 'classes/Directories.php';

$message = '';
$link = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName  = $_POST['folderName'] ?? '';
    $fileContent = $_POST['fileContent'] ?? '';

    $directories = new Directories();
    $result = $directories->createDirectoryAndFile($folderName, $fileContent);

    $message = $result['message'];
    $link    = $result['link'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File and Directory Assignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-2">
    <h1>File and Directory Assignment</h1>
    <p>Enter a folder name and the contents of a file. Folder names should contain alpha numeric characters only.</p>

    <?php if ($message !== ''): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($link !== null): ?>
        <p><a href="<?php echo htmlspecialchars($link); ?>">Path where file is located</a></p>
    <?php endif; ?>

    <form method="post" action="index.php">
        <div class="form-group">
            <label for="folderName">Folder Name</label>
            <input type="text" class="form-control" id="folderName" name="folderName" pattern="[A-Za-z0-9]+" required>
        </div>
        <div class="form-group">
            <label for="fileContent">File Content</label>
            <textarea class="form-control" id="fileContent" name="fileContent" rows="6"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
