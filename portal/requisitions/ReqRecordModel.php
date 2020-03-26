<?php

require_once "../class/ClRecordModel.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];
    
	// options
	switch ($type) {
		case 'approved':
			$result = $ClassRecordModel->GetApproved();
		break;
		case 'blocked':
			$result = $ClassRecordModel->GetBlocked();
		break;
		case 'wait':
			$result = $ClassRecordModel->GetWait();
		break;
		default:
			$result = $ClassRecordModel->GetTotal();
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
