<?php
date_default_timezone_set('America/Sao_Paulo');
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

class ClassPermissions extends DbConnect
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
	public function Add($_name, $_description, $_service_type, $_service_status, $_service_payment, $_service_tickets, $_partner_category, $_partner_consult, $_client, $_market_status, $_market_consult, $_notifications, $_permissions, $_users)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description, $_service_type, $_service_status, $_service_payment, $_service_tickets, $_partner_category, $_partner_consult, $_client, $_market_status, $_market_consult, $_notifications, $_permissions, $_users);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO `Permissions`
						(`name`, `description`, `service_type`, `service_status`, `service_payment`, `service_tickets`, `partner_category`, `partner_consult`, `client`, `market_status`, `market_consult`, `notifications`, `permissions`, `users`)
						VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
	public function Update($_id, $_name, $_description, $_service_type, $_service_status, $_service_payment, $_service_tickets, $_partner_category, $_partner_consult, $_client, $_market_status, $_market_consult, $_notifications, $_permissions, $_users)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description, $_service_type, $_service_status, $_service_payment, $_service_tickets, $_partner_category, $_partner_consult, $_client, $_market_status, $_market_consult, $_notifications, $_permissions, $_users, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("UPDATE Permissions
						SET name = ?, description = ?, service_type = ?, service_status = ?, service_payment = ?, service_tickets = ?, partner_category = ?, partner_consult = ?, client = ?, market_status = ?, market_consult = ?, notifications = ?, permissions = ?, users = ?
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
					$_query = $_pdo->prepare("DELETE FROM Permissions WHERE id = ?");
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
						FROM Permissions
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array();
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

$ClassPermissions = ClassPermissions::GetInstance();

?>
