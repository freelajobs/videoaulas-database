<?php

require_once "../class/ClServicesStatus.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	//Login options
	switch ($type) {
		case 'add':
			$result = $ClassServiceStatus->Add(
				$data['name'],
				$data['description']
			);
		break;
		case 'update':
			$result = $ClassServiceStatus->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$ClassServiceStatus->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'get':
			$result = $ClassServiceStatus->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassServiceStatus->GetAll();
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
