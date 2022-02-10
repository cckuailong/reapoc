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

$select = ! empty( $val ) ? ' checked="checked"' : '';
$id     = ( $id === null ) ? '' : ' id="' . esc_attr( $id ) . '"';

?>
<input type="checkbox" <?php echo wp_kses_post( $id . $select . $class ); ?>>
<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="">