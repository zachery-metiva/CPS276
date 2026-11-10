<?php
/* This class extends the database connection class and builds on it with PDO commands */
/* The database connection class can be stored outside of the example files so you cannot see the connection information. Also it is more secure */
require_once "Db_conn.php";

class PdoMethods extends Db_conn {

	private $sth;
	private $conn;
	private $db;
	private $error;

	/* This method is for all SELECT statements that need to have a binding to protect the data.
	The script takes the SQL statements and the binding array as its parameters and performs the query.
	It will run the query and return the result as an associative array or an error string. */
	public function selectBinded($sql, $bindings){
		$this->error = false;

		// I created a try catch block to catch any errors that might arise and returns an error message.
		try{
			$this->db_connection();
			$this->sth = $this->conn->prepare($sql);
			$this->createBinding($bindings);
			$this->sth->execute();
		}
		catch(PDOException $e){
			// This will output the error message to the browser. Remove if in production.
			echo $e->getMessage();
			return 'error';
		}

		// This closes the database connection
		$this->conn = null;

		// This returns a record set
		return $this->sth->fetchAll(PDO::FETCH_ASSOC);
	}

	/* This function does the same as the above but does not need any binded parameters as no parameters are passed */
	public function selectNotBinded($sql){
		$this->error = false;

		// I created a try catch block to catch any errors that might arise and returns an error message.
		try{
			$this->db_connection();
			$this->sth = $this->conn->prepare($sql);
			$this->sth->execute();
		}
		catch (PDOException $e){
			// This will output the error message to the browser. Remove if in production.
			echo $e->getMessage();
			return 'error';
		}

		// This closes the database connection
		$this->conn = null;

		// This returns the record set as an array
		return $this->sth->fetchAll(PDO::FETCH_ASSOC);
	}

	/* Because only SELECT queries return a value, this method does all the rest: CREATE, UPDATE, DELETE */
	public function otherBinded($sql, $bindings){
		$this->error = false;

		// I created a try catch block to catch any errors that might arise and returns an error message.
		try{
			$this->db_connection();
			$this->sth = $this->conn->prepare($sql);
			$this->createBinding($bindings);
			$this->sth->execute();
		}
		catch(PDOException $e) {
			// This will output the error message to the browser. Remove if in production.
			echo $e->getMessage();
			return 'error';
		}

		// This closes the database connection
		$this->conn = null;

		// No error means everything worked
		return 'noerror';
	}

	public function otherNotBinded($sql){
		$this->error = false;

		// I created a try catch block to catch any errors that might arise and returns an error message.
		try{
			$this->db_connection();
			$this->sth = $this->conn->prepare($sql);
			$this->sth->execute();
		}
		catch (PDOException $e){
			// This will output the error message to the browser. Remove if in production.
			echo $e->getMessage();
			return 'error';
		}

		// This closes the database connection
		$this->conn = null;

		// This returns noerror if no errors
		return 'noerror';
	}

	/* Creates a connection to the database */
	private function db_connection(){
		$this->db = new Db_conn();
		$this->conn = $this->db->dbOpen();
	}

	/* Creates the bindings */
	private function createBinding($bindings){
		foreach($bindings as $value){
			switch($value[2]){
				case "str" : $this->sth->bindParam($value[0], $value[1], PDO::PARAM_STR); break;
				case "int" : $this->sth->bindParam($value[0], $value[1], PDO::PARAM_INT); break;
			}
		}
	}
}
?>
