<?php

require_once "../class/ClSystemUsers.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$id = $data['id'];
	$type = $data['type'];
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	switch ($type) {
		case 'document':
			$path = $path + '/images/partners/cpf_identify_' . $id . '.png';
			break;
		default:
			$path = $path + '/images/system_profile/profile_identify_' . $id . '.png';
		break;
	}
	
	$result = $DbImages->base64_to_jpeg(
		$data['image'],
		$path
	);

	if ($result === true) {

		switch ($type) {
			case 'document':
				$img = 'cpf_identify_' . $id . '.png';
				$result = 'cpf_identify_' . $id . '.png';
				break;
			default:
				$img = 'profile_identify_' . $id . '.png';
				$result = $ClassSystemUsers->UpdateProfileImage($id, $img);
			break;
		}
		
		$result = array(
			'status' => true,
			'message' => $result
		);  
	} else {
		$result = array(
			'status' => false,
			'error' => $result
		);  
	}
	
} else {
	$result = array(
        'status' => false,
        'error' => 'dados não enviados'
    );  
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
