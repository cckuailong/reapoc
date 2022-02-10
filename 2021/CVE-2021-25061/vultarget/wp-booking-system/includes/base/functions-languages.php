<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns an array with supported languages
 *
 * @return array
 *
 */
function wpbs_get_languages() {

	$languages = array( 
		'en' => 'English', 
		'bg' => 'Bulgarian',
		'ca' => 'Catalan',
		'hr' => 'Croatian',
		'cz' => 'Czech',
		'da' => 'Danish',
		'nl' => 'Dutch',
		'et' => 'Estonian',
		'fi' => 'Finnish',
		'fr' => 'French',
		'gl' => 'Galician',
		'de' => 'German',
		'el' => 'Greek',
		'hu' => 'Hungarian',
		'it' => 'Italian',
		'jp' => 'Japanese',
		'lt' => 'Lithuanian',
		'no' => 'Norwegian',
		'pl' => 'Polish',
		'pt' => 'Portugese',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sr' => 'Serbian',
		'sk' => 'Slovak',
		'sl' => 'Slovenian',
		'es' => 'Spanish',
		'sv' => 'Swedish',
		'tr' => 'Turkish',
		'ua' => 'Ukrainian'
	);

	/**
	 * Filter to modify the languages array just before returning
	 *
	 * @param array $languages
	 *
	 */
	$languages = apply_filters( 'wpbs_get_languages', $languages );

	return $languages;

}


/**
 * Returns an array with the first letter of each day of the week in order, starting
 * from Monday until Sunday
 *
 * @param string $language_code
 *
 * @return array
 *
 */
function wpbs_get_days_first_letters( $language_code = 'en' ) {

    $days_first_letters = array(
        'af' => array( 'M','D','W','D','V','S','S' ), /* South Africa */
		'bg' => array( 'П','В','С','Ч','П','С','Н' ), /* Bulgarian */
        'ca' => array( 'D','D','D','D','D','D','D' ), /* Catalan */
		'hr' => array( 'P','U','S','Č','P','S','N' ), /* Croatian */
		'cz' => array( 'P','Ú','S','Č','P','S','N' ), /* Czech */
		'cs' => array( 'P','Ú','S','Č','P','S','N' ), /* Czech */
		'da' => array( 'M','T','O','T','F','L','S' ), /* Danish */
		'nl' => array( 'M','D','W','D','V','Z','Z' ), /* Dutch */
        'en' => array( 'M','T','W','T','F','S','S' ), /* English */
		'et' => array( 'E','T','K','N','R','L','P' ), /* Estonian */
		'fi' => array( 'M','T','K','T','P','L','S' ), /* Finnish */
		'fr' => array( 'L','M','M','J','V','S','D' ), /* French */
		'gl' => array( 'L','M','M','X','V','S','D' ), /* Galician */
		'de' => array( 'M','D','M','D','F','S','S' ), /* German */
		'el' => array( 'Δ','Τ','Τ','Π','Π','Σ','Κ' ), /* Greek */
		'hu' => array( 'H','K','S','C','P','S','V' ), /* Hungarian */
		'it' => array( 'L','M','M','G','V','S','D' ), /* Italian */
        'jp' => array( '月','火','水','木','金','土','日' ), /* Japanese */
        'ja' => array( '月','火','水','木','金','土','日' ), /* Japanese */
		'no' => array( 'M','T','O','T','F','L','S' ), /* Norwegian */
        'lt' => array( 'P','A','T','K','P','Š','S' ), /* Lithuanian */        
		'nb' => array( 'M','T','O','T','F','L','S' ), /* Norwegian */
		'pl' => array( 'P','W','S','C','P','S','N' ), /* Polish */
		'pt' => array( 'S','T','Q','Q','S','S','D' ), /* Portugese */
        'ro' => array( 'L','M','M','J','V','S','D' ), /* Romanian */
		'ru' => array( 'П','В','С','Ч','П','С','В' ), /* Russian */
		'sr' => array( 'P','U','S','Č','P','S','N' ), /* Serbian */
		'sk' => array( 'P','U','S','Š','P','S','N' ), /* Slovak */
		'sl' => array( 'P','T','S','Č','P','S','N' ), /* Slovenian */
		'es' => array( 'L','M','M','J','V','S','D' ), /* Spanish */
		'sv' => array( 'M','T','O','T','F','L','S' ), /* Swedish */
		'tr' => array( 'P','S','Ç','P','C','C','P' ), /* Turkish */
		'uk' => array( 'П','В','С','Ч','П','С','Н' )  /* Ukrainian */
    );

    return ( ! empty( $days_first_letters[$language_code] ) ? $days_first_letters[$language_code] : $days_first_letters['en'] );

}


