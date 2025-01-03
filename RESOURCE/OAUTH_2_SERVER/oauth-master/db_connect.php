<?php
/**********************************/
class DB_CONNECT {
	// Constructor
        public $con;
        function __construct() {
		// Trying to connect to the database
		$this->connect();
	}
	// Destructor
	function __destruct() {
		// Closing the  connection to database
		//$this->close();
	}
	
	// Function to connect to the database
	function connect() {
		// importing dbconfig.php file which contains database credentials
		$filepath = realpath (dirname(__FILE__));
		require_once($filepath."/dbconfig.php");
		// Connecting to mysql (phpmyadmin) database
		$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE);
                if (!$con){
                    die("Could not connect: " . mysqli_error());
                }
		return $con;
	}
	
	// Function to close the database
	function close() {
		// Closing data base connection
		mysqli_close();
	}
}
?>