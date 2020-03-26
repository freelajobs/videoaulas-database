<?php
date_default_timezone_set('America/Sao_Paulo');
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

class ClassMarket extends DbConnect
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
	public function Add($_title, $_mail, $_url, $_duration, $_value, $_description, $_url_pic)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		$_today = new DateTime();
		$_today = $_today->format('Y-m-d');
		//Create Data
		$_data = array($_title, $_url, $_url_pic, $_description, $_today, $_duration, $_value, $_mail);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO `Marketing`
						(`id_status`, `name`, `url`, `url_pic`, `description`, `date_created`, `date_payment`, `duration`, `value`, `mail`)
						VALUES (1, ?, ?, ?, ?, ?, NULL, ?, ?, ?)
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
	public function Update($_id, $_name, $_mail, $_url, $_duration, $_value, $_description, $_url_pic)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_url, $_description, $_duration, $_value, $_mail, $_url_pic, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					$set = "name = ?, url = ?, description = ?, duration = ?, value = ?, mail = ?";
					if ($_parameters[6] != null) $set = $set . ", url_pic = ?";

					$_parameters2 = array_values($_parameters);

					if ($_parameters[6] == null) {
            unset($_parameters[6]); // remove item at index 0
            $_parameters2 = array_values($_parameters); // 'reindex' array
	        }
					//Create Query
					$_query = $_pdo->prepare("UPDATE Marketing
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
					$_query = $_pdo->prepare("SELECT mk.id, ms.name status, mk.name, mk.mail, mk.url, mk.date_created, mk.date_payment, mk.duration, mk.value, mk.url_pic, mk.description
							FROM Marketing mk
							LEFT JOIN MarketingStatus ms ON ms.id = mk.id_status
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_row['date_created'] = DateTime::createFromFormat('Y-m-d',$_row['date_created'])->format('d-m-Y');
						if(!is_null($_row['date_payment']))
							$_row['date_payment'] = DateTime::createFromFormat('Y-m-d',$_row['date_payment'])->format('d-m-Y');
						$_row['url_pic'] = strlen($_row['url_pic']) > 1 ? "http://database.redwindow.com.br/images/market/".$_row['url_pic'] : null;

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

$ClassMarket = ClassMarket::GetInstance();

?>
