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
	private $image_path = "http://database.redwindow.com.br/images/partners/";
	private $image_app_path = "http://database.redwindow.com.br/images/partners_app/";
	private $image_client_path = "http://database.redwindow.com.br/images/perfil/";
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
	public function UpdateStatus($_id, $_blocked, $description)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_blocked, $description,  $_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE Partners
					SET blocked = ?, statusDescription = ?
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
	public function UpdateApproved($_id)
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
					UPDATE Partners
					SET approved = 1, blocked = 0
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
	public function UpdateReproved($_id, $description)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($description, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE Partners
					SET statusDescription = ?, reproved = 1, blocked = 0
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
						pt.id, 
						ct.name category, 
						CONCAT(pt.first_name, ' ', pt.last_name) partner_name, 
						pt.nick_name partner_nick, 
						pt.mail partner_mail, 
						pt.fone partner_fone, 
						pt.gender gender, 
						pt.blocked blocked, 
						pt.approved approved,
						pt.reproved reproved
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
					$_row['reproved'] = $_row['reproved'] == 0 ? false : true;

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
				// Get Partner
				$_query = $_pdo->prepare("
					SELECT 
						pt.*, 
						ct.name category,
						pd.*,
						ps.id_services services_ids,
						ps.prices services_prices,
						ps.actives services_actives
					FROM Partners pt
					LEFT JOIN Categories ct ON ct.id = pt.id_category
					LEFT JOIN PartnerDiary pd ON pd.id_partner = pt.id
					LEFT JOIN PartnerServices ps ON ps.id_partner = pt.id
					WHERE pt.id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_partner = array();
					$_partner['id'] = $_row['id'];
					$_partner['first_name'] = $_row['first_name'];
					$_partner['last_name'] = $_row['last_name'];
					$_partner['nick_name'] = $_row['nick_name'];
					$_partner['mail'] = $_row['mail'];
					$_partner['phone'] = $_row['fone'];
					$_partner['cel_phone'] = $_row['celfone'];
					$_partner['age'] = $_row['age'];
					$_partner['cpf'] = $_row['cpf'];
					$_partner['gender'] = $this->ConvertBoolClient($_row['gender']);
					$_partner['category'] = $this->ConvertBoolClient($_row['category']);
					$_partner['description'] = $_row['description'];
					$_partner['rate'] = $_row['rate'];
					$_partner['address'] = $_row['address'];
					$_partner['latitude'] = $_row['latitude'];
					$_partner['longitude'] = $_row['longitude'];
					$_partner['statusDescription'] = $_row['statusDescription'];
					$_partner['service_terms'] = $this->ConvertBoolClient($_row['service_terms']);
					$_partner['blocked'] = $this->ConvertBoolClient($_row['blocked']);
					$_partner['approved'] = $this->ConvertBoolClient($_row['approved']);
					$_partner['reproved'] = $this->ConvertBoolClient($_row['reproved']);
					
					$_partner['url_cpf'] = strlen($_row['url_cpf']) > 1 ?
						$this->image_path . $_row['url_cpf'] :
						null;
					$_partner['url_profile'] = strlen($_row['url_profile']) > 1 ?
						$this->image_path . $_row['url_profile'] :
						null;

					$_partner['pictures'] = array();
					if(strlen($_row['url_pic_1']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_1']);
					}
					if(strlen($_row['url_pic_2']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_2']);
					}
					if(strlen($_row['url_pic_3']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_3']);
					}
					if(strlen($_row['url_pic_4']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_4']);
					}
					if(strlen($_row['url_pic_5']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_5']);
					}

					$_diary = $this->GenerateSchendule($_row);

					if($_row['services_ids'] !== null) 
					{
						$_services_ids = array_map(
							'intval', 
							explode(',', $_row['services_ids'])
						);
						$_services = $this->GetServices($_row['services_ids']);
						$_services = $this->SortServices(
							$_services,
							array(
								'ids'    => $_row['services_ids'],
								'prices' => $_row['services_prices'],
								'actives'=> $_row['services_actives']
							)
						);
					} else {
						$_services['data'] = array();
					}

					$_comments = $this->GetComments($_row['id']);

					$_locations = $this->GetLocations($_row['id']);

					$_tmp = array(
						'partner' => $_partner,
						'services' => $_services['data'],
						'locations' => $_locations['data'],
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

	private function PopulateArray($ids, $prices, $actives, $service) {
        $result = null;
        foreach ($ids as $key => $value) {
            if ((string) $service['id'] === (string) $value) {
                $result = array(
                    "id" => $service['id'],
                    "name" => $service['name'],
					"price" => $prices[$key],
					"active" => $actives[$key] === "1" ? true : false
                );
                break;
            }
        }
        if (is_null($result)) {
            $result = array(
                "id" => $service['id'],
				"name" => $service['name'],
				"price" => $service['name'] === "Hora" ? "100,00" : "0,00",
                "active" => $service['name'] === "Hora" ? true : false
            );
        }
        return $result;
	}

    private function SortServices($system_services, $partner_services) {

        $new_menu = false;
        if (is_null($partner_services)) {
			$this->_response["data"] = Array();
        	return $this->_response;
        } else {
            $ids = explode(",", $partner_services['ids']);
            $prices = explode(",", $partner_services['prices']);
			$actives = explode(",", $partner_services['actives']);
			
            $partner_services = array();
        }

        foreach ($system_services as $value) {
            if ($new_menu === true) {
                array_push($partner_services, array(
                    "id" => $value['id'],
                    "name" => $value['name'],
					"price" => $value['name'] === "Hora" ? "100,00" : "0,00",
					"active" => $value['name'] === "Hora" ? true : false
                ));
                continue;
            } else {
                $tmp = $this->PopulateArray($ids, $prices, $actives, $value);
                array_push($partner_services, $tmp);
            }
        }
        usort($partner_services, function($a, $b) {
            return $a['id'] - $b['id'];
        });
        $this->_response["data"] = $partner_services;
        return $this->_response;
    }

	/**
	 * Create Schendule Data
	 */
	private function CreateSchendule($hoursArray, $tag)
	{
		$hours = array();//starHours
		foreach ($hoursArray as $key => $value) {
			$data = str_replace("[","",$value);
			$data = str_replace("]","",$data);
			$data = explode(",", $data);
			array_push($hours, array( //StartHour
				"id"		 =>(int)$data[0],//id_hour
				"idLocation" => $data[1] == "-" ? -1 : (int)$data[1],
				"selected"	 => $this->ConvertBoolClient($data[2])
			));
		}
		$day = array(
			"day"=>$tag,
			"hours"=>$hours
		);
		return $day;
	}

	private function GenerateSchendule($_row) 
	{
		$_data = array();
		$seg = $this->CreateSchendule(explode("|", $_row['seg_schendule']), "seg");
		$ter = $this->CreateSchendule(explode("|", $_row['ter_schendule']), "ter");
		$qua = $this->CreateSchendule(explode("|", $_row['qua_schendule']), "qua");
		$qui = $this->CreateSchendule(explode("|", $_row['qui_schendule']), "qui");
		$sex = $this->CreateSchendule(explode("|", $_row['sex_schendule']), "sex");
		$sab = $this->CreateSchendule(explode("|", $_row['sab_schendule']), "sab");
		$dom = $this->CreateSchendule(explode("|", $_row['dom_schendule']), "dom");

		array_push($_data, Array(
			'name' => $seg['day'],
			'open' => $_row['open_seg'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $seg['hours']
		));
		array_push($_data, Array(
			'name' => $ter['day'],
			'open' => $_row['open_ter'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $ter['hours']
		));
		array_push($_data, Array(
			'name' => $qua['day'],
			'open' => $_row['open_qua'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $qua['hours']
		));
		array_push($_data, Array(
			'name' => $qui['day'],
			'open' => $_row['open_qui'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $qui['hours']
		));
		array_push($_data, Array(
			'name' => $sex['day'],
			'open' => $_row['open_sex'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $sex['hours']
		));
		array_push($_data, Array(
			'name' => $sab['day'],
			'open' => $_row['open_sab'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $sab['hours']
		));
		array_push($_data, Array(
			'name' => $dom['day'],
			'open' => $_row['open_dom'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $dom['hours']
		));

		return $_data;
	}

	/**
	 * Get on Service data
	 * recive id
	 */
	public function GetServices($_list)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_list);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				$_cond = '(' . $_parameters[0] . ')';

				//Create Query
				$_query = $_pdo->prepare("
					SELECT 
						id,
						name
					FROM `Services`
					WHERE `id` IN " . $_cond
				);
				//Execute Query
				$_query->execute($_parameters);

				$_data = array();
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					array_push($_data, $_row);
				}
				return $_data;
			}
		);
	}

	/**
	* Busca os locais de serviço da modelo
	*/
	public function GetLocations($id) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id`,
					`address`,
					`description`,
					`latitude`,
					`longitude`,
					`max_time`
					FROM PartnerLocation
					WHERE `id_partner` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					array_push($_data, $_row);
				}

				//Tratamento da resposta
				if (is_null($_data)) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
					$this->_response["data"] = null;
				} else {
					$this->_response["status"] = true;
					$this->_response["error"] = null;
					$this->_response["data"] = $_data;
				}
				return $this->_response;
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
					WHERE cm.id_partner = ?
				");
				//Execute Query
				$_query->execute($_parameters);

				$_data = null;
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['user_url_perfil'] = strlen($_row['user_url_perfil']) > 1 ? 
						$this->image_client_path . $_row['user_url_perfil'] : 
						null;
					$_row['date'] = $this->ConvertDataClient($_row['date']);
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
