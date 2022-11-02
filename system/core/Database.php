<?php
	// include('application/config/database.php');
	include_once('drivers/Driver_SQLSRV.php');
	class Database extends Driver_database {

		function __construct(){
			parent::__construct();
		}

	}
?>
