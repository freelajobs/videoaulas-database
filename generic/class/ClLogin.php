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
	public function LoginSystem($_mail, $_password)
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
						$_row['url_perfil'] = strlen($_row['url_perfil']) > 1 ? "http://database.redwindow.com.br/images/system_profile/".$_row['url_perfil'] : null;
						$_data = $_row;
					}
					$_id = $_data['id_job'];

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
						$_row['service_type'] = $_row['service_type'] == 0 ? false : true;
						$_row['service_status'] = $_row['service_status'] == 0 ? false : true;
						$_row['service_payment'] = $_row['service_payment'] == 0 ? false : true;
						$_row['service_tickets'] = $_row['service_tickets'] == 0 ? false : true;
						$_row['partner_category'] = $_row['partner_category'] == 0 ? false : true;
						$_row['partner_consult'] = $_row['partner_consult'] == 0 ? false : true;
						$_row['client'] = $_row['client'] == 0 ? false : true;
						$_row['market_status'] = $_row['market_status'] == 0 ? false : true;
						$_row['market_consult'] = $_row['market_consult'] == 0 ? false : true;
						$_row['notifications'] = $_row['notifications'] == 0 ? false : true;
						$_row['permissions'] = $_row['permissions'] == 0 ? false : true;
						$_row['users'] = $_row['users'] == 0 ? false : true;
						$_permissions = $_row;
					}

					$_data['permission'] = $_permissions;

					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
						$this->_response["error"]  = $_query->errorInfo();
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

	/**
	 * Return SystemUser data
	 * recive mail and password strings
	 */
	public function LoginPartner($_mail, $_password)
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
						FROM Partners
						WHERE mail = ? AND password = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_data = $_row;
					}
					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
						$this->_response["error"]  = $_query->errorInfo();
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

	/**
	 * Return SystemUser data
	 * recive mail and password strings
	 */
	public function LoginMobile($_mail, $_password)
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
						FROM Users
						WHERE mail = ? AND password = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_data = $_row;
					}
					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
						$this->_response["error"]  = $_query->errorInfo();
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

	/**
	 * Return SystemUser data
	 * recive mail and password strings
	 */
	public function LoginFacebook($_mail)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_mail);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("SELECT *
						FROM Users
						WHERE mail = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_data = $_row;
					}
					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
						$this->_response["error"]  = $_query->errorInfo();
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

// echo json_encode($ClassLogin->LoginSystem('rodrigoazurex@gmail.com', '240489'));
?>
