<?php
/**
 * Live preview block
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="live-preview">
	<h3><span class="dashicons dashicons-admin-customizer"></span>
		<?php esc_html_e( 'Live Preview', 'modal-window' ); ?>
	</h3>
	<div class="toggle-preview">
		<span class="plus is-hidden"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
		<span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
	</div>
	<div class="live-builder">
		<div id="builder">

            <div id="wow-modal-overlay" class="wow-modal-overlay">
                <div class="wow-modal-window">
                    <div class="mw-close-btn"></div>
                    <div class="modal-window-content">
                    </div>
                </div>
            </div>

		</div>
	</div>
</div>
