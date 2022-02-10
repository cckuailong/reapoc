<?php
/**
 * Template for field select
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label   = ! empty( $arg['label'] ) ? $arg['label'] : '';
$attr    = ! empty( $arg['attr'] ) ? $arg['attr'] : '';
$help    = ! empty( $arg['help'] ) ? $arg['help'] : '';
$icon    = ! empty( $arg['icon'] ) ? $arg['icon'] : '';
$value   = ! empty( $arg['attr']['value'] ) ? $arg['attr']['value'] : '';
$tooltip = ! empty( $arg['tooltip'] ) ? $arg['tooltip'] : '';

$check_id       = '';
$checkbox_class = '';
$checkbox       = '';
if ( ! empty( $arg['checkbox'] ) ) {
	$checkbox_class = ' checkbox';
	$check_name     = $arg['checkbox']['name'];
	$check_val      = $arg['checkbox']['value'];
	$check_id       = $arg['checkbox']['id'];
	$disabled       = isset( $arg['checkbox']['disabled'] ) ? ' disabled="disabled"' : '';
	$check_class    = isset( $arg['checkbox']['class'] ) ? ' ' . $arg['checkbox']['class'] : '';
	$cheched        = ! empty( $arg['checkbox']['value'] ) ? ' checked="checked"' : '';
	$checkbox       = '<input type="hidden" name="' . esc_attr( $check_name ) . '" value="">';
	$checkbox       .= '<input type="checkbox" class="is-radiusless' . esc_attr( $check_class ) . '" id="' . esc_attr( $check_id ) . '" name="' . esc_attr( $check_name ) . '" value="1"' . $cheched . $disabled . '>';
}

$add_control_class = ! empty( $icon ) ? ' has-icons-left' : '';
$control_classes   = 'control' . $add_control_class;

$add_field_class = ! empty( $arg['attr']['class'] ) ? ' ' . $arg['attr']['class'] : '';
$field_classes   = 'is-radiusless' . $add_field_class;

$attributes = '';
foreach ( $attr as $key => $val ) {
	if ( $key == 'class' || $key == 'value' ) {
		continue;
	}
	$attributes .= esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
}

if ( ! empty( $arg['func'] ) ) {
	$attributes .= 'onchange="' . esc_attr( $arg['func'] ) . ';"';
}

$option = '';
foreach ( $arg['options'] as $key => $val ) {
	$selected = ( $value == $key ) ? 'selected="selected"' : '';
	$option   .= '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_attr( $val ) . '</option>';
}

?>

<?php if ( ! empty( $label ) ) : ?>
    <label class="label<?php echo esc_attr( $checkbox_class ); ?>" for="<?php echo esc_attr( $check_id ); ?>">
		<?php echo $checkbox; ?>
		<?php echo esc_attr( $label ); ?>
		<?php if ( ! empty( $tooltip ) ) : ?>
            <span class="is-primary has-tooltip-multiline has-tooltip-right"
                  data-tooltip="<?php echo esc_attr( $tooltip ); ?>">
                <span class="wow-help dashicons dashicons-editor-help"></span>
            </span>
		<?php endif; ?>
    </label>
<?php endif; ?>
    <div class="field">
        <div class="<?php echo esc_attr( $control_classes ); ?>">
            <div class="select is-primary is-fullwidth">
                <select class="<?php echo esc_attr( $field_classes ); ?>" <?php echo $attributes; ?>>
					<?php echo $option; ?>
                </select>
            </div>
			<?php if ( ! empty( $icon ) ) : ?>
                <span class="icon is-small is-left">
	      <i class="<?php echo esc_attr( $icon ); ?>"></i>
	    </span>
			<?php endif; ?>
        </div>
    </div>
<?php if ( ! empty( $help ) ) : ?>
    <p class="help is-info"><?php echo esc_attr( $help ); ?></p>
<?php endif; ?>