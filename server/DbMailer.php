<?php

$LIST_INCLUDE = array(
	"../../mailer/PHPMailerAutoload.php"

);
foreach ($LIST_INCLUDE as $value)
{
	if (!file_exists($value))
	{
		die('Classe não encontrada ' . $value);
	}
	require_once $value;
}

class DbMailer
{
  /**
   * Arquivos fixos
   */
  private $path = "https://database.redwindow.com.br/";
  private $banner = "mailer/model/banner.png";
  private $html = "mailer/model/mail.html";

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
  }

  /**
   * Dispara um mail
   * recebe $email, $files, $subject, $body
   */
   public function SendMail($email, $files, $subject, $body)
   {
       $mail = new PHPMailer;

       $mail->isSMTP();

       $mail->Host       = "br858.hostgator.com.br";
       $mail->SMTPAuth   = true;
       $mail->SMTPSecure = "ssl";
       $mail->Port       = 465;

       $mail->Username   = "contato@redwindow.com.br";
       $mail->Password   = "redwindow@2018";

       $mail->setFrom("contato@redwindow.com.br", "Red Window");
       $mail->addAddress($email);
       $mail->WordWrap = 50; // Set word wrap to 50 characters

       foreach ($files as $file) {
           $mail->addAttachment($file['path'], $file['name']); // attachment
       }
       $mail->isHTML(true); // Set email format to HTML

       $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
       $mail->Subject = $subject;
       $mail->Body    = html_entity_decode($body);

       if (!$mail->send()) {
           return array(
               'status' => false,
               'error' => $mail->ErrorInfo
           );
       } else {
           return array(
               'status' => true,
               'error' => ""
           );
       }
   }

   /**
    * Retorna o Html de usuario, que será incorporado no email
    * recebe $name, $mail, $password
    */
   public function NewUser($name, $mail, $password)
   {
       $file = file_get_contents($this->banner_new_user);
       $dom = new DOMDocument();
       @$dom->loadHTML($file);
       //Banner
       $el            = $dom->getElementById('banner');
       $el->nodeValue = '<img  src="'.$this->banner_new_user.'">';
       $el            = $dom->getElementById('linkButton');
       $el->setAttribute('href', 'http://www.bymove.com.br/login_cliente.php');
       //Message html
       $message       = '<br>';
       $message       = $message . '<br>';
       $message       = $message . '<br>';
       $message       = $message . '<br>';
       $message       = $message . '<span class="textBody">Seja bem-vindo ao ByMove <b>' . $name . '</b>.</span><br>';
       $message       = $message . '<br>';
       $message       = $message . '<span class="textBody">Seu login é: '.$mail.',</span><br>';
       $message       = $message . '<span class="textBody">e sua senha é: '.$password.'.</span><br>';
       $message       = $message . '<br>';
       $message       = $message . '<span class="textBody">Finalize seu cadastro e fique por dentro<br>do que está rolando.<br>';
       $message       = $message . '<br>';
       $message       = $message . '<br>';
       $el            = $dom->getElementById('message');
       $el->nodeValue = $message;
       // Buttom Text
       $el            = $dom->getElementById('buttomText');
       $el->nodeValue = '<span class="textButton">Finalizar Cadastro!</span>';
       //SaveHtml
       return $dom->saveHTML($el->documentElement);
   }

    /**
    * Retorna o Html de usuario, que será incorporado no email
    * recebe $name, $mail, $password
    */
   function LostPassword($name, $pass, $mail)
    {
        $banner = file_get_contents($this->path . $this->banner);
        $html = file_get_contents($this->path . $this->html);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        //Banner
        $el            = $dom->getElementById('banner');
        $el->nodeValue = '<img  src=' . $banner . '>';
        $el            = $dom->getElementById('linkButton');
        $el->nodeValue = "<a hidden>";
        //Message html
        $message       = '<br>';
        $message       = $message . '<br>';
        $message       = $message . '<br>';
        $message       = $message . '<br>';
        $message       = $message . '<span class="textBody">Olá <b>'.$name.'</b>,</span><br>';
        $message       = $message . '<br>';
        $message       = $message . '<span class="textBody">Sua senha é: <b>'.$pass.'</b></span><br>';
        $message       = $message . '<br>';
        $message       = $message . '<span class="textBody">Caso você não tenha pedido sua senha através de nosso app,<br>ignore este e-mail.</span><br>';;
        $message       = $message . '<br>';
        $message       = $message . '<span class="textBody">Se tiver qualquer dúvida, fique à<br>vontade para entrar em contato conosco.</span><br>';
        $message       = $message . '<br>';
        $message       = $message . '<br>';
        $message       = $message . '<br>';
        $el            = $dom->getElementById('message');
        $el->nodeValue = $message;
        // Buttom Text
        $el            = $dom->getElementById('linkButton');
        $el->nodeValue = "<a hidden>";
        //SaveHtml
        return $dom->saveHTML($dom->documentElement);
    }
}

$DbMailer = DbMailer::GetInstance();

?>
