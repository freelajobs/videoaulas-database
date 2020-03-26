<?php

$LIST_INCLUDE = array(
	"../../server/DbImages.php",
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

class ClassSystemUsers extends DbConnect
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
	 * Add System User in database
	 * recive mail, password, first name
	 */
	public function Add($_first_name, $_last_name, $_mail, $_password, $_fone, $_cpf, $_id_job)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_first_name, $_last_name, $_mail, $_password, $_fone, $_cpf, $_id_job);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO `SystemUsers`
						( `first_name`,  `last_name`,  `mail`,  `password`,  `fone`,  `cpf`,  `id_job`)
						VALUES (?, ?, ?, ?, ?, ?, ?)
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
	public function Update($_id, $_first_name, $_last_name, $_mail, $_password, $_fone, $_cpf, $_id_job, $_pic)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_first_name, $_last_name, $_mail, $_password, $_fone, $_cpf, $_id_job, $_pic, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					$set = "first_name = ?, last_name = ?, mail = ?, password = ?, fone = ?, cpf = ?, id_job = ?";
					if ($_parameters[7] != null) $set = $set . ", url_perfil = ?";

					$_parameters2 = array_values($_parameters);

	        if ($_parameters[7] == null) {
	            unset($_parameters[7]); // remove item at index 0
	            $_parameters2 = array_values($_parameters); // 'reindex' array
	        }
					//Create Query
					$_query = $_pdo->prepare("UPDATE SystemUsers
						SET " . $set . "
						WHERE id = ?
					");
					//Execute Query
					$_query->execute($_parameters2);
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
					$_query = $_pdo->prepare("SELECT su.id, su.mail, su.first_name, su.last_name, pm.id job_id, pm.name job, su.fone, su.cpf, su.url_perfil, su.password
							FROM SystemUsers su
							LEFT JOIN Permissions pm ON pm.id = su.id_job
							WHERE su.id = ?
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

					//Create Query
					$_query = $_pdo->prepare("SELECT id, name FROM Permissions");
					//Execute Query
					$_query->execute($_parameters);
					$_tmp = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						array_push($_tmp, $_row);
					}
					$_data['jobs'] = array($_tmp);
					
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
					$_query = $_pdo->prepare("SELECT su.id, su.mail, su.first_name, su.last_name, pm.name job, su.fone, su.cpf, su.url_perfil
							FROM SystemUsers su
							LEFT JOIN Permissions pm ON pm.id = su.id_job
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array(
						'data' => array(),
						'jobs' => array()
					);
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_tmp = $_row;
						array_push($_data['data'], $_tmp);
					}

					//Create Query
					$_query = $_pdo->prepare("SELECT id, name FROM Permissions");
					//Execute Query
					$_query->execute($_parameters);
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_data['jobs'] = array($_row);
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
	public function Remove($_id)
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
					$_query = $_pdo->prepare("DELETE FROM SystemUsers WHERE id = ?");
					//Execute Query
					$_query->execute($_parameters);
					//Create Response
					$this->_response["status"] = true;
					return $this->_response;
				}
		);
	}
}

$ClassSystemUsers = ClassSystemUsers::GetInstance();

?>
