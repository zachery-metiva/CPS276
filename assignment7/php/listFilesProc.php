<?php
// BRING IN THE INSTRUCTOR'S PDO CLASSES
require_once __DIR__ . "/../classes/Pdo_methods.php";

/* THIS FUNCTION SELECTS ALL THE ROWS FROM THE files TABLE AND BUILDS AN
UNORDERED LIST WHERE EACH FILE IS A LINK THAT OPENS IN A NEW TAB.
NO USER INPUT GOES INTO THIS QUERY SO selectNotBinded IS USED. */
function getFileList(){

	$pdo = new PdoMethods();

	$sql = "SELECT file_name, file_path FROM files";

	$records = $pdo->selectNotBinded($sql);

	// IF THERE WAS AN ERROR DISPLAY A MESSAGE
	if($records === 'error'){
		return "There has been an error processing your request";
	}

	// IF THERE ARE NO ROWS YET
	if(count($records) == 0){
		return "No files have been uploaded yet.";
	}

	// BUILD THE LIST OF LINKS
	$list = "<ul>";
	foreach($records as $row){
		$list .= "<li><a target='_blank' href='{$row['file_path']}'>{$row['file_name']}</a></li>";
	}
	$list .= "</ul>";

	return $list;
}
?>
