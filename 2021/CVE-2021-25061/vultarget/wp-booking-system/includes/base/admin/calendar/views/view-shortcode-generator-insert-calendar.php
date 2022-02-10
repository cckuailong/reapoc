<!-- Modal Tab: Calendar Single -->
<div class="wpbs-tab wpbs-modal-tab wpbs-active" data-tab="insert-calendar">
    
    <h3><?php echo __( 'Insert a Calendar', 'wp-booking-system' ); ?></h3>
    <p><?php echo __( 'Select which calendar you wish to insert and use the options to customize it to your needs.', 'wp-booking-system' ); ?></p>

    <h4><?php echo __( 'Calendar Options', 'wp-booking-system' ); ?></h4>
    <hr />

    <!-- Row -->
    <div class="wpbs-row">

        <!-- Column: Calendar -->
        <div class="wpbs-col-1-4">
            
            <label for="modal-add-calendar-shortcode-calendar"><?php echo __( 'Calendar', 'wp-booking-system' ); ?></label>

            <select id="modal-add-calendar-shortcode-calendar" class="wpbs-shortcode-generator-field-calendar" data-attribute="id">
                <?php
                    foreach( $calendars as $calendar )
                        echo '<option value="' . $calendar->get('id') . '">' . $calendar->get('name') . '</option>';
                ?>
            </select>

        </div>

        <!-- Column: Calendar Title -->
        <div class="wpbs-col-1-4">
            
            <label for="modal-add-calendar-shortcode-calendar-title"><?php echo __( 'Display Calendar Title', 'wp-booking-system' ); ?></label>

            <select id="modal-add-calendar-shortcode-calendar-title" class="wpbs-shortcode-generator-field-calendar" data-attribute="title">
                <option value="yes"><?php echo __( 'Yes', 'wp-booking-system' ); ?></option>
                <option value="no"><?php echo __( 'No', 'wp-booking-system' ); ?></option>
            </select>

        </div>

        <!-- Column: Legend -->
        <div class="wpbs-col-1-4">
            
            <label for="modal-add-calendar-shortcode-legend"><?php echo __( 'Display Legend', 'wp-booking-system' ); ?></label>

            <select id="modal-add-calendar-shortcode-legend" class="wpbs-shortcode-generator-field-calendar" data-attribute="legend">
                <option value="yes"><?php echo __( 'Yes', 'wp-booking-system' ); ?></option>
                <option value="no"><?php echo __( 'No', 'wp-booking-system' ); ?></option>
            </select>

        </div>

        <!-- Column: Language -->
        <div class="wpbs-col-1-4">

            <label for="modal-add-calendar-shortcode-language"><?php echo __( 'Language', 'wp-booking-system' ); ?></label>

            <select id="modal-add-calendar-shortcode-language" class="wpbs-shortcode-generator-field-calendar" data-attribute="language">
                
                <option value="auto"><?php echo __( 'Auto (let WP choose)', 'wp-booking-system' ); ?></option>

                <?php

                    $settings 		  = get_option( 'wpbs_settings', array() );
                    $languages 		  = wpbs_get_languages();
                    $active_languages = ( ! empty( $settings['active_languages'] ) ? $settings['active_languages'] : array() );

                    foreach( $active_languages as $code ) {
                        echo '<option value="' . esc_attr( $code ) . '">' . ( ! empty( $languages[$code] ) ? $languages[$code] : '' ) . '</option>';
                    }

                ?>

            </select>

        </div>

    </div><!-- / Row -->

   

    <h4><?php echo __( 'Form Options', 'wp-booking-system' ); ?></h4>
    <hr />

    

    <!-- Row -->
    <div class="wpbs-row">

        <!-- Column: Form -->
        <div class="wpbs-col-1-4">

            <?php $forms = wpbs_get_forms(array('status' => 'active')); ?>
            
            <label for="modal-add-calendar-shortcode-form"><?php echo __( 'Form', 'wp-booking-system' ); ?></label>

            <select id="modal-add-calendar-shortcode-form" class="wpbs-shortcode-generator-field-calendar" data-attribute="form_id">
                <option value="0"><?php echo __('No Form','wp-booking-system') ?></option>
                <?php
                    foreach( $forms as $form )
                        echo '<option value="' . $form->get('id') . '">' . $form->get('name') . '</option>';
                ?>
            </select>

        </div>


    </div><!-- / Row -->

    <!-- Row -->
   

    <hr />

    <!-- Shortcode insert -->
    <a href="#" id="wpbs-insert-shortcode-single-calendar" class="button button-primary"><?php echo __( 'Insert Calendar', 'wp-booking-system' ); ?></a>
    <a href="#" class="button button-secondary wpbs-modal-close"><?php echo __( 'Cancel', 'wp-booking-system' ); ?></a>

</div>