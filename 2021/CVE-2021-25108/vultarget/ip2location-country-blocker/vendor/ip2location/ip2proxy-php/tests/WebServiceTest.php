<?php

declare(strict_types=1);

namespace IP2Proxy\Test\WebServiceTest;

use PHPUnit\Framework\TestCase;

class WebServiceTest extends TestCase
{
	public function testCredit()
	{
		$ws = new \IP2Proxy\WebService('demo', 'PX10', true);
		$this->assertMatchesRegularExpression('/^[0-9]+$/', (string) $ws->getCredit());
	}

	public function testInvalidIp()
	{
		$ws = new \IP2Proxy\WebService('demo', 'PX10', true);

		try {
			$records = $ws->lookup('1.0.0.x');
		} catch (\Exception $e) {
			$this->assertStringContainsString('INVALID IP ADDRESS', $e->getMessage());
		}
	}

	public function testCountryCodeIpv4()
	{
		$ws = new \IP2Proxy\WebService('demo', 'PX10', true);

		$records = $ws->lookup('1.0.0.8');

		$this->assertEquals(
			'US',
			$records['countryCode'],
		);
	}

	public function testCountryCodeIpv6()
	{
		$ws = new \IP2Proxy\WebService('demo', 'PX10', true);

		$records = $ws->lookup('2c0f:ffa0::4');

		$this->assertEquals(
			'UG',
			$records['countryCode'],
		);
	}
}
