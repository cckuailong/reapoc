<?php

namespace Paymill\Services;

/**
 * PaymillException
 */
class Util
{
	public function isNumericArray($array){
		return array_keys($array) === range(0,count($array) - 1);
	}
}
