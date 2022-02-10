<?php
/**
 * Template for field color
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = ( $id === null ) ? '' : ' id="' . $id . '"';
?>
    <input type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>"
           class="wp-color-picker-field color-picker" data-alpha-enabled="true"<?php echo wp_kses_post( $id ); ?>>
<?php echo '<' . esc_attr( $separator ) . '/>'; ?>