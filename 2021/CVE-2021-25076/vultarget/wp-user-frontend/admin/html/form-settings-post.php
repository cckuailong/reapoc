<?php
global $post;

$form_settings = wpuf_get_form_settings( $post->ID );

$post_status_selected  = isset( $form_settings['post_status'] ) ? $form_settings['post_status'] : 'publish';
$restrict_message      = __( 'This page is restricted. Please %login% / %register% to view this page.', 'wp-user-frontend' );

$post_type_selected    = isset( $form_settings['post_type'] ) ? $form_settings['post_type'] : 'post';

$post_format_selected  = isset( $form_settings['post_format'] ) ? $form_settings['post_format'] : 0;
$default_cat           = !empty( $form_settings['default_cat'] ) ? $form_settings['default_cat'] : [];

$redirect_to           = isset( $form_settings['redirect_to'] ) ? $form_settings['redirect_to'] : 'post';
$message               = isset( $form_settings['message'] ) ? $form_settings['message'] : __( 'Post saved', 'wp-user-frontend' );
$update_message        = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Post updated successfully', 'wp-user-frontend' );
$page_id               = isset( $form_settings['page_id'] ) ? $form_settings['page_id'] : 0;
$url                   = isset( $form_settings['url'] ) ? $form_settings['url'] : '';
$comment_status        = isset( $form_settings['comment_status'] ) ? $form_settings['comment_status'] : 'open';

