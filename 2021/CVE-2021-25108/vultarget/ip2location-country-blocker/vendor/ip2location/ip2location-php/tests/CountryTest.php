<?php

declare(strict_types=1);

namespace IP2Location\Test\CountryTest;

use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
	public function testCountryCodeField()
	{
		$country = new \IP2Location\Country('./data/IP2LOCATION-COUNTRY-INFORMATION-BASIC.CSV');

		$this->assertArrayHasKey('country_code', $country->getCountryInfo('US'));
	}

	public function testCountryCodeValue()
	{
		$country = new \IP2Location\Country('./data/IP2LOCATION-COUNTRY-INFORMATION-BASIC.CSV');

		$this->assertContains('US', $country->getCountryInfo('US'));
	}

	public function testCapital()
	{
		$country = new \IP2Location\Country('./data/IP2LOCATION-COUNTRY-INFORMATION-BASIC.CSV');

		$this->assertContains('Kuala Lumpur', $country->getCountryInfo('MY'));
	}
}
