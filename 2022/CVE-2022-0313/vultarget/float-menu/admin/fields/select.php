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

$disabled = isset( $arg['disabled'] ) ? ' disabled' : '';
$readonly = isset( $arg['readonly'] ) ? ' readonly' : '';
$id       = ( $id === null ) ? '' : ' id="' . $id . '"';
?>

<div class="control">
    <div class="select is-fullwidth">
        <select name="<?php echo esc_attr( $name ); ?>'"<?php echo wp_kses_post( $class . $disabled . $readonly . $id ); ?>>
			<?php
			foreach ( $options as $key => $value ) {
				if ( strrpos( $key, '_start' ) != false ) {
					echo '<optgroup label="' . esc_attr( $value ) . '">';
				} elseif ( strrpos( $key, '_end' ) != false ) {
					echo '</optgroup>';
				} else {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $val, false ) . '>' . esc_attr( $value ) . '</option>';
				}
			} ?>
        </select>
    </div>
</div>
