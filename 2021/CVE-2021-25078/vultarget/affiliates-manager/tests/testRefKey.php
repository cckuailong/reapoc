<?php
/**
 * @author John Hargrove
 * 
 * Date: Jul 8, 2010
 * Time: 9:04:16 PM
 */

if ( ! defined( 'WPAM_DEBUG' ) ) define( 'WPAM_DEBUG', true );

require_once dirname( __FILE__ ) . '/../source/Util/BinConverter.php';
require_once dirname( __FILE__ ) . '/../source/Tracking/TrackingKey.php';
require_once dirname( __FILE__ ) . '/../source/Tracking/UniqueIdGenerator.php';

$binConverter = new WPAM_Util_BinConverter();
$idGenerator = new WPAM_Tracking_UniqueIdGenerator();
$uniqueRefKeyBin = $idGenerator->generateId();
$uniqueRefKey = $binConverter->binToString($uniqueRefKeyBin);
//$uniqueRefKey = substr( $uniqueRefKey, 0, 20 );
//var_dump($uniqueRefKey);


function to_refkey( $token ) {
	$binConverter = new WPAM_Util_BinConverter();
	$testRefKey = new WPAM_Tracking_TrackingKey();
	$testRefKey->unpack($token);

	$refkey = $binConverter->binToString($testRefKey->getAffiliateRefKey());
	var_dump($testRefKey);
	var_dump($refkey);
	var_dump($binConverter->binToString($binConverter->binToString($testRefKey->getAffiliateRefKey())));
	return $refkey;
}
$token = 'FxjiRElrO8RryBSjNwSdh94rxBl00000000000';
//$token = 'zBVGC-a7cCyBJfVLhI1TkrdFO1i00000000000';

$refkey = to_refkey( $token );

//do it in reverse
function to_token( $uniqueRefKey ) {
	$binConverter = new WPAM_Util_BinConverter();
	$binRefKey = $binConverter->stringToBin( $uniqueRefKey );
	$creative_id = 1;
	$reserved = 0;

	$revRefKey = new WPAM_Tracking_TrackingKey();
	$revRefKey->setAffiliateRefKey( $binRefKey );
	$revRefKey->setCreativeId( $creative_id );
	$revRefKey->setReserved( $reserved );

	$token = $revRefKey->pack();
	
	var_dump( $token );
	return $token;
}

$new_token = to_token( $refkey );

to_refkey( $new_token );