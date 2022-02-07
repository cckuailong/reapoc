<?php
/**
 * Settings
 *
 * @package     Wow_Plugin
 * @subpackage  Settings/
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'settings/settings.php' );

?>


	<div class="container">
		<div class="element">
			<label><?php esc_html_e( 'Type', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $type_help ); ?><br/>
			<?php echo self::option( $type ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'Button appearance',
					$this->plugin['text'] ); ?></label><?php echo self::tooltip( $appearance_help ); ?><br/>
			<?php echo self::option( $appearance ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'Rotate button', $this->plugin['text'] ); ?></label><br/>
			<?php echo self::option( $rotate_button ); ?>
		</div>
	</div>

	<div class="button-text">
		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Text', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $text_help ); ?><br/>
				<?php echo self::option( $text ); ?>
			</div>
			<div class="element text-location">
				<label><?php esc_html_e( 'Text location',
						$this->plugin['text'] ); ?></label><?php echo self::tooltip( $text_location_help ); ?><br/>
				<?php echo self::option( $text_location ); ?>
			</div>
			<div class="element">
			</div>
		</div>
	</div>

	<div class="button-icon">
		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Icon', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $icon_help ); ?><br/>
				<?php echo self::option( $icon ); ?>
			</div>
			<div class="element">
				<label><?php esc_html_e( 'Rotate icon', $this->plugin['text'] ); ?></label><br/>
				<?php echo self::option( $rotate_icon ); ?>
			</div>
			<div class="element">
			</div>
		</div>
	</div>

	<div class="container">
		<div class="element">
			<?php esc_html_e( 'Item type', $this->plugin['text'] ); ?> <?php echo self::tooltip( $item_type_help ); ?> <?php echo self::pro(); ?> <br/>
			<?php echo self::option( $item_type ); ?>
		</div>
		<div class="element type-param">
			<div class="type-link">
				<span class="type-link-text">Link</span>
				<br/>
				<?php echo self::option( $item_link ); ?>
			</div>
		</div>
		<div class="element type-link-blank">
			<input type="checkbox" disabled="disabled">
			<?php _e( 'Open in new window', $this->plugin['text'] ); ?>
			<?php echo self::pro(); ?>
		</div>
	</div>

	<div class="container">
		<div class="element">
			<label><?php esc_html_e( 'Class', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $button_class_help ); ?>
			<br/>
			<?php echo self::option( $button_class ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'ID', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $button_id_help ); ?><br/>
			<?php echo self::option( $button_id ); ?>
		</div>
		<div class="element">
		</div>
	</div>

	<fieldset class="button-floating">
		<legend><?php esc_html_e( 'Location', $this->plugin['text'] ); ?></legend>
		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Location', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $location_help ); ?>
				<br/>
				<?php echo self::option( $location ); ?>
			</div>
			<div class="element top-bottom">
				<div id="lg-top">
					<label><?php esc_html_e( 'Top', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $location_top_help ); ?>
					<br/>
					<?php echo self::option( $location_top ); ?>
				</div>
				<div id="lg-bottom">
					<label><?php esc_html_e( 'Bottom',
							$this->plugin['text'] ); ?></label><?php echo self::tooltip( $location_bottom_help ); ?><br/>
					<?php echo self::option( $location_bottom ); ?>
				</div>
			</div>
			<div class="element left-right">
				<div id="lg-left">
					<label><?php esc_html_e( 'Left',
							$this->plugin['text'] ); ?></label><?php echo self::tooltip( $location_left_help ); ?>
					<br/>
					<?php echo self::option( $location_left ); ?>
				</div>
				<div id="lg-right">
					<label><?php esc_html_e( 'Right',
							$this->plugin['text'] ); ?></label><?php echo self::tooltip( $location_right_help ); ?><br/>
					<?php echo self::option( $location_right ); ?>
				</div>
			</div>
		</div>
	</fieldset>
<?php
