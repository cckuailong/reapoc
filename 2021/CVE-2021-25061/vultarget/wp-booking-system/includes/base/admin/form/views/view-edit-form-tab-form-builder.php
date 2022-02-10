<div id="post-body" class="metabox-holder columns-2 meta-box-sortables">

    <!-- Main Post Body Content -->
    <div id="post-body-content" class="postbox-container">

        <!-- Form Builder -->
        <div class="postbox">
            <h3 class="hndle"><?php echo __( 'Form Builder', 'wp-booking-system' ); ?></h3>
            <div class="inside">    
                <div id="wpbs-form-builder" class="<?php echo (!empty($settings['recaptcha_v2_site_key']) || !empty($settings['recaptcha_v2_secret_key'])) ? 'hide-notice_captcha' : '';?>"><!-- --></div>
                <input type="hidden" id="wpbs_form_field_id_index" name="wpbs_form_field_id_index" value="<?php echo ( !empty($form_meta['wpbs_form_field_id_index'][0]) ) ? esc_attr($form_meta['wpbs_form_field_id_index'][0]) : '1';?>">
                <p class="wpbs-start-building"><?php echo __('Start building yor form by adding a form element. Click on any of the elements on the right to add them here!', 'wp-booking-system') ?></p>
            </div>
        </div>

        <?php

            /**
             * Action hook to add extra form fields to the main form edit area
             *
             * @param WPBS_Form $form
             *
             */
            do_action( 'wpbs_view_edit_form_main', $form );

        ?>

    </div><!-- / Main Post Body Content -->

    <!-- Sidebar Content -->
    <div id="postbox-container-1">
        <!-- Form Fields -->
        <div class="postbox">
            <h3 class="hndle"><?php echo __( 'Form Elements', 'wp-booking-system' ); ?></h3>
            <div class="inside">
                <div class="wpbs-form-builder-add-form-fields ">
                    <?php foreach($available_field_types as $form_field): ?>
                        <a href="#" data-field-type="<?php echo $form_field['type'];?>" class="button button-secondary">
                            <i class="wpbs-icon-<?php echo $form_field['type']; ?>"></i>
                            <span><?php echo str_replace('_',' ', $form_field['type']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div><!-- / Form Fields -->


        <?php
            /**
             * Action hook to add extra form fields to the main form edit area
             *
             * @param WPBS_Form $form
             *
             */
            do_action( 'wpbs_view_edit_form_sidebar', $form );
        ?>

    </div><!-- / Sidebar Content -->

</div><!-- / #post-body -->

<br class="clear">