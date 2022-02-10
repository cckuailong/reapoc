<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 6, 2010
 * Time: 8:50:14 PM
 */

class WPAM_Util_BinConverter
{
	public function binToString($bin)
	{
		static $bin_chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-";

		$p = 0;
		$q = strlen($bin);

		$w = 0;
		$have = 0;
		$mask = 0x3F;

		$out = '';

		while (1)
		{
			if ($have < 6)
			{
				if ($p < $q)
				{
					$w |= ord($bin[$p++]) << $have;
					$have += 8;
				}
				else
				{
					if ($have == 0)
						break;
					$have = 6;
				}
			}

			$out .= $bin_chars[($w & $mask)];

			$w >>= 6;
			$have -= 6;
		}

		return $out;
	}

	public function stringToBin($str)
	{
		$alpha = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-";
		$nbits = 6;
		$length = strlen($str);
		$out = '';
		$at = 0;
		$rshift = 0;
		$char_in = 0;
		$char_out = chr (0);

		while (1)
		{
			$char_in = strcspn ($alpha, $str[$at++]);
			if ($rshift > 0)
			{
				$char_out |= chr ($char_in << 8 - $rshift);
				$out .= $char_out;
				$char_out = chr (0);
				if ($at >= $length)
				{
					break;
				}
			}
			$char_out |= chr ($char_in >> $rshift);
			$rshift += 2;
			if ($rshift === 8)
			{
				$rshift = 0;
			}
		}

		return $out;
	}
}
