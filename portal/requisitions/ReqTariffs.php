<?php

require_once "../class/ClTarrifs.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];
	
	//Login options
	switch ($type) {
		case 'add':
			$result = $ClassTarrifs->Add(
				$data['max_value'], 
				$ClassTarrifs->ConvertBoolServer($data['is_percent']),
				$data['value']
			);
		break;
		case 'update':
			$result = $ClassTarrifs->Update(
				$data['id'],
				$data['max_value'], 
				$ClassTarrifs->ConvertBoolServer($data['is_percent']),
				$data['value']
			);
		break;
		case 'get':
			$result = $ClassTarrifs->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassTarrifs->GetAll();
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
