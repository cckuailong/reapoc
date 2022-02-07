<?php

namespace Never5\DownloadMonitor\Shop\Tax;

class TaxClassManager {

	/**
	 * Get all available tax rates.
	 *
	 * @todo actually load these from the database
	 *
	 * @return string[]
	 */
	public function get_tax_rates() {
		return array(
			'standard',
			'reduced',
			'zero'
		);
	}

}