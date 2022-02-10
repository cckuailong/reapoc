    <?php

/**
 * Tools page
 *
 * @package     Wow_Plugin
 * @subpackage  Admin/Main_page
 * @author      Wow-Company <helper@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

    <div class="wrap">
        <div class="postbox">
            <div class="inside">
                <h3><span><?php esc_attr_e( 'Export Settings', 'modal-window' ); ?></span></h3>

                <p><?php
					printf( esc_attr__( 'Export the  settings for %s as a .json file. This allows you to easily import the configuration into another site.', 'modal-window' ), '<b>' . esc_attr( $this->plugin['name'] ) . '</b>' );; ?></p>
                <form method="post" action="">
                    <p><input type="hidden" name="wow_action" value="export_database"/></p>
                    <p>
						<?php wp_nonce_field( $this->plugin['slug'] . '_export_nonce', $this->plugin['slug'] . '_export_nonce' ); ?><?php submit_button( __( 'Export', 'modal-window' ), 'secondary', 'submit', false ); ?>
                    </p>
                </form>
            </div>
        </div>

        <div class="postbox">
            <div class="inside">
                <h3><span><?php esc_attr_e( 'Import Settings', 'modal-window' ); ?></span></h3>

                <p><?php
					printf( esc_attr__( 'Import the %s settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'modal-window' ), '<b>' . esc_attr( $this->plugin['name'] ) . '</b>    ' );; ?></p>
                <form method="post" enctype="multipart/form-data" action="">
                    <p>
                        <input type="file" name="import_file"/>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="wow_import_update" value="1">
							<?php esc_attr_e( 'Update item if item already exists.' ); ?>
                        </label>

                    </p>

                    <p>
                        <input type="hidden" name="wow_action" value="import_database"/>
						<?php wp_nonce_field( $this->plugin['slug'] . '_import_nonce', $this->plugin['slug'] . '_import_nonce' ); ?>
						<?php submit_button( __( 'Import', 'modal-window' ), 'secondary', 'submit', false ); ?>
                    </p>
                </form>
            </div>
        </div>


    </div>

<?php


