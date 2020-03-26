<?php
require_once "../class/ClPlans.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	//plan options
	switch ($type) {
		case 'add':
			$result = $ClassPlans->Add(
				$data['name'],
				$data['description'],
				$data['full_description'],
				$data['duration'],
				$data['value'],
				$ClassPlans->ConvertBoolServer($data['the_best']),
				$data['for'],
				$data['power'],
				$data['economic']
			);
		break;
		case 'get':
			$result = $ClassPlans->Get(
				$data['id']
			);
		break;
		case 'update':
			$result = $ClassPlans->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$data['full_description'],
				$data['duration'],
				$data['value'],
				$ClassPlans->ConvertBoolServer($data['the_best']),
				$data['for'],
				$data['power'],
				$data['economic'],
				$ClassPlans->ConvertBoolServer($data['active'])
			);
		break;
		default:
			$result = $ClassPlans->GetAll($data['for']);
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
