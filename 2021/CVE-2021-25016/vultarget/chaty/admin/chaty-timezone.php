<?php
if(!function_exists('chaty_timezone_choice')) {
    function chaty_timezone_choice($selected_zone = '', $utc = true) {
        $country_name = json_decode(chaty_country_city_name(), true );
        $continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

        $zonen = array();

        foreach ( timezone_identifiers_list() as $zone ) {
            $zone = explode( '/', $zone );
            if ( ! in_array( $zone[0], $continents, true ) ) {
                continue;
            }

            // This determines what gets set and translated - we don't translate Etc/* strings here, they are done later.
            $exists    = array(
                0 => ( isset( $zone[0] ) && $zone[0] ),
                1 => ( isset( $zone[1] ) && $zone[1] ),
                2 => ( isset( $zone[2] ) && $zone[2] ),
            );
            $exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
            $exists[4] = ( $exists[1] && $exists[3] );
            $exists[5] = ( $exists[2] && $exists[3] );

            // phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
            $zonen[] = array(
                'continent'   => ( $exists[0] ? $zone[0] : '' ),
                'city'        => ( $exists[1] ? $zone[1] : '' ),
                'subcity'     => ( $exists[2] ? $zone[2] : '' ),
                't_continent' => ( $exists[3] ? str_replace( '_', ' ', $zone[0] ) : '' ),
                't_city'      => ( $exists[4] ? str_replace( '_', ' ', $zone[1] ) : '' ),
                't_subcity'   => ( $exists[5] ? str_replace( '_', ' ', $zone[2] ) : '' ),
            );
            // phpcs:enable
        }
        usort( $zonen, '_chaty_timezone_sort' );

        $structure = array();

        if ( empty( $selected_zone ) ) {
            $structure[] = '<option selected="selected" value="">Select a city or country</option>';
        }

        foreach ( $zonen as $key => $zone ) {
            // Build value in an array to join later.
            $value = array( $zone['continent'] );
            $display = '';

            if ( isset( $country_name[$zone['city']] )  && $country_name[$zone['city']] != '' ) {
                $display .= $country_name[$zone['city']] . "/";
            }

            if ( empty( $zone['city'] ) ) {
                // It's at the continent level (generally won't happen).
                $display .= $zone['t_continent'];
            } else {
                // It's inside a continent group.

                // Continent optgroup.
                if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
                    $label       = $zone['t_continent'];
                    $structure[] = '<optgroup label="' . $label . '">';
                }

                // Add the city to the value.
                $value[] = $zone['city'];

                $display .= $zone['t_city'];
                if ( ! empty( $zone['subcity'] ) ) {
                    // Add the subcity to the value.
                    $value[]  = $zone['subcity'];
                    $display .= ' - ' . $zone['t_subcity'];
                }
            }

            // Build the value.
            $value    = join( '/', $value );
            $selected = '';
            if ( $value === $selected_zone ) {
                $selected = 'selected="selected" ';
            }
            $structure[] = '<option ' . $selected . 'value="' . $value . '">' . $display . '</option>';

            // Close continent optgroup.
            if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
                $structure[] = '</optgroup>';
            }
        }

        // Do UTC.
        $structure[] = '<optgroup label="UTC">';
        $selected    = '';
        if ( 'UTC' === $selected_zone ) {
            $selected = 'selected="selected" ';
        }
        $structure[] = '<option ' . $selected . 'value="UTC">UTC</option>';
        $structure[] = '</optgroup>';

        // Do manual UTC offsets.
        $structure[]  = '<optgroup label="Manual Offsets">';
        $offset_range = array(
            -12,
            -11.5,
            -11,
            -10.5,
            -10,
            -9.5,
            -9,
            -8.5,
            -8,
            -7.5,
            -7,
            -6.5,
            -6,
            -5.5,
            -5,
            -4.5,
            -4,
            -3.5,
            -3,
            -2.5,
            -2,
            -1.5,
            -1,
            -0.5,
            0,
            0.5,
            1,
            1.5,
            2,
            2.5,
            3,
            3.5,
            4,
            4.5,
            5,
            5.5,
            5.75,
            6,
            6.5,
            7,
            7.5,
            8,
            8.5,
            8.75,
            9,
            9.5,
            10,
            10.5,
            11,
            11.5,
            12,
            12.75,
            13,
            13.75,
            14,
        );
        foreach ( $offset_range as $offset ) {
            if ( 0 <= $offset ) {
                $offset_name = ($utc ) ? '+' . $offset : $offset;
            } else {
                $offset_name = (string) $offset;
            }

            $offset_value = $offset_name;
            $offset_name  = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
            if ( 0 <= $offset && !$utc ){
                $offset_name  = 'UTC+' . $offset_name;
            } else {
                $offset_name  = 'UTC' . $offset_name;
            }
            $offset_value = ($utc ) ? 'UTC' . $offset_value : $offset_value ;
            $selected     = '';
            if ($offset_value === $selected_zone ) {
                $selected = 'selected="selected" ';
            }
            $structure[] = '<option ' . $selected . 'value="' . $offset_value . '">' . $offset_name . '</option>';

        }
        $structure[] = '</optgroup>';

        return join( "\n", $structure );
    }
}



