<?php
/* 
CPS 276 - Assignment 8: Date-Time Notes

VIDEO QUESTIONS:
1. Why store a timestamp (or one standard format) instead of a display string like "September 3, 2026"?
2. Why put date work in a class instead of copying date() calls on every page?
3. When the user asks for notes in a date range, what steps does the app take to show only those notes?
4. Why do time zones matter when you convert between a form value and a stored timestamp?
5. Why validate that the start date is not after the end date?

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
