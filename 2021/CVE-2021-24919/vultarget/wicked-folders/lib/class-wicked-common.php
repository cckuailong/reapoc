<?php

namespace Wicked_Folders;

// Disable direct load
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Wicked_Common' ) ) {
	return false;
}

final class Wicked_Common {

	private function __construct() {
	}

	/**
	 * Inserts an array into an existing array after the specified key while preserving
	 * the inserted array's keys.
	 *
	 * @param array $array
	 *  The array to insert into.
	 *
	 * @param string $key
	 *  The key to insert the new array after.
	 *
	 * @param array $array_to_insert
	 *  The array to be inserted.
	 *
	 */
	public static function array_insert_after_key( &$array, $key, $array_to_insert ) {

		// Created this function because the following approach does not preserve the inserted array's keys:
		// array_splice( $array, $index + 1, 0, $array_to_insert );
		$offset = array_search( $key, array_keys( $array ) );
		$offset++;
		$array = array_slice( $array, 0, $offset, true ) + $array_to_insert + array_slice( $array, $offset, NULL, true );

	}

	/**
	 * Inserts an array into an existing array before the specified key while preserving
	 * the inserted array's keys.
	 *
	 * @param array $array
	 *  The array to insert into.
	 *
	 * @param string $key
	 *  The key to insert the new array before.
	 *
	 * @param array $array_to_insert
	 *  The array to be inserted.
	 *
	 */
	public static function array_insert_before_key( &$array, $key, $array_to_insert ) {

		$offset = array_search( $key, array_keys( $array ) );
		$array = array_slice( $array, 0, $offset, true ) + $array_to_insert + array_slice( $array, $offset, NULL, true );

	}

