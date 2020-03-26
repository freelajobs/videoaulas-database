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

class ClassPartners extends DbConnect
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
	 * Get Comments
	 * recive id
	 */
	public function GetCommentsData($_id, $_start, $_end)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id, $_start, $_end);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("SELECT 
						cm.id id, 
						cm.description description, 
						cm.ratting rate, 
						cm.created date, 
						cm.type `type`,
						us.nick_name user_nick, 
						us.url_profile user_url_perfil
					FROM Comments cm
					LEFT JOIN Users us ON us.id = cm.id_user
					WHERE cm.id_partner = ? AND (cm.created BETWEEN ? AND ?)
                ");
				//Execute Query
				$_query->execute($_parameters);
                // echo $_query->debugDumpParams();
				$_data = array();
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['user_url_perfil'] = strlen($_row['user_url_perfil']) > 1 ? 
						$this->image_client_path . $_row['user_url_perfil'] : 
						null;
					$_row['date'] = $this->ConvertDataClient($_row['date']);
					array_push($_data, $_row);
				}
				return $_data;
			}
		);
	}

	/**
	 * Add Partner in database
	 * first_name, last_name, mail, password, home_address, fone, cpf, url_cpf, service_terms, blocked,
	 * approved, nick_name, description, id_category, ids_services, work_address, hour_value, age, gender, imei,
	 * url_movie, url_pic_1, url_pic_2, url_pic_3, url_pic_4, url_pic_5
	 */
	public function Add(
		$_first_name, $_last_name, $_mail, $_password, $_home_address, $_fone, $_cpf, $_url_cpf, $_service_terms, $_blocked,
		$_approved, $_nick_name, $_description, $_id_category, $_ids_services, $_work_address, $_hour_value, $_age, $_gender, $_imei,
		$_url_movie, $_url_pic_1, $_url_pic_2, $_url_pic_3, $_url_pic_4, $_url_pic_5
		)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array(
			$_first_name, $_last_name, $_mail, $_password, $_home_address, $_fone, $_cpf, $_url_cpf, $_service_terms, $_blocked,
			$_approved, $_nick_name, $_description, $_id_category, $_ids_services, $_work_address, $_hour_value, $_age, $_gender, $_imei,
			$_url_movie, $_url_pic_1, $_url_pic_2, $_url_pic_3, $_url_pic_4, $_url_pic_5
		);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO Partners(
							`first_name`, `last_name`, `mail`, `password`, `home_address`, `fone`, `cpf`, `url_cpf`, `service_terms`, `blocked`,
							`approved`, `nick_name`, `description`, `id_category`, `ids_services`, `work_address`, `hour_value`, `age`, `gender`, `imei`,
							`url_movie`, `url_pic_1`, `url_pic_2`, `url_pic_3`, `url_pic_4`, `url_pic_5`
						)
						VALUES ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')
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
					$_query = $_pdo->prepare("SELECT pt.*, ct.name category
						FROM Partners pt
						LEFT JOIN Categories ct ON ct.id = pt.id_category
						WHERE pt.id = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_row['service_terms'] = $_row['service_terms'] == 0 ? false : true;
						$_row['blocked']  = $_row['blocked']  == 0 ? false : true;
						$_row['approved'] = $_row['approved'] == 0 ? false : true;
						$_row['gender']   = $_row['gender']   == 0 ? false : true;

						$_services = array();
						$_ids = explode(",", $_row['ids_services']);
						for ($i=0; $i < count($_ids); $i++)
						{
								$_service = $this->GetService($_ids[$i]);
								array_push($_services, $_service);
						}

						$_comments = $this->GetComments($_row['id']);
						$_diary = $this->GetDiary($_row['id']);

						$_tmp = array(
							'partner' => $_row,
							'services' => $_services,
							'diary' => $_diary,
							'comments' => $_comments
						);
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
					$_query = $_pdo->prepare("SELECT pt.id, ct.name category, CONCAT(pt.first_name, ' ', pt.last_name) partner_name, pt.nick_name partner_nick, pt.mail partner_mail, pt.fone partner_fone, pt.gender gender, pt.blocked blocked, pt.approved approved
						FROM Partners pt
						LEFT JOIN Categories ct ON ct.id = pt.id_category
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_row['gender']  = $_row['gender']  == 0 ? false : true;
						$_row['blocked'] = $_row['blocked'] == 0 ? false : true;
						$_row['approved'] = $_row['approved'] == 0 ? false : true;

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
	 * Get on Service data
	 * recive id
	 */
	public function GetService($_id)
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
					$_query = $_pdo->prepare("SELECT name, description value FROM Services WHERE id = ?");
					//Execute Query
					$_query->execute($_parameters);

					$_data = null;
					while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
							$_data = $_row;
					}
					return $_data;
				}
		);
	}

	/**
	 * Get Comments
	 * recive id
	 */
	public function GetComments($_id)
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
						ct.id comment_id, 
						ct.title comment_title, 
						ct.description comment_description, 
						ct.ratting comment_ratting, 
						ct.date comment_date, 
						us.nick_name user_nick, 
						us.url_perfil user_url_perfil
					FROM PartnerComments ct
					LEFT JOIN ServiceCard sc ON sc.id = ct.id_serviceCard
					LEFT JOIN Users us ON us.id = sc.id_user
					WHERE ct.id = ?
				");
				//Execute Query
				$_query->execute($_parameters);

				$_data = null;
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
						$_row['user_url_perfil'] = strlen($_row['user_url_perfil']) > 1 ? "http://database.redwindow.com.br/images/perfil/".$_row['user_url_perfil'] : null;
						$_row['comment_date'] = DateTime::createFromFormat('Y-m-d H:i:s',$_row['comment_date'])->format('d-M-Y');
						$_data = $_row;
				}
				return $_data;
			}
		);
	}

	/**
	 * Get Comments
	 * recive id
	 */
	public function GetDiary($_id)
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
				$_query = $_pdo->prepare("SELECT * FROM PartnerDiary WHERE id = ?");
				//Execute Query
				$_query->execute($_parameters);

				$_data = null;
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['open_seg']  = $_row['open_seg']  == 0 ? false : true;
					$_row['open_ter']  = $_row['open_ter']  == 0 ? false : true;
					$_row['open_qua']  = $_row['open_qua']  == 0 ? false : true;
					$_row['open_qui']  = $_row['open_qui']  == 0 ? false : true;
					$_row['open_sex']  = $_row['open_sex']  == 0 ? false : true;
					$_row['open_sab']  = $_row['open_sab']  == 0 ? false : true;
					$_row['open_dom']  = $_row['open_dom']  == 0 ? false : true;

					$_row['today'] = DateTime::createFromFormat('Y-m-d',$_row['today'])->format('d-m-Y');
					$_data = $_row;
				}
				return $_data;
			}
		);
	}

	/**
	 * Get Comments
	 * recive id
	 */
	public function GetDays($_id)
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
				$_query = $_pdo->prepare("SELECT * FROM PartnerDiary WHERE id = ?");
				//Execute Query
				$_query->execute($_parameters);

				$_data = null;
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['open_seg']  = $_row['open_seg']  == 0 ? false : true;
					$_row['open_ter']  = $_row['open_ter']  == 0 ? false : true;
					$_row['open_qua']  = $_row['open_qua']  == 0 ? false : true;
					$_row['open_qui']  = $_row['open_qui']  == 0 ? false : true;
					$_row['open_sex']  = $_row['open_sex']  == 0 ? false : true;
					$_row['open_sab']  = $_row['open_sab']  == 0 ? false : true;
					$_row['open_dom']  = $_row['open_dom']  == 0 ? false : true;

					$_row['today'] = DateTime::createFromFormat('Y-m-d',$_row['today'])->format('d-m-Y');
					$_data = $_row;
				}
				return $_data;
			}
		);
	}
}

$ClassPartners = ClassPartners::GetInstance();

// echo json_encode($ClassPartners->Get(1));
?>
