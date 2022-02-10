<?php

declare(strict_types=1);

namespace IP2Location\Test\IpToolsTest;

use IP2Location\IpTools;
use PHPUnit\Framework\TestCase;

class IpToolsTest extends TestCase
{
	public function testIpv4() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			true,
			$ipTools->isIpv4('8.8.8.8')
		);
	}

	public function testInvalidIpv4() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			false,
			$ipTools->isIpv4('8.8.8.555')
		);
	}

	public function testIpv6() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			true,
			$ipTools->isIpv6('2001:4860:4860::8888')
		);
	}

	public function testInvalidIpv6() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			false,
			$ipTools->isIpv6('2001:4860:4860::ZZZZ')
		);
	}

	public function testIpv4Decimal() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			134744072,
			$ipTools->ipv4ToDecimal('8.8.8.8')
		);
	}

	public function testDecimalIpv4() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			'8.8.8.8',
			$ipTools->DecimalToIpv4('134744072')
		);
	}

	public function testIpv6Decimal() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			'42541956123769884636017138956568135816',
			$ipTools->ipv6ToDecimal('2001:4860:4860::8888')
		);
	}

	public function testDecimalIpv6() {
		$ipTools = new \IP2Location\IpTools;

		$this->assertEquals(
			'2001:4860:4860::8888',
			$ipTools->DecimalToIpv6('42541956123769884636017138956568135816')
		);
	}
}
