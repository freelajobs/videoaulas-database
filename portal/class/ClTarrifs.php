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

class ClassTarrifs extends DbConnect
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
	public function Add($max_value, $is_percent, $value)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($max_value, $is_percent, $value);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("
					INSERT INTO `Tariffs`
						(
							`max_value`, 
							`is_percent`, 
							`value`
						)
					VALUES 
						(
							?, 
							?, 
							?
						)
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
	public function Update($_id, $max_value, $is_percent, $value)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($max_value, $is_percent, $value, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				$set = "max_value = ?, is_percent = ?, value = ?";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE Tariffs
					SET " . $set . "
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
						`id`,
						`max_value`, 
						`is_percent`, 
						`value`
					FROM Tariffs
					WHERE id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['is_percent'] = $this->ConvertBoolClient($_row['is_percent']);
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
						`id`,
						`max_value`, 
						`is_percent`, 
						`value`
					FROM Tariffs
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['is_percent'] = $this->ConvertBoolClient($_row['is_percent']);
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

$ClassTarrifs = ClassTarrifs::GetInstance();

// echo json_encode($ClassTarrifs->GetAll());
?>