	public static function countries() {

		$counties = array(
			array(
				'name' 			=> 'Afghanistan',
				'iso_code_2' 	=> 'AF',
				'iso_code_3' 	=> 'AFG',
			),
			array(
				'name' 			=> 'Albania',
				'iso_code_2' 	=> 'AL',
				'iso_code_3' 	=> 'ALB',
			),
			array(
				'name' 			=> 'Algeria',
				'iso_code_2' 	=> 'DZ',
				'iso_code_3' 	=> 'DZA',
			),
			array(
				'name' 			=> 'American Samoa',
				'iso_code_2' 	=> 'AS',
				'iso_code_3' 	=> 'ASM',
			),
			array(
				'name' 			=> 'Andorra',
				'iso_code_2' 	=> 'AD',
				'iso_code_3' 	=> 'AND',
			),
			array(
				'name' 			=> 'Angola',
				'iso_code_2' 	=> 'AO',
				'iso_code_3' 	=> 'AGO',
			),
			array(
				'name' 			=> 'Anguilla',
				'iso_code_2' 	=> 'AI',
				'iso_code_3' 	=> 'AIA',
			),
			array(
				'name' 			=> 'Antarctica',
				'iso_code_2' 	=> 'AQ',
				'iso_code_3' 	=> 'ATA',
			),
			array(
				'name' 			=> 'Antigua and Barbuda',
				'iso_code_2' 	=> 'AG',
				'iso_code_3' 	=> 'ATG',
			),
			array(
				'name' 			=> 'Argentina',
				'iso_code_2' 	=> 'AR',
				'iso_code_3' 	=> 'ARG',
			),
			array(
				'name' 			=> 'Armenia',
				'iso_code_2' 	=> 'AM',
				'iso_code_3' 	=> 'ARM',
			),
			array(
				'name' 			=> 'Aruba',
				'iso_code_2' 	=> 'AW',
				'iso_code_3' 	=> 'ABW',
			),
			array(
				'name' 			=> 'Australia',
				'iso_code_2' 	=> 'AU',
				'iso_code_3' 	=> 'AUS',
			),
			array(
				'name' 			=> 'Austria',
				'iso_code_2' 	=> 'AT',
				'iso_code_3' 	=> 'AUT',
			),
			array(
				'name' 			=> 'Azerbaijan',
				'iso_code_2' 	=> 'AZ',
				'iso_code_3' 	=> 'AZE',
			),
			array(
				'name' 			=> 'Bahamas',
				'iso_code_2' 	=> 'BS',
				'iso_code_3' 	=> 'BHS',
			),
			array(
				'name' 			=> 'Bahrain',
				'iso_code_2' 	=> 'BH',
				'iso_code_3' 	=> 'BHR',
			),
			array(
				'name' 			=> 'Bangladesh',
				'iso_code_2' 	=> 'BD',
				'iso_code_3' 	=> 'BGD',
			),
			array(
				'name' 			=> 'Barbados',
				'iso_code_2' 	=> 'BB',
				'iso_code_3' 	=> 'BRB',
			),
			array(
				'name' 			=> 'Belarus',
				'iso_code_2' 	=> 'BY',
				'iso_code_3' 	=> 'BLR',
			),
			array(
				'name' 			=> 'Belgium',
				'iso_code_2' 	=> 'BE',
				'iso_code_3' 	=> 'BEL',
			),
			array(
				'name' 			=> 'Belize',
				'iso_code_2' 	=> 'BZ',
				'iso_code_3' 	=> 'BLZ',
			),
			array(
				'name' 			=> 'Benin',
				'iso_code_2' 	=> 'BJ',
				'iso_code_3' 	=> 'BEN',
			),
			array(
				'name' 			=> 'Bermuda',
				'iso_code_2' 	=> 'BM',
				'iso_code_3' 	=> 'BMU',
			),
			array(
				'name' 			=> 'Bhutan',
				'iso_code_2' 	=> 'BT',
				'iso_code_3' 	=> 'BTN',
			),
			array(
				'name' 			=> 'Bolivia',
				'iso_code_2' 	=> 'BO',
				'iso_code_3' 	=> 'BOL',
			),
			array(
				'name' 			=> 'Bosnia and Herzegowina',
				'iso_code_2' 	=> 'BA',
				'iso_code_3' 	=> 'BIH',
			),
			array(
				'name' 			=> 'Botswana',
				'iso_code_2' 	=> 'BW',
				'iso_code_3' 	=> 'BWA',
			),
			array(
				'name' 			=> 'Bouvet Island',
				'iso_code_2' 	=> 'BV',
				'iso_code_3' 	=> 'BVT',
			),
			array(
				'name' 			=> 'Brazil',
				'iso_code_2' 	=> 'BR',
				'iso_code_3' 	=> 'BRA',
			),
			array(
				'name' 			=> 'British Indian Ocean Territory',
				'iso_code_2' 	=> 'IO',
				'iso_code_3' 	=> 'IOT',
			),
			array(
				'name' 			=> 'Brunei Darussalam',
				'iso_code_2' 	=> 'BN',
				'iso_code_3' 	=> 'BRN',
			),
			array(
				'name' 			=> 'Bulgaria',
				'iso_code_2' 	=> 'BG',
				'iso_code_3' 	=> 'BGR',
			),
			array(
				'name' 			=> 'Burkina Faso',
				'iso_code_2' 	=> 'BF',
				'iso_code_3' 	=> 'BFA',
			),
			array(
				'name' 			=> 'Burundi',
				'iso_code_2' 	=> 'BI',
				'iso_code_3' 	=> 'BDI',
			),
			array(
				'name' 			=> 'Cambodia',
				'iso_code_2' 	=> 'KH',
				'iso_code_3' 	=> 'KHM',
			),
			array(
				'name' 			=> 'Cameroon',
				'iso_code_2' 	=> 'CM',
				'iso_code_3' 	=> 'CMR',
			),
			array(
				'name' 			=> 'Canada',
				'iso_code_2' 	=> 'CA',
				'iso_code_3' 	=> 'CAN',
			),
			array(
				'name' 			=> 'Cape Verde',
				'iso_code_2' 	=> 'CV',
				'iso_code_3' 	=> 'CPV',
			),
			array(
				'name' 			=> 'Cayman Islands',
				'iso_code_2' 	=> 'KY',
				'iso_code_3' 	=> 'CYM',
			),
			array(
				'name' 			=> 'Central African Republic',
				'iso_code_2' 	=> 'CF',
				'iso_code_3' 	=> 'CAF',
			),
			array(
				'name' 			=> 'Chad',
				'iso_code_2' 	=> 'TD',
				'iso_code_3' 	=> 'TCD',
			),
			array(
				'name' 			=> 'Chile',
				'iso_code_2' 	=> 'CL',
				'iso_code_3' 	=> 'CHL',
			),
			array(
				'name' 			=> 'China',
				'iso_code_2' 	=> 'CN',
				'iso_code_3' 	=> 'CHN',
			),
			array(
				'name' 			=> 'Christmas Island',
				'iso_code_2' 	=> 'CX',
				'iso_code_3' 	=> 'CXR',
			),
			array(
				'name' 			=> 'Cocos (Keeling) Islands',
				'iso_code_2' 	=> 'CC',
				'iso_code_3' 	=> 'CCK',
			),
			array(
				'name' 			=> 'Colombia',
				'iso_code_2' 	=> 'CO',
				'iso_code_3' 	=> 'COL',
			),
			array(
				'name' 			=> 'Comoros',
				'iso_code_2' 	=> 'KM',
				'iso_code_3' 	=> 'COM',
			),
			array(
				'name' 			=> 'Congo',
				'iso_code_2' 	=> 'CG',
				'iso_code_3' 	=> 'COG',
			),
			array(
				'name' 			=> 'Cook Islands',
				'iso_code_2' 	=> 'CK',
				'iso_code_3' 	=> 'COK',
			),
			array(
				'name' 			=> 'Costa Rica',
				'iso_code_2' 	=> 'CR',
				'iso_code_3' 	=> 'CRI',
			),
			array(
				'name' 			=> 'Cote D\'Ivoire',
				'iso_code_2' 	=> 'CI',
				'iso_code_3' 	=> 'CIV',
			),
			array(
				'name' 			=> 'Croatia',
				'iso_code_2' 	=> 'HR',
				'iso_code_3' 	=> 'HRV',
			),
			array(
				'name' 			=> 'Cuba',
				'iso_code_2' 	=> 'CU',
				'iso_code_3' 	=> 'CUB',
			),
			array(
				'name' 			=> 'Cyprus',
				'iso_code_2' 	=> 'CY',
				'iso_code_3' 	=> 'CYP',
			),
			array(
				'name' 			=> 'Czech Republic',
				'iso_code_2' 	=> 'CZ',
				'iso_code_3' 	=> 'CZE',
			),
			array(
				'name' 			=> 'Denmark',
				'iso_code_2' 	=> 'DK',
				'iso_code_3' 	=> 'DNK',
			),
			array(
				'name' 			=> 'Djibouti',
				'iso_code_2' 	=> 'DJ',
				'iso_code_3' 	=> 'DJI',
			),
			array(
				'name' 			=> 'Dominica',
				'iso_code_2' 	=> 'DM',
				'iso_code_3' 	=> 'DMA',
			),
			array(
				'name' 			=> 'Dominican Republic',
				'iso_code_2' 	=> 'DO',
				'iso_code_3' 	=> 'DOM',
			),
			array(
				'name' 			=> 'East Timor',
				'iso_code_2' 	=> 'TP',
				'iso_code_3' 	=> 'TMP',
			),
			array(
				'name' 			=> 'Ecuador',
				'iso_code_2' 	=> 'EC',
				'iso_code_3' 	=> 'ECU',
			),
			array(
				'name' 			=> 'Egypt',
				'iso_code_2' 	=> 'EG',
				'iso_code_3' 	=> 'EGY',
			),
			array(
				'name' 			=> 'El Salvador',
				'iso_code_2' 	=> 'SV',
				'iso_code_3' 	=> 'SLV',
			),
			array(
				'name' 			=> 'Equatorial Guinea',
				'iso_code_2' 	=> 'GQ',
				'iso_code_3' 	=> 'GNQ',
			),
			array(
				'name' 			=> 'Eritrea',
				'iso_code_2' 	=> 'ER',
				'iso_code_3' 	=> 'ERI',
			),
			array(
				'name' 			=> 'Estonia',
				'iso_code_2' 	=> 'EE',
				'iso_code_3' 	=> 'EST',
			),
			array(
				'name' 			=> 'Ethiopia',
				'iso_code_2' 	=> 'ET',
				'iso_code_3' 	=> 'ETH',
			),
			array(
				'name' 			=> 'Falkland Islands (Malvinas)',
				'iso_code_2' 	=> 'FK',
				'iso_code_3' 	=> 'FLK',
			),
			array(
				'name' 			=> 'Faroe Islands',
				'iso_code_2' 	=> 'FO',
				'iso_code_3' 	=> 'FRO',
			),
			array(
				'name' 			=> 'Fiji',
				'iso_code_2' 	=> 'FJ',
				'iso_code_3' 	=> 'FJI',
			),
			array(
				'name' 			=> 'Finland',
				'iso_code_2' 	=> 'FI',
				'iso_code_3' 	=> 'FIN',
			),
			array(
				'name' 			=> 'France',
				'iso_code_2' 	=> 'FR',
				'iso_code_3' 	=> 'FRA',
			),
			array(
				'name' 			=> 'France, Metropolitan',
				'iso_code_2' 	=> 'FX',
				'iso_code_3' 	=> 'FXX',
			),
			array(
				'name' 			=> 'French Guiana',
				'iso_code_2' 	=> 'GF',
				'iso_code_3' 	=> 'GUF',
			),
			array(
				'name' 			=> 'French Polynesia',
				'iso_code_2' 	=> 'PF',
				'iso_code_3' 	=> 'PYF',
			),
			array(
				'name' 			=> 'French Southern Territories',
				'iso_code_2' 	=> 'TF',
				'iso_code_3' 	=> 'ATF',
			),
			array(
				'name' 			=> 'Gabon',
				'iso_code_2' 	=> 'GA',
				'iso_code_3' 	=> 'GAB',
			),
			array(
				'name' 			=> 'Gambia',
				'iso_code_2' 	=> 'GM',
				'iso_code_3' 	=> 'GMB',
			),
			array(
				'name' 			=> 'Georgia',
				'iso_code_2' 	=> 'GE',
				'iso_code_3' 	=> 'GEO',
			),
			array(
				'name' 			=> 'Germany',
				'iso_code_2' 	=> 'DE',
				'iso_code_3' 	=> 'DEU',
			),
			array(
				'name' 			=> 'Ghana',
				'iso_code_2' 	=> 'GH',
				'iso_code_3' 	=> 'GHA',
			),
			array(
				'name' 			=> 'Gibraltar',
				'iso_code_2' 	=> 'GI',
				'iso_code_3' 	=> 'GIB',
			),
			array(
				'name' 			=> 'Greece',
				'iso_code_2' 	=> 'GR',
				'iso_code_3' 	=> 'GRC',
			),
			array(
				'name' 			=> 'Greenland',
				'iso_code_2' 	=> 'GL',
				'iso_code_3' 	=> 'GRL',
			),
			array(
				'name' 			=> 'Grenada',
				'iso_code_2' 	=> 'GD',
				'iso_code_3' 	=> 'GRD',
			),
			array(
				'name' 			=> 'Guadeloupe',
				'iso_code_2' 	=> 'GP',
				'iso_code_3' 	=> 'GLP',
			),
			array(
				'name' 			=> 'Guam',
				'iso_code_2' 	=> 'GU',
				'iso_code_3' 	=> 'GUM',
			),
			array(
				'name' 			=> 'Guatemala',
				'iso_code_2' 	=> 'GT',
				'iso_code_3' 	=> 'GTM',
			),
			array(
				'name' 			=> 'Guinea',
				'iso_code_2' 	=> 'GN',
				'iso_code_3' 	=> 'GIN',
			),
			array(
				'name' 			=> 'Guinea-bissau',
				'iso_code_2' 	=> 'GW',
				'iso_code_3' 	=> 'GNB',
			),
			array(
				'name' 			=> 'Guyana',
				'iso_code_2' 	=> 'GY',
				'iso_code_3' 	=> 'GUY',
			),
			array(
				'name' 			=> 'Haiti',
				'iso_code_2' 	=> 'HT',
				'iso_code_3' 	=> 'HTI',
			),
			array(
				'name' 			=> 'Heard and Mc Donald Islands',
				'iso_code_2' 	=> 'HM',
				'iso_code_3' 	=> 'HMD',
			),
			array(
				'name' 			=> 'Honduras',
				'iso_code_2' 	=> 'HN',
				'iso_code_3' 	=> 'HND',
			),
			array(
				'name' 			=> 'Hong Kong',
				'iso_code_2' 	=> 'HK',
				'iso_code_3' 	=> 'HKG',
			),
			array(
				'name' 			=> 'Hungary',
				'iso_code_2' 	=> 'HU',
				'iso_code_3' 	=> 'HUN',
			),
			array(
				'name' 			=> 'Iceland',
				'iso_code_2' 	=> 'IS',
				'iso_code_3' 	=> 'ISL',
			),
			array(
				'name' 			=> 'India',
				'iso_code_2' 	=> 'IN',
				'iso_code_3' 	=> 'IND',
			),
			array(
				'name' 			=> 'Indonesia',
				'iso_code_2' 	=> 'ID',
				'iso_code_3' 	=> 'IDN',
			),
			array(
				'name' 			=> 'Iran (Islamic Republic of)',
				'iso_code_2' 	=> 'IR',
				'iso_code_3' 	=> 'IRN',
			),
			array(
				'name' 			=> 'Iraq',
				'iso_code_2' 	=> 'IQ',
				'iso_code_3' 	=> 'IRQ',
			),
			array(
				'name' 			=> 'Ireland',
				'iso_code_2' 	=> 'IE',
				'iso_code_3' 	=> 'IRL',
			),
			array(
				'name' 			=> 'Israel',
				'iso_code_2' 	=> 'IL',
				'iso_code_3' 	=> 'ISR',
			),
			array(
				'name' 			=> 'Italy',
				'iso_code_2' 	=> 'IT',
				'iso_code_3' 	=> 'ITA',
			),
			array(
				'name' 			=> 'Jamaica',
				'iso_code_2' 	=> 'JM',
				'iso_code_3' 	=> 'JAM',
			),
			array(
				'name' 			=> 'Japan',
				'iso_code_2' 	=> 'JP',
				'iso_code_3' 	=> 'JPN',
			),
			array(
				'name' 			=> 'Jordan',
				'iso_code_2' 	=> 'JO',
				'iso_code_3' 	=> 'JOR',
			),
			array(
				'name' 			=> 'Kazakhstan',
				'iso_code_2' 	=> 'KZ',
				'iso_code_3' 	=> 'KAZ',
			),
			array(
				'name' 			=> 'Kenya',
				'iso_code_2' 	=> 'KE',
				'iso_code_3' 	=> 'KEN',
			),
			array(
				'name' 			=> 'Kiribati',
				'iso_code_2' 	=> 'KI',
				'iso_code_3' 	=> 'KIR',
			),
			array(
				'name' 			=> 'Korea, Democratic People\'s Republic of',
				'iso_code_2' 	=> 'KP',
				'iso_code_3' 	=> 'PRK',
			),
			array(
				'name' 			=> 'Korea, Republic of',
				'iso_code_2' 	=> 'KR',
				'iso_code_3' 	=> 'KOR',
			),
			array(
				'name' 			=> 'Kuwait',
				'iso_code_2' 	=> 'KW',
				'iso_code_3' 	=> 'KWT',
			),
			array(
				'name' 			=> 'Kyrgyzstan',
				'iso_code_2' 	=> 'KG',
				'iso_code_3' 	=> 'KGZ',
			),
			array(
				'name' 			=> 'Lao People\'s Democratic Republic',
				'iso_code_2' 	=> 'LA',
				'iso_code_3' 	=> 'LAO',
			),
			array(
				'name' 			=> 'Latvia',
				'iso_code_2' 	=> 'LV',
				'iso_code_3' 	=> 'LVA',
			),
			array(
				'name' 			=> 'Lebanon',
				'iso_code_2' 	=> 'LB',
				'iso_code_3' 	=> 'LBN',
			),
			array(
				'name' 			=> 'Lesotho',
				'iso_code_2' 	=> 'LS',
				'iso_code_3' 	=> 'LSO',
			),
			array(
				'name' 			=> 'Liberia',
				'iso_code_2' 	=> 'LR',
				'iso_code_3' 	=> 'LBR',
			),
			array(
				'name' 			=> 'Libyan Arab Jamahiriya',
				'iso_code_2' 	=> 'LY',
				'iso_code_3' 	=> 'LBY',
			),
			array(
				'name' 			=> 'Liechtenstein',
				'iso_code_2' 	=> 'LI',
				'iso_code_3' 	=> 'LIE',
			),
			array(
				'name' 			=> 'Lithuania',
				'iso_code_2' 	=> 'LT',
				'iso_code_3' 	=> 'LTU',
			),
			array(
				'name' 			=> 'Luxembourg',
				'iso_code_2' 	=> 'LU',
				'iso_code_3' 	=> 'LUX',
			),
			array(
				'name' 			=> 'Macau',
				'iso_code_2' 	=> 'MO',
				'iso_code_3' 	=> 'MAC',
			),
			array(
				'name' 			=> 'Macedonia, The Former Yugoslav Republic of',
				'iso_code_2' 	=> 'MK',
				'iso_code_3' 	=> 'MKD',
			),
			array(
				'name' 			=> 'Madagascar',
				'iso_code_2' 	=> 'MG',
				'iso_code_3' 	=> 'MDG',
			),
			array(
				'name' 			=> 'Malawi',
				'iso_code_2' 	=> 'MW',
				'iso_code_3' 	=> 'MWI',
			),
			array(
				'name' 			=> 'Malaysia',
				'iso_code_2' 	=> 'MY',
				'iso_code_3' 	=> 'MYS',
			),
			array(
				'name' 			=> 'Maldives',
				'iso_code_2' 	=> 'MV',
				'iso_code_3' 	=> 'MDV',
			),
			array(
				'name' 			=> 'Mali',
				'iso_code_2' 	=> 'ML',
				'iso_code_3' 	=> 'MLI',
			),
			array(
				'name' 			=> 'Malta',
				'iso_code_2' 	=> 'MT',
				'iso_code_3' 	=> 'MLT',
			),
			array(
				'name' 			=> 'Marshall Islands',
				'iso_code_2' 	=> 'MH',
				'iso_code_3' 	=> 'MHL',
			),
			array(
				'name' 			=> 'Martinique',
				'iso_code_2' 	=> 'MQ',
				'iso_code_3' 	=> 'MTQ',
			),
			array(
				'name' 			=> 'Mauritania',
				'iso_code_2' 	=> 'MR',
				'iso_code_3' 	=> 'MRT',
			),
			array(
				'name' 			=> 'Mauritius',
				'iso_code_2' 	=> 'MU',
				'iso_code_3' 	=> 'MUS',
			),
			array(
				'name' 			=> 'Mayotte',
				'iso_code_2' 	=> 'YT',
				'iso_code_3' 	=> 'MYT',
			),
			array(
				'name' 			=> 'Mexico',
				'iso_code_2' 	=> 'MX',
				'iso_code_3' 	=> 'MEX',
			),
			array(
				'name' 			=> 'Micronesia, Federated States of',
				'iso_code_2' 	=> 'FM',
				'iso_code_3' 	=> 'FSM',
			),
			array(
				'name' 			=> 'Moldova, Republic of',
				'iso_code_2' 	=> 'MD',
				'iso_code_3' 	=> 'MDA',
			),
			array(
				'name' 			=> 'Monaco',
				'iso_code_2' 	=> 'MC',
				'iso_code_3' 	=> 'MCO',
			),
			array(
				'name' 			=> 'Mongolia',
				'iso_code_2' 	=> 'MN',
				'iso_code_3' 	=> 'MNG',
			),
			array(
				'name' 			=> 'Montserrat',
				'iso_code_2' 	=> 'MS',
				'iso_code_3' 	=> 'MSR',
			),
			array(
				'name' 			=> 'Morocco',
				'iso_code_2' 	=> 'MA',
				'iso_code_3' 	=> 'MAR',
			),
			array(
				'name' 			=> 'Mozambique',
				'iso_code_2' 	=> 'MZ',
				'iso_code_3' 	=> 'MOZ',
			),
			array(
				'name' 			=> 'Myanmar',
				'iso_code_2' 	=> 'MM',
				'iso_code_3' 	=> 'MMR',
			),
			array(
				'name' 			=> 'Namibia',
				'iso_code_2' 	=> 'NA',
				'iso_code_3' 	=> 'NAM',
			),
			array(
				'name' 			=> 'Nauru',
				'iso_code_2' 	=> 'NR',
				'iso_code_3' 	=> 'NRU',
			),
			array(
				'name' 			=> 'Nepal',
				'iso_code_2' 	=> 'NP',
				'iso_code_3' 	=> 'NPL',
			),
			array(
				'name' 			=> 'Netherlands',
				'iso_code_2' 	=> 'NL',
				'iso_code_3' 	=> 'NLD',
			),
			array(
				'name' 			=> 'Netherlands Antilles',
				'iso_code_2' 	=> 'AN',
				'iso_code_3' 	=> 'ANT',
			),
			array(
				'name' 			=> 'New Caledonia',
				'iso_code_2' 	=> 'NC',
				'iso_code_3' 	=> 'NCL',
			),
			array(
				'name' 			=> 'New Zealand',
				'iso_code_2' 	=> 'NZ',
				'iso_code_3' 	=> 'NZL',
			),
			array(
				'name' 			=> 'Nicaragua',
				'iso_code_2' 	=> 'NI',
				'iso_code_3' 	=> 'NIC',
			),
			array(
				'name' 			=> 'Niger',
				'iso_code_2' 	=> 'NE',
				'iso_code_3' 	=> 'NER',
			),
			array(
				'name' 			=> 'Nigeria',
				'iso_code_2' 	=> 'NG',
				'iso_code_3' 	=> 'NGA',
			),
			array(
				'name' 			=> 'Niue',
				'iso_code_2' 	=> 'NU',
				'iso_code_3' 	=> 'NIU',
			),
			array(
				'name' 			=> 'Norfolk Island',
				'iso_code_2' 	=> 'NF',
				'iso_code_3' 	=> 'NFK',
			),
			array(
				'name' 			=> 'Northern Mariana Islands',
				'iso_code_2' 	=> 'MP',
				'iso_code_3' 	=> 'MNP',
			),
			array(
				'name' 			=> 'Norway',
				'iso_code_2' 	=> 'NO',
				'iso_code_3' 	=> 'NOR',
			),
			array(
				'name' 			=> 'Oman',
				'iso_code_2' 	=> 'OM',
				'iso_code_3' 	=> 'OMN',
			),
			array(
				'name' 			=> 'Pakistan',
				'iso_code_2' 	=> 'PK',
				'iso_code_3' 	=> 'PAK',
			),
			array(
				'name' 			=> 'Palau',
				'iso_code_2' 	=> 'PW',
				'iso_code_3' 	=> 'PLW',
			),
			array(
				'name' 			=> 'Panama',
				'iso_code_2' 	=> 'PA',
				'iso_code_3' 	=> 'PAN',
			),
			array(
				'name' 			=> 'Papua New Guinea',
				'iso_code_2' 	=> 'PG',
				'iso_code_3' 	=> 'PNG',
			),
			array(
				'name' 			=> 'Paraguay',
				'iso_code_2' 	=> 'PY',
				'iso_code_3' 	=> 'PRY',
			),
			array(
				'name' 			=> 'Peru',
				'iso_code_2' 	=> 'PE',
				'iso_code_3' 	=> 'PER',
			),
			array(
				'name' 			=> 'Philippines',
				'iso_code_2' 	=> 'PH',
				'iso_code_3' 	=> 'PHL',
			),
			array(
				'name' 			=> 'Pitcairn',
				'iso_code_2' 	=> 'PN',
				'iso_code_3' 	=> 'PCN',
			),
			array(
				'name' 			=> 'Poland',
				'iso_code_2' 	=> 'PL',
				'iso_code_3' 	=> 'POL',
			),
			array(
				'name' 			=> 'Portugal',
				'iso_code_2' 	=> 'PT',
				'iso_code_3' 	=> 'PRT',
			),
			array(
				'name' 			=> 'Puerto Rico',
				'iso_code_2' 	=> 'PR',
				'iso_code_3' 	=> 'PRI',
			),
			array(
				'name' 			=> 'Qatar',
				'iso_code_2' 	=> 'QA',
				'iso_code_3' 	=> 'QAT',
			),
			array(
				'name' 			=> 'Reunion',
				'iso_code_2' 	=> 'RE',
				'iso_code_3' 	=> 'REU',
			),
			array(
				'name' 			=> 'Romania',
				'iso_code_2' 	=> 'RO',
				'iso_code_3' 	=> 'ROM',
			),
			array(
				'name' 			=> 'Russian Federation',
				'iso_code_2' 	=> 'RU',
				'iso_code_3' 	=> 'RUS',
			),
			array(
				'name' 			=> 'Rwanda',
				'iso_code_2' 	=> 'RW',
				'iso_code_3' 	=> 'RWA',
			),
			array(
				'name' 			=> 'Saint Kitts and Nevis',
				'iso_code_2' 	=> 'KN',
				'iso_code_3' 	=> 'KNA',
			),
			array(
				'name' 			=> 'Saint Lucia',
				'iso_code_2' 	=> 'LC',
				'iso_code_3' 	=> 'LCA',
			),
			array(
				'name' 			=> 'Saint Vincent and the Grenadines',
				'iso_code_2' 	=> 'VC',
				'iso_code_3' 	=> 'VCT',
			),
			array(
				'name' 			=> 'Samoa',
				'iso_code_2' 	=> 'WS',
				'iso_code_3' 	=> 'WSM',
			),
			array(
				'name' 			=> 'San Marino',
				'iso_code_2' 	=> 'SM',
				'iso_code_3' 	=> 'SMR',
			),
			array(
				'name' 			=> 'Sao Tome and Principe',
				'iso_code_2' 	=> 'ST',
				'iso_code_3' 	=> 'STP',
			),
			array(
				'name' 			=> 'Saudi Arabia',
				'iso_code_2' 	=> 'SA',
				'iso_code_3' 	=> 'SAU',
			),
			array(
				'name' 			=> 'Senegal',
				'iso_code_2' 	=> 'SN',
				'iso_code_3' 	=> 'SEN',
			),
			array(
				'name' 			=> 'Seychelles',
				'iso_code_2' 	=> 'SC',
				'iso_code_3' 	=> 'SYC',
			),
			array(
				'name' 			=> 'Sierra Leone',
				'iso_code_2' 	=> 'SL',
				'iso_code_3' 	=> 'SLE',
			),
			array(
				'name' 			=> 'Singapore',
				'iso_code_2' 	=> 'SG',
				'iso_code_3' 	=> 'SGP',
			),
			array(
				'name' 			=> 'Slovakia (Slovak Republic)',
				'iso_code_2' 	=> 'SK',
				'iso_code_3' 	=> 'SVK',
			),
			array(
				'name' 			=> 'Slovenia',
				'iso_code_2' 	=> 'SI',
				'iso_code_3' 	=> 'SVN',
			),
			array(
				'name' 			=> 'Solomon Islands',
				'iso_code_2' 	=> 'SB',
				'iso_code_3' 	=> 'SLB',
			),
			array(
				'name' 			=> 'Somalia',
				'iso_code_2' 	=> 'SO',
				'iso_code_3' 	=> 'SOM',
			),
			array(
				'name' 			=> 'South Africa',
				'iso_code_2' 	=> 'ZA',
				'iso_code_3' 	=> 'ZAF',
			),
			array(
				'name' 			=> 'South Georgia and the South Sandwich Islands',
				'iso_code_2' 	=> 'GS',
				'iso_code_3' 	=> 'SGS',
			),
			array(
				'name' 			=> 'Spain',
				'iso_code_2' 	=> 'ES',
				'iso_code_3' 	=> 'ESP',
			),
			array(
				'name' 			=> 'Sri Lanka',
				'iso_code_2' 	=> 'LK',
				'iso_code_3' 	=> 'LKA',
			),
			array(
				'name' 			=> 'St. Helena',
				'iso_code_2' 	=> 'SH',
				'iso_code_3' 	=> 'SHN',
			),
			array(
				'name' 			=> 'St. Pierre and Miquelon',
				'iso_code_2' 	=> 'PM',
				'iso_code_3' 	=> 'SPM',
			),
			array(
				'name' 			=> 'Sudan',
				'iso_code_2' 	=> 'SD',
				'iso_code_3' 	=> 'SDN',
			),
			array(
				'name' 			=> 'Suriname',
				'iso_code_2' 	=> 'SR',
				'iso_code_3' 	=> 'SUR',
			),
			array(
				'name' 			=> 'Svalbard and Jan Mayen Islands',
				'iso_code_2' 	=> 'SJ',
				'iso_code_3' 	=> 'SJM',
			),
			array(
				'name' 			=> 'Swaziland',
				'iso_code_2' 	=> 'SZ',
				'iso_code_3' 	=> 'SWZ',
			),
			array(
				'name' 			=> 'Sweden',
				'iso_code_2' 	=> 'SE',
				'iso_code_3' 	=> 'SWE',
			),
			array(
				'name' 			=> 'Switzerland',
				'iso_code_2' 	=> 'CH',
				'iso_code_3' 	=> 'CHE',
			),
			array(
				'name' 			=> 'Syrian Arab Republic',
				'iso_code_2' 	=> 'SY',
				'iso_code_3' 	=> 'SYR',
			),
			array(
				'name' 			=> 'Taiwan',
				'iso_code_2' 	=> 'TW',
				'iso_code_3' 	=> 'TWN',
			),
			array(
				'name' 			=> 'Tajikistan',
				'iso_code_2' 	=> 'TJ',
				'iso_code_3' 	=> 'TJK',
			),
			array(
				'name' 			=> 'Tanzania, United Republic of',
				'iso_code_2' 	=> 'TZ',
				'iso_code_3' 	=> 'TZA',
			),
			array(
				'name' 			=> 'Thailand',
				'iso_code_2' 	=> 'TH',
				'iso_code_3' 	=> 'THA',
			),
			array(
				'name' 			=> 'Togo',
				'iso_code_2' 	=> 'TG',
				'iso_code_3' 	=> 'TGO',
			),
			array(
				'name' 			=> 'Tokelau',
				'iso_code_2' 	=> 'TK',
				'iso_code_3' 	=> 'TKL',
			),
			array(
				'name' 			=> 'Tonga',
				'iso_code_2' 	=> 'TO',
				'iso_code_3' 	=> 'TON',
			),
			array(
				'name' 			=> 'Trinidad and Tobago',
				'iso_code_2' 	=> 'TT',
				'iso_code_3' 	=> 'TTO',
			),
			array(
				'name' 			=> 'Tunisia',
				'iso_code_2' 	=> 'TN',
				'iso_code_3' 	=> 'TUN',
			),
			array(
				'name' 			=> 'Turkey',
				'iso_code_2' 	=> 'TR',
				'iso_code_3' 	=> 'TUR',
			),
			array(
				'name' 			=> 'Turkmenistan',
				'iso_code_2' 	=> 'TM',
				'iso_code_3' 	=> 'TKM',
			),
			array(
				'name' 			=> 'Turks and Caicos Islands',
				'iso_code_2' 	=> 'TC',
				'iso_code_3' 	=> 'TCA',
			),
			array(
				'name' 			=> 'Tuvalu',
				'iso_code_2' 	=> 'TV',
				'iso_code_3' 	=> 'TUV',
			),
			array(
				'name' 			=> 'Uganda',
				'iso_code_2' 	=> 'UG',
				'iso_code_3' 	=> 'UGA',
			),
			array(
				'name' 			=> 'Ukraine',
				'iso_code_2' 	=> 'UA',
				'iso_code_3' 	=> 'UKR',
			),
			array(
				'name' 			=> 'United Arab Emirates',
				'iso_code_2' 	=> 'AE',
				'iso_code_3' 	=> 'ARE',
			),
			array(
				'name' 			=> 'United Kingdom',
				'iso_code_2' 	=> 'GB',
				'iso_code_3' 	=> 'GBR',
			),
			array(
				'name' 			=> 'United States',
				'iso_code_2' 	=> 'US',
				'iso_code_3' 	=> 'USA',
			),
			array(
				'name' 			=> 'United States Minor Outlying Islands',
				'iso_code_2' 	=> 'UM',
				'iso_code_3' 	=> 'UMI',
			),
			array(
				'name' 			=> 'Uruguay',
				'iso_code_2' 	=> 'UY',
				'iso_code_3' 	=> 'URY',
			),
			array(
				'name' 			=> 'Uzbekistan',
				'iso_code_2' 	=> 'UZ',
				'iso_code_3' 	=> 'UZB',
			),
			array(
				'name' 			=> 'Vanuatu',
				'iso_code_2' 	=> 'VU',
				'iso_code_3' 	=> 'VUT',
			),
			array(
				'name' 			=> 'Vatican City State (Holy See)',
				'iso_code_2' 	=> 'VA',
				'iso_code_3' 	=> 'VAT',
			),
			array(
				'name' 			=> 'Venezuela',
				'iso_code_2' 	=> 'VE',
				'iso_code_3' 	=> 'VEN',
			),
			array(
				'name' 			=> 'Viet Nam',
				'iso_code_2' 	=> 'VN',
				'iso_code_3' 	=> 'VNM',
			),
			array(
				'name' 			=> 'Virgin Islands (British)',
				'iso_code_2' 	=> 'VG',
				'iso_code_3' 	=> 'VGB',
			),
			array(
				'name' 			=> 'Virgin Islands (U.S.)',
				'iso_code_2' 	=> 'VI',
				'iso_code_3' 	=> 'VIR',
			),
			array(
				'name' 			=> 'Wallis and Futuna Islands',
				'iso_code_2' 	=> 'WF',
				'iso_code_3' 	=> 'WLF',
			),
			array(
				'name' 			=> 'Western Sahara',
				'iso_code_2' 	=> 'EH',
				'iso_code_3' 	=> 'ESH',
			),
			array(
				'name' 			=> 'Yemen',
				'iso_code_2' 	=> 'YE',
				'iso_code_3' 	=> 'YEM',
			),
			array(
				'name' 			=> 'Yugoslavia',
				'iso_code_2' 	=> 'YU',
				'iso_code_3' 	=> 'YUG',
			),
			array(
				'name' 			=> 'Zaire',
				'iso_code_2' 	=> 'ZR',
				'iso_code_3' 	=> 'ZAR',
			),
			array(
				'name' 			=> 'Zambia',
				'iso_code_2' 	=> 'ZM',
				'iso_code_3' 	=> 'ZMB',
			),
			array(
				'name' 			=> 'Zimbabwe',
				'iso_code_2' 	=> 'ZW',
				'iso_code_3' 	=> 'ZWE',
			),
		);

		return apply_filters( 'wicked_counties', $counties );

	}

