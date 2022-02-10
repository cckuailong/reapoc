<?php
$post_status_selected = isset( $form_settings['edit_post_status'] ) ? $form_settings['edit_post_status'] : 'publish';
$redirect_to          = isset( $form_settings['edit_redirect_to'] ) ? $form_settings['edit_redirect_to'] : 'same';
$update_message       = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Post updated successfully', 'wp-user-frontend' );
$page_id              = isset( $form_settings['edit_page_id'] ) ? $form_settings['edit_page_id'] : 0;
$url                  = isset( $form_settings['edit_url'] ) ? $form_settings['edit_url'] : '';
$update_text          = isset( $form_settings['update_text'] ) ? $form_settings['update_text'] : __( 'Update', 'wp-user-frontend' );
$subscription         = isset( $form_settings['subscription'] ) ? $form_settings['subscription'] : null;
$lock_edit_post       = isset( $form_settings['lock_edit_post'] ) ? $form_settings['lock_edit_post'] : '';
?>
<table class="form-table">

    <tr class="wpuf-post-status">
        <th><?php esc_html_e( 'Set Post Status to', 'wp-user-frontend' ); ?></th>
        <td>
            <select name="wpuf_settings[edit_post_status]">
                <?php
                $statuses = get_post_statuses();

                foreach ( $statuses as $status => $label ) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $status ), selected( esc_attr( $post_status_selected ), $status, false ), esc_html( $label ) );
                }

                printf( '<option value="_nochange"%s>%s</option>', esc_attr( selected( $post_status_selected, '_nochange', false ) ), esc_html( __( 'No Change', 'wp-user-frontend' ) ) );
                ?>
            </select>
        </td>
    </tr>

    <tr class="wpuf-redirect-to">
        <th><?php esc_html_e( 'Redirect To', 'wp-user-frontend' ); ?></th>
        <td>
            <select name="wpuf_settings[edit_redirect_to]">
                <?php
                $redirect_options = [
                    'post' => __( 'Newly created post', 'wp-user-frontend' ),
                    'same' => __( 'Same Page', 'wp-user-frontend' ),
                    'page' => __( 'To a page', 'wp-user-frontend' ),
                    'url'  => __( 'To a custom URL', 'wp-user-frontend' ),
                ];

                foreach ( $redirect_options as $to => $label ) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $to ), esc_attr( selected( $redirect_to, $to, false ) ), esc_html( $label ) );
                }
                ?>
            </select>
            <p class="description">
                <?php esc_html_e( 'After successfull submit, where the page will redirect to', $domain = 'wp-user-frontend' ); ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-same-page">
        <th><?php esc_html_e( 'Post Update Message', 'wp-user-frontend' ); ?></th>
        <td>
            <textarea rows="3" cols="40" name="wpuf_settings[update_message]"><?php echo esc_textarea( $update_message ); ?></textarea>
        </td>
    </tr>

    <tr class="wpuf-page-id">
        <th><?php esc_html_e( 'Page', 'wp-user-frontend' ); ?></th>
        <td>
            <select name="wpuf_settings[edit_page_id]">
                <?php
                $pages = get_posts(  [ 'numberposts' => -1, 'post_type' => 'page'] );

                foreach ( $pages as $page ) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $page->ID ), esc_attr( selected( $page_id, $page->ID, false ) ), esc_attr( $page->post_title ) );
                }
                ?>
            </select>
        </td>
    </tr>

    <tr class="wpuf-url">
        <th><?php esc_html_e( 'Custom URL', 'wp-user-frontend' ); ?></th>
        <td>
            <input type="url" name="wpuf_settings[edit_url]" value="<?php echo esc_attr( $url ); ?>">
        </td>
    </tr>

    <tr class="wpuf-subscription-pack" style="display: none;">
        <th><?php esc_html_e( 'Subscription Title', 'wp-user-frontend' ); ?></th>
        <td>
            <select id="wpuf-subscription-list" name="wpuf_settings[subscription]">
                <?php $this->subscription_dropdown( $subscription ); ?>
            </select>
        </td>
    </tr>

    <tr class="wpuf-update-text">
        <th><?php esc_html_e( 'Update Post Button text', 'wp-user-frontend' ); ?></th>
        <td>
            <input type="text" name="wpuf_settings[update_text]" value="<?php echo esc_attr( $update_text ); ?>">
        </td>
    </tr>

    <tr class="lock-edit-post">
        <th><?php esc_html_e( 'Lock User From Editing After', 'wp-user-frontend' ); ?></th>
        <td>
            <input type="number" name="wpuf_settings[lock_edit_post]" id="lock_edit_post" value="<?php echo esc_attr( $lock_edit_post ); ?>">
            <span><?php esc_html_e( 'hours', 'wp-user-frontend' ); ?></span>
            <p class="description">
                <?php esc_html_e( 'After how many hours user will be locked from editing the submitted post.', 'wp-user-frontend' ); ?>
            </p>
        </td>
    </tr>
</table>
