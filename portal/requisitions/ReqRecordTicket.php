<?php

require_once "../class/ClRecordTicket.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];
    
	// options
	switch ($type) {
		case 'today':
			$result = $ClassRecordTicket->GetToday();
		break;
		case 'week':
			$result = $ClassRecordTicket->GetWeek();
		break;
		case 'year':
			$result = $ClassRecordTicket->GetYear();
		break;
		default:
			$result = $ClassRecordTicket->GetTotal();
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