/**
 * Returns an array with the names of the months
 *
 * @param int 	 $month
 * @param string $language_code
 *
 * @return array
 *
 */
function wpbs_get_month_name( $month, $language_code = 'en' ) {

    $month_names = array(

    	/* South Africa */
	    'af'  => array( 'Januarie', 'Februarie', 'Maart', 'April', 'Mei', 'Junie', 'Julie', 'Augustus', 'September', 'Oktober', 'November', 'Desember' ),

	    /* Bulgarian */
		'bg' => array( 'Януари', 'Февруари', 'Март', 'Април', 'Май', 'Юни', 'Юли', 'Август', 'Септември', 'Октомври', 'Ноември', 'Декември' ),

		/* Catalan */
        'ca' => array( 'Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre' ),

        /* Croatian */
		'hr' => array( 'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac' ),

		/* Czech */
		'cz' => array( 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec' ),

		/* Czech */ 
		'cs' => array( 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec' ),
	
		/* Danish */
		'da' => array( 'Januar', 'Februar', 'Marts', 'April', 'Maj', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'December' ),

		/* Dutch */
		'nl' => array( 'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December' ),

		/* English */
        'en' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),

        /* Estonian */
		'et' => array( 'Jaanuar', 'Veebruar', 'Märts', 'Aprill', 'Mai', 'Juuni', 'Juuli', 'August', 'September', 'Oktoober', 'November', 'Detsember' ),

		/* Finnish */
		'fi' => array( 'Tammikuu', 'Helmikuu', 'Maaliskuu', 'Huhtikuu', 'Toukokuu', 'Kesäkuu', 'Heinäkuu', 'Elokuu', 'Syyskuu', 'Lokakuu', 'Marraskuu', 'Joulukuu' ),

		/* French */
		'fr' => array( 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ),
		
		/* Galician */
		'gl' => array( 'Xaneiro', 'Febreiro', 'Marzo', 'Abril', 'Maio', 'Xuño', 'Xullo', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Decembro' ),

		/* German */
		'de' => array( 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' ),		
		
		/* Greek */
		'el' => array( 'Ιανουάριος', 'Φεβρουάριος', 'Μάρτιος', 'Απρίλιος', 'Μάιος', 'Ιούνιος', 'Ιούλιος', 'Αύγουστος', 'Σεπτέμβριος', 'Οκτώβριος', 'Νοέμβριος', 'Δεκέμβριος' ),		
        
        /* Hungarian */
        'hu' => array( 'Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December' ),
		
		/* Italian */
		'it' => array( 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre' ),
        
        /* Japanese */
        'jp' => array( '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月' ),
        'ja' => array( '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月' ),
		
		/* Lithuanian */
		'lt' => array( 'Sausis', 'Vasaris', 'Kovas', 'Balandis', 'Gegužis', 'Birželis', 'Liepa', 'Rugpjūtis', 'Rugsėjis', 'Spalis', 'Lapkritis', 'Gruodis' ),
        
		/* Norwegian */
        'no' => array( 'Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember' ),
		
		/* Norwegian */
		'nb' => array( 'Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember' ),
		
		/* Polish */
		'pl' => array( 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień' ),
		
		/* Portugese */
		'pt' => array( 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Septembro', 'Outubro', 'Novembro', 'Dezembro' ),
        
        /* Romanian */
        'ro' => array( 'Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie' ),
		
		/* Russian */
		'ru' => array( 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' ),
		
		/* Serbian */
		'sr' => array( 'Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar' ),

		/* Slovak */
		'sk' => array( 'Január', 'Február', 'Marec', 'Apríl', 'Máj', 'Jún', 'Júl', 'August', 'September', 'Október', 'November', 'December' ),	
		
		/* Slovenian */
		'sl' => array( 'Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December' ),
		
		/* Spanish */
		'es' => array( 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ),
		
		/* Swedish */
		'sv' => array( 'Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December' ),
		
		/* Turkish */
		'tr' => array( 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık' ),
		
		/* Ukrainian */
		'uk' => array( 'Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень' )

    );

    return ( ! empty( $month_names[$language_code][$month - 1] ) ? $month_names[$language_code][$month - 1] : $month_names['en'][$month - 1] );

}