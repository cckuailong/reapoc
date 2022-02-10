<?php

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Compile_Less_Sass {

	public function __construct(){
		$this->init();
	}
	public function init(){
	}

	public function compileLessFile( $less_file = '', $css_file = '', $css_min_file = '' ){
		global $wp_filesystem;

		if( empty( $less_file ) )
			$less_file      = dirname( __FILE__ ) . '/assets/css/style.less';
		if( empty( $css_file ) )
			$css_file       = dirname( __FILE__ ) . '/assets/css/style.css';
		if( empty( $css_min_file ) )
			$css_min_file       = dirname( __FILE__ ) . '/assets/css/style.min.css';

		// Write less file
    	if ( is_writable( $css_file ) && is_writable( $css_min_file ) ) {

			if ( ! class_exists( 'a3_lessc' ) ){
				include( dirname( __FILE__ ) . '/lib/lessc.inc.php' );
			}
			if ( ! class_exists( 'a3_CSSmin' ) ){
				include( dirname( __FILE__ ) . '/lib/cssmin.inc.php' );
			}

			try {

				$less         = new a3_lessc();

				$compiled_css = $less->compileFile( $less_file );

				if ( $compiled_css != '' ){
					$wp_filesystem->put_contents( $css_file, $compiled_css );

					$compressor = new a3_CSSmin();
					$compressor->set_memory_limit( '512M' );
					$compressor->set_max_execution_time( 120 );

					$compiled_css_min = $compressor->run( $compiled_css );
					if ( $compiled_css_min != '' )
						$wp_filesystem->put_contents( $css_min_file, $compiled_css_min );
				}

			} catch ( exception $ex ) {

				//echo ( __( 'Could not compile .less:', 'sass' ) . ' ' . $ex->getMessage() );
			}
		}

	}
}