/**
 * Sort-helper for timezones.
 *
 * @param array $a
 * @param array $b
 * @return int
 * @since 2.9.0
 * @access private
 *
 */
if(!function_exists('_chaty_timezone_sort')) {
    function _chaty_timezone_sort($a, $b) {
        // Don't use translated versions of Etc.
        if ( 'Etc' === $a['continent'] && 'Etc' === $b['continent'] ) {
            // Make the order of these more like the old dropdown.
            if ( 'GMT+' === substr( $a['city'], 0, 4 ) && 'GMT+' === substr( $b['city'], 0, 4 ) ) {
                return -1 * ( strnatcasecmp( $a['city'], $b['city'] ) );
            }
            if ( 'UTC' === $a['city'] ) {
                if ( 'GMT+' === substr( $b['city'], 0, 4 ) ) {
                    return 1;
                }
                return -1;
            }
            if ( 'UTC' === $b['city'] ) {
                if ( 'GMT+' === substr( $a['city'], 0, 4 ) ) {
                    return -1;
                }
                return 1;
            }
            return strnatcasecmp( $a['city'], $b['city'] );
        }
        if ( $a['t_continent'] == $b['t_continent'] ) {
            if ( $a['t_city'] == $b['t_city'] ) {
                return strnatcasecmp( $a['t_subcity'], $b['t_subcity'] );
            }
            return strnatcasecmp( $a['t_city'], $b['t_city'] );
        } else {
            // Force Etc to the bottom of the list.
            if ( 'Etc' === $a['continent'] ) {
                return 1;
            }
            if ( 'Etc' === $b['continent'] ) {
                return -1;
            }
            return strnatcasecmp( $a['t_continent'], $b['t_continent'] );
        }
    }
}

