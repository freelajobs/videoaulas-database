<?php

require_once "../class/ClCategories.php";

$log = 'logs/Categories';

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
	$category = 'categories';
	$url_web = 'url_web';
	$url_app = 'url_app';
	$web = 'web';
	$app = 'app';
	
	//Login options
	switch ($type) {
		case 'add':
			// $_web = $DbImages->SaveImage(
			// 	$category,
			// 	$url_web,
			// 	$web,
			// 	$data[$url_web]
			// );

			// $_web = 'web.png';
			// $DbImages->base64_to_jpeg($data[$url_web], $_web);

			$result = $ClassCategory->Add(
				$data['name'],
				$data['description'],
				// $_web["name"],
				// $_app["name"],
				$ClassCategory->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'update':
			// $_app = $this->GetImage(
			// 	$data[$url_app],
			// 	$category,
			// 	$url_app,
			// 	$app,
			// 	$data['name']
			// );

			// $_web = $this->GetImage(
			// 	$data[$url_web],
			// 	$category,
			// 	$url_web,
			// 	$web,
			// 	$data['name']
			// );

			$result = $ClassCategory->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				// !is_null($_web) ? $_web['name'] : null,
				// !is_null($_app) ? $_app['name'] : null,
				$ClassCategory->ConvertBoolServer($data['blocked'])
			);
		break;
		case 'get':
			$result = $ClassCategory->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassCategory->GetAll();
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

// Save Log
file_put_contents($log . '_' . $type . '.txt', $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
