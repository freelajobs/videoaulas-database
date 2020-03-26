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

class ClassCategory extends DbConnect
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
	public function Add($_name, $_description, $_url_web, $_url_mobile, $_blocked)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description, $_url_web, $_url_mobile, $_blocked);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO `Categories`
						(`name`, `description`, `url_web`, `url_mobile`, `blocked`)
						VALUES (?, ?, ?, ?, ?)
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
	public function Update($_id, $_name, $_description, $_url_web, $_url_mobile, $_blocked)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description, $_blocked, $_url_web, $_url_mobile, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					$set = "name = ?, description = ?, blocked = ?";
					if ($_parameters[3] != null) $set = $set . ", url_web = ?";
					if ($_parameters[4] != null) $set = $set . ", url_mobile = ?";

					$_parameters2 = array_values($_parameters);

					if ($_parameters[4] == null) {
            unset($_parameters[4]); // remove item at index 0
            $_parameters2 = array_values($_parameters); // 'reindex' array
	        }
	        if ($_parameters[3] == null) {
	            unset($_parameters[3]); // remove item at index 0
	            $_parameters2 = array_values($_parameters); // 'reindex' array
	        }
					//Create Query
					$_query = $_pdo->prepare("UPDATE Categories
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
					$_query = $_pdo->prepare("SELECT *
						FROM Categories
						WHERE id = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_row['blocked'] = $_row['blocked'] == 0 ? false : true;
						$_row['url_web'] = strlen($_row['url_web']) > 1 ? "http://database.redwindow.com.br/images/category/".$_row['url_web'] : null;
						$_row['url_mobile'] = strlen($_row['url_mobile']) > 1 ? "http://database.redwindow.com.br/images/category/".$_row['url_mobile'] : null;
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
					$_query = $_pdo->prepare("SELECT *
						FROM Categories
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_row['blocked'] = $_row['blocked'] == 0 ? false : true;
						$_row['url_web'] = strlen($_row['url_web']) > 1 ? "http://database.redwindow.com.br/images/categories/".$_row['url_web'] : null;
						$_row['url_app'] = strlen($_row['url_mobile']) > 1 ? "http://database.redwindow.com.br/images/categories/".$_row['url_mobile'] : null;
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

$ClassCategory = ClassCategory::GetInstance();

?>
