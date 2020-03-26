<?php

require_once "../class/ClSystemUsers.php";

$postdata = file_get_contents("php://input");

function GetImage($file, $category, $url, $type, $data) {
	$tmp = is_null($file) || empty($file) ? 
		null : 
		$DbImages->SaveImage(
			$category,
			$url,
			$type,
			$data
		);
	return $tmp;
}

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	//Login options
	switch ($type) {
		case 'get_office':
			$result = $ClassSystemUsers->GetOffices();
		break;
		case 'add':
			$result = $ClassSystemUsers->Add(
				$data['first_name'],
				$data['last_name'],
				$data['mail'],
				$data['password'],
				$data['fone'],
				$data['cpf'],
				$data['id_job']
			);
		break;
		case 'update':
			// $_web = 'web.png';
			// $DbImages->base64_to_jpeg($data[$url_web], $_web);
			
			$result = $ClassSystemUsers->Update(
				$data['id'],
				$data['first_name'],
				$data['last_name'],
				$data['mail'],
				$data['password'],
				$data['fone'],
				$data['cpf'],
				$data['id_job'],
				$ClassSystemUsers->ConvertBoolServer($data['blocked']),
				null
			);
		break;
		case 'get':
			$result = $ClassSystemUsers->Get(
				$data['id']
			);
		break;
		case 'remove':
			$result = $ClassSystemUsers->Remove(
				$data['id']
			);
		break;
		default:
			$result = $ClassSystemUsers->GetAll();
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