	public static function current_url( $append_query_string = true ) {

		global $wp;

		$url 			= site_url( $wp->request );
		$url 			= trailingslashit( $url );
		$query_string 	= $_SERVER['QUERY_STRING'];

		if ( $query_string && $append_query_string ) {
			$url .= '?' . $query_string;
		}

		return $url;

	}

	/**
	 * Returns the full country name from a two or three letter
	 * ISO country code.
	 *
	 * @param string $abbreviation		The two or three letter
	 *									ISO country code.
	 */
	public static function full_country_name( $abbreviation ) {

		$name 		= $abbreviation;
		$countries 	= Wicked_Common::countries();

		foreach ( $countries as $country ) {
			if ( $abbreviation == $country['iso_code_2'] || $abbreviation == $country['iso_code_3'] ) {
				$name = $country['name'];
			}
		}

		$filter_args = array(
			'abbreviation' 	=> $abbreviation,
			'countries' 	=> $countries,
		);

		return apply_filters( 'wicked_common_full_country_name', $name, $filter_args );

	}

	public static function states() {

		$states = array(
			array(
				'name' 		=> 'Alabama',
				'code' 		=> 'AL',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Alaska',
				'code' 		=> 'AK',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Arizona',
				'code' 		=> 'AZ',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Arkansas',
				'code' 		=> 'AR',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'California',
				'code' 		=> 'CA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Colorado',
				'code' 		=> 'CO',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Connecticut',
				'code' 		=> 'CT',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Delaware',
				'code' 		=> 'DE',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'District of Columbia',
				'code' 		=> 'DC',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Florida',
				'code' 		=> 'FL',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Georgia',
				'code' 		=> 'GA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Hawaii',
				'code' 		=> 'HI',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Idaho',
				'code' 		=> 'ID',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Illinois',
				'code' 		=> 'IL',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Indiana',
				'code' 		=> 'IN',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Iowa',
				'code' 		=> 'IA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Kansas',
				'code' 		=> 'KS',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Kentucky',
				'code' 		=> 'KY',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Louisiana',
				'code' 		=> 'LA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Maine',
				'code' 		=> 'ME',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Maryland',
				'code' 		=> 'MD',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Massachusetts',
				'code' 		=> 'MA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Michigan',
				'code' 		=> 'MI',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Minnesota',
				'code' 		=> 'MN',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Mississippi',
				'code' 		=> 'MS',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Missouri',
				'code' 		=> 'MO',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Montana',
				'code' 		=> 'MT',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Nebraska',
				'code' 		=> 'NE',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Nevada',
				'code' 		=> 'NV',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'New Hampshire',
				'code' 		=> 'NH',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'New Jersey',
				'code' 		=> 'NJ',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'New Mexico',
				'code' 		=> 'NM',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'New York',
				'code' 		=> 'NY',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'North Carolina',
				'code' 		=> 'NC',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'North Dakota',
				'code' 		=> 'ND',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Ohio',
				'code' 		=> 'OH',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Oklahoma',
				'code' 		=> 'OK',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Oregon',
				'code' 		=> 'OR',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Pennsylvania',
				'code' 		=> 'PA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Rhode Island',
				'code' 		=> 'RI',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'South Carolina',
				'code' 		=> 'SC',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'South Dakota',
				'code' 		=> 'SD',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Tennessee',
				'code' 		=> 'TN',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Texas',
				'code' 		=> 'TX',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Utah',
				'code' 		=> 'UT',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Vermont',
				'code' 		=> 'VT',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Virginia',
				'code' 		=> 'VA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Washington',
				'code' 		=> 'WA',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'West Virginia',
				'code' 		=> 'WV',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Wisconsin',
				'code' 		=> 'WI',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Wyoming',
				'code' 		=> 'WY',
				'country' 	=> 'USA',
			),
			array(
				'name' 		=> 'Alberta',
				'code' 		=> 'AB',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'British Columbia',
				'code' 		=> 'BC',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Manitoba',
				'code' 		=> 'MB',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Newfoundland',
				'code' 		=> 'NF',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'New Brunswick',
				'code' 		=> 'NB',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Nova Scotia',
				'code' 		=> 'NS',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Northwest Territories',
				'code' 		=> 'NT',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Nunavut',
				'code' 		=> 'NU',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Ontario',
				'code' 		=> 'ON',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Prince Edward Island',
				'code' 		=> 'PE',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Quebec',
				'code' 		=> 'QC',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Saskatchewan',
				'code' 		=> 'SK',
				'country' 	=> 'CAN',
			),
			array(
				'name' 		=> 'Yukon Territory',
				'code' 		=> 'YT',
				'country' 	=> 'CAN',
			),
		);

		return apply_filters( 'wicked_states', $states );

	}

