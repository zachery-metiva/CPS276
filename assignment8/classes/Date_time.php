<?php
/*
==========================================================================
ASSIGNMENT 8 QUESTIONS AND ANSWERS
==========================================================================

1. Why store a timestamp (or one standard format) instead of a display
   string like "September 3, 2026"?

   A timestamp is a single integer, so the database can compare and sort
   it directly. That is exactly what the query in getNotes() relies on:
   BETWEEN :begDate AND :endDate only works because the values are
   numbers, and ORDER BY date_time DESC sorts chronologically. A string
   like "September 3, 2026" would sort alphabetically (April before
   January) and range queries would be impossible. Storing one standard
   format also separates storage from display: buildTable() converts the
   integer back with date('n/d/Y h:i a', ...), so if the required display
   format ever changed I would edit that one line and every stored note
   would show the new format.

2. Why put date work in a class instead of copying date() calls on every
   page?

   All of the conversion, validation and query logic lives in the
   Date_time class, so index.php and display_notes.php only need three
   lines of PHP: require the class, instantiate it, and call
   checkSubmit(). If I copied strtotime() and date() calls onto every
   page, a change to the format would mean hunting down every copy, and
   the copies would eventually drift apart. With the class, the display
   format exists in exactly one place (buildTable()), so every page shows
   dates identically, and the pages themselves stay almost pure HTML,
   which makes them easier to read and maintain.

3. When the user asks for notes in a date range, what steps does the app
   take to show only those notes?

   The getNotes() method works through five steps. First it reads begDate
   and endDate from the POST. Second it validates that both were entered;
   if not it returns the "No notes found for the date range selected"
   message. Third it converts the dates to timestamps that cover the
   whole days: the beginning date at 00:00:00 and the ending date at
   23:59:59, so a note written any time on the last day is still
   included. Fourth it runs a bound prepared query using BETWEEN, ordered
   DESC so the newest note comes first. Finally each stored timestamp is
   converted back to a readable m/dd/yyyy hh:mm am/pm string and placed
   into the Bootstrap table, or a message is shown if no rows matched.

4. Why do time zones matter when you convert between a form value and a
   stored timestamp?

   The datetime-local field sends a local wall clock value like
   2026-11-09T14:30 with no time zone attached, but a Unix timestamp is
   an absolute moment counted from UTC. strtotime() has to assume a time
   zone to turn the string into a number, and date() has to assume one to
   turn the number back into a string. If those two assumptions differ,
   for example the server defaulting to UTC while the user is in Eastern
   time, every note would display four or five hours off, and a range
   query could even miss notes written near midnight. That is why the
   constructor calls date_default_timezone_set('America/Detroit'): both
   directions of the conversion use the same zone, so the time the user
   typed is the time they see back.

5. Why validate that the start date is not after the end date?

   A reversed range can never match anything, because BETWEEN needs a
   value that is greater than the start and less than the end at the same
   time. Without the check the user would just see "no notes found" and
   might think their notes were lost, when really the form was filled in
   backwards. Catching it in getNotes() with if($begTs > $endTs) lets me
   show a specific message, "The beginning date cannot be after the
   ending date", which tells the user exactly how to fix their input. It
   also avoids sending the database a query that is guaranteed to return
   nothing.

==========================================================================

This class does ALL operations for adding and displaying notes.
The pages (index.php and display_notes.php) only require this class,
instantiate it, and call checkSubmit().
*/


require_once 'Pdo_methods.php';

class Date_time {

	private $pdo;

	public function __construct(){
		/* Set the time zone so strtotime() and date() convert consistently
		   between the form values and the stored timestamps (Question 4) */
		date_default_timezone_set('America/Detroit');
		$this->pdo = new PdoMethods();
	}

	/* Starting method called by both pages. It checks which form (if any)
	   was submitted and routes to the correct operation. */
	public function checkSubmit(){
		if(isset($_POST['addNote'])){
			return $this->addNote();
		} elseif(isset($_POST['getNotes'])){
			return $this->getNotes();
		} else {
			return '';
		}
	}

