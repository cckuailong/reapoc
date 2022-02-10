<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 6, 2010
 * Time: 8:39:27 PM
 */

class WPAM_Tracking_TrackingKey
{
	private $affiliateRefKey;
	private $creativeId;
	private $reserved = 0;

	public function getAffiliateRefKey() { return $this->affiliateRefKey; }
	public function setAffiliateRefKey($val) { $this->affiliateRefKey = $val; }
	public function getCreativeId() { return $this->creativeId; }
	public function setCreativeId($val) { $this->creativeId = $val; }
	public function getReserved() { return $this->reserved; }
	public function setReserved($val) { $this->reserved = $val; }

	public function pack()
	{
		$binConverter = new WPAM_Util_BinConverter();
		$p = pack("a20LL", $this->affiliateRefKey, (int)$this->creativeId, (int)$this->reserved);
		return $binConverter->binToString($p);
	}

	public function unpack($data)
	{
		$binConverter = new WPAM_Util_BinConverter();
		$data = $binConverter->stringToBin($data);
		
		if (strlen($data) != 28)
			throw new Exception( sprintf( __( 'invalid refkey format. (length=%s)', 'affiliates-manager' ), strlen($data) ) );

		//try both unpacking methods - test cases refKey zBVGC-a7cCyBJfVLhI1TkrdFO12 and FxjiRElrO8RryBSjNwSdh94rxB5
		$unpacked = unpack("A20aid/Lcid/Lr", $data);
		if ( strlen( $unpacked['aid'] ) != 20 )
			$unpacked = unpack("a20aid/Lcid/Lr", $data);
		//this output will prevent wp_redirect from working
		//if ( WPAM_DEBUG )
		//	var_export($unpacked);
		$this->affiliateRefKey = $unpacked['aid'];
		$this->creativeId = $unpacked['cid'];
		$this->reserved = $unpacked['r'];
	}

	public function __toString() {
		$binConverter = new WPAM_Util_BinConverter();
		$key = $binConverter->binToString($this->affiliateRefKey);
		
		$object = "
affiliateRefKey = {$key}
creativeId = {$this->creativeId}
reserved = {$this->reserved}
";
		return $object;
	}
}
