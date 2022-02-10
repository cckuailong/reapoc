<?php
/**
 * @author John Hargrove
 * 
 * Date: May 31, 2010
 * Time: 5:14:57 PM
 */

class WPAM_Pages_RawResponse 
{
	private $html;
	public function __construct($html)
	{
		$this->html = $html;
	}
	public function render()
	{
		return $this->html;
	}
}
