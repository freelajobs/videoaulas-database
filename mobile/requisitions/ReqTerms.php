<?php

require_once "../class/ClTerms.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";
$is_term = isset($_POST['term']) ? $_POST['term'] : "";

$log = 'logs/Term.txt';

$path   = "https://www.database.redwindow.com.br/terms/";
$term   = "file_terms.txt";
$politc = "file_politcs.txt";

if($is_term === "True") {
	$file = file_get_contents($path . $term);
	$result = array(
		'status' => 'true',
		'error'  => '',
		'data'   => $file
	);
} else {
	$file = file_get_contents($path . $politc);
	$result = array(
		'status' => 'true',
		'error'  => '',
		'data'   => $file
	);
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
