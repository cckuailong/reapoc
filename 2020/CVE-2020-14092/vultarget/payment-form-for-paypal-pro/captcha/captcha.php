<?php
/*
PHP Captcha by Codepeople.net
http://www.codepeople.net
*/

if (!ini_get("zlib.output_compression")) ob_clean();

if (!isset($_GET["ps"])) $_GET["ps"] = '';

if ($_GET["hdwtest"] == "sessiontest")
{
    session_start();
    session_register("tmpvar");
    if ($_GET["autocall"]!=1){
        $_SESSION["tmpvar"] = "ok";
    } else {
        if ($_SESSION["tmpvar"]!="ok") {
            die("Session Error");
        } else {
            die("Sessions works on your server!");
        }
    }   
    header("Location: ".$PHP_SELF."?hdwtest=sessiontest&autocall=1" );
    exit;
}

if ($_GET["width"] == '' || !is_numeric($_GET["width"])) $_GET["width"] = "180";
if ($_GET["height"] == '' || !is_numeric($_GET["height"])) $_GET["height"] = "60";
if ($_GET["letter_count"] == ''|| !is_numeric($_GET["letter_count"])) $_GET["letter_count"] = "5";
if ($_GET["min_size"] == ''|| !is_numeric($_GET["min_size"])) $_GET["min_size"] = "35";
if ($_GET["max_size"] == ''|| !is_numeric($_GET["max_size"])) $_GET["max_size"] = "45";
if ($_GET["noise"] == ''|| !is_numeric($_GET["noise"])) $_GET["noise"] = "200";
if ($_GET["noiselength"] == ''|| !is_numeric($_GET["noiselength"])) $_GET["noiselength"] = "5";
if ($_GET["bcolor"] == '') $_GET["bcolor"] = "FFFFFF";
if ($_GET["border"] == '') $_GET["border"] = "000000";

//configuration
$imgX = $_GET["width"]; 
$imgY = $_GET["height"]; 

$letter_count = $_GET["letter_count"];
$min_size = $_GET["min_size"]; 
$max_size = $_GET["max_size"]; 
$noise = $_GET["noise"]; 
$noiselength = $_GET["noiselength"]; 
$bcolor = cpcff_decodeColor($_GET["bcolor"]);  
$border = cpcff_decodeColor($_GET["border"]);  

$noisecolor = 0xcdcdcd;         
$random_noise_color= true;      
$tcolor = cpcff_decodeColor("666666"); 
$random_text_color= true;                                
                       
                         
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");  
  
function cpcff_decodeColor($hexcolor)
{
   $color = hexdec($hexcolor);
   $c["b"] = $color % 256;
   $color = $color / 256;
   $c["g"] = $color % 256;
   $color = $color / 256;
   $c["r"] = $color % 256;
   return $c;
}

function cpcff_similarColors($c1, $c2)
{
   return sqrt( pow($c1["r"]-$c2["r"],2) + pow($c1["g"]-$c2["g"],2) + pow($c1["b"]-$c2["b"],2)) < 125;
}


// USAGE
// if (empty($_POST['hdcaptcha']) || $_POST['hdcaptcha'] != $_SESSION['rand_code']) header("Location: /form.html");

session_start();

function cpcff_make_seed() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
mt_srand(cpcff_make_seed());
$randval = mt_rand();

$str = "";
$length = 0;
for ($i = 0; $i < $letter_count; $i++) {
	 $str .= chr(mt_rand(97, 122))." ";
}
$_SESSION['rand_code'.$_GET["ps"]] = str_replace(" ", "", $str);

setCookie('rand_code'.$_GET["ps"], md5(str_replace(" ", "", $str)), time()+36000,"/");

$image = imagecreatetruecolor($imgX, $imgY);
$backgr_col = imagecolorallocate($image, $bcolor["r"],$bcolor["g"],$bcolor["b"]);
$border_col = imagecolorallocate($image, $border["r"],$border["g"],$border["b"]);

if ($random_text_color)
{
  do 
  {
     $selcolor = mt_rand(0,256*256*256);
  } while ( cpcff_similarColors(cpcff_decodeColor($selcolor), $bcolor) );
  $tcolor = cpcff_decodeColor($selcolor);
}    

$text_col = imagecolorallocate($image, $tcolor["r"],$tcolor["g"],$tcolor["b"]);
    
imagefilledrectangle($image, 0, 0, $imgX, $imgY, $backgr_col);
imagerectangle($image, 0, 0, $imgX-1, $imgY-1, $border_col);
for ($i=0;$i<$noise;$i++)
{
  if ($random_noise_color)
      $color = mt_rand(0, 256*256*256);
  else
      $color = $noisecolor;
  $x1 = mt_rand(2,$imgX-2);
  $y1 = mt_rand(2,$imgY-2);
  imageline ( $image, $x1, $y1, mt_rand($x1-$noiselength,$x1+$noiselength), mt_rand($y1-$noiselength,$y1+$noiselength), $color);
}  

$font = dirname( __FILE__ ) . "/font-1.ttf"; // font
if ($_GET["font"]) $font = dirname( __FILE__ ) . "/".$_GET["font"];       
/**if (!file_exists($font))
    $font = $_SERVER["DOCUMENT_ROOT"]."/HDWFormCaptcha/".$font;
if (!file_exists($font))
    $font = dirname(__FILE__)."/".$font;   
*/

$font_size = rand($min_size, $max_size);
  
$angle = rand(-15, 15);

if (function_exists("imagettfbbox") && function_exists("imagettftext"))
{
    $box = imagettfbbox($font_size, $angle, $font, $str);
    $x = (int)($imgX - $box[4]) / 2;
    $y = (int)($imgY - $box[5]) / 2;
    imagettftext($image, $font_size, $angle, $x, $y, $text_col, $font, $str);
} 
else if (function_exists("imageFtBBox") && function_exists("imageFTText"))
{
    $box = imageFtBBox($font_size, $angle, $font, $str);
    $x = (int)($imgX - $box[4]) / 2;
    $y = (int)($imgY - $box[5]) / 2;
    imageFTText ($image, $font_size, $angle, $x, $y, $text_col, $font, $str);	
}
else
{
    $angle = 0;
    $font = 6;
    $wf = ImageFontWidth(6) * strlen($str); 
    $hf = ImageFontHeight(6);
    $x = (int)($imgX - $wf) / 2;
    $y = (int)($imgY - $hf) / 2;
    imagestring ( $image, $font, $x, $y, $str, $text_col);	
}

function ppp_output_handler($img) {
    header('Content-type: image/png');
    header('Content-Length: ' . strlen($img));
    return $img;
}

ob_start("ppp_output_handler");
imagepng($image);
ob_end_flush();
imagedestroy ($image);
exit;
?>