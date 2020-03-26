<?php

require_once "../class/ClServicesPayment.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	//Login options
	switch ($type) {
		case 'add':
			$result = $ClassServicePayment->Add(
				$data['name'],
				$data['description']
			);
		break;
		case 'update':
			$result = $ClassServicePayment->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$ClassServicePayment->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'get':
			$result = $ClassServicePayment->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassServicePayment->GetAll();
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
