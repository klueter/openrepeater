<?php
// --------------------------------------------------------
// SESSION CHECK TO SEE IF USER IS LOGGED IN.
session_start();
if ((!isset($_SESSION['username'])) || (!isset($_SESSION['userID']))){
	header('location: ../login.php');
} else { // If they are, show the page.
// --------------------------------------------------------

################################################################################
# AUTOLOAD CLASSES
require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/includes/autoloadClasses.php');
################################################################################
$Database = new Database();

$allowed_keys = [
	'callSign', 'echolink_callSign', 'ctcssFreqRX', 'ctcssFreqTX',
	'rxFreq', 'txFreq', 'txPower', 'squelch', 'idInterval',
	'shortIdent', 'longIdent', 'idTone', 'courtesyTone',
	'dtmfMuting', 'timezone', 'orp_Mode'
];

foreach($_POST as $key=>$value){
	if (!in_array($key, $allowed_keys, true)) { continue; }

	// SPECIAL FORMATING
	if ($key == "callSign") { $value = strtoupper($value); }
	if ($key == "echolink_callSign") { $value = strtoupper($value); }

	$query = $Database->execute_prepared(
		"UPDATE settings SET value = :val WHERE keyID = :key",
		[':val' => $value, ':key' => $key]
	);
}

if ($query) { return true; } else { return false; }


// --------------------------------------------------------
// SESSION CHECK TO SEE IF USER IS LOGGED IN.
 } // close ELSE to end login check from top of page
// --------------------------------------------------------
?>