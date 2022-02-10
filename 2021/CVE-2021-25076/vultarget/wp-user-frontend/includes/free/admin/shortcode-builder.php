<div id="wpuf-media-dialog" style="display: none;">

    <div class="wpuf-popup-container">

        <h3><?php esc_html_e( 'Select a form to insert', 'wp-user-frontend' ); ?></h3>

        <?php $form_types = apply_filters( 'wpuf_shortcode_dialog_form_type', [
            'post'         => __( 'Post Form', 'wp-user-frontend' ),
            'registration' => __( 'Registration Form', 'wp-user-frontend' ),
        ] ); ?>

        <div class="wpuf-div">
            <label for="wpuf-form-type" class="label"><?php esc_html_e( 'Form Type', 'wp-user-frontend' ); ?></label>
            <select id="wpuf-form-type">

                <?php foreach ( $form_types as $key => $form_type ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $form_type ); ?></option>
                <?php } ?>

            </select>
        </div>

        <?php foreach ( $form_types as $key => $form_type ) {
            switch ( $key ) {
                case 'post':
                    $form_post_type = 'wpuf_forms';
                    break;

                case 'registration':
                    $form_post_type = 'wpuf_profile';
                    break;

                default:
                    $form_post_type = apply_filters( 'wpuf_shortcode_dialog_form_type_post', $key, $form_types );
                    break;

            } ?>

            <div class="wpuf-div show-if-<?php echo esc_attr( $key ); ?>">

                <label for="wpuf-form-<?php echo esc_attr( $key ); ?>" class="label"><?php echo esc_html( $form_type ); ?></label>

                <select id="wpuf-form-<?php echo esc_attr( $key ); ?>">

                    <?php
                    $args = [
                        'post_type'   => $form_post_type,
                        'post_status' => 'publish',
                    ];

                    $form_posts = get_posts( $args );

                    foreach ( $form_posts as $form ) { ?>

                        <option value="<?php echo esc_attr( $form->ID ); ?>"><?php echo esc_html( $form->post_title ); ?></option>

                    <?php } ?>

                </select>

            </div>

        <?php
        }

        do_action( 'wpuf_shortcode_dialog_content', $form_types ); ?>

        <div class="submit-button wpuf-div">
            <button id="wpuf-form-insert" class="button-primary"><?php esc_html_e( 'Insert Form', 'wp-user-frontend' ); ?></button>
            <button id="wpuf-form-close" class="button-secondary" style="margin-left: 5px;" onClick="tb_remove();"><?php esc_html_e( 'Close', 'wp-user-frontend' ); ?></a>
        </div>

    </div>
</div>

<style type="text/css">
    .wpuf-popup-container {
        padding: 15px 0 0 20px;
    }
    .wpuf-div {
        padding: 10px;
        clear: left;
    }
    .wpuf-div label.label {
        float: left;
        width: 25%;
    }
</style>
