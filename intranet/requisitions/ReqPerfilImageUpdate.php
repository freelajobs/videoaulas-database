<?php

require_once "../class/ClPartner.php";

$imgUrl = $_POST['imgUrl'];
// original sizes
$imgInitW = $_POST['imgInitW'];
$imgInitH = $_POST['imgInitH'];
// resized sizes
$imgW = $_POST['imgW'];
$imgH = $_POST['imgH'];
// offsets
$imgY1 = $_POST['imgY1'];
$imgX1 = $_POST['imgX1'];
// crop box
$cropW = $_POST['cropW'];
$cropH = $_POST['cropH'];
// rotation angle
$angle = $_POST['rotation'];

$jpeg_quality = 100;

//user info
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$img_id = filter_input(INPUT_POST, 'img_id', FILTER_SANITIZE_STRING);

$path = $_SERVER['DOCUMENT_ROOT'] . '/intranet/images/partners_app' . $folder . DIRECTORY_SEPARATOR;
$filename = "partner_" . $user_id . "_img_" . $img_id;
$output_filename = $path . $filename;

$what = getimagesize($imgUrl);

switch (strtolower($what['mime'])) {
    case 'image/png':
        $img_r = imagecreatefrompng($imgUrl);
        $source_image = imagecreatefrompng($imgUrl);
        $type = '.png';
        break;
    case 'image/jpeg':
        $img_r = imagecreatefromjpeg($imgUrl);
        $source_image = imagecreatefromjpeg($imgUrl);
        $type = '.jpeg';
        break;
    case 'image/gif':
        $img_r = imagecreatefromgif($imgUrl);
        $source_image = imagecreatefromgif($imgUrl);
        $type = '.gif';
        break;
    default: die('image type not supported');
}


//Check write Access to Directory

if (!is_writable(dirname($output_filename))) {
    $response = Array(
        "status" => 'error',
        "message" => 'não é possivel salvar a imagem no momento, tente mais tarde'
    );
} else {

    // resize the original image to size of editor
    $resizedImage = imagecreatetruecolor($imgW, $imgH);
    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
    // rotate the rezized image
    $rotated_image = imagerotate($resizedImage, -$angle, 0);
    // find new width & height of rotated image
    $rotated_width = imagesx($rotated_image);
    $rotated_height = imagesy($rotated_image);
    // diff between rotated & original sizes
    $dx = $rotated_width - $imgW;
    $dy = $rotated_height - $imgH;
    // crop rotated image to fit into original rezized rectangle
    $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
    imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
    imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
    // crop image into selected area
    $final_image = imagecreatetruecolor($cropW, $cropH);
    imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
    imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
    // finally output png image
    //imagepng($final_image, $output_filename.$type, $png_quality);
    imagejpeg($final_image, $output_filename . $type, $jpeg_quality);
    $response = Array(
        "status" => 'success',
        "url" => $output_filename . $type
    );

//    $result = 
    $ClassPartner->UpdateProfileImage(
            $user_id, $filename . $type, $img_id
    );

//    error_log(json_encode($result));
}
print json_encode($response);
