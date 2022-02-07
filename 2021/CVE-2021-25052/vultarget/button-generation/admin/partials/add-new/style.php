<?php
/**
 * Button style settings
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

include_once( 'settings/style.php' );

?>

	<div class="container">
		<div class="element">
			<label><?php esc_html_e( 'Width', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $width_help ); ?>
			<br/>
		<?php echo self::option( $width ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'Height', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $height_help ); ?>
			<br/>
		<?php echo self::option( $height ); ?>

		</div>
		<div class="element">
			<label><?php esc_html_e( 'Z-index',
			  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $zindex_help ); ?><br/>
		<?php echo self::option( $zindex ); ?>
		</div>
	</div>

	<div class="container">
		<div class="element">
			<label><?php esc_html_e( 'Color', $this->plugin['text'] ); ?></label><?php echo self::tooltip( $color_help ); ?>
			<br/>
		<?php echo self::option( $color ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'Background',
			  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $background_help ); ?>
			<br/>
		<?php echo self::option( $background ); ?>
		</div>
		<div class="element">
		</div>
	</div>

	<div class="container">
		<div class="element">
			<label><?php esc_html_e( 'Hover Color',
			  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $hover_color_help ); ?>
			<br/>
		<?php echo self::option( $hover_color ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'Hover Background',
			  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $hover_background_help ); ?><br/>
		<?php echo self::option( $hover_background ); ?>
		</div>
		<div class="element">
			<label><?php esc_html_e( 'Hover Effect',
			  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $hover_effects_help ); ?>
		<?php echo self::pro(); ?><br/>
		<?php echo self::option( $hover_effects ); ?>
		</div>
	</div>

	<fieldset>
		<legend><?php esc_html_e( 'Border', $this->plugin['text'] ); ?></legend>

		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Radius',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $border_radius_help ); ?>
				<br/>
		  <?php echo self::option( $border_radius ); ?>

			</div>
			<div class="element">
				<label><?php esc_html_e( 'Style',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $border_style_help ); ?>
				<br/>
		  <?php echo self::option( $border_style ); ?>
			</div>
			<div class="element">
				<label></label><br/>

			</div>
		</div>

		<div class="container">
			<div class="element border">
				<label><?php esc_html_e( 'Color', $this->plugin['text'] ); ?></label><br/>
		  <?php echo self::option( $border_color ); ?>
			</div>
			<div class="element border">
				<label><?php esc_html_e( 'Thickness', $this->plugin['text'] ); ?></label><br/>
		  <?php echo self::option( $border_width ); ?>
			</div>
			<div class="element border">

			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend><?php esc_html_e( 'Drop Shadow', $this->plugin['text'] ); ?></legend>
		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Shadow',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $shadow_help ); ?><br/>
		  <?php echo self::option( $shadow ); ?>
			</div>
			<div class="element shadow">
				<label><?php esc_html_e( 'Horizontal Position',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $shadow_h_offset_help ); ?><br/>
		  <?php echo self::option( $shadow_h_offset ); ?>
			</div>
			<div class="element shadow">
				<label><?php esc_html_e( 'Vertical Position',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $shadow_v_offset_help ); ?><br/>
		  <?php echo self::option( $shadow_v_offset ); ?>
			</div>
		</div>

		<div class="shadow-block">
			<div class="container">
				<div class="element">
					<label><?php esc_html_e( 'Blur',
				  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $shadow_blur_help ); ?>
					<br/>
			<?php echo self::option( $shadow_blur ); ?>
				</div>
				<div class="element">
					<label><?php esc_html_e( 'Spread',
				  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $shadow_spread_help ); ?><br/>
			<?php echo self::option( $shadow_spread ); ?>
				</div>
				<div class="element">
					<label><?php esc_html_e( 'Color',
				  $this->plugin['text'] ); ?></label><?php echo self::tooltip( $shadow_color_help ); ?>
					<br/>
			<?php echo self::option( $shadow_color ); ?>
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset id="lg-popup-title">
		<legend><?php esc_html_e( 'Font', $this->plugin['text'] ); ?></legend>

		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Font Size',
				$this->plugin['text'] ); ?></label><?php echo self::tooltip( $font_size_help ); ?>
				<br/>
		  <?php echo self::option( $font_size ); ?>
			</div>
			<div class="element">
				<label><?php esc_html_e( 'Font Family', $this->plugin['text'] ); ?></label><br/>
		  <?php echo self::option( $font_family ); ?>
			</div>
			<div class="element">
			</div>
		</div>

		<div class="container">
			<div class="element">
				<label><?php esc_html_e( 'Font Weight', $this->plugin['text'] ); ?></label><br/>
		  <?php echo self::option( $font_weight ); ?>
			</div>
			<div class="element">
				<label><?php esc_html_e( 'Font Style', $this->plugin['text'] ); ?></label><br/>
		  <?php echo self::option( $font_style ); ?>
			</div>
			<div class="element">
			</div>
		</div>

	</fieldset>
<?php
