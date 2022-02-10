<?php
/**
 * Targeting
 *
 * @package     Wow_Pluign
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once( 'settings.php' );

?>


<!--region Devices-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Devices Control', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-half">
					<?php $this->number($screen_more); ?>
                </div>
                <div class="column is-half">
	                <?php $this->number($screen); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->



<!--region Display-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Display on site', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns">
                <div class="column is-half">
					<?php $this->select($show); ?>
                </div>

            </div>
            <div class="columns">
                <div class="column is-full shortcode">
                    <label class="label"><?php esc_attr_e( 'Shortcode', 'modal-window' ); ?></label>
                    <code>[<?php echo $this->plugin['shortcode']; ?> id="<?php echo $tool_id; ?>"]</code>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->