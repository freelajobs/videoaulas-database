<?php
date_default_timezone_set('America/Sao_Paulo');

$LIST_INCLUDE = array(
    "../../server/DbImages.php",
    "../../server/DbConnect.php"
);
foreach ($LIST_INCLUDE as $value) {
    if (!file_exists($value)) {
        die('Classe não encontrada ' . $value);
    }
    require_once $value;
}

class ClassRecordSignature extends DbConnect {

    private static $instance = NULL;
    private $_response = array(
        'status' => '',
        'error' => '',
        'data' => ''
    );

    function __construct() {

    }

    /**
     * Pega a instancia unica do objeto de classe
     */
    public static function GetInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function GetTotalPartner() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    COUNT(*) `count`
                    FROM `PartnerSignature`
                    WHERE canceled = 0
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = null;
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'];
                }
                //Tratamento da resposta
                if (is_null($_data)) {
                    $this->_response["status"] = false;
                    $this->_response["error"] = $_query->errorInfo();
                } else {
                    $this->_response["status"] = true;
                    $this->_response["data"] = $_data;
                }
                return $this->_response;
            }
        );
    }

    public function GetTotalClient() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    COUNT(*) `count`
                    FROM `UsersSignature` 
                    WHERE canceled = 0
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = null;
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'];
                }
                //Tratamento da resposta
                if (is_null($_data)) {
                    $this->_response["status"] = false;
                    $this->_response["error"] = $_query->errorInfo();
                } else {
                    $this->_response["status"] = true;
                    $this->_response["data"] = $_data;
                }
                return $this->_response;
            }
        );
    }

    public function GetTotal() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    (
                        SELECT COUNT(*) 
                        FROM `UsersSignature` 
                        WHERE canceled = 0
                    ) as user,
                    (
                        SELECT COUNT(*) 
                        FROM `PartnerSignature` 
                        WHERE canceled = 0
                    ) as partner
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = null;
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $tmp = $_row['user'] + $_row['partner'];
                    $_data = $tmp;
                }
                //Tratamento da resposta
                if (is_null($_data)) {
                    $this->_response["status"] = false;
                    $this->_response["error"] = $_query->errorInfo();
                } else {
                    $this->_response["status"] = true;
                    $this->_response["data"] = $_data;
                }
                return $this->_response;
            }
        );
    }

}

$ClassRecordSignature = ClassRecordSignature::GetInstance();

// echo json_encode($ClassBankAccount->Get());
