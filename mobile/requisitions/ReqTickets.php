<?php

require_once "../class/ClTickets.php";
$log = 'logs/Tickets.txt';

$idUser = isset($_POST['idUser']) ? $_POST['idUser'] : "";
$type   = isset($_POST['type']) ? $_POST['type'] : "";
$page   = isset($_POST['page']) ? $_POST['page'] : "";

// Pega todos os serviços do servidor
$services = $ClassTickets->GetAll($idUser);

if ($services['status'] === false) {
	$result = Array (
		"status" => 'false',
		"error" => 'não foi possível resgatar seus compromissos no momento.'
	);
} else {
    // Eventualmente usar o paginator
    $services['page'] = Array(
        'number' => 1,
        'total' => '1',
    ); 
    $result = $services;
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