	/* Validates the add note form, converts the datetime-local value to a
	   Unix timestamp (Question 1), and inserts the timestamp and note. */
	private function addNote(){
		$dateTime = isset($_POST['dateTime']) ? trim($_POST['dateTime']) : '';
		$note     = isset($_POST['note'])     ? trim($_POST['note'])     : '';

		// Code-based validation message (not an HTML required error)
		if($dateTime === '' || $note === ''){
			return '<div class="alert alert-danger" role="alert">You must enter a date, time, and note.</div>';
		}

		// Convert the datetime-local string (ex 2026-11-09T14:30) to a timestamp
		$timestamp = strtotime($dateTime);

		if($timestamp === false){
			return '<div class="alert alert-danger" role="alert">You must enter a valid date and time.</div>';
		}

		$sql = "INSERT INTO note (date_time, note) VALUES (:dateTime, :note)";
		$bindings = array(
			array(':dateTime', $timestamp, 'int'),
			array(':note', $note, 'str')
		);

		$result = $this->pdo->otherBinded($sql, $bindings);

		if($result === 'noerror'){
			return '<div class="alert alert-success" role="alert">Your note was added.</div>';
		} else {
			return '<div class="alert alert-danger" role="alert">There was a problem adding your note.</div>';
		}
	}

	/* Steps to show only the notes in the range (Question 3):
	   1. Read the beginning and ending dates from the form
	   2. Validate both were entered and the range makes sense (Question 5)
	   3. Convert them to timestamps covering the whole days
	   4. Run a bound BETWEEN query ordered newest first
	   5. Convert each stored timestamp back to a readable date for display */
	private function getNotes(){
		$begDate = isset($_POST['begDate']) ? trim($_POST['begDate']) : '';
		$endDate = isset($_POST['endDate']) ? trim($_POST['endDate']) : '';

		// Code-based validation message (not an HTML required error)
		if($begDate === '' || $endDate === ''){
			return '<div class="alert alert-danger" role="alert">No notes found for the date range selected</div>';
		}

		// Beginning of the first day through the end of the last day
		$begTs = strtotime($begDate . ' 00:00:00');
		$endTs = strtotime($endDate . ' 23:59:59');

		// Validate the start date is not after the end date (Question 5)
		if($begTs > $endTs){
			return '<div class="alert alert-danger" role="alert">The beginning date cannot be after the ending date.</div>';
		}

		$sql = "SELECT date_time, note FROM note WHERE date_time BETWEEN :begDate AND :endDate ORDER BY date_time DESC";
		$bindings = array(
			array(':begDate', $begTs, 'int'),
			array(':endDate', $endTs, 'int')
		);

		$rows = $this->pdo->selectBinded($sql, $bindings);

		if($rows === 'error'){
			return '<div class="alert alert-danger" role="alert">There was a problem getting your notes.</div>';
		}

		if(count($rows) === 0){
			return '<div class="alert alert-warning" role="alert">No notes found for the date range selected</div>';
		}

		return $this->buildTable($rows);
	}

	/* Builds the Bootstrap table. Each stored timestamp is converted back
	   to a readable date time of m/dd/yyyy hh:mm am/pm (Questions 1 and 2:
	   the format lives in ONE place, so every page displays dates the same way). */
	private function buildTable($rows){
		$table  = '<table class="table table-striped table-bordered">';
		$table .= '<thead><tr><th scope="col">Date and Time</th><th scope="col">Note</th></tr></thead>';
		$table .= '<tbody>';

		foreach($rows as $row){
			$displayDate = date('n/d/Y h:i a', $row['date_time']);
			$table .= '<tr><td>' . $displayDate . '</td><td>' . htmlspecialchars($row['note']) . '</td></tr>';
		}

		$table .= '</tbody></table>';
		return $table;
	}
}
?>
