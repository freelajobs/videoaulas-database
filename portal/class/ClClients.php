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

class ClassClients extends DbConnect
{
	private static $instance = NULL;
	private $url_profile = "http://database.redwindow.com.br/images/perfil/";  
	private $url_document = "http://database.redwindow.com.br/images/documents/";
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
	 * Add Partner in database
	 * first_name, last_name, mail, password, home_address, fone, cpf, url_cpf, service_terms, blocked,
	 * approved, nick_name, description, id_category, ids_services, work_address, hour_value, age, gender, imei,
	 * url_movie, url_pic_1, url_pic_2, url_pic_3, url_pic_4, url_pic_5
	 */
	public function Add($_first_name, $_last_name, $_mail, $_password, $_fone, $_home_address, $_cpf, $_url_cpf, $_service_terms,
		$_nick_name, $_url_perfil, $_age, $_description, $_gender, $_imei
	)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_first_name, $_last_name, $_mail, $_password, $_fone, $_home_address, $_cpf, $_url_cpf, $_service_terms,
			$_nick_name, $_url_perfil, $_age, $_description, $_gender, $_imei
		);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO Users(
							`first_name`, `last_name`, `mail`, `password`, `fone`, `home_address`, `cpf`, `url_cpf`, `service_terms`,
							 `nick_name`, `url_perfil`, `age`, `description`, `gender`, `imei`
						)
						VALUES ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '')
					");
					//Execute Query
					$_query->execute($_parameters);

					$id = $_pdo->lastInsertId();
					//Tratamento da resposta
					if(is_null($_query->errorInfo()))
					{
						$this->_response["status"] = false;
						$this->_response["error"]  = $_query->errorInfo();
					}
					else
					{
						$this->_response["status"] = true;
						$this->_response["data"] = $id;
					}
					return $this->_response;
				}
		);
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function Update($_id, $_name, $_description, $_blocked)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description, $_blocked, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("UPDATE Services
						SET name = ?, description = ?, blocked = ?
						WHERE id = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					//Tratamento da resposta
					if(is_null($_query->errorInfo()))
					{
						$this->_response["status"] = false;
						$this->_response["error"]  = $_query->errorInfo();
					}
					else
					{
						$this->_response["status"] = true;
					}
					return $this->_response;
				}
		);
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function UpdateStatus($_id, $_blocked)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_blocked, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("UPDATE Users
					SET blocked = ?
					WHERE id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				//Tratamento da resposta
				if(is_null($_query->errorInfo()))
				{
					$this->_response["status"] = false;
					$this->_response["error"]  = $_query->errorInfo();
				}
				else
				{
					$this->_response["status"] = true;
					$this->_response["data"] = $_parameters[0];
				}
				return $this->_response;
			}
		);
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function Get($_id)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("
					SELECT 
						*
					FROM Users
					WHERE id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['url_cpf'] = strlen($_row['url_cpf']) > 1 ? 
						$this->url_document . $_row['url_cpf'] : 
						null;

					$_row['url_profile'] = strlen($_row['url_profile']) > 1 ? 
						$this->url_profile . $_row['url_profile'] : 
						null;
					$_row['service_terms'] = $this->ConvertBoolClient($_row['service_terms']);
					$_row['gender'] = $this->ConvertBoolClient($_row['gender']);
					$_row['blocked'] = $this->ConvertBoolClient($_row['blocked']);
					$_row['approved'] = $this->ConvertBoolClient($_row['approved']);
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
	 * Get on System User in database
	 * recive id
	 */
	public function GetAll()
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("
					SELECT 
						id, 
						CONCAT(first_name, ' ', last_name) name, 
						nick_name nick, 
						mail mail, 
						phone, 
						gender, 
						blocked, 
						approved
					FROM Users
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['gender'] = $this->ConvertBoolClient($_row['gender']);
					$_row['blocked'] = $this->ConvertBoolClient($_row['blocked']);
					$_row['approved'] = $this->ConvertBoolClient($_row['approved']);
					$_tmp = $_row;
					array_push($_data, $_tmp);
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

$ClassClients = ClassClients::GetInstance();

?>
