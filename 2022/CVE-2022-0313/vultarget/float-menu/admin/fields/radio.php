<?php
/**
 * Template for field radio
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
echo '<div class="control">';
foreach ( $options as $key => $value ) : ?>
    <label class="radio">
        <input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $key ); ?>"
               id="<?php echo esc_attr( $id ); ?>_<?php echo esc_attr( $key ); ?>"<?php checked( ( $key == $val ) ); ?><?php echo wp_kses_post( $func . $class ); ?>>
		<?php echo esc_html( $value ); ?>
    </label>
<?php endforeach;
echo '</div>';
