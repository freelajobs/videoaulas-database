<?php

require_once "../class/ClTickets.php";
require_once "../class/ClUserComment.php";
$log = 'logs/Ticket.txt';

$id   = isset($_POST['id']) ? $_POST['id'] : "4";
$type = isset($_POST['type']) ? $_POST['type'] : "";

$service = $ClassTickets->Get($id);

if ($service['status'] === false) {
	$result = Array (
		"status" => 'false',
		"error" => 'não foi possível resgatar esse compromisso no momento.'
	);
} else {
	$comments = $ClassUserComment->GetService($id);
	if ($comments['status'] === true) {
		$service['data']['comment'] = $comments['data'];
		$result = $service;
	} 
	
	$result = $service;
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
