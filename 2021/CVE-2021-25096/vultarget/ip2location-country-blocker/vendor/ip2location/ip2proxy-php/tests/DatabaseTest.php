<?php

declare(strict_types=1);

namespace IP2Proxy\Test\DatabaseTest;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
	public function testInvalidDatabase()
	{
		try {
			$db = new \IP2Proxy\Database('./data/NULL.BIN', \IP2Proxy\Database::FILE_IO);
		} catch (\Exception $e) {
			$this->assertStringContainsString('does not seem to exist.', $e->getMessage());
		}
	}

	public function testInvalidIp()
	{
		$db = new \IP2Proxy\Database('./data/PX10.SAMPLE.BIN', \IP2Proxy\Database::FILE_IO);

		$records = $db->lookup('1.0.0.x', \IP2Proxy\Database::ALL);

		$this->assertStringContainsString('INVALID IP ADDRESS', $records['countryCode']);
	}

	public function testIpv4CountryCode()
	{
		$db = new \IP2Proxy\Database('./data/PX10.SAMPLE.BIN', \IP2Proxy\Database::FILE_IO);

		$records = $db->lookup('1.0.0.8', \IP2Proxy\Database::ALL);

		$this->assertEquals(
			'US',
			$records['countryCode'],
		);
	}

	public function testIpv4CountryName()
	{
		$db = new \IP2Proxy\Database('./data/PX10.SAMPLE.BIN', \IP2Proxy\Database::FILE_IO);

		$records = $db->lookup('1.0.0.8', \IP2Proxy\Database::ALL);

		$this->assertEquals(
			'United States of America',
			$records['countryName'],
		);
	}

	public function testIpv6CountryCode()
	{
		$db = new \IP2Proxy\Database('./data/PX10.SAMPLE.BIN', \IP2Proxy\Database::FILE_IO);

		$records = $db->lookup('2c0f:ffa0::4', \IP2Proxy\Database::ALL);

		$this->assertEquals(
			'UG',
			$records['countryCode'],
		);
	}

	public function testIpv6CountryName()
	{
		$db = new \IP2Proxy\Database('./data/PX10.SAMPLE.BIN', \IP2Proxy\Database::FILE_IO);

		$records = $db->lookup('2c0f:ffa0::4', \IP2Proxy\Database::ALL);

		$this->assertEquals(
			'Uganda',
			$records['countryName'],
		);
	}
}
