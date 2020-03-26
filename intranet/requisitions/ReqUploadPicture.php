<?php

require_once "../class/ClPartner.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$id = $data['id'];
	$type = $data['type'];
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	if ($id === null) {
		$result = array(
			'status' => false,
			'error' => 'dados não enviados pk'
		); 
	} else {

		switch ($type) {
			case 'profissional':
				$index = $data['index'];
				$path = $path . '/images/partners_app/partner_'. $id .'_img_' . $index . '.jpeg';;
				break;
			case 'document':
				$path = $path . '/images/partners/cpf_identify_' . $id . '.png';
				break;
			case 'picture_document':
				$path = $path . '/images/partners/document_identify_' . $id . '.png';
				break;
			default:
				$path = $path . '/images/partners/profile_identify_' . $id . '.png';
			break;
		}
		
		$result = $DbImages->base64_to_jpeg(
			$data['image'],
			$path
		);
	
		if ($result === true) {
			$http = 'https://database.redwindow.com.br';
			switch ($type) {
				case 'document':
					$img = 'cpf_identify_' . $id . '.png';
					$ClassPartner->UpdateProfileCPF($id, $img);
					$result = $http . '/images/partners/cpf_identify_' . $id . '.png';
					break;
				case 'picture_document':
					$img = 'document_identify_' . $id . '.png';
					$ClassPartner->UpdateProfileDocument($id, $img);
					$result = $http . '/images/partners/document_identify_' . $id . '.png';
					break;
				case 'profissional':
					$name = 'partner_'. $id .'_img_' . $index . '.jpeg';
					$index = $data['index'];
					$result = $ClassPartner->UpdateAppImage($id, $index, $name);
					if($result['status'] === true){
						$result = $http . '/images/partners_app/' . $name;
					}
					break;
				default:
					$img = 'profile_identify_' . $id . '.png';
					$ClassPartner->UpdateProfileImage($id, $img);
					$result = $http . '/images/partners/profile_identify_' . $id . '.png';
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
