<?php

require_once "../class/ClClients.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	//Login options
	switch ($type) {
		case 'update':
			$result = $ClassClients->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$ClassClients->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'update_status':
			$result = $ClassClients->UpdateStatus(
				$data['id'],
				$ClassClients->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'get':
			$result = $ClassClients->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassClients->GetAll();
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

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
