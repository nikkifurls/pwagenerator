<?php

header("Content-type: application/json");

require_once(dirname(__DIR__) . "/build.php");

if (isset($_POST) && isset($_POST["project"]) && isset($_POST["field"]) && isset($_POST["value"])) {

	$project = $_POST["project"];
	$field = $_POST["field"];
	$value = $_POST["value"];

	if (($value == "on") || ($value == "off") || ($value == "true") || ($value == "false")) {
		$value = (($value == "on") || ($value == "true")) ? true : false;
	}

	$result = api_update_project($project, $field, $value);

	echo json_encode($result);

} else {
	
	echo json_encode(false);
}