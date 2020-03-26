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

class ClassModels extends DbConnect
{
  	private static $instance = NULL;

	private $_image_path = 'http://database.redwindow.com.br/images/partners_app/';
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
     * Get on All services type from database
     */
    public function GetAllServices() {
        //Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id`,
					`name`, 
					`description` 
					FROM `Services` 
					WHERE `blocked` = 0
				");
				//Execute Query
				$_query->execute();
				$_data = array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_tmp = $_row;
					array_push($_data, $_tmp);
				}
				//Tratamento da resposta
				if (!$_query) {
					$this->_response["status"] = false;
					$this->_response["error"] = $this->_response["error"] = $_query->errorInfo();
				} else {
					$this->_response["status"] = true;
					$this->_response["data"] = $_data;
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

	// </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Service Functions">
    public function SortServices($system_services, $partner_services) {

        $new_menu = false;
        if (is_null($partner_services)) {
			$this->_response["data"] = Array();
        	return $this->_response;
        } else {
            $ids = explode(",", $partner_services['id_services']);
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
	* Busca as modelos
	*/
	public function Get() {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					p.id id,
					p.url_pic_1 image,
					p.nick_name nick,
					p.age age,
					p.gender gender,
					p.rate rate,
					p.id_category categories,
					p.address address,
					p.latitude latitude,
					p.longitude longitude,
					ps.id_services,
					ps.prices,
					ps.actives
					FROM Partners p
					LEFT JOIN PartnerServices ps ON ps.id_partner = p.id
					WHERE `blocked` = 0 && `approved` = 1
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_tmp = Array();
					$_tmp['id'] = $_row['id'];
					$_tmp['image'] = strlen($_row['image']) > 1 ? 
						$this->_image_path . $_row['image'] : "";
					
					$_tmp['nick'] = $_row['nick'];
					$_tmp['age'] = $_row['age'];
					$_tmp['gender'] = $this->ConvertBoolGender($_row['gender']);
					$_tmp['rate'] = $_row['rate'];

					// TODO: precisa de alteração?
					$_tmp['categories'] = $_row['categories'];

					$_tmp['address'] = $_row['address'];
					$_tmp['latitude'] = $_row['latitude'];
					$_tmp['longitude'] = $_row['longitude'];

					$_tmp['services'] = Array(
						'id_services' => $_row['id_services'],
						'prices' => $_row['prices'],
						'actives' => $_row['actives'],
					);

					array_push($_data, $_tmp);
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
	* Busca as modelos
	*/
	public function GetFiltered($filter) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					p.id id,
					p.url_pic_1 image,
					p.nick_name nick,
					p.age age,
					p.gender gender,
					p.rate rate,
					p.id_category categories,
					p.address address,
					p.latitude latitude,
					p.longitude longitude,
					ps.id_services,
					ps.prices,
					ps.actives
					FROM Partners p
					LEFT JOIN PartnerServices ps ON ps.id_partner = p.id
					WHERE `blocked` = 0 && `approved` = 1
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_tmp = Array();
					$_tmp['id'] = $_row['id'];
					$_tmp['image'] = strlen($_row['image']) > 1 ? 
						$this->_image_path . $_row['image'] : "";
					
					$_tmp['nick'] = $_row['nick'];
					$_tmp['age'] = $_row['age'];
					$_tmp['gender'] = $this->ConvertBoolGender($_row['gender']);
					$_tmp['rate'] = $_row['rate'];

					// TODO: precisa de alteração?
					$_tmp['categories'] = $_row['categories'];

					$_tmp['address'] = $_row['address'];
					$_tmp['latitude'] = $_row['latitude'];
					$_tmp['longitude'] = $_row['longitude'];

					$_tmp['services'] = Array(
						'id_services' => $_row['id_services'],
						'prices' => $_row['prices'],
						'actives' => $_row['actives'],
					);

					array_push($_data, $_tmp);
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
	* Busca os planos para usuários, que estão ativos no servidor
	*/
	public function GetModel($id) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id` id,
					`url_pic_1` pic_1,
					`url_pic_2` pic_2,
					`url_pic_3` pic_3,
					`url_pic_4` pic_4,
					`url_pic_5` pic_5,
					`nick_name` nick,
					`age` age,
					`gender` gender,
					`rate` rate,
					`description` description
					FROM Partners
					WHERE `id` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_tmp = Array();
					$_tmp['id'] = $_row['id'];
					
					$_tmp['images'] = Array();
					if (strlen($_row['pic_1']) > 1) {
						array_push(
							$_tmp['images'], 
							$this->_image_path . $_row['pic_1']
						);
					}
					if (strlen($_row['pic_2']) > 1) {
						array_push(
							$_tmp['images'], 
							$this->_image_path . $_row['pic_2']
						);
					}
					if (strlen($_row['pic_1']) > 1) {
						array_push(
							$_tmp['images'], 
							$this->_image_path . $_row['pic_3']
						);
					}
					if (strlen($_row['pic_4']) > 1) {
						array_push(
							$_tmp['images'], 
							$this->_image_path . $_row['pic_4']
						);
					}
					if (strlen($_row['pic_5']) > 1) {
						array_push(
							$_tmp['images'], 
							$this->_image_path . $_row['pic_5']
						);
					}
					
					$_tmp['favorite'] = false;

					$_tmp['nick'] = $_row['nick'];
					$_tmp['age'] = $_row['age'];
					$_tmp['gender'] = $this->ConvertBoolGender($_row['gender']);
					$_tmp['rate'] = $_row['rate'];

					$_tmp['description'] = $_row['description'];

					$_tmp['price'] = null;
					$_tmp['max_time'] = null;

					$_tmp['locations'] = Array();
					$_tmp['comments'] = Array();
					$_tmp['services'] = Array();

					$_data = $_tmp;
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
	* Busca os locais de serviço da modelo
	*/
	public function GetModelLocations($id) {
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
	* Busca os serviços da modelo
	*/
	public function GetModelServiçe($id) {
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
					`id_services`,
					`prices`,
					`actives`
					FROM PartnerServices
					WHERE `id_partner` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_data = $_row;
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
	* Busca a agenda da modelo
	*/
	public function GetModelSchendule($id) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					*
					FROM PartnerDiary
					WHERE `id_partner` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
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
	* Busca os serviços
	*/
	public function GetServices() {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`name`
					FROM Services
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					array_push($_data, $_row['name']);
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
	* Busca os GetCategories
	*/
	public function GetCategories() {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`name`
					FROM Categories
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					array_push($_data, $_row['name']);
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
}

$ClassModels = ClassModels::GetInstance();

// echo json_encode($ClassModels->Get());
// echo json_encode($ClassModels->GetAllServices());
// echo json_encode($ClassModels->GetModel(1));
// echo json_encode($ClassModels->GetModelLocations(1));
// echo json_encode($ClassModels->GetModelServiçe(1));
// echo json_encode($ClassModels->GetModelSchendule(1));
?>
