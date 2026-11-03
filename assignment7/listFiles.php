<?php
// THE PROCESSING LOGIC LIVES IN THE php FOLDER
require_once __DIR__ . "/php/listFilesProc.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>List Files</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-4">
		<h1>List Files</h1>
		<a href="index.php">Add File</a>

		<div class="mt-3">
			<?php echo getFileList(); ?>
		</div>
	</div>
</body>
</html>
