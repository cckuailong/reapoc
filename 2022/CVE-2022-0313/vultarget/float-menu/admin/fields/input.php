<?php
/**
 * Template for field input
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$option = '';
if ( is_array( $options ) ) {
	foreach ( $options as $key => $value ) {
		$option .= ' ' . $key . '="' . $value . '"';
	}
}
$id = ( $id === null ) ? '' : ' id="' . $id . '"';
?>
<div class="control is-expanded">
    <input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>"
           value="<?php echo esc_attr( $val ); ?>" <?php echo wp_kses_post( $id . $func . $option . $class ); ?>>
</div>