<?php
require_once 'classes/Date_time.php';
$dt = new Date_time();
$notes = $dt->checkSubmit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add Note</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-4">
		<h1>Add Note</h1>
		<a href="display_notes.php">Display Notes</a>

		<!-- ONE SPOT FOR MESSAGES: appears above the Date and Time label -->
		<div class="mt-3">
			<?php echo $notes; ?>
		</div>

		<form action="index.php" method="post">

			<div class="mb-3">
				<label for="dateTime" class="form-label">Date and Time</label>
				<input type="datetime-local" class="form-control" id="dateTime" name="dateTime">
			</div>

			<div class="mb-3">
				<label for="note" class="form-label">Note</label>
				<textarea class="form-control" id="note" name="note" rows="8"></textarea>
			</div>

			<button type="submit" name="addNote" class="btn btn-primary">Add Note</button>

		</form>
	</div>
</body>
</html>
