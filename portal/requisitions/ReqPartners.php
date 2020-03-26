<?php

require_once "../class/ClPartners.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];
	
	//Login options
	switch ($type) {
		case 'update':
			$result = $ClassPartners->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$ClassPartners->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'update_status':
			$result = $ClassPartners->UpdateStatus(
				$data['id'],
				$ClassPartners->ConvertBoolServer($data['blocked']),
				$data['description']
			);
		break;
		case 'approved':
			$result = $ClassPartners->UpdateApproved(
				$data['id']
			);
		break;
		case 'reproved':
			$result = $ClassPartners->UpdateReproved(
				$data['id'],
				$data['description']
			);
		break;
		case 'get':
			$result = $ClassPartners->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassPartners->GetAll();
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
