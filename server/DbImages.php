<?php
header('Content-Type: image/png');

class DbImages
{
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

  public function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    return fclose( $ifp ); 
  }

  /**
   * Usado para validar o arquivo
   */
  function PrepareImage($name)
  {
      $fileName    = $_FILES[$name]['name'];
      $fileType    = $_FILES[$name]['type'];
      $fileError   = $_FILES[$name]['error'];
      $fileContent = file_get_contents($_FILES[$name]['tmp_name']);
      $fileContent = $_FILES[$name]['tmp_name'];

      if ($fileError == UPLOAD_ERR_OK) {
          return $fileContent;
      } else {
          switch ($fileError) {
              case UPLOAD_ERR_INI_SIZE:
                  $message = 'Erro ao carregar um arquivo que excede o tamanho permitido.';
                  break;
              case UPLOAD_ERR_FORM_SIZE:
                  $message = 'Erro ao carregar um arquivo que excede o tamanho permitido.';
                  break;
              case UPLOAD_ERR_PARTIAL:
                  $message = 'Erro: O upload do arquivo não terminou.';
                  break;
              case UPLOAD_ERR_NO_FILE:
                  $message = 'Error: Nenhum arquivo foi carregado.';
                  break;
              case UPLOAD_ERR_NO_TMP_DIR:
                  $message = 'Erro: Diretório não existe.';
                  break;
              case UPLOAD_ERR_CANT_WRITE:
                  $message = 'Erro: Possível falha em salvar o arquivo.';
                  break;
              case UPLOAD_ERR_EXTENSION:
                  $message = 'Erro: Extenção do arquivo inválida.';
                  break;
              default:
                  $message = 'Erro: O carregamento do arquivo não foi concluído.';
                  break;
          }
          return $message;
      }
  }

  /**
   * Salva uma imagem no servidor
   * recebe $folder, $file, $type, $id
   */
  function SaveImage($folder, $file, $type, $id)
  {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/images/' .$folder.DIRECTORY_SEPARATOR;

    // $path = '/Users/rodrigopimentel/Sites/WEB/RedWindow/database/mobile/requisitions/'.$folder.DIRECTORY_SEPARATOR;
    $name = $_FILES[$file]['name'];
    $size = $_FILES[$file]['size'];
    $valid_formats = array('jpg','jpe','jpeg','png');

    if(strlen($name))
    {
      list($txt, $ext) = explode(".", $name);
      if(in_array($ext,$valid_formats))
      {
        if($size<10097152)
        {
          $actual_image_name = $type.'_identify_'.$id.'.png';
          $target_path = $path . basename($actual_image_name);
          $tmp = $_FILES[$file]['tmp_name'];
          if(move_uploaded_file($tmp, $target_path))
          {
            return array(
              'status'=>'true',
              'name'=>basename($actual_image_name)
            );
          }
          else
          {
            return array(
              'status' => 'false',
              'error'=>'Falhou ao salvar a imagem ' . $actual_image_name . ' no path ' . $target_path,
              'data' => ''
            );
          }
        }
        else
        {
          return array(
            'status' => 'false',
            'error' => 'Imagem com tamanho maior que o permitido! Só é Permitido Upload de imagens com tamanho até 10 MB!',
            'data' => ''
          );
        }
      }
      else
      {
        return array(
          'status' => 'false',
          'error' => 'Falhou pois o formato do arquivo é invalido',
          'data' => ''
        );
      }
    }
    else {
      return array(
        'status' => 'false',
        'error' => 'Falhou pois não tem arquivo',
        'data' => ''
      );
    }
  }

  /**
   * Chamado para deletar um arquivo de imagem
   * recebe $folder, $type, $id
   */
  function RemoveImage($folder, $type, $id)
  {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/images/'.$folder.DIRECTORY_SEPARATOR;
    $image = $type.'_id_'.$id.'.png';
    $archive = $path.$image;
    if(is_file($archive)) unlink($archive);
    return $archive;
  }
}

$DbImages = DbImages::GetInstance();
?>
