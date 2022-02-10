<?php
/**
 * @author John Hargrove
 * 
 * Date: Jul 8, 2010
 * Time: 9:20:49 PM
 */

require_once '../source/Tracking/UniqueIdGenerator.php';
require_once '../source/Util/BinConverter.php';

$idgen = new WPAM_Tracking_UniqueIdGenerator();
$binConverter = new WPAM_Util_BinConverter();
echo $binConverter->binToString($idgen->generateId()) . PHP_EOL;
?>