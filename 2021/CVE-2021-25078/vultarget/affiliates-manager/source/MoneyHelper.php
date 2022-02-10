<?php

class WPAM_MoneyHelper {

	public static function getDollarSign() {
		$info = localeconv();
		if( ! empty( $info['currency_symbol'] ) ) {
			return $info['currency_symbol'];
		} else {
			return '$';
		}
	}

	public static function getCurrencyCode() {
            $currency_code = get_option(WPAM_PluginConfig::$AffCurrencyCode);
            if(empty($currency_code)){
               $currency_code = 'USD';
            }
            return $currency_code;
	}
}