$submit_text           = isset( $form_settings['submit_text'] ) ? $form_settings['submit_text'] : __( 'Submit', 'wp-user-frontend' );
$draft_text            = isset( $form_settings['draft_text'] ) ? $form_settings['draft_text'] : __( 'Save Draft', 'wp-user-frontend' );
$preview_text          = isset( $form_settings['preview_text'] ) ? $form_settings['preview_text'] : __( 'Preview', 'wp-user-frontend' );
$draft_post            = isset( $form_settings['draft_post'] ) ? $form_settings['draft_post'] : 'false';
?>
    <table class="form-table">

        <tr class="wpuf-post-type">
            <th><?php esc_html_e( 'Post Type', 'wp-user-frontend' ); ?></th>
            <td>
                <select name="wpuf_settings[post_type]" id="wpuf_settings_posttype">
                    <?php
                    $post_types = get_post_types();
                    unset( $post_types['attachment'] );
                    unset( $post_types['revision'] );
                    unset( $post_types['nav_menu_item'] );
                    unset( $post_types['wpuf_forms'] );
                    unset( $post_types['wpuf_profile'] );
                    unset( $post_types['wpuf_input'] );
                    unset( $post_types['wpuf_subscription'] );
                    unset( $post_types['custom_css'] );
                    unset( $post_types['customize_changeset'] );
                    unset( $post_types['wpuf_coupon'] );
                    unset( $post_types['oembed_cache'] );

                    foreach ( $post_types as $post_type ) {
                        printf( '<option value="%s"%s>%s</option>', esc_attr( $post_type ), esc_attr( selected( $post_type_selected, $post_type, false ) ), esc_html( $post_type ) );
                    }
                    ?>
                </select>
                <p class="description"><?php esc_html_e( 'Custom Post Type will appear here. ', 'wp-user-frontend' ); ?><a target="_blank" href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/different-custom-post-type-submission-2/"><?php esc_html_e( 'Learn More ', 'wp-user-frontend' ); ?></a></p>
            </td>
        </tr>

        <tr class="wpuf-post-status">
            <th><?php esc_html_e( 'Post Status', 'wp-user-frontend' ); ?></th>
            <td>
                <select name="wpuf_settings[post_status]">
                    <?php
                    $statuses = get_post_statuses();

                    foreach ( $statuses as $status => $label ) {
                        printf( '<option value="%s"%s>%s</option>', esc_attr( $status ), esc_attr( selected( $post_status_selected, $status, false ) ), esc_html( $label ) );
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr class="wpuf-post-fromat">
            <th><?php esc_html_e( 'Post Format', 'wp-user-frontend' ); ?></th>
            <td>
                <select name="wpuf_settings[post_format]">
                    <option value="0"><?php esc_html_e( '- None -', 'wp-user-frontend' ); ?></option>
                    <?php
                    $post_formats = get_theme_support( 'post-formats' );

                    if ( isset( $post_formats[0] ) && is_array( $post_formats[0] ) ) {
                        foreach ( $post_formats[0] as $format ) {
                            printf( '<option value="%s"%s>%s</option>', esc_attr( $format ), esc_attr( selected( $post_format_selected, $format, false ) ), esc_html( $format ) );
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>

        <?php
            $post_taxonomies = get_object_taxonomies( $post_type_selected, 'objects' );
            foreach ( $post_taxonomies as $tax ) {
                if ( $tax->hierarchical ) {
                    $name             = 'default_'. $tax->name;
                    $default_category = !empty( $form_settings[$name] ) ? $form_settings[$name] : [];
                    if ( !is_array( $default_category ) ) {
                        $default_category = (array) $default_category;
                    }

                    $args = [
                        'hide_empty'       => false,
                        'hierarchical'     => true,
                        'selected'         => $default_category,
                        'taxonomy'         => $tax->name,
                    ];

                    $tax = '<tr class="wpuf_settings_taxonomy"> <th> Default '. $post_type_selected . ' '. $tax->name .'</th> <td>
                    <select multiple name="wpuf_settings[default_'.$tax->name.'][]">';
                    $categories = get_terms( $args );

                    foreach ( $categories as $category ) {
                        $selected = '';
                        if ( in_array( $category->term_id, $default_category ) ) {
                            $selected = 'selected ';
                        }

                        $tax .= '<option ' . $selected . 'value="' . $category->term_id . '">' . $category->name . '</option>';
                    }

                    $tax .='</select>
                        <p class="description">'.
                            esc_html( __( "If users are not allowed to choose any category, this category will be used instead (if post type supports)", "wp-user-frontend" ) )
                        .'</p>
                    </td>';

                    echo $tax; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
            }
        ?>

        <tr class="wpuf-redirect-to">
            <th><?php esc_html_e( 'Redirect To', 'wp-user-frontend' ); ?></th>
            <td>
                <select name="wpuf_settings[redirect_to]">
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
            <th><?php esc_html_e( 'Message to show', 'wp-user-frontend' ); ?></th>
            <td>
                <textarea rows="3" cols="40" name="wpuf_settings[message]"><?php echo esc_textarea( $message ); ?></textarea>
            </td>
        </tr>

        <tr class="wpuf-page-id">
            <th><?php esc_html_e( 'Page', 'wp-user-frontend' ); ?></th>
            <td>
                <select name="wpuf_settings[page_id]">
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
                <input type="url" name="wpuf_settings[url]" value="<?php echo esc_attr( $url ); ?>">
            </td>
        </tr>

        <tr class="wpuf-comment">
            <th><?php esc_html_e( 'Comment Status', 'wp-user-frontend' ); ?></th>
            <td>
                <select name="wpuf_settings[comment_status]">
                    <option value="open" <?php selected( $comment_status, 'open' ); ?>><?php esc_html_e( 'Open', 'wp-user-frontend' ); ?></option>
                    <option value="closed" <?php selected( $comment_status, 'closed' ); ?>><?php esc_html_e( 'Closed', 'wp-user-frontend' ); ?></option>
                </select>
            </td>
        </tr>

        <tr class="wpuf-submit-text">
            <th><?php esc_html_e( 'Submit Post Button text', 'wp-user-frontend' ); ?></th>
            <td>
                <input type="text" name="wpuf_settings[submit_text]" value="<?php echo esc_attr( $submit_text ); ?>">
            </td>
        </tr>

        <tr>
            <th><?php esc_html_e( 'Post Draft', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="hidden" name="wpuf_settings[draft_post]" value="false">
                    <input type="checkbox" name="wpuf_settings[draft_post]" value="true"<?php checked( $draft_post, 'true' ); ?> />
                    <?php esc_html_e( 'Enable Saving as draft', 'wp-user-frontend' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'It will show a button to save as draft', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>

        <?php do_action( 'wpuf_form_setting', $form_settings, $post ); ?>
    </table>
