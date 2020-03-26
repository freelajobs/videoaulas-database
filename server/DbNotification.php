<?php
class DbNotification
{
  private $API_ACCESS_KEY_PREFIX = "";
  private $API_ACCESS_KEY_SUFIX = "";
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
  public function __construct($prefix = "AAAAN1dbK8c", $sufix = "APA91bGW7g52E5a2xmfeoBL6-VU_01V59TFDZBs9se2sLVKiK2gSjMpMBvir09kXbx4SMf-nCBrHfz-kqcBdXBjaqQd0BVJZ3oOpmIPCRkcv0a8Hi4HUp2q8qtM7kbFoqj2liKPaZlwD")
  {
     $this->API_ACCESS_KEY_PREFIX = $prefix;
     $this->API_ACCESS_KEY_SUFIX = $sufix;
  }

  /**
   * Usado para definir a chave de acesso
   * recebe os parametros strings prefix e sufix
   */
  public function SetAccessKey($prefix, $sufix)
  {
     $this->API_ACCESS_KEY_PREFIX = $prefix;
     $this->API_ACCESS_KEY_SUFIX = $sufix;
  }

  /**
   * Retorna a chave de acesso
   */
  public function GetAccessKey()
  {
    echo $this->API_ACCESS_KEY_PREFIX.':'.$this->API_ACCESS_KEY_SUFIX;
  }

  /**
   * Chamado para enviar as notificaçoes
   * recebe um array de menssagens
   */
  public function SendNotification($list)
  {
    if(!defined('API_ACCESS_KEY')) {
      define('API_ACCESS_KEY', $this->API_ACCESS_KEY_PREFIX.':'.$this->API_ACCESS_KEY_SUFIX);
    }
      $headers = array(
          'Authorization: key=' . API_ACCESS_KEY,
          'Content-Type: application/json'
      );
      $ch      = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($list));
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
  }

}

$DbNotification = DbNotification::GetInstance();
?>
