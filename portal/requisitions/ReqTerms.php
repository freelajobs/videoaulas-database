<?php

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];

	static $path   = "https://www.database.redwindow.com.br/terms/";
	$path_upload   = $_SERVER['DOCUMENT_ROOT'] . '/terms/';
	static $term   = "file_terms.txt";
	static $policy = "file_politcs.txt";

	//Login options
	switch ($type) {
		case 'get_term':
			$file_model = file_get_contents($path . 'model/' . $term);
			$file_client = file_get_contents($path . 'client/' . $term);
			$result = array(
				'status' => true,
				'error'  => '',
				'data'   => array(
					'client' => $file_client,
					'model' => $file_model
				)
			);
		break;
		case 'update_term':
			file_put_contents($path_upload . 'model/' . $term, $data['term_model']);
			file_put_contents($path_upload . 'client/' . $term,  $data['term_client']);
			$result = array(
				'status' => true,
				'error'  => ''
			);
		break;
		case 'update_policy':
			file_put_contents($path_upload . 'model/' . $policy, $data['term_model']);
			file_put_contents($path_upload . 'client/' . $policy,  $data['term_client']);
			$result = array(
				'status' => true,
				'error'  => ''
			);
		break;
		default:
			$file_client = file_get_contents($path . 'client/' . $policy);
			$file_model = file_get_contents($path . 'model/' . $policy);
			$result = array(
				'status' => true,
				'error'  => '',
				'data'   => array(
					'client' => $file_client,
					'model' => $file_model
				)
			);
		break;
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