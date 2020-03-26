<?php

require_once "../class/ClLogin.php";

$login_type = isset($_POST['type']) ? $_POST['type'] : "";

//Login options
switch ($login_type) {
	case 'login_partner':
		$result = $ClassLogin->LoginPartner(
			"primeiro_parceiro@redwindow.com.br", //$_POST['mail']
			"redwindow" //$_POST['password']
		);
	break;
	case 'login_mobile':
		$result = $ClassLogin->LoginMobile(
			"usuario_sistema@redwindow.com.br", //$_POST['mail']
			"redwindow" //$_POST['password']
		);
	break;
	case 'login_facebook':
		$result = $ClassLogin->LoginFacebook(
			"usuario_sistema@redwindow.com.br" //$_POST['mail']
		);
	break;
	default:
		$result = $ClassLogin->LoginSystem(
			"rodrigoazurex@gmail.com", //$_POST['mail']
			"240489" //$_POST['password']
		);
		break;
}

//Return Json
echo json_encode($result);
?>
