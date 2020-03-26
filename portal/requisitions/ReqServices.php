<?php

require_once "../class/ClServices.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	switch ($type) {
		case 'add':
			$result = $ClassService->Add(
				$data['name'],
				$data['description']
			);
		break;
		case 'update':
			$result = $ClassService->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$ClassService->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'get':
			$result = $ClassService->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassService->GetAll();
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
