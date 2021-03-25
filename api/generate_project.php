<?php

header("Content-type: application/json");

require_once(dirname(__DIR__) . "/build.php");

if (isset($_POST) && isset($_POST["project"])) {
	
	$project = $_POST["project"];
	$deploy = (isset($_POST["deploy"]) && $_POST["deploy"]) ? true : false;

	$result = api_generate_project($project, $deploy);

	echo json_encode($result);

} else {
	echo json_encode(false);
}