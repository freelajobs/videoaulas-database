<?php

require_once "../class/ClPartner.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $id = $data['id'];
    $date_start = $data['date_start'];
    $date_end = $data['date_end'];

	$result = $ClassPartner->GetCommentsData(
        $id, 
        $ClassPartner->ConvertDataServer($date_start), 
        $ClassPartner->ConvertDataServer($date_end) 
    );

} else {
	$result = array(
        'status' => 'false',
        'error' => $postdata
    );  
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>