if(!function_exists('chaty_country_city_name')) {
    function chaty_country_city_name() {
        return '{"Abidjan":"Ivory Coast","Accra":"Ghana","Addis Ababa":"Ethiopia","Algiers":"Algeria","Asmara":"Eritrea","Bamako":"Mali","Bangui":"Central African Republic","Banjul":"Gambia","Bissau":"Guinea-Bissau","Blantyre":"Malawi","Brazzaville":"Republic of the Congo","Bujumbura":"Burundi","Cairo":"Egypt","Casablanca":"Morocco","Ceuta":"Spain","Conakry":"Guinea","Dakar":"Senegal","Dar es Salaam":"Tanzania","Djibouti":"Djibouti","Douala":"Cameroon","Freetown":"Sierra Leone","Gaborone":"Botswana","Harare":"Zimbabwe","Johannesburg":"South Africa","Juba":"South Sudan","Kampala":"Uganda","Khartoum":"Sudan","Kigali":"Rwanda","Kinshasa":"Congo","Lagos":"Nigeria","Libreville":"Gabon","Lome":"Togo","Luanda":"Angola","Lubumbashi":"Congo","Lusaka":"Zambia","Malabo":"Equatorial Guinea","Maputo":"Mozambique","Maseru":"Lesotho","Mbabane":"Swaziland","Mogadishu":"Somalia","Monrovia":"Liberia","Nairobi":"Kenya","Niamey":"Niger","Nouakchott":"Mauritania","Ouagadougou":"Burkina Faso","Sao Tome":"Brazil","Tripoli":"Libya","Tunis":"Tunisia","Windhoek":"Namibia","Adak":"United States","Anchorage":"United States","Anguilla":"United States","Antigua":"Spain","Asuncion":"Paraguay","Atikokan":"Canada","Belem":"Brazil","Boa Vista":"Brazil","Bogota":"United States","Boise":"United States","Cambridge Bay":"Canada","Campo Grande":"Brazil","Caracas":"Venezuela","Chicago":"Mexico","Chihuahua":"Mexico","Creston":"Canada","Cuiaba":"Brazil","Dawson":"Australia","Dawson Creek":"Canada","Denver":"United States","Detroit":"United States","Dominica":"Dominican Republic","Edmonton":"Canada","El Salvador":"Guatemala","Fort Nelson":"Canada","Fortaleza":"Brazil","Glace Bay":"Canada","Grenada":"United States","Guayaquil":"Ecuador","Halifax":"Canada","Havana":"Cuba","Hermosillo":"Mexico","Indiana":"United States","Inuvik":"Canada","Iqaluit":"Canada","Jamaica":"United States","Juneau":"United States","Kralendijk":"Bonaire","La Paz":"Uruguay","Lima":"Argentina","Los Angeles":"Panama","Managua":"Nicaragua","Manaus":"Brazil","Marigot":"Dominica","Mazatlan":"Mexico","Menominee":"United States","Metlakatla":"United States","Mexico City":"Mexico","Moncton":"Canada","Monterrey":"Mexico","Montevideo":"Uruguay","Montserrat":"Argentina","Nassau":"Bahamas","New York":"United States","Nipigon":"Canada","Nome":"United States","Ojinaga":"Mexico","Panama":"United States","Paramaribo":"Suriname","Phoenix":"South Africa","Port-au-Prince":"Haiti","Port of Spain":"Trinidad and Tobago","Porto Velho":"Brazil","Puerto Rico":"Argentina","Punta Arenas":"Chile","Rankin Inlet":"Canada","Recife":"Brazil","Regina":"Canada","Rio Branco":"Brazil","Santiago":"Peru","Santo Domingo":"Costa Rica","Sao Paulo":"Brazil","Sitka":"United States","Swift Current":"Canada","Tegucigalpa":"Honduras","Thunder Bay":"Canada","Tijuana":"Mexico","Toronto":"Canada","Tortola":"British Virgin Islands","Vancouver":"Canada","Whitehorse":"Canada","Winnipeg":"Canada","Yellowknife":"Canada","Casey":"United States","Davis":"United States","Palmer":"Puerto Rico","Vostok":"Kazakhstan","Longyearbyen":"Svalbard and Jan Mayen","Aden":"Yemen","Almaty":"Kazakhstan","Amman":"Hashemite Kingdom of Jordan","Aqtau":"Kazakhstan","Ashgabat":"Turkmenistan","Atyrau":"Kazakhstan","Baghdad":"Iraq","Baku":"Azerbaijan","Bangkok":"Thailand","Barnaul":"Russia","Beirut":"Lebanon","Bishkek":"Kyrgyzstan","Chita":"Russia","Colombo":"Sri Lanka","Damascus":"Syria","Dhaka":"Bangladesh","Dili":"East Timor","Dubai":"United Arab Emirates","Dushanbe":"Tajikistan","Famagusta":"Cyprus","Gaza":"Palestine","Hebron":"Palestine","Hong Kong":"Hong Kong","Irkutsk":"Russia","Jakarta":"Indonesia","Jayapura":"Indonesia","Jerusalem":"Israel","Kabul":"Afghanistan","Kamchatka":"Russia","Karachi":"Pakistan","Kathmandu":"Nepal","Kolkata":"India","Krasnoyarsk":"Russia","Kuala Lumpur":"Malaysia","Kuching":"Malaysia","Macau":"Brazil","Magadan":"Russia","Makassar":"Indonesia","Manila":"Philippines","Muscat":"Oman","Nicosia":"Cyprus","Novokuznetsk":"Russia","Novosibirsk":"Russia","Omsk":"Russia","Oral":"Kazakhstan","Phnom Penh":"Cambodia","Pontianak":"Indonesia","Pyongyang":"North Korea","Riyadh":"Saudi Arabia","Seoul":"Republic of Korea","Shanghai":"China","Singapore":"Singapore","Taipei":"Taiwan","Tashkent":"Uzbekistan","Tbilisi":"Georgia","Thimphu":"Bhutan","Tokyo":"Japan","Tomsk":"Russia","Vientiane":"Laos","Vladivostok":"Russia","Yakutsk":"Russia","Yangon":"Myanmar [Burma]","Yekaterinburg":"Russia","Yerevan":"Armenia","Madeira":"Portugal","Reykjavik":"Iceland","Stanley":"Falkland Islands","Adelaide":"Australia","Brisbane":"Australia","Broken Hill":"Australia","Currie":"United Kingdom","Darwin":"Australia","Hobart":"Australia","Melbourne":"United Kingdom","Perth":"Canada","Sydney":"Canada","Amsterdam":"Netherlands","Andorra":"Spain","Astrakhan":"Russia","Athens":"Canada","Belgrade":"Serbia","Berlin":"Germany","Bratislava":"Slovakia","Brussels":"Belgium","Bucharest":"Romania","Budapest":"Hungary","Copenhagen":"Denmark","Dublin":"Ireland","Gibraltar":"Gibraltar","Guernsey":"United States","Helsinki":"Finland","Istanbul":"Turkey","Jersey":"United States","Kaliningrad":"Russia","Kiev":"Ukraine","Kirov":"Russia","Lisbon":"Portugal","Ljubljana":"Slovenia","London":"South Africa","Luxembourg":"Luxembourg","Madrid":"Colombia","Malta":"Latvia","Mariehamn":"\u00c5land","Minsk":"Belarus","Monaco":"Monaco","Moscow":"Russia","Oslo":"Norway","Paris":"Canada","Podgorica":"Montenegro","Prague":"Czech Republic","Riga":"Latvia","Rome":"Italy","Samara":"Russia","San Marino":"San Marino","Sarajevo":"Bosnia and Herzegovina","Saratov":"Russia","Simferopol":"Ukraine","Skopje":"Macedonia","Sofia":"Bulgaria","Stockholm":"Sweden","Tallinn":"Estonia","Ulyanovsk":"Russia","Vaduz":"Liechtenstein","Vienna":"Austria","Vilnius":"Republic of Lithuania","Volgograd":"Russia","Warsaw":"Poland","Zagreb":"Croatia","Zurich":"Switzerland","Antananarivo":"Madagascar","Christmas":"United States","Cocos":"Brazil","Apia":"Samoa","Auckland":"New Zealand","Chatham":"Canada","Funafuti":"Tuvalu","Galapagos":"Spain","Gambier":"United States","Honolulu":"United States","Majuro":"Marshall Islands","Midway":"United States","Norfolk":"United States","Noumea":"New Caledonia","Pago Pago":"American Samoa","Palau":"Spain","Pitcairn":"United States","Port Moresby":"Papua New Guinea","Saipan":"Northern Mariana Islands","Wake":"United States","Wallis":"United States"}';
    }
}