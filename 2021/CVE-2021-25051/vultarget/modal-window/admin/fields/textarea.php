<?php
/**
 * Template for field textarea
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
$tooltip = ! empty( $arg['tooltip'] ) ? $arg['tooltip'] : '';

$control_classes = 'control';

$add_field_class = ! empty( $arg['attr']['class'] ) ? ' ' . $arg['attr']['class'] : '';
$field_classes   = 'textarea is-primary is-radiusless' . $add_field_class;

$attributes = '';
foreach ( $attr as $key => $val ) {
	if ( $key == 'class' || $key == 'value' ) {
		continue;
	}
	$attributes .= esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
}
?>

<?php if ( ! empty( $label ) ) : ?>
    <label class="label">
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
            <textarea
                    class="<?php echo esc_attr( $field_classes ); ?>" <?php echo $attributes; ?>><?php echo esc_attr( $attr['value'] ); ?></textarea>
        </div>

    </div>
<?php if ( ! empty( $help ) ) : ?>
    <p class="help is-info"><?php echo esc_attr( $help ); ?></p>
<?php endif; ?>