	public static function prefix_array_values( $a, $prefix = 'id-' ) {
		if ( ! is_array( $a ) ) return $a;
		return preg_filter( '/^/', $prefix, $a );
	}

	/**
	 * Returns an array with the requested prefix/string removed from each value.
	 *
	 * @param array $a
	 *  The array to remove prfixes from.
	 *
	 * @param string $prefix
	 *  The prefix or string to strip.
	 *
	 * @return array
	 */
	public static function unprefix_array_values( $a, $prefix = 'id-' ) {
		if ( ! is_array( $a ) ) return $a;
		foreach ( $a as $key => $value ) {
			$a[ $key ] = str_replace( $prefix, '', $value );
		}
		return $a;
	}

	public static function wicked_plugins_url() {
		if ( defined( 'WICKED_PLUGINS_URL' ) ) return WICKED_PLUGINS_URL;
		return 'http://wickedplugins.com';
	}

	public static function delete_transients_with_prefix( $prefix ) {
		foreach ( self::get_transient_keys_with_prefix( $prefix ) as $key ) {
			delete_transient( $key );
		}
	}

	/**
	 * Gets all transient keys in the database with a specific prefix.
	 *
	 * Note that this doesn't work for sites that use a persistent object
	 * cache, since in that case, transients are stored in memory.
	 *
	 * @param  string $prefix
	 *  Prefix to search for.
	 *
	 * @return array
	 *  Transient keys with prefix, or empty array on error.
	 */
	public static function get_transient_keys_with_prefix( $prefix ) {
		global $wpdb;

		$prefix = $wpdb->esc_like( '_transient_' . $prefix );
		$sql    = "SELECT `option_name` FROM $wpdb->options WHERE `option_name` LIKE '%s'";
		$keys   = $wpdb->get_results( $wpdb->prepare( $sql, $prefix . '%' ), ARRAY_A );

		if ( is_wp_error( $keys ) ) {
			return [];
		}

		return array_map( function( $key ) {
			// Remove '_transient_' from the option name
			return ltrim( $key['option_name'], '_transient_' );
		}, $keys );
	}

