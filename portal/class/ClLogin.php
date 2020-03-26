<?php

$LIST_INCLUDE = array(
	"../../server/DbConnect.php"
);
foreach ($LIST_INCLUDE as $value)
{
	if (!file_exists($value))
	{
		die('Classe não encontrada ' . $value);
	}
	require_once $value;
}

class ClassLogin extends DbConnect
{
	private static $instance = NULL;
	private $path_image = "http://database.redwindow.com.br/images/system_profile/";  
	private $default_image = "default_user.png";  
	private $_response = array(
		'status' => '',
		'error'  => '',
		'data'   => ''
	);

	function __construct()
	{

	}

	/**
	 * Pega a instancia unica do objeto de classe
	 */
	public static function GetInstance()
	{
			if(is_null(self::$instance)){
					self::$instance = new self();
			}
			return self::$instance;
	}

	/**
	 * Return SystemUser data
	 * recive mail and password strings
	 */
	public function Login($_mail, $_password)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_mail, $_password);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("SELECT *
					FROM SystemUsers
					WHERE mail = ? AND password = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					if ($_row['id'] !== null) {
						$_tmp = Array(
							"token" => $_row['id'],
							"blocked" => $this->ConvertBoolClient($_row['blocked']),
							"office_pk" => $_row['id_job'],
							"mail" => $_row['mail'],
							"password" => $_row['password'],
							"first_name" => $_row['first_name'],
							"last_name" => $_row['last_name'],
							"fone" => $_row['fone'],
							"cpf" => $_row['cpf'],
							"url_perfil" => $_row['url_perfil']
						);
	
						$_tmp['url_perfil'] = strlen($_tmp['url_perfil']) > 1 ? 
							$this->path_image.$_tmp['url_perfil'] : 
							$this->path_image . $this->default_image;
							
						$_data = $_tmp;
					}
				}
				
				if ($_data !== null) {
					$_id = $_data['office_pk'];

					//Create Query
					$_query = $_pdo->prepare("SELECT *
						FROM Permissions WHERE id = ?
					");
					//Execute Query
					$_query->execute(array($_id));
					$_permissions = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_permissions['name']        = $_row['name'];
						$_permissions['description'] = $_row['description'];
						$_permissions['permissions'] = Array(
							'service_type' => $this->ConvertBoolClient($_row['service_type']),
							'service_status' => $this->ConvertBoolClient($_row['service_status']),
							'service_payment' => $this->ConvertBoolClient($_row['service_payment']),
							'service_tickets' => $this->ConvertBoolClient($_row['service_tickets']),
							'partner_category' => $this->ConvertBoolClient($_row['partner_category']),
							'partner_consult' => $this->ConvertBoolClient($_row['partner_consult']),
							'client' => $this->ConvertBoolClient($_row['client']),
							'market_status' => $this->ConvertBoolClient($_row['market_status']),
							'market_consult' => $this->ConvertBoolClient($_row['market_consult']),
							'notifications' => $this->ConvertBoolClient($_row['notifications']),
							'permissions' => $this->ConvertBoolClient($_row['permissions']),
							'users' => $this->ConvertBoolClient($_row['users']),
							'reports' => $this->ConvertBoolClient($_row['reports']),
							'plan_costs' => $this->ConvertBoolClient($_row['plan_costs']),
							'plan_models' => $this->ConvertBoolClient($_row['plan_models']),
							'plan_clients' => $this->ConvertBoolClient($_row['plan_clients'])
						);
					}
					$_data['office'] = $_permissions;
				}

				//Tratamento da resposta
				if(is_null($_data))
				{
					$this->_response["status"] = false;
					$this->_response["error"]  = $_query->errorInfo();
				} 
				else if($_data['blocked'] === true)
				{
					$this->_response["status"] = false;
					$this->_response["error"]  = "Usuário bloqueado, entre em contato com o administrador do sistema";
				}
				else
				{
					$this->_response["status"] = true;
					$this->_response["data"]   = $_data;
				}
				return $this->_response;
			}
		);
	}
}

$ClassLogin = ClassLogin::GetInstance();
echo json_encode($ClassLogin->Login('rodrigoazurex@gmail.com', '240489'));
?>
