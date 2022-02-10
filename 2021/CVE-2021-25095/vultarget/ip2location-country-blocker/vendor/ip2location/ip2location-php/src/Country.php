<?php

namespace IP2Location;

/**
 * Country class.
 */
class Country
{
	/**
	 * Unable to locate CSV file.
	 *
	 * @var int
	 */
	public const EXCEPTION_FILE_NOT_EXISTS = 10000;

	/**
	 * Invalid CSV file.
	 *
	 * @var int
	 */
	public const EXCEPTION_INVALID_CSV = 10001;

	/**
	 * Unable to read the CSV file.
	 *
	 * @var int
	 */
	public const EXCEPTION_UNABLE_TO_OPEN_CSV = 10002;

	/**
	 * No record found.
	 *
	 * @var int
	 */
	public const EXCEPTION_NO_RECORD = 10003;

	/**
	 * Fields from CSV.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Records from CSV.
	 *
	 * @var array
	 */
	protected $records = [];

	/**
	 * Constructor.
	 *
	 * @param string $csv Path to CSV file
	 *
	 * @throws \Exception
	 */
	public function __construct($csv)
	{
		if (!file_exists($csv)) {
			throw new \Exception(__CLASS__ . ': The CSV file "' . $csv . '" is not found.', self::EXCEPTION_FILE_NOT_EXISTS);
		}

		$file = fopen($csv, 'r');

		if (!$file) {
			throw new \Exception(__CLASS__ . ': Unable to read "' . $csv . '".', self::EXCEPTION_UNABLE_TO_OPEN_CSV);
		}

		$line = 1;

		while (!feof($file)) {
			$data = fgetcsv($file);

			if (!$data) {
				++$line;
				continue;
			}

			// Parse the CSV fields
			if ($line == 1) {
				if ($data[0] != 'country_code') {
					throw new \Exception(__CLASS__ . ': Invalid country information CSV file.', self::EXCEPTION_INVALID_CSV);
				}

				$this->fields = $data;
			} else {
				$this->records[$data[0]] = $data;
			}

			++$line;
		}
	}

	/**
	 * Get the country information.
	 *
	 * @param string $countryCode The country ISO 3166 country code.
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function getCountryInfo($countryCode = '')
	{
		if (empty($this->records)) {
			throw new \Exception(__CLASS__ . ': No record available.', self::EXCEPTION_NO_RECORD);
		}

		$results = [];

		if ($countryCode) {
			if (!isset($this->records[$countryCode])) {
				return [];
			}

			for ($i = 0; $i < \count($this->fields); ++$i) {
				$results[$this->fields[$i]] = $this->records[$countryCode][$i];
			}

			return $results;
		}

		foreach ($this->records as $record) {
			$data = [];

			for ($i = 0; $i < \count($this->fields); ++$i) {
				$data[$this->fields[$i]] = $record[$i];
			}

			$results[] = $data;
		}

		return $results;
	}
}
