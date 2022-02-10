<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\PageViewsCount\FrameWork {

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------
A3rev Plugin Fonts Face

TABLE OF CONTENTS

- var default_fonts
- var google_fonts
- __construct()
- get_default_fonts()
- get_google_fonts()
- generate_font_css()
- generate_google_webfonts()

-----------------------------------------------------------------------------------*/

class Fonts_Face extends Admin_UI
{

	/**
	 * Window Default Fonts
	 */
	public $default_fonts = array(
			'Arial, sans-serif'												=> 'Arial',
			'Verdana, Geneva, sans-serif'									=> 'Verdana',
			'Trebuchet MS, Tahoma, sans-serif'								=> 'Trebuchet',
			'Georgia, serif'												=> 'Georgia',
			'Times New Roman, serif'										=> 'Times New Roman',
			'Tahoma, Geneva, Verdana, sans-serif'							=> 'Tahoma',
			'Palatino, Palatino Linotype, serif'							=> 'Palatino',
			'Helvetica Neue, Helvetica, sans-serif'							=> 'Helvetica*',
			'Calibri, Candara, Segoe, Optima, sans-serif'					=> 'Calibri*',
			'Myriad Pro, Myriad, sans-serif'								=> 'Myriad Pro*',
			'Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif'	=> 'Lucida',
			'Arial Black, sans-serif'										=> 'Arial Black',
			'Gill Sans, Gill Sans MT, Calibri, sans-serif'					=> 'Gill Sans*',
			'Geneva, Tahoma, Verdana, sans-serif'							=> 'Geneva*',
			'Impact, Charcoal, sans-serif'									=> 'Impact',
			'Courier, Courier New, monospace'								=> 'Courier',
			'Century Gothic, sans-serif'									=> 'Century Gothic',
	);

	/**
	 * Google API Key use to get dynamic list of fonts from google, you can create new API KEY to use from https://developers.google.com/fonts/docs/developer_api
	 */
	public $google_api_key = '';

	/*-----------------------------------------------------------------------------------*/
	/* Google Webfonts Array */
	/* Documentation:
	/*
	/* name: The name of the Google Font.
	/* variant: The Google Font API variants available for the font.
	/*-----------------------------------------------------------------------------------*/

	// Available Google webfont names
	public $google_fonts = array(	array( 'name' => "Cantarell", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Cardo", 'variant' => ''),
							array( 'name' => "Crimson Text", 'variant' => ''),
							array( 'name' => "Droid Sans", 'variant' => ':r,b'),
							array( 'name' => "Droid Sans Mono", 'variant' => ''),
							array( 'name' => "Droid Serif", 'variant' => ':r,b,i,bi'),
							array( 'name' => "IM Fell DW Pica", 'variant' => ':r,i'),
							array( 'name' => "Inconsolata", 'variant' => ''),
							array( 'name' => "Josefin Sans", 'variant' => ':400,400italic,700,700italic'),
							array( 'name' => "Josefin Slab", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Lobster", 'variant' => ''),
							array( 'name' => "Molengo", 'variant' => ''),
							array( 'name' => "Nobile", 'variant' => ':r,b,i,bi'),
							array( 'name' => "OFL Sorts Mill Goudy TT", 'variant' => ':r,i'),
							array( 'name' => "Old Standard TT", 'variant' => ':r,b,i'),
							array( 'name' => "Reenie Beanie", 'variant' => ''),
							array( 'name' => "Tangerine", 'variant' => ':r,b'),
							array( 'name' => "Vollkorn", 'variant' => ':r,b'),
							array( 'name' => "Yanone Kaffeesatz", 'variant' => ':r,b'),
							array( 'name' => "Cuprum", 'variant' => ''),
							array( 'name' => "Neucha", 'variant' => ''),
							array( 'name' => "Neuton", 'variant' => ''),
							array( 'name' => "PT Sans", 'variant' => ':r,b,i,bi'),
							array( 'name' => "PT Sans Caption", 'variant' => ':r,b'),
							array( 'name' => "PT Sans Narrow", 'variant' => ':r,b'),
							array( 'name' => "Philosopher", 'variant' => ''),
							array( 'name' => "Allerta", 'variant' => ''),
							array( 'name' => "Allerta Stencil", 'variant' => ''),
							array( 'name' => "Arimo", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Arvo", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Bentham", 'variant' => ''),
							array( 'name' => "Coda", 'variant' => ':800'),
							array( 'name' => "Cousine", 'variant' => ''),
							array( 'name' => "Covered By Your Grace", 'variant' => ''),
							array( 'name' => "Geo", 'variant' => ''),
							array( 'name' => "Just Me Again Down Here", 'variant' => ''),
							array( 'name' => "Puritan", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Raleway", 'variant' => ':100'),
							array( 'name' => "Tinos", 'variant' => ':r,b,i,bi'),
							array( 'name' => "UnifrakturCook", 'variant' => ':bold'),
							array( 'name' => "UnifrakturMaguntia", 'variant' => ''),
							array( 'name' => "Mountains of Christmas", 'variant' => ''),
							array( 'name' => "Lato", 'variant' => ':400,700,400italic'),
							array( 'name' => "Orbitron", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Allan", 'variant' => ':bold'),
							array( 'name' => "Anonymous Pro", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Copse", 'variant' => ''),
							array( 'name' => "Kenia", 'variant' => ''),
							array( 'name' => "Ubuntu", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Vibur", 'variant' => ''),
							array( 'name' => "Sniglet", 'variant' => ':800'),
							array( 'name' => "Syncopate", 'variant' => ''),
							array( 'name' => "Cabin", 'variant' => ':400,400italic,700,700italic,'),
							array( 'name' => "Merriweather", 'variant' => ''),
							array( 'name' => "Maiden Orange", 'variant' => ''),
							array( 'name' => "Just Another Hand", 'variant' => ''),
							array( 'name' => "Kristi", 'variant' => ''),
							array( 'name' => "Corben", 'variant' => ':b'),
							array( 'name' => "Gruppo", 'variant' => ''),
							array( 'name' => "Buda", 'variant' => ':light'),
							array( 'name' => "Lekton", 'variant' => ''),
							array( 'name' => "Luckiest Guy", 'variant' => ''),
							array( 'name' => "Crushed", 'variant' => ''),
							array( 'name' => "Chewy", 'variant' => ''),
							array( 'name' => "Coming Soon", 'variant' => ''),
							array( 'name' => "Crafty Girls", 'variant' => ''),
							array( 'name' => "Fontdiner Swanky", 'variant' => ''),
							array( 'name' => "Permanent Marker", 'variant' => ''),
							array( 'name' => "Rock Salt", 'variant' => ''),
							array( 'name' => "Sunshiney", 'variant' => ''),
							array( 'name' => "Unkempt", 'variant' => ''),
							array( 'name' => "Calligraffitti", 'variant' => ''),
							array( 'name' => "Cherry Cream Soda", 'variant' => ''),
							array( 'name' => "Homemade Apple", 'variant' => ''),
							array( 'name' => "Irish Growler", 'variant' => ''),
							array( 'name' => "Kranky", 'variant' => ''),
							array( 'name' => "Schoolbell", 'variant' => ''),
							array( 'name' => "Slackey", 'variant' => ''),
							array( 'name' => "Walter Turncoat", 'variant' => ''),
							array( 'name' => "Radley", 'variant' => ''),
							array( 'name' => "Meddon", 'variant' => ''),
							array( 'name' => "Kreon", 'variant' => ':r,b'),
							array( 'name' => "Dancing Script", 'variant' => ''),
							array( 'name' => "Goudy Bookletter 1911", 'variant' => ''),
							array( 'name' => "PT Serif Caption", 'variant' => ':r,i'),
							array( 'name' => "PT Serif", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Astloch", 'variant' => ':b'),
							array( 'name' => "Bevan", 'variant' => ''),
							array( 'name' => "Anton", 'variant' => ''),
							array( 'name' => "Expletus Sans", 'variant' => ':b'),
							array( 'name' => "VT323", 'variant' => ''),
							array( 'name' => "Pacifico", 'variant' => ''),
							array( 'name' => "Candal", 'variant' => ''),
							array( 'name' => "Architects Daughter", 'variant' => ''),
							array( 'name' => "Indie Flower", 'variant' => ''),
							array( 'name' => "League Script", 'variant' => ''),
							array( 'name' => "Quattrocento", 'variant' => ''),
							array( 'name' => "Amaranth", 'variant' => ''),
							array( 'name' => "Irish Grover", 'variant' => ''),
							array( 'name' => "Oswald", 'variant' => ':400,300,700'),
							array( 'name' => "EB Garamond", 'variant' => ''),
							array( 'name' => "Nova Round", 'variant' => ''),
							array( 'name' => "Nova Slim", 'variant' => ''),
							array( 'name' => "Nova Script", 'variant' => ''),
							array( 'name' => "Nova Cut", 'variant' => ''),
							array( 'name' => "Nova Mono", 'variant' => ''),
							array( 'name' => "Nova Oval", 'variant' => ''),
							array( 'name' => "Nova Flat", 'variant' => ''),
							array( 'name' => "Terminal Dosis Light", 'variant' => ''),
							array( 'name' => "Michroma", 'variant' => ''),
							array( 'name' => "Miltonian", 'variant' => ''),
							array( 'name' => "Miltonian Tattoo", 'variant' => ''),
							array( 'name' => "Annie Use Your Telescope", 'variant' => ''),
							array( 'name' => "Dawning of a New Day", 'variant' => ''),
							array( 'name' => "Sue Ellen Francisco", 'variant' => ''),
							array( 'name' => "Waiting for the Sunrise", 'variant' => ''),
							array( 'name' => "Special Elite", 'variant' => ''),
							array( 'name' => "Quattrocento Sans", 'variant' => ''),
							array( 'name' => "Smythe", 'variant' => ''),
							array( 'name' => "The Girl Next Door", 'variant' => ''),
							array( 'name' => "Aclonica", 'variant' => ''),
							array( 'name' => "News Cycle", 'variant' => ''),
							array( 'name' => "Damion", 'variant' => ''),
							array( 'name' => "Wallpoet", 'variant' => ''),
							array( 'name' => "Over the Rainbow", 'variant' => ''),
							array( 'name' => "MedievalSharp", 'variant' => ''),
							array( 'name' => "Six Caps", 'variant' => ''),
							array( 'name' => "Swanky and Moo Moo", 'variant' => ''),
							array( 'name' => "Bigshot One", 'variant' => ''),
							array( 'name' => "Francois One", 'variant' => ''),
							array( 'name' => "Sigmar One", 'variant' => ''),
							array( 'name' => "Carter One", 'variant' => ''),
							array( 'name' => "Holta3revd One SC", 'variant' => ''),
							array( 'name' => "Paytone One", 'variant' => ''),
							array( 'name' => "Monofett", 'variant' => ''),
							array( 'name' => "Rokkitt", 'variant' => ':400,700'),
							array( 'name' => "Megrim", 'variant' => ''),
							array( 'name' => "Judson", 'variant' => ':r,ri,b'),
							array( 'name' => "Didact Gothic", 'variant' => ''),
							array( 'name' => "Play", 'variant' => ':r,b'),
							array( 'name' => "Ultra", 'variant' => ''),
							array( 'name' => "Metrophobic", 'variant' => ''),
							array( 'name' => "Mako", 'variant' => ''),
							array( 'name' => "Shanti", 'variant' => ''),
							array( 'name' => "Caudex", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Jura", 'variant' => ''),
							array( 'name' => "Ruslan Display", 'variant' => ''),
							array( 'name' => "Brawler", 'variant' => ''),
							array( 'name' => "Nunito", 'variant' => ''),
							array( 'name' => "Wire One", 'variant' => ''),
							array( 'name' => "Podkova", 'variant' => ''),
							array( 'name' => "Muli", 'variant' => ''),
							array( 'name' => "Maven Pro", 'variant' => ':400,500,700'),
							array( 'name' => "Tenor Sans", 'variant' => ''),
							array( 'name' => "Limelight", 'variant' => ''),
							array( 'name' => "Playfair Display", 'variant' => ''),
							array( 'name' => "Artifika", 'variant' => ''),
							array( 'name' => "Lora", 'variant' => ''),
							array( 'name' => "Kameron", 'variant' => ':r,b'),
							array( 'name' => "Cedarville Cursive", 'variant' => ''),
							array( 'name' => "Zeyada", 'variant' => ''),
							array( 'name' => "La Belle Aurore", 'variant' => ''),
							array( 'name' => "Shadows Into Light", 'variant' => ''),
							array( 'name' => "Lobster Two", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Nixie One", 'variant' => ''),
							array( 'name' => "Redressed", 'variant' => ''),
							array( 'name' => "Bangers", 'variant' => ''),
							array( 'name' => "Open Sans Condensed", 'variant' => ':300italic,400italic,700italic,400,300,700'),
							array( 'name' => "Open Sans", 'variant' => ':r,i,b,bi'),
							array( 'name' => "Varela", 'variant' => ''),
							array( 'name' => "Goblin One", 'variant' => ''),
							array( 'name' => "Asset", 'variant' => ''),
							array( 'name' => "Gravitas One", 'variant' => ''),
							array( 'name' => "Hammersmith One", 'variant' => ''),
							array( 'name' => "Stardos Stencil", 'variant' => ''),
							array( 'name' => "Love Ya Like A Sister", 'variant' => ''),
							array( 'name' => "Loved by the King", 'variant' => ''),
							array( 'name' => "Bowlby One SC", 'variant' => ''),
							array( 'name' => "Forum", 'variant' => ''),
							array( 'name' => "Patrick Hand", 'variant' => ''),
							array( 'name' => "Varela Round", 'variant' => ''),
							array( 'name' => "Yeseva One", 'variant' => ''),
							array( 'name' => "Give You Glory", 'variant' => ''),
							array( 'name' => "Modern Antiqua", 'variant' => ''),
							array( 'name' => "Bowlby One", 'variant' => ''),
							array( 'name' => "Tienne", 'variant' => ''),
							array( 'name' => "Istok Web", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Yellowtail", 'variant' => ''),
							array( 'name' => "Pompiere", 'variant' => ''),
							array( 'name' => "Unna", 'variant' => ''),
							array( 'name' => "Rosario", 'variant' => ''),
							array( 'name' => "Leckerli One", 'variant' => ''),
							array( 'name' => "Snippet", 'variant' => ''),
							array( 'name' => "Ovo", 'variant' => ''),
							array( 'name' => "IM Fell English", 'variant' => ':r,i'),
							array( 'name' => "IM Fell English SC", 'variant' => ''),
							array( 'name' => "Gloria Hallelujah", 'variant' => ''),
							array( 'name' => "Kelly Slab", 'variant' => ''),
							array( 'name' => "Black Ops One", 'variant' => ''),
							array( 'name' => "Carme", 'variant' => ''),
							array( 'name' => "Aubrey", 'variant' => ''),
							array( 'name' => "Federo", 'variant' => ''),
							array( 'name' => "Delius", 'variant' => ''),
							array( 'name' => "Rochester", 'variant' => ''),
							array( 'name' => "Rationale", 'variant' => ''),
							array( 'name' => "Abel", 'variant' => ''),
							array( 'name' => "Marvel", 'variant' => ':r,b,i,bi'),
							array( 'name' => "Actor", 'variant' => ''),
							array( 'name' => "Delius Swash Caps", 'variant' => ''),
							array( 'name' => "Smokum", 'variant' => ''),
							array( 'name' => "Tulpen One", 'variant' => ''),
							array( 'name' => "Coustard", 'variant' => ':r,b'),
							array( 'name' => "Andika", 'variant' => ''),
							array( 'name' => "Alice", 'variant' => ''),
							array( 'name' => "Questrial", 'variant' => ''),
							array( 'name' => "Comfortaa", 'variant' => ':r,b'),
							array( 'name' => "Geostar", 'variant' => ''),
							array( 'name' => "Geostar Fill", 'variant' => ''),
							array( 'name' => "Volkhov", 'variant' => ''),
							array( 'name' => "Voltaire", 'variant' => ''),
							array( 'name' => "Montez", 'variant' => ''),
							array( 'name' => "Short Stack", 'variant' => ''),
							array( 'name' => "Vidaloka", 'variant' => ''),
							array( 'name' => "Aldrich", 'variant' => ''),
							array( 'name' => "Numans", 'variant' => ''),
							array( 'name' => "Days One", 'variant' => ''),
							array( 'name' => "Gentium Book Basic", 'variant' => ''),
							array( 'name' => "Monoton", 'variant' => ''),
							array( 'name' => "Alike", 'variant' => ''),
							array( 'name' => "Delius Unicase", 'variant' => ''),
							array( 'name' => "Abril Fatface", 'variant' => ''),
							array( 'name' => "Dorsa", 'variant' => ''),
							array( 'name' => "Antic", 'variant' => ''),
							array( 'name' => "Passero One", 'variant' => ''),
							array( 'name' => "Fana3revd Text", 'variant' => ''),
							array( 'name' => "Prociono", 'variant' => ''),
							array( 'name' => "Merienda One", 'variant' => ''),
							array( 'name' => "Changa One", 'variant' => ''),
							array( 'name' => "Julee", 'variant' => ''),
							array( 'name' => "Prata", 'variant' => ''),
							array( 'name' => "Adamina", 'variant' => ''),
							array( 'name' => "Sorts Mill Goudy", 'variant' => ''),
							array( 'name' => "Terminal Dosis", 'variant' => ''),
							array( 'name' => "Sansita One", 'variant' => ''),
							array( 'name' => "Chivo", 'variant' => ''),
							array( 'name' => "Spinnaker", 'variant' => ''),
							array( 'name' => "Poller One", 'variant' => ''),
							array( 'name' => "Alike Angular", 'variant' => ''),
							array( 'name' => "Gochi Hand", 'variant' => ''),
							array( 'name' => "Poly", 'variant' => ''),
							array( 'name' => "Andada", 'variant' => ''),
							array( 'name' => "Federant", 'variant' => ''),
							array( 'name' => "Ubuntu Condensed", 'variant' => ''),
							array( 'name' => "Ubuntu Mono", 'variant' => ''),
							array( 'name' => "Sancreek", 'variant' => ''),
							array( 'name' => "Coda", 'variant' => ''),
							array( 'name' => "Rancho", 'variant' => ''),
							array( 'name' => "Satisfy", 'variant' => ''),
							array( 'name' => "Pinyon Script", 'variant' => ''),
							array( 'name' => "Vast Shadow", 'variant' => ''),
							array( 'name' => "Marck Script", 'variant' => ''),
							array( 'name' => "Salsa", 'variant' => ''),
							array( 'name' => "Amatic SC", 'variant' => ''),
							array( 'name' => "Quicksand", 'variant' => ''),
							array( 'name' => "Linden Hill", 'variant' => ''),
							array( 'name' => "Corben", 'variant' => ''),
							array( 'name' => "Creepster Caps", 'variant' => ''),
							array( 'name' => "Butcherman Caps", 'variant' => ''),
							array( 'name' => "Eater Caps", 'variant' => ''),
							array( 'name' => "Nosifer Caps", 'variant' => ''),
							array( 'name' => "Atomic Age", 'variant' => ''),
							array( 'name' => "Contrail One", 'variant' => ''),
							array( 'name' => "Jockey One", 'variant' => ''),
							array( 'name' => "Cabin Sketch", 'variant' => ':r,b'),
							array( 'name' => "Cabin Condensed", 'variant' => ':r,b'),
							array( 'name' => "Fjord One", 'variant' => ''),
							array( 'name' => "Rametto One", 'variant' => ''),
							array( 'name' => "Mate", 'variant' => ':r,i'),
							array( 'name' => "Mate SC", 'variant' => ''),
							array( 'name' => "Arapey", 'variant' => ':r,i'),
							array( 'name' => "Supermercado One", 'variant' => ''),
							array( 'name' => "Petrona", 'variant' => ''),
							array( 'name' => "Lancelot", 'variant' => ''),
							array( 'name' => "Convergence", 'variant' => ''),
							array( 'name' => "Cutive", 'variant' => ''),
							array( 'name' => "Karla", 'variant' => ':400,400italic,700,700italic'),
							array( 'name' => "Bitter", 'variant' => ':r,i,b'),
							array( 'name' => "Asap", 'variant' => ':400,700,400italic,700italic'),
							array( 'name' => "Bree Serif", 'variant' => '')

	);

	/*-----------------------------------------------------------------------------------*/
	/* Fonts Face Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();

		if ( ! $this->is_load_google_fonts ) {
			$this->google_fonts = array();
			return;
		}

		// Enable Google Font API Key
		if ( isset( $_POST[ $this->google_api_key_option . '_enable' ] ) ) {
			$old_google_api_key_enable = get_option( $this->google_api_key_option . '_enable', 0 );

			update_option( $this->google_api_key_option . '_enable', 1 );

			$option_value = trim( sanitize_text_field( $_POST[ $this->google_api_key_option ] ) );

			$old_google_api_key_option = get_option( $this->google_api_key_option );

			if ( 1 != $old_google_api_key_enable || $option_value != $old_google_api_key_option ) {

				update_option( $this->google_api_key_option, $option_value );

				// Clear cached of google api key status
				delete_transient( $this->google_api_key_option . '_status' );
			}

		// Disable Google Font API Key
		} elseif ( isset( $_POST[ $this->google_api_key_option ] ) ) {
			$old_google_api_key_enable = get_option( $this->google_api_key_option . '_enable', 0 );

			update_option( $this->google_api_key_option . '_enable', 0 );

			$option_value = trim( sanitize_text_field( $_POST[ $this->google_api_key_option ] ) );
			update_option( $this->google_api_key_option, $option_value );

			if ( 0 != $old_google_api_key_enable ) {
				// Clear cached of google api key status
				delete_transient( $this->google_api_key_option . '_status' );
			}
		}

		if ( apply_filters( $this->plugin_name . '_new_google_fonts_enable', true ) ) {
			$this->is_valid_google_api_key();
			$google_fonts = get_option( $this->plugin_name . '_google_font_list', array() );
		} else {
			$google_fonts = array();
		}

		if ( ! is_array( $google_fonts ) || count( $google_fonts ) < 1 ) {
			$google_fonts = apply_filters( $this->plugin_name . '_google_fonts', $this->google_fonts );
		}

		sort( $google_fonts );

		$new_google_fonts = array();
		foreach ( $google_fonts as $row ) {
			$new_google_fonts[$row['name']]  = $row;
		}

		$this->google_fonts = $new_google_fonts;

	}

	public function validate_google_api_key( $g_key = '' ) {
		$g_key = trim( $g_key );
		$response_fonts = array();

		if ( ! empty( $g_key ) ) {
			$respone_api = wp_remote_get( "https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=" . $g_key,
				array(
					'sslverify' => false,
					'timeout'   => 45
				)
			);

			// Check it is a valid request
			if ( ! is_wp_error( $respone_api ) ) {

				$json_string = version_compare( PHP_VERSION, '7.4', '>=' ) || get_magic_quotes_gpc() ? stripslashes( $respone_api['body'] ) : $respone_api['body']; // @codingStandardsIgnoreLine // phpcs:ignore
				$response_fonts = json_decode( $json_string, true );
			}
		}

		return $response_fonts;
	}

	public function is_valid_google_api_key( $cache=true ) {
		$is_valid = false;

		if ( ! $this->is_load_google_fonts ) {
			return false;
		}

		$this->google_api_key  = get_option( $this->google_api_key_option, '' );
		$google_api_key_enable = get_option( $this->google_api_key_option . '_enable', 0 );

		if ( '' != trim( $this->google_api_key ) && 1 == $google_api_key_enable ) {

			$google_api_key_status = get_transient( $this->google_api_key_option . '_status' );

			if ( ! $cache ) {
				$google_api_key_status = null;
			}

			if ( ! $google_api_key_status ) {

				$font_list = array();
				$response_fonts = $this->validate_google_api_key( $this->google_api_key );

				// Check it is a valid request
				if ( ! empty( $response_fonts ) ) {
					// Make sure that the valid response from google is not an error message
					if ( ! isset( $response_fonts['error'] ) ) {
						$google_api_key_status = 'valid';
					} else {
						$google_api_key_status = 'invalid';
					}
				} else {
					$google_api_key_status = 'invalid';
				}

				// Get font list from default webfonts.json file of plugin
				if ( 'invalid' == $google_api_key_status && file_exists( $this->admin_plugin_dir() . '/assets/webfonts/webfonts.json' ) ) {
					$response = wp_remote_get( $this->admin_plugin_url() . '/assets/webfonts/webfonts.json', array( 'timeout' => 120 ) );
					$webfonts = wp_remote_retrieve_body( $response );
					if ( ! empty( $webfonts ) ) {
						$json_string = version_compare( PHP_VERSION, '7.4', '>=' ) || get_magic_quotes_gpc() ? stripslashes( $webfonts ) : $webfonts; // @codingStandardsIgnoreLine // phpcs:ignore
						$response_fonts = json_decode( $json_string, true );
					}
				}

				// Saving the font list to database for get later
				if ( is_array( $response_fonts )
					&& isset( $response_fonts['items'] )
					&& is_array( $response_fonts['items'] )
					&& count( $response_fonts['items'] ) > 0 ) {

					foreach ( $response_fonts['items'] as $font_item ) {
						$variants = '';
						$comma = '';
						foreach ( $font_item['variants'] as $variant ) {
							$variants .= $comma . trim( $variant );
							$comma = ',';
						}
						if ( '' != trim( $variants ) ) {
							$variants = ':' . $variants;
						}

						$font_list[] = array( 'name' => trim( $font_item['family'] ), 'variant' => $variants );
					}
				}

				update_option( $this->plugin_name . '_google_font_list', $font_list );

				//caching google api status for 24 hours
				set_transient( $this->google_api_key_option . '_status', $google_api_key_status, 86400 );
			}

			if ( 'valid' == $google_api_key_status ) {
				$is_valid = true;
			}

		} else {

			$google_api_key_status = get_transient( $this->google_api_key_option . '_status' );

			if ( ! $cache ) {
				$google_api_key_status = null;
			}

			if ( ! $google_api_key_status ) {

				$font_list = array();
				$response_fonts = array();

				// Get font list from default webfonts.json file of plugin
				if ( file_exists( $this->admin_plugin_dir() . '/assets/webfonts/webfonts.json' ) ) {
					$response = wp_remote_get( $this->admin_plugin_url() . '/assets/webfonts/webfonts.json', array( 'timeout' => 120 ) );
					$webfonts = wp_remote_retrieve_body( $response );
					if ( ! empty( $webfonts ) ) {
						$json_string = version_compare( PHP_VERSION, '7.4', '>=' ) || get_magic_quotes_gpc() ? stripslashes( $webfonts ) : $webfonts; // @codingStandardsIgnoreLine // phpcs:ignore
						$response_fonts = json_decode( $json_string, true );
					}
				}

				// Saving the font list to database for get later
				if ( is_array( $response_fonts )
					&& isset( $response_fonts['items'] )
					&& is_array( $response_fonts['items'] )
					&& count( $response_fonts['items'] ) > 0 ) {

					foreach ( $response_fonts['items'] as $font_item ) {
						$variants = '';
						$comma = '';
						foreach ( $font_item['variants'] as $variant ) {
							$variants .= $comma . trim( $variant );
							$comma = ',';
						}
						if ( '' != trim( $variants ) ) {
							$variants = ':' . $variants;
						}

						$font_list[] = array( 'name' => trim( $font_item['family'] ), 'variant' => $variants );
					}
				}

				update_option( $this->plugin_name . '_google_font_list', $font_list );

				//caching google api status for 24 hours
				set_transient( $this->google_api_key_option . '_status', 'invalid', 86400 );

			}
		}

		return $is_valid;
	}

	/*-----------------------------------------------------------------------------------*/
	/* Get Window Default Fonts */
	/*-----------------------------------------------------------------------------------*/
	public function get_default_fonts() {
		$default_fonts = apply_filters( $this->plugin_name . '_default_fonts', $this->default_fonts );

		asort( $default_fonts );

		return $default_fonts;
	}

	/*-----------------------------------------------------------------------------------*/
	/* Get Google Fonts */
	/*-----------------------------------------------------------------------------------*/
	public function get_google_fonts() {

		return $this->google_fonts;
	}

	/*-----------------------------------------------------------------------------------*/
	/* generate_font_css() */
	/* Generate font CSS for frontend */
	/*-----------------------------------------------------------------------------------*/
	public function generate_font_css( $option, $em = '1.2' ) {
		$google_fonts = $this->get_google_fonts();

		if ( array_key_exists( $option['face'], $google_fonts ) ) {
			$option['face'] = "'" . $option['face'] . "', arial, sans-serif";
		}

		$line_height = '1.4em';
        if( isset( $option['line_height'] ) ){
            $line_height = $option['line_height'];
        }

		if ( !@$option['style'] && !@$option['size'] && !@$option['color'] )
			return 'font-family: '.stripslashes($option["face"]).' !important;';
		else
			return 'font:'.$option['style'].' '.$option['size'].'/' . $line_height . ' ' .stripslashes($option['face']).' !important; color:'.$option['color'].' !important;';
	}


	/*-----------------------------------------------------------------------------------*/
	/* Google Webfonts Stylesheet Generator */
	/*-----------------------------------------------------------------------------------*/
	/*
	INSTRUCTIONS: Needs to be loaded for the Google Fonts options to work for font options.

	add_action( 'wp_head', array( $this, 'generate_google_webfonts' ) );
	*/
	public function generate_google_webfonts( $my_google_fonts = array(), $echo = true ) {
		$google_fonts = $this->get_google_fonts();

		$fonts = '';
		$output = '';

		// Go through the options
		if ( is_array( $my_google_fonts ) ) {
			foreach ( $my_google_fonts as $font_face ) {
				// Check if the google font name exists in the current "face" option
				if ( array_key_exists( $font_face, $google_fonts ) && !strstr( $fonts, $font_face ) ) {
					$fonts .= $google_fonts[$font_face]['name'].$google_fonts[$font_face]['variant']."|";
				}
			} // End Foreach Loop

			// Output google font css in header
			if ( trim( $fonts ) != '' ) {
				$fonts = str_replace( " ","+",$fonts);
				$output .= "\n<!-- Google Webfonts -->\n";
				$output .= '<link href="http'. ( is_ssl() ? 's' : '' ) .'://fonts.googleapis.com/css?family=' . $fonts .'" rel="stylesheet" type="text/css" />'."\n";
				$output = str_replace( '|"','"',$output);
			}
		}

		if ( $echo )
			echo $output;
		else
			return $output;

	} // End generate_google_webfonts()

}

}
