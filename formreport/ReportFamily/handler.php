<?php
ini_set("memory_limit","-1");
ini_set("max_execution_time", 0);
require_once "../../application/config/database.php";
require_once "stimulsoft/helper.php";

// Please configure the security level as you required.
// By default is to allow any requests from any domains.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token");

$handler = new StiHandler();
$handler->registerErrorHandlers();

$handler->onBeginProcessData = function ($args) {
	if ($args->connection == "FamilyMart")
		// $args->connectionString = "Data Source=".$tServername.";Initial Catalog=".$tDBName.";Integrated Security=False;User ID=".$tUsername.";Password=".$tPassword."";
		$args->connectionString = "Data Source=".tServername.";Initial Catalog=".tDBName.";Integrated Security=False;User ID=".tUsername.";Password=".tPassword."";

	return StiResult::success();
};

$handler->process();