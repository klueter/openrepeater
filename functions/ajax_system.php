<?php
// This is part of an AJAX operation to pass data back and forth between JavaScript, PHP, and BASH (linux system). It lets ORP do things the web server normally wouldn't have privilages to do. Results polled from the the System Class are returned to JavaScript in JSON format.

session_start();
if ((!isset($_SESSION['username'])) || (!isset($_SESSION['userID']))) {
	http_response_code(403);
	die('Unauthorized');
}

$allowed_commands = [
	'system' => ['restart', 'stop'],
	'svxlink' => ['start', 'stop', 'status', 'restart'],
];

// Check to make sure options are passed by JavaScript and if so, call the function and echo results
if (!empty($_POST)) {
	$service = $_POST['post_service'] ?? '';
	$option = $_POST['post_option'] ?? '';

	if (!isset($allowed_commands[$service]) || !in_array($option, $allowed_commands[$service], true)) {
		http_response_code(400);
		echo 'Invalid service/option';
		exit;
	}

	echo exec_orp_helper($service, $option);
}


// Function to pass and return commands from orp_helper script in BASH
function exec_orp_helper ($opt1, $opt2) {
	$command = 'sudo orp_helper ' . escapeshellarg($opt1) . ' ' . escapeshellarg($opt2);
	ob_start();
	passthru($command);
	return trim(ob_get_clean());
}


// Pass System Info updates back to javascript
if( isset($_GET['update']) ) {
	require_once('../includes/classes/System.php');
	$classSystem = new System();

	switch ( $_GET['update'] ) {
		case "time":
 			$outputArray = $classSystem->system_time();
	        break;
		case "systemStatic":
 			$outputArray = $classSystem->system_static();
	        break;
		case "systemDynamic":
 			$outputArray = $classSystem->system_dynamic();
	        break;
		case "svxlink":
 			$outputArray = $classSystem->svxlink_status();
	        break;
	    case "memory":
 			$outputArray = $classSystem->memory_usage();
	        break;
	    case "disk":
 			$outputArray = $classSystem->disk_usage();
	        break;
	}
	
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($outputArray);
}
?>
