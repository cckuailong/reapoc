<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_FRONTEND_TRACKING_MORE_FORM {
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_shortcode( 'vi_wot_tracking_more_form', array( $this, 'shortcode_init' ) );
	}

	public function shortcode_init() {
		global $wot_tracking_more_form;
		if ( $wot_tracking_more_form !== null ) {
			return '';
		} else {
			wp_enqueue_script( 'vi-wot-tracking-more-form', VI_WOO_ORDERS_TRACKING_JS . 'tracking-more-form.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
			$wot_tracking_more_form = true;
			ob_start();
			?>
            <div style="width: 100%;margin:0 auto;text-align:center;">
                <form role="form" action="//track.trackingmore.com" method="get" onsubmit="return false">
                    <div class="TM_input-group">
                        <input type="text" class="TM_my_search_input_style" id="button_tracking_number"
                               placeholder="Tracking Number" name="button_tracking_number" value="" autocomplete="off"
                               maxlength="100" style="border-color: #ff4a00;height: 46px">
                        <span class="TM_input-group-btn">
                            <button class="TM_my_search_button_style " id="query" type="button"
                                    onclick="return doTrack()"
                                    style="background-color: #ff4a00"><?php esc_html_e( 'Track', 'woo-orders-tracking' ) ?></button>
                        </span>
                    </div>
                    <input type="hidden" name="lang" value="en"/>
                    <input id="button_express_code" type="hidden" name="lang" value=""/>
                </form>
                <div id="TRNum"></div>
            </div>
			<?php
			return ob_get_clean();
		}
	}
}