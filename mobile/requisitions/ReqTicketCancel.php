<?php

require_once "../class/ClTickets.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";
$id = isset($_POST['id']) ? $_POST['id'] : "0";
$nick = isset($_POST['nick']) ? $_POST['nick'] : "";
$comment = isset($_POST['comment']) ? $_POST['comment'] : "";
$commentType = isset($_POST['commentType']) ? $_POST['commentType'] : "";

$file = 'logs/TicketComment.txt';

file_put_contents($file, json_encode(array(
	'data' => array(
		'type' => $type,
		'id' => $id,
		'nick' => $nick,
		'comment' => $comment,
		'commentType' => $commentType
	)
)));

$result = array(
    "status" => "true",
     "error" => "",
     "data" =>  array(
         "id" => "1",
         "type" => $commentType,
         "nick" => $nick,
         "image" => "http://localhost/~rodrigopimentel/Trabalhos/RedWindow/database/mobile/requisitions/images/document_identify_1.png",
         "rating" => "0",
         "message" => $comment
     )
);

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
