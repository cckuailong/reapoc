<?php
/**
 * Style settings
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

<!--region General-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'General', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-half">
					<?php $this->number( $modal_width ); ?>
                </div>
                <div class="column is-half">
					<?php $this->number( $modal_height ); ?>
                </div>
                <div class="column is-half">
					<?php $this->number( $modal_zindex ); ?>
                </div>
                <div class="column is-half">
					<?php $this->select( $modal_position ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Location-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Location', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-half">
					<?php $this->number( $modal_top ); ?>
                </div>
                <div class="column is-half">
					<?php $this->number( $modal_bottom ); ?>
                </div>
                <div class="column is-half">
					<?php $this->number( $modal_left ); ?>
                </div>
                <div class="column is-half">
					<?php $this->number( $modal_right ); ?>
                </div>
                <div class="column is-full">
                    <p class="is-size-6"><span class="has-text-danger">Notice!</span> If you want to align the modal window horizontally, set values:</p>
                    <ul>
                        <li>Left = 0%</li>
                        <li>Right = 0%</li>
                    </ul>
                    <p class="is-size-6"> If you want to place the modal window in the center of the screen:</p>
                    <ol>
                        <li>Set the option 'Modal Height' in px</li>
                        <li>Set the location options 'Top', 'Bottom', 'Left', 'Right' = 0% </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Background-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Background', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-one-third">
					<?php $this->color( $overlay_color ); ?>
                </div>
                <div class="column is-one-third">
					<?php $this->color( $bg_color ); ?>
                </div>

            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Title-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Title', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
	        <?php $this->checkbox($popup_title);?>
            <div class="columns is-multiline popup-title">
                <div class="column is-one-third">
	                <?php $this->number( $title_size ); ?>
                </div>
                <div class="column is-one-third">
	                <?php $this->number( $title_line_height ); ?>
                </div>
                <div class="column is-one-third">
	                <?php $this->select( $title_align ); ?>
                </div>
                <div class="column is-one-third">
	                <?php $this->color( $title_color ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Content-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Content', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-half">
		            <?php $this->number( $modal_padding ); ?>
                </div>
                <div class="column is-half">
	                <?php $this->number( $content_size ); ?>
                </div>

            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Close Button-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Close Button', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-one-third">
					<?php $this->select($close_location); ?>
                </div>
                <div class="column is-one-third" id="close-top">
	                <?php $this->number($close_top_position); ?>
                </div>
                <div class="column is-one-third" id="close-bottom">
	                <?php $this->number($close_bottom_position); ?>
                </div>
                <div class="column is-one-third" id="close-left">
		            <?php $this->number($close_left_position); ?>
                </div>
                <div class="column is-one-third" id="close-right">
		            <?php $this->number($close_right_position); ?>
                </div>
            </div>
            <div class="columns is-multiline">
                <div class="column is-one-third">
	                <?php $this->select($close_type); ?>
                </div>
                <div class="column is-one-third close-text">
		            <?php $this->input($close_content); ?>
                </div>
                <div class="column is-one-third close-text">
		            <?php $this->input($close_padding); ?>
                </div>
                <div class="column is-one-third close-icon">
		            <?php $this->number($close_box_size); ?>
                </div>
            </div>
            <div class="columns is-multiline">
                <div class="column is-one-third">
	                <?php $this->number($close_size); ?>
                </div>

                <div class="column is-one-third">
	                <?php $this->number($close_border_radius); ?>
                </div>



            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Border-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Border', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-half">
					<?php $this->number( $border_radius ); ?>
                </div>
                <div class="column is-half">
					<?php $this->select( $border_style ); ?>
                </div>
                <div class="column is-half">
					<?php $this->number( $border_width ); ?>
                </div>
                <div class="column is-half">
					<?php $this->color( $border_color ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Mobile Rule-->
<div class="accordion-wrap">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Mobile Rule', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-half">
					<?php $this->number($screen_size); ?>
                </div>
                <div class="column is-half">
	                <?php $this->number($mobile_width); ?>
                </div>

            </div>
        </div>
    </div>
</div>
<!--endregion-->
