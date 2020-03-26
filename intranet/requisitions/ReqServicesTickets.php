<?php

require_once "../class/ClServicesTickets.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $id = $data['id'];
    $type = $data['type'];
    switch ($type) {
        case 'today':
            $result = $ClassServiceTickets->GetToday($id);
            break;        
        case 'finished':
            $result = $ClassServiceTickets->GetFinished($id);
            break;        
        case 'schendule':
            $result = $ClassServiceTickets->GetSchendule($id);
            break;        
        default:
            $result = $ClassServiceTickets->Get($id);
            break;
    }

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