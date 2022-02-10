<?php

namespace IP2Location;

/**
 * IpTools class.
 */
class IpTools
{
	public function isIpv4($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) ? true : false;
	}

	public function isIpv6($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) ? true : false;
	}

	public function ipv4ToDecimal($ip)
	{
		if (!$this->isIpv4($ip)) {
			return;
		}

		return sprintf('%u', ip2long($ip));
	}

	public function ipv6ToDecimal($ipv6)
	{
		if (!$this->isIpv6($ipv6)) {
			return;
		}

		return (string) gmp_import(inet_pton($ipv6));
	}

	public function decimalToIpv4($number)
	{
		if (!preg_match('/^\d+$/', $number)) {
			return;
		}

		if ($number > 4294967295) {
			return;
		}

		return long2ip($number);
	}

	public function decimalToIpv6($number)
	{
		if (!preg_match('/^\d+$/', $number)) {
			return;
		}

		if ($number <= 4294967295) {
			return;
		}

		return inet_ntop(str_pad(gmp_export($number), 16, "\0", STR_PAD_LEFT));
	}
}
