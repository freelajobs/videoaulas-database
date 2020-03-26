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
	private $url_profile = "http://database.redwindow.com.br/images/perfil/"; 
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
	public function Update($_id, $_first_name, $_last_name, $_mail, $_password, $_fone, $_cpf, $_id_job, $_blocked, $_pic)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_first_name, $_last_name, $_mail, $_password, $_fone, $_cpf, $_id_job, $_blocked, $_pic, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				$set = "first_name = ?, last_name = ?, mail = ?, password = ?, fone = ?, cpf = ?, id_job = ?, blocked = ?";
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
				$_query = $_pdo->prepare("
					SELECT 
						su.id, 
						su.mail, 
						su.first_name, 
						su.last_name, 
						su.fone, su.cpf, 
						su.url_perfil url_profile, 
						su.password,
						su.blocked,
						pm.id job_id, 
						pm.name job
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
					$_tmp = $_row;
					$_tmp['blocked'] = $this->ConvertBoolClient($_row['blocked']);

					$_tmp['url_profile'] = strlen($_tmp['url_profile']) > 1 ? 
						$this->url_profile . $_tmp['url_profile'] : 
						null;
					$_data = $_tmp;
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
						su.id, 
						su.mail, 
						su.first_name, 
						su.last_name,  
						su.fone, 
						su.cpf,
						su.blocked,  
						pm.name job
					FROM SystemUsers su
					LEFT JOIN Permissions pm ON pm.id = su.id_job
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['blocked'] = $this->ConvertBoolClient($_row['blocked']);
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

	/**
	 * Get on System Offices in system
	 * recive id
	 */
	public function GetOffices()
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
						name
					FROM Permissions
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
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

	public function UpdateProfileImage($id, $name) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($name, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
        {
            $set = "url_perfil = ?";

            //Create Query
            $_query = $_pdo->prepare("UPDATE `SystemUsers`
                SET " . $set . "
                WHERE id = ?
            ");
            //Execute Query
            $_query->execute($_parameters);
            //Tratamento da resposta
            if (is_null($_query->errorInfo())) {
                $this->_response["status"] = false;
                $this->_response["error"] = $_query->errorInfo();
            } else {
                $this->_response["status"] = true;
            }
            return $this->_response;
        });
	}
}

$ClassSystemUsers = ClassSystemUsers::GetInstance();

// echo json_encode($ClassSystemUsers->GetOffices());
?>
