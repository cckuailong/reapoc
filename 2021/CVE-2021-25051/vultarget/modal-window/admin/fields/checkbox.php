<?php
/**
 * Template for field checkbox
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
$tooltip = ! empty( $arg['tooltip'] ) ? $arg['tooltip'] : '';

$add_field_class = ! empty( $arg['attr']['class'] ) ? ' ' . $arg['attr']['class'] : '';
$field_classes   = 'is-radiusless ' . $add_field_class;

$attributes = '';
foreach ( $attr as $key => $val ) {
	if ( $key == 'class' || $key == 'value' ) {
		continue;
	}
	$attributes .= esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
}

if ( ! empty( $arg['func'] ) ) {
	$attributes .= 'onclick="' . esc_attr( $arg['func'] ) . '();"';
}

$checked = ! empty( $attr['value'] ) ? ' checked="checked"' : '';

?>

    <div class="field">
        <label class="label checkbox" for="<?php echo esc_attr( $attr['id'] ); ?>">
            <input type="hidden" name="<?php echo esc_attr( $attr['name'] ); ?>">
            <input class="<?php echo esc_attr( $field_classes ); ?>"
                   type="checkbox" <?php echo $attributes . $checked; ?> value="1">
			<?php echo esc_attr( $label ); ?>
			<?php if ( ! empty( $tooltip ) ) : ?>
                <span class="is-primary has-tooltip-multiline has-tooltip-right"
                      data-tooltip="<?php echo esc_attr( $tooltip ); ?>">
                    <span class="wow-help dashicons dashicons-editor-help"></span>
                </span>
			<?php endif; ?>
        </label>
    </div>
<?php if ( ! empty( $help ) ) : ?>
    <p class="help is-info"><?php echo $help; ?></p>
<?php endif; ?>