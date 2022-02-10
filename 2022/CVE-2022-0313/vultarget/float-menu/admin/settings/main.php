<?php
/**
 * Main Settings
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once( 'options/main.php' );

?>
<fieldset class="itembox">
    <legend>
		<?php esc_attr_e( 'Main', $this->plugin['text'] ); ?>
    </legend>

    <div class="columns is-multiline">
        <div class="column is-4">
            <div class="field">
                <label class="label">
					<?php esc_attr_e( 'Position', $this->plugin['text'] ); ?><?php self::tooltip( $menu_help ); ?>
                </label>
				<?php self::option( $menu ); ?>
            </div>
        </div>
        <div class="column is-4">
            <div class="field">
                <label class="label">
				    <?php esc_attr_e( 'Side Space', $this->plugin['text'] ); ?><?php self::tooltip( $sideSpace_help ); ?>
                </label>
			    <?php self::option( $sideSpace ); ?>
            </div>
        </div>

        <div class="column is-4">
            <div class="field">
                <label class="label">
					<?php esc_attr_e( 'Button Space', $this->plugin['text'] ); ?><?php self::tooltip( $buttonSpace_help ); ?>
                </label>
				<?php self::option( $buttonSpace ); ?>
            </div>
        </div>
        <div class="column is-4">
            <div class="field">
                <label class="label">
					<?php esc_attr_e( 'Label On', $this->plugin['text'] ); ?><?php self::tooltip( $labelsOn_help ); ?>
                </label>
				<?php self::option( $labelsOn ); ?>
            </div>
        </div>
        <div class="column is-4">
            <div class="field">
                <label class="label">
					<?php esc_attr_e( 'Label Space', $this->plugin['text'] ); ?><?php self::tooltip( $labelSpace_help ); ?>
                </label>
				<?php self::option( $labelSpace ); ?>
            </div>
        </div>
        <div class="column is-4">
            <div class="field">
                <label class="label">
					<?php esc_attr_e( 'Label Connected', $this->plugin['text'] ); ?><?php self::tooltip( $labelConnected_help ); ?>
                </label>
				<?php self::option( $labelConnected ); ?>
            </div>
        </div>

        <div class="column is-4">
            <label class="label">
				<?php esc_attr_e( 'Label Speed (ms)', $this->plugin['text'] ); ?><?php self::tooltip( $labelSpeed_help ); ?>
            </label>
            <div class="field has-addons">
				<?php self::option( $labelSpeed ); ?>
                <div class="control">
                    <span class="addon">ms</span>
                </div>
            </div>
        </div>
        <div class="column is-4">
            <div class="field">
                <label class="label">
					<?php esc_attr_e( 'Z-index', $this->plugin['text'] ); ?>
                </label>
				<?php self::option( $z_index ); ?>
            </div>
        </div>
    </div>

</fieldset>