	/**
     * Checks if the current request is a WP REST API request.
     *
     * Case #1: After WP_REST_Request initialisation
     * Case #2: Support "plain" permalink settings
     * Case #3: It can happen that WP_Rewrite is not yet initialized,
     *          so do this (wp-settings.php)
     * Case #4: URL Path begins with wp-json/ (your REST prefix)
     *          Also supports WP installations in subfolders
     *
     * @returns boolean
     * @author matzeeable
     */
    function is_rest_request() {
		// Statically cache function response
		static $is_rest_request = null;

		if ( null !== $is_rest_request ) return $is_rest_request;

        $prefix = rest_get_url_prefix( );

        if ( defined( 'REST_REQUEST' ) && REST_REQUEST // (#1)
                || isset( $_GET['rest_route'] ) // (#2)
                        && strpos( trim( sanitize_text_field( $_GET['rest_route'] ), '\\/' ), $prefix , 0 ) === 0)
                $is_rest_request = true;

        // (#3)
        global $wp_rewrite;

        if ( $wp_rewrite === null ) $wp_rewrite = new \WP_Rewrite();

        // (#4)
        $rest_url 		= wp_parse_url( trailingslashit( rest_url( ) ) );
        $current_url 	= wp_parse_url( add_query_arg( array( ) ) );

        $is_rest_request = strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;

		return $is_rest_request;
    }
}
