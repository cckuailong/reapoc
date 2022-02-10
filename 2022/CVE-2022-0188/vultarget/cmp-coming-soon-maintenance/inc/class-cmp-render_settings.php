<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/**
 * Create custom Class for CMP render settings to avoid repeating fields
 *
 * @since 2.4
 */
if ( ! class_exists( 'cmp_render_settings' ) ) {
    class cmp_render_settings {

        /**
         * Settings Constructor.
         *
         * @since 2.4
         */
		function __construct() {

        }


        /**
         * Submit Settings Button.
         *
         * @since 2.4
         */
        public function submit( $value = false ) {

            $value = ( $value === false ) ? __('Save All Changes', 'cmp-coming-soon-maintenance') : $value;

            ob_start(); ?>

            <tr><th>
                <p class="cmp-submit">
                    <input type="submit" name="submit" class="button cmp-button submit" value="<?php echo esc_attr( $value ); ?>" form="csoptions"/>
                </p>
            </th></tr>

            <?php 

            return ob_get_clean();
        }

    }
}