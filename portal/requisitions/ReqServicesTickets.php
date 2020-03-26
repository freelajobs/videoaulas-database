<?php

require_once "../class/ClServicesTickets.php";

$log = 'logs/ServiceTickets';
$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	//Login options
	switch ($type) {
		case 'get':
			// TODO: get comments, get services
			$result = $ClassServiceTickets->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassServiceTickets->GetAll();
			break;
	}
} else {
	$result = array(
        'status' => 'false',
        'error' => 'dados não enviados'
    );  
}
//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log . '_' . $type . '.txt', $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
