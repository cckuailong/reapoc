<?php
/**
 * Notification settings
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once( 'settings.php' );

?>

<!--region Triggers-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Triggers', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-one-third">
					<?php $this->select( $modal_show ); ?>
                </div>
                <div class="column is-one-third">
					<?php $this->number( $modal_timer ); ?>
                </div>
                <div class="column is-one-third scrolled">
					<?php $this->number( $reach_window ); ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
		            <?php $this->select( $use_cookies ); ?>
                </div>
                <div class="column is-one-third cookie">
		            <?php $this->number( $modal_cookies ); ?>
                </div>
            </div>
	        <p class="has-text-danger has-text-weight-bold"><?php _e( 'You can open popup via adding to the element:', 'modal-window' ); ?></p>
            <ul>
                <li><strong>Class</strong> - wow-modal-id-<?php echo $tool_id; ?>, like <code>&lt;span
                        class="wow-modal-id-<?php echo $tool_id; ?>"&gt;Open Popup&lt;/span&gt;</code></li>
                <li><strong>ID</strong> - wow-modal-id-<?php echo $tool_id; ?>, like <code>&lt;span
                        id="wow-modal-id-<?php echo $tool_id; ?>"&gt;Open Popup&lt;/span&gt;</code></li>
                <li><strong>URL</strong> - #wow-modal-id-<?php echo $tool_id; ?>, like <code>&lt;a
                        href="#wow-modal-id-<?php echo $tool_id; ?>">Open Popup&lt;/a&gt;</code></li>
            </ul>
        </div>
    </div>
</div>
<!--endregion-->

<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Closing modal window', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">

                <div class="column is-one-third">
	                <?php $this->checkbox($close_button_overlay); ?>
                </div>
                <div class="column is-one-third">
	                <?php $this->checkbox($close_button_esc); ?>
                </div>
            </div>

            <p class="has-text-danger has-text-weight-bold"><?php _e( 'You can сlose popup via adding to the element:', 'modal-window' ); ?></p>
            <ul>
                <li><strong>Class</strong> - wow-modal-close-<?php echo $tool_id; ?>, like <code>&lt;span
                        class="wow-modal-close-<?php echo $tool_id; ?>"&gt;Close Popup&lt;/span&gt;</code></li>
                <li><strong>ID</strong> - wow-modal-close-<?php echo $tool_id; ?>, like <code>&lt;span
                        id="wow-modal-close-<?php echo $tool_id; ?>"&gt;Close Popup&lt;/span&gt;</code></li>
                <li><strong>URL</strong> - #wow-modal-close-<?php echo $tool_id; ?>, like <code>&lt;a
                        href="#wow-modal-close-<?php echo $tool_id; ?>">Close Popup&lt;/a&gt;</code></li>
            </ul>
        </div>
    </div>
</div>
