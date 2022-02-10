<?php

declare(strict_types=1);

namespace IP2Location\Test\DatabaseTest;

use IP2Location\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
	public function testInvalidDatabase() {
		try {
			$db = new \IP2Location\Database('./data/NULL.BIN', \IP2Location\Database::FILE_IO);
		} catch (\Exception $e) {
			$this->assertStringContainsString('does not seem to exist.', $e->getMessage());
		}
	}

	public function testIpv4CountryCode() {
		$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

		$this->assertEquals(
			'US',
			$records['countryCode'],
		);
	}

	public function testIpv4CountryName() {
		$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

		$this->assertEquals(
			'United States of America',
			$records['countryName'],
		);
	}

	public function testIpv4UnsupportedField() {
		$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

		$this->assertStringContainsString('unavailable', $records['cityName']);
	}

	public function testIpv6CountryCode() {
		$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.IPV6.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('2001:4860:4860::8888', \IP2Location\Database::ALL);

		$this->assertEquals(
			'US',
			$records['countryCode'],
		);
	}

	public function testIpv6CountryName() {
		$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.IPV6.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('2001:4860:4860::8888', \IP2Location\Database::ALL);

		$this->assertEquals(
			'United States of America',
			$records['countryName'],
		);
	}

	public function testIpv6UnsupportedField() {
		$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.IPV6.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('2001:4860:4860::8888', \IP2Location\Database::ALL);

		$this->assertStringContainsString('unavailable', $records['cityName']);
	}
}
