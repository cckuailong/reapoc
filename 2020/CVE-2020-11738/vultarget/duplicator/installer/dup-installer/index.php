<?php
$API['BaseRootPath'] =  str_ireplace('dup-installer', '', dirname(__FILE__));
// for ngrok url and Local by Flywheel Live URL
if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
	$host = $_SERVER['HTTP_X_ORIGINAL_HOST'];
} else {
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];//WAS SERVER_NAME and caused problems on some boxes
}
$API['BaseRootURL']  = '//' . $host . str_ireplace('dup-installer', '', dirname($_SERVER['PHP_SELF']));

if (file_exists("{$API['BaseRootPath']}\installer.php")) 
{
	header( "Location: {$API['BaseRootURL']}/installer.php" ) ;
} 

echo "Please browse to the 'installer.php' from your web browser to proceed with your install!";

?>