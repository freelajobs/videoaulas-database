<?php

require_once "../class/ClNotification.php";

$postdata = file_get_contents("php://input");
$log = 'logs/Notification';
if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];
	//Login options
	switch ($type) {
		case 'add':
			$result = $ClassNotification->Add(
				$data['title'],
				$data['date'],
				$data['target'],
				$data['description']
			);
		break;
		case 'update':
			$result = $ClassNotification->Update(
				$data['id'],
				$data['title'],
				$data['date'],
				$data['target'],
				$data['description']
			);
		break;
		case 'remove':
			$result = $ClassNotification->Remove(
				$data['id']
			);
		break;
		case 'get':
			$result = $ClassNotification->Get(
				$data['id']
			);
		break;
		case 'send':
			$result = array(
				'status' => 'false',
				'error' => 'funcionalidade não disponível no momento'
			);
		break;
		default:
			$result = $ClassNotification->GetAll();
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

file_put_contents($log . '_' . $type . '.txt', $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
