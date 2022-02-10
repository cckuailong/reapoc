<?php
/**
 * Modal button
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

<div class="columns">
	<div class="column is-one-third">
		<?php $this->select($umodal_button); ?>
	</div>
	<div class="column is-one-third show-button">
		<?php $this->select($button_type); ?>
	</div>
	<div class="column is-one-third show-button button-text">
		<?php $this->input($umodal_button_text); ?>
	</div>
</div>

<!--region Icon-->
<div class="accordion-wrap show-button button-icon">
	<div class="accordion-block">
		<div class="accordion-title">
			<span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
			<span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
			<span class="faq-title"><?php esc_attr_e( 'Icon', 'modal-window' ); ?></span>
		</div>
		<div class="accordion-content content">
            <div class="columns">
                <div class="column is-one-third">
	                <?php $this->select($button_icon); ?>
                </div>
                <div class="column is-one-third">
	                <?php $this->select($rotate_icon); ?>
                </div>
                <div class="column is-one-third button-text-icon">
	                <?php $this->select($button_icon_after); ?>
                </div>
                <div class="column is-one-third button-shape">
	                <?php $this->select($button_shape); ?>
                </div>
            </div>
		</div>
	</div>
</div>
<!--endregion-->

<!--region Location-->
<div class="accordion-wrap show-button">
    <div class="accordion-block">
        <div class="accordion-title">
            <span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
            <span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
            <span class="faq-title"><?php esc_attr_e( 'Location', 'modal-window' ); ?></span>
        </div>
        <div class="accordion-content content">
            <div class="columns is-multiline">
                <div class="column is-one-third">
					<?php $this->select($umodal_button_position); ?>
                </div>
                <div class="column is-one-third button-position">
	                <?php $this->number($button_position); ?>
                </div>
                <div class="column is-one-third button-margin">
	                <?php $this->number($button_margin); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--endregion-->

<!--region Style-->
<div class="accordion-wrap show-button">
	<div class="accordion-block">
		<div class="accordion-title">
			<span class="plus"><i class="dashicons dashicons-arrow-down-alt2"></i></span>
			<span class="minus"><i class="dashicons dashicons-arrow-up-alt2"></i></span>
			<span class="faq-title"><?php esc_attr_e( 'Style', 'modal-window' ); ?></span>
		</div>
		<div class="accordion-content content">
			<div class="columns is-multiline">
				<div class="column is-one-third">
					<?php $this->number($button_text_size); ?>
				</div>
				<div class="column is-one-third">
					<?php $this->input($button_padding); ?>
				</div>
				<div class="column is-one-third">
					<?php $this->number($button_radius); ?>
				</div>
				<div class="column is-half">
					<?php $this->color($button_text_color); ?>
				</div>
				<div class="column is-half">
					<?php $this->color($button_text_hcolor); ?>
				</div>
				<div class="column is-half">
					<?php $this->color($umodal_button_color); ?>
				</div>
				<div class="column is-half">
					<?php $this->color($umodal_button_hover); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!--endregion-->