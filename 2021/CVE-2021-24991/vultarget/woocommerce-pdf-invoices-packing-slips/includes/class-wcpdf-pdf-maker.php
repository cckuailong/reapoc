<?php
namespace WPO\WC\PDF_Invoices;

use Dompdf\Dompdf;
use Dompdf\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\PDF_Maker' ) ) :

class PDF_Maker {
	public $html;
	public $settings;

	public function __construct( $html, $settings = array() ) {
		$this->html = $html;

		$default_settings = array(
			'paper_size'		=> 'A4',
			'paper_orientation'	=> 'portrait',
			'font_subsetting'	=> false,
		);
		$this->settings = $settings + $default_settings;
	}

	public function output() {
		if ( empty( $this->html ) ) {
			return;
		}
		
		require WPO_WCPDF()->plugin_path() . '/vendor/autoload.php';

		// set options
		$options = new Options( apply_filters( 'wpo_wcpdf_dompdf_options', array(
			'tempDir'					=> WPO_WCPDF()->main->get_tmp_path('dompdf'),
			'fontDir'					=> WPO_WCPDF()->main->get_tmp_path('fonts'),
			'fontCache'					=> WPO_WCPDF()->main->get_tmp_path('fonts'),
			'chroot'					=> $this->get_chroot_paths(),
			'logOutputFile'				=> WPO_WCPDF()->main->get_tmp_path('dompdf') . "/log.htm",
			'defaultFont'				=> 'dejavu sans',
			'isRemoteEnabled'			=> true,
			// HTML5 parser requires iconv
			'isHtml5ParserEnabled'		=> ( isset(WPO_WCPDF()->settings->debug_settings['use_html5_parser']) && extension_loaded('iconv') ) ? true : false,
			'isFontSubsettingEnabled'	=> $this->settings['font_subsetting'],
		) ) );

		// instantiate and use the dompdf class
		$dompdf = new Dompdf( $options );
		$dompdf->loadHtml( $this->html );
		$dompdf->setPaper( $this->settings['paper_size'], $this->settings['paper_orientation'] );
		$dompdf = apply_filters( 'wpo_wcpdf_before_dompdf_render', $dompdf, $this->html );
		$dompdf->render();
		$dompdf = apply_filters( 'wpo_wcpdf_after_dompdf_render', $dompdf, $this->html );

		return $dompdf->output();
	}

	private function get_chroot_paths() {
		$chroot = array( WP_CONTENT_DIR ); // default

		if( $wp_upload_base = WPO_WCPDF()->main->get_wp_upload_base() ) {
			$chroot[] = $wp_upload_base;
		}
		if( $tmp_base = WPO_WCPDF()->main->get_tmp_base() ) {
			$chroot[] = $tmp_base;
		}

		return apply_filters( 'wpo_wcpdf_dompdf_chroot', $chroot );
	}
}

endif; // class_exists
