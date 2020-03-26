<?php

header('Content-Type: application/json');

class DbConnect
{
  private $pdo = NULL;
  private static $instance = NULL;

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
   * Construtor da classe
   */
  public function __construct()
  {
    define("Host", "ftp.redwindow.com.br");
    define("Port", "porta");
    define("DbName", "redwi251_redWindow");
    define("User", "redwi251_rWindow");
    define("Pass", "redwindow@2018");
  }

  public function ConnectDatabase()
  {
      try {
          $this->pdo = new PDO("mysql:host=" . Host . "; port=" . Port . "; dbname=" . DbName, User, Pass);
          $this->pdo->exec("set names utf8");
          $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          return $this->pdo;
      }
      catch (PDOException $e) {
          echo $e->getMessage();
          exit;
      }
  }

  public function CallDatabase($pdo, $info, $func)
  {
      try {
          $result = $func($pdo, $info);
          return $result;
      }
      catch (PDOException $e) {
          return array(
              'status' => 'false',
              'error' => $e->getMessage(),
              'data' => null
          );
      }
  }

  public function ConvertDataHourServer($_date) 
  {
    $_date = DateTime::createFromFormat(
                'd-m-Y H:i',
                $_date
            )->format('Y-m-d H:i:s');
    return $_date;
  }

  public function ConvertDataServer($_date) 
  {
    $_date = DateTime::createFromFormat(
                'd-m-Y',
                $_date
            )->format('Y-m-d');
    return $_date;
  }

  public function ConvertDataHourClient($_date, $_mask = 'd-m-Y H:i') 
  {
    $_date = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $_date
            )->format($_mask);
    return $_date;
  }

  public function ConvertDataClient($_date, $mask = 'Y-m-d H:i:s') 
  {
    $_date = DateTime::createFromFormat(
                $mask,
                $_date
            )->format('d-m-Y');
    return $_date;
  }

  public function ConvertHourClient($_date, $mask = 'Y-m-d H:i:s') 
  {
    $_date = DateTime::createFromFormat(
                $mask,
                $_date
            )->format('H:i');
    return $_date;
  }

  public function ConvertBoolServer($_value)
  {
      if (is_bool($_value)) {
        return $_value === false ? 0 : 1;    
      }
      return $_value === 'false' ? 0 : 1;
  }

  public function ConvertBoolClient($_value)
  {
      return (int)$_value === 1 ? true : false;
  }

  public function ConvertBoolGender($_value)
  {
      return (int)$_value === 1 ? "female" : "male";
  }
  public function MaxAttributeArray($data, $key='value')
  {
    $max = 0;
    for ($k = 0; $k < sizeof($data); $k++) {
        $tmp = $data[$k][$key];
        if($max < (float)$tmp){
            $max = (float)$tmp;
        }
        
    }
    return $max;
  }   
}

$DbConnect = DbConnect::GetInstance();

?>
