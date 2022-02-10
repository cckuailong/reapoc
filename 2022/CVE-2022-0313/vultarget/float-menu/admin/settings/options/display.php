<?php
/**
 * Display options
 *
 * @package     Lead_Generation
 * @subpackage  Settings
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enable Don’t show on screens more than
$include_more_screen = array(
	'id'   => 'include_more_screen',
	'name' => 'param[include_more_screen]',
	'type' => 'checkbox',
	'val'  => isset( $param['include_more_screen'] ) ? $param['include_more_screen'] : 0,
	'func' => 'screen_more(this);',
);

// Show on screens helper
$show_screen_help = array(
	'text' => esc_attr__( 'Specify the window breakpoint in px when the button will be shown.', $this->plugin['text'] ),
);

// Max screen value
$screen_more = array(
	'id'     => 'screenmore',
	'name'   => 'param[screen_more]',
	'type'   => 'number',
	'val'    => isset( $param['screen_more'] ) ? $param['screen_more'] : 1024,
	'option' => array(
		'min'         => '0',
		'max'         => '3000',
		'step'        => '1',
		'placeholder' => '1024',
	),
);

// Enable Don’t show on screens less than
$include_mobile = array(
	'id'   => 'include_mobile',
	'name' => 'param[include_mobile]',
	'type' => 'checkbox',
	'val'  => isset( $param['include_mobile'] ) ? $param['include_mobile'] : 0,
	'func' => 'screen_less(this);',
);

// Enable Don’t show on screens less than helper
$include_mobile_help = array(
	'text' => esc_attr__( 'Specify the window breakpoint ( mix width) in px.', $this->plugin['text'] ),
);

// Min screen value
$screen = array(
	'id'     => 'screen',
	'name'   => 'param[screen]',
	'type'   => 'number',
	'val'    => isset( $param['screen'] ) ? $param['screen'] : 480,
	'option' => array(
		'min'         => '0',
		'max'         => '3000',
		'step'        => '1',
		'placeholder' => '480',
	),
);

// Show for users
$item_user = array(
	'id'     => 'item_user',
	'name'   => 'param[item_user]',
	'type'   => 'select',
	'class'  => 'item_user',
	'val'    => isset( $param['item_user'] ) ? $param['item_user'] : '1',
	'option' => array(
		'1' => esc_attr__( 'All Users', $this->plugin['text'] ),
		'2' => esc_attr__( 'Authorized Users', $this->plugin['text'] ),
		'3' => esc_attr__( 'Unauthorized Users', $this->plugin['text'] ),
	),
	'sep'    => 'br',
	'func'   => 'usersroles(this);',
);


// Users role
$add_users      = array( 'all' => array( 'name' => esc_attr__( 'All Users', $this->plugin['text'] ) ) );
$editable_role  = array_reverse( get_editable_roles() );
$editable_roles = array_merge( $add_users, $editable_role );
$users_arr      = array();
foreach ( $editable_roles as $role => $details ) {
	$name                           = translate_user_role( $details['name'] );
	$users_arr[ esc_attr( $role ) ] = $name;
}

$user_role = array(
	'id'     => 'users_roles',
	'name'   => 'param[user_role]',
	'type'   => 'select',
	'val'    => isset( $param['user_role'] ) ? $param['user_role'] : 'all',
	'option' => $users_arr,
);

// Enable Depending on the language
$depending_language = array(
	'id'   => 'depending_language',
	'name' => 'param[depending_language]',
	'type' => 'checkbox',
	'val'  => isset( $param['depending_language'] ) ? $param['depending_language'] : 0,
	'func' => 'languages(this);',
);

$languages = array(
	'af'             => 'Afrikaans',
	'ar'             => 'العربية',
	'ary'            => 'العربية المغربية',
	'as'             => 'অসমীয়া',
	'az'             => 'Azərbaycan dili',
	'azb'            => 'گؤنئی آذربایجان',
	'bel'            => 'Беларуская мова',
	'bg_BG'          => 'Български',
	'bn_BD'          => 'বাংলা',
	'bo'             => 'བོད་ཡིག',
	'bs_BA'          => 'Bosanski',
	'ca'             => 'Català',
	'ceb'            => 'Cebuano',
	'cs_CZ'          => 'Čeština',
	'cy'             => 'Cymraeg',
	'da_DK'          => 'Dansk',
	'de_DE'          => 'Deutsch',
	'de_CH_informal' => 'Deutsch (Schweiz, Du)',
	'de_CH'          => 'Deutsch (Schweiz)',
	'de_DE_formal'   => 'Deutsch (Sie)',
	'de_AT'          => 'Deutsch (Österreich)',
	'dzo'            => 'རྫོང་ཁ',
	'el'             => 'Ελληνικά',
	'en_US'          => 'English (United States)',
	'en_GB'          => 'English (UK)',
	'en_AU'          => 'English (Australia)',
	'en_NZ'          => 'English (New Zealand)',
	'en_CA'          => 'English (Canada)',
	'en_ZA'          => 'English (South Africa)',
	'eo'             => 'Esperanto',
	'es_ES'          => 'Español',
	'es_VE'          => 'Español de Venezuela',
	'es_GT'          => 'Español de Guatemala',
	'es_CR'          => 'Español de Costa Rica',
	'es_MX'          => 'Español de México',
	'es_CO'          => 'Español de Colombia',
	'es_PE'          => 'Español de Perú',
	'es_CL'          => 'Español de Chile',
	'es_AR'          => 'Español de Argentina',
	'et'             => 'Eesti',
	'eu'             => 'Euskara',
	'fa_IR'          => 'فارسی',
	'fi'             => 'Suomi',
	'fr_FR'          => 'Français',
	'fr_CA'          => 'Français du Canada',
	'fr_BE'          => 'Français de Belgique',
	'fur'            => 'Friulian',
	'gd'             => 'Gàidhlig',
	'gl_ES'          => 'Galego',
	'gu'             => 'ગુજરાતી',
	'haz'            => 'هزاره گی',
	'he_IL'          => 'עִבְרִית',
	'hi_IN'          => 'हिन्दी',
	'hr'             => 'Hrvatski',
	'hu_HU'          => 'Magyar',
	'hy'             => 'Հայերեն',
	'id_ID'          => 'Bahasa Indonesia',
	'is_IS'          => 'Íslenska',
	'it_IT'          => 'Italiano',
	'ja'             => '日本語',
	'jv_ID'          => 'Basa Jawa',
	'ka_GE'          => 'ქართული',
	'kab'            => 'Taqbaylit',
	'kk'             => 'Қазақ тілі',
	'km'             => 'ភាសាខ្មែរ',
	'kn'             => 'ಕನ್ನಡ',
	'ko_KR'          => '한국어',
	'ckb'            => 'كوردی‎',
	'lo'             => 'ພາສາລາວ',
	'lt_LT'          => 'Lietuvių kalba',
	'lv'             => 'Latviešu valoda',
	'mk_MK'          => 'Македонски јазик',
	'ml_IN'          => 'മലയാളം',
	'mn'             => 'Монгол',
	'mr'             => 'मराठी',
	'ms_MY'          => 'Bahasa Melayu',
	'my_MM'          => 'ဗမာစာ',
	'nb_NO'          => 'Norsk bokmål',
	'ne_NP'          => 'नेपाली',
	'nl_NL'          => 'Nederlands',
	'nl_NL_formal'   => 'Nederlands (Formeel)',
	'nl_BE'          => 'Nederlands (België)',
	'nn_NO'          => 'Norsk nynorsk',
	'oci'            => 'Occitan',
	'pa_IN'          => 'ਪੰਜਾਬੀ',
	'pl_PL'          => 'Polski',
	'ps'             => 'پښتو',
	'pt_PT'          => 'Português',
	'pt_AO'          => 'Português de Angola',
	'pt_PT_ao90'     => 'Português (AO90)',
	'pt_BR'          => 'Português do Brasil',
	'rhg'            => 'Ruáinga',
	'ro_RO'          => 'Română',
	'ru_RU'          => 'Русский',
	'sah'            => 'Сахалыы',
	'si_LK'          => 'සිංහල',
	'sk_SK'          => 'Slovenčina',
	'skr'            => 'سرائیکی',
	'sl_SI'          => 'Slovenščina',
	'sq'             => 'Shqip',
	'sr_RS'          => 'Српски језик',
	'sv_SE'          => 'Svenska',
	'szl'            => 'Ślōnskŏ gŏdka',
	'ta_IN'          => 'தமிழ்',
	'te'             => 'తెలుగు',
	'th'             => 'ไทย',
	'tl'             => 'Tagalog',
	'tr_TR'          => 'Türkçe',
	'tt_RU'          => 'Татар теле',
	'tah'            => 'Reo Tahiti',
	'ug_CN'          => 'ئۇيغۇرچە',
	'uk'             => 'Українська',
	'ur'             => 'اردو',
	'uz_UZ'          => 'O‘zbekcha',
	'vi'             => 'Tiếng Việt',
	'zh_CN'          => '简体中文',
	'zh_TW'          => '繁體中文',
	'zh_HK'          => '香港中文版',
);

$default_lang = 'en_US';

if ( isset( $param['lang'] ) && ! isset( $param['language'] ) ) {
	$old_lang = $param['lang'];
	if ( $old_lang !== 'all' ) {
		foreach ( $languages as $key => $val ) {
			$pos = strpos( $key, $old_lang );
			if ( $pos !== false ) {
				$default_lang = $key;
				break;
			}
		}
	}

}

// Languages
$language = array(
	'id'     => 'language',
	'name'   => 'param[language]',
	'type'   => 'select',
	'val'    => isset( $param['language'] ) ? $param['language'] : $default_lang,
	'option' => $languages,
);

// Disable FontAwesome on front-end of the site
$disable_fontawesome = array(
	'id'   => 'disable_fontawesome',
	'name' => 'param[disable_fontawesome]',
	'type' => 'checkbox',
	'val'  => isset( $param['disable_fontawesome'] ) ? $param['disable_fontawesome'] : 0,
);

$disable_fontawesome_help = array(
	'title' => esc_attr__( 'Disable Font Awesome 5 style on front-end of the site.', $this->plugin['text'] ),
	'ul'    => array(
		__( 'If you already have a Font Awesome 5 installed on the site, you can disable the include the Font Awesome 5 style.',
			$this->plugin['text'] ),
	),
);

// Mobile rules Enable
$mobile_rules = array(
	'name'  => 'param[mobile_rules]',
	'id'    => 'mobile_rules',
	'class' => '',
	'type'  => 'checkbox',
	'val'   => isset( $param['mobile_rules'] ) ? $param['mobile_rules'] : 0,
	'func'  => 'mobileRules(this);',
	'sep'   => '',
);

$mobile_rules_help = array(
	'title' => esc_attr__( 'Enable mobile rules for menu. Defines menu behavior on mobile devices:', $this->plugin['text'] ),
	'ul'    => array(
		__( 'First click opens the label', $this->plugin['text'] ),
		__( 'Second click calls the link AND closes the label', $this->plugin['text'] ),
		__( 'Hide label after 3 sec, if it open', $this->plugin['text'] ),
	),
);

// Screen for mobile rules
$mobile_screen = array(
	'name'   => 'param[mobile_screen]',
	'id'     => 'mobile_screen',
	'type'   => 'number',
	'val'    => isset( $param['mobile_screen'] ) ? $param['mobile_screen'] : '768',
	'option' => array(
		'step'        => '1',
		'placeholder' => '768',
	),
);

$mobile_screen_help = array(
	'text' => esc_attr__( 'Set the screen width for mobile devices when mobile rules are applied.', $this->plugin['text'] ),
);

//region Schedule
$weekday = array(
	'name'    => 'param[weekday]',
	'id'      => 'weekday',
	'type'    => 'select',
	'val'     => isset( $param['weekday'] ) ? $param['weekday'] : 'none',
	'option'  => [
		'none' => esc_attr__( 'Everyday', $this->plugin['text'] ),
		'1'    => esc_attr__( 'Monday', $this->plugin['text'] ),
		'2'    => esc_attr__( 'Tuesday', $this->plugin['text'] ),
		'3'    => esc_attr__( 'Wednesday', $this->plugin['text'] ),
		'4'    => esc_attr__( 'Thursday', $this->plugin['text'] ),
		'5'    => esc_attr__( 'Friday', $this->plugin['text'] ),
		'6'    => esc_attr__( 'Saturday', $this->plugin['text'] ),
		'7'    => esc_attr__( 'Sunday ', $this->plugin['text'] ),

	],
	'tooltip' => esc_attr__( 'Select the day of the week when the notification will be displayed.', $this->plugin['text'] ),
	'icon'    => '',
	'func'    => '',
);

$weekday_help = array(
	'text' => esc_attr__( 'Select the day of the week when the menu will be displayed.', $this->plugin['text'] ),
);

$time_start = array(
	'name' => 'param[time_start]',
	'id'   => 'time_start',
	'type' => 'time',
	'val'  => isset( $param['time_start'] ) ? $param['time_start'] : '00:00',
);

$time_start_help = array(
	'text' => esc_attr__( 'Specify what from time of the day to show the menu', $this->plugin['text'] ),
);

$time_end = array(
	'name'  => 'param[time_end]',
	'id'    => 'time_end',
	'val' => isset( $param['time_end'] ) ? $param['time_end'] : '23:59',
	'type' => 'time',
);

$time_end_help = array(
	'text' => esc_attr__( 'Specify what to time of the day to show the menu', $this->plugin['text'] ),
);

$set_dates = array(
	'name'  => 'param[set_dates]',
	'id'    => 'set_dates',
	'val' => isset( $param['set_dates'] ) ? $param['set_dates'] : '',
	'type' => 'checkbox',
);

$set_dates_help = array(
	'text' => esc_attr__( 'Check this if you want to set the show menu between dates.', $this->plugin['text'] ),
);

$date_start = array(
	'name'  => 'param[date_start]',
	'id'    => 'date_start',
	'val' => isset( $param['date_start'] ) ? $param['date_start'] : '',
	'type' => 'time',
);

$date_start_help = array(
	'text' => esc_attr__( 'Set the date start.', $this->plugin['text'] ),
);

$date_end = array(
	'name'  => 'param[date_end]',
	'id'    => 'date_end',
	'val' => isset( $param['date_end'] ) ? $param['date_end'] : '',
	'type' => 'time',
);

$date_start_help = array(
	'text' => esc_attr__( 'Set the date end.', $this->plugin['text'] ),
);

//endregion

//region Browser

$all_browser = array(
	'name'  => 'param[all_browser]',
	'id'    => 'all_browser',
	'val' => isset( $param['all_browser'] ) ? $param['all_browser'] : '0',
	'type' => 'checkbox'
);

$br_opera = array(
	'name'  => 'param[br_opera]',
	'id'    => 'br_opera',
	'val' => isset( $param['br_opera'] ) ? $param['br_opera'] : '0',
	'type' => 'checkbox'
);

$br_edge = array(
	'name'  => 'param[br_edge]',
	'id'    => 'br_edge',
	'val' => isset( $param['br_edge'] ) ? $param['br_edge'] : '0',
	'class' => 'browser',
	'type' => 'checkbox'
);

$br_chrome = array(
	'name'  => 'param[br_chrome]',
	'id'    => 'br_chrome',
	'val' => isset( $param['br_chrome'] ) ? $param['br_chrome'] : '0',
	'class' => 'browser',
	'type' => 'checkbox'
);

$br_safari = array(
	'name'  => 'param[br_safari]',
	'id'    => 'br_safari',
	'val' => isset( $param['br_safari'] ) ? $param['br_safari'] : '0',
	'class' => 'browser',
	'type' => 'checkbox'
);

$br_firefox = array(
	'name'  => 'param[br_firefox]',
	'id'    => 'br_firefox',
	'val' => isset( $param['br_firefox'] ) ? $param['br_firefox'] : '0',
	'class' => 'browser',
	'type' => 'checkbox'

);

$br_ie = array(
	'name'  => 'param[br_ie]',
	'id'    => 'br_ie',
	'val' => isset( $param['br_ie'] ) ? $param['br_ie'] : '0',
	'class' => 'browser',
	'type' => 'checkbox'
);

$br_other = array(
	'name'  => 'param[br_other]',
	'id'    => 'br_other',
	'val' => isset( $param['br_other'] ) ? $param['br_other'] : '0',
	'type' => 'checkbox'
);

//endregion