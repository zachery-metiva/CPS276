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
	<title>Display Notes</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-4">
		<h1>Display Notes</h1>
		<a href="index.php">Add Note</a>

		<form action="display_notes.php" method="post" class="mt-3">

			<div class="mb-3">
				<label for="begDate" class="form-label">Beginning Date</label>
				<input type="date" class="form-control" id="begDate" name="begDate">
			</div>

			<div class="mb-3">
				<label for="endDate" class="form-label">Ending Date</label>
				<input type="date" class="form-control" id="endDate" name="endDate">
			</div>

			<button type="submit" name="getNotes" class="btn btn-primary">Get Notes</button>

		</form>

		<!-- ONE SPOT FOR MESSAGES AND/OR THE NOTES TABLE -->
		<div class="mt-3">
			<?php echo $notes; ?>
		</div>
	</div>
</body>
</html>
