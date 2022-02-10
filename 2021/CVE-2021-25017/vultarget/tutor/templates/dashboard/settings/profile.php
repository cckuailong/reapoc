<?php
/**
 * @package TutorLMS/Templates
 * @version 1.6.2
 */

$user = wp_get_current_user();

$profile_placeholder = tutor()->url.'assets/images/profile-photo.png';
$profile_photo_src = $profile_placeholder;
$profile_photo_id = get_user_meta($user->ID, '_tutor_profile_photo', true);
if ($profile_photo_id){
    $url = wp_get_attachment_image_url($profile_photo_id, 'full');
    !empty($url) ? $profile_photo_src = $url : 0;
}

$cover_placeholder = tutor()->url.'assets/images/cover-photo.jpg';
$cover_photo_src = $cover_placeholder;
$cover_photo_id = get_user_meta($user->ID, '_tutor_cover_photo', true);
if ($cover_photo_id){
    $url = wp_get_attachment_image_url($cover_photo_id, 'full');
    !empty($url) ? $cover_photo_src = $url : 0;
}
?>

<div class="tutor-dashboard-content-inner">

    <?php do_action('tutor_profile_edit_form_before'); ?>

    <div id="tutor_profile_cover_photo_editor">
        <input id="tutor_photo_dialogue_box" type="file" accept=".png,.jpg,.jpeg"/>
        <div id="tutor_cover_area" data-fallback="<?php echo $cover_placeholder; ?>" style="background-image:url(<?php echo $cover_photo_src; ?>)">
            <span class="tutor_cover_deleter">
                <i class="tutor-icon-garbage"></i>
            </span>
            <div class="tutor_overlay">
                <button class="tutor_cover_uploader">
                    <i class="tutor-icon-image-ans"></i>
                    <span>
                       <?php
                            echo $profile_photo_id ? __('Update Cover Photo', 'tutor') : __('Upload Cover Photo', 'tutor');
                        ?> 
                    </span>
                </button>
            </div>
        </div>
        <div id="tutor_photo_meta_area">
            <img src="<?php echo tutor()->url . '/assets/images/'; ?>info-icon.svg" />
            <span><?php _e('Profile Photo Size', 'tutor'); ?>: <span><?php _e('200x200', 'tutor'); ?></span> <?php _e('pixels', 'tutor'); ?>,</span>
            <span>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Cover Photo Size', 'tutor'); ?>: <span><?php _e('700x430', 'tutor'); ?></span> <?php _e('pixels', 'tutor'); ?> </span>
            <span class="loader-area"><?php _e('Saving...', 'tutor'); ?></span>
        </div>
        <div id="tutor_profile_area" data-fallback="<?php echo $profile_placeholder; ?>" style="background-image:url(<?php echo $profile_photo_src; ?>)">
            <div class="tutor_overlay">
                <i class="tutor-icon-image-ans"></i>
            </div>
        </div>
        <div id="tutor_pp_option">
            <div class="up-arrow">
                <i></i>
            </div>
            
            <span class="tutor_pp_uploader">
                <i class="tutor-icon-image"></i> <?php _e('Upload Photo', 'tutor'); ?>
            </span>
            <span class="tutor_pp_deleter">
                <i class="tutor-icon-garbage"></i> <?php _e('Delete', 'tutor'); ?>
            </span>

            <div></div>
        </div>
    </div>

    <form action="" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
        <input type="hidden" value="tutor_profile_edit" name="tutor_action" />

        <?php
        $errors = apply_filters('tutor_profile_edit_validation_errors', array());
        if (is_array($errors) && count($errors)){
            echo '<div class="tutor-alert-warning tutor-mb-10"><ul class="tutor-required-fields">';
            foreach ($errors as $error_key => $error_value){
                echo "<li>{$error_value}</li>";
            }
            echo '</ul></div>';
        }
        ?>

        <?php do_action('tutor_profile_edit_input_before'); ?>

        <div class="tutor-form-row">
            <div class="tutor-form-col-6">
                <div class="tutor-form-group">
                    <label>
                        <?php _e('First Name', 'tutor'); ?>
                    </label>
                    <input type="text" name="first_name" value="<?php echo $user->first_name; ?>" placeholder="<?php _e('First Name', 'tutor'); ?>">
                </div>
            </div>

            <div class="tutor-form-col-6">
                <div class="tutor-form-group">
                    <label>
                        <?php _e('Last Name', 'tutor'); ?>
                    </label>
                    <input type="text" name="last_name" value="<?php echo $user->last_name; ?>" placeholder="<?php _e('Last Name', 'tutor'); ?>">
                </div>
            </div>
        </div>

        <div class="tutor-form-row">
            <div class="tutor-form-col-6">
                <div class="tutor-form-group">
                    <label>
                        <?php _e('User Name', 'tutor'); ?>
                    </label>
                    <input type="text" disabled="disabled" value="<?php echo $user->user_login; ?>">
                </div>
            </div>

            <div class="tutor-form-col-6">
                <div class="tutor-form-group">
                    <label>
                        <?php _e('Phone Number', 'tutor'); ?>
                    </label>
                    <input type="number" min="1" name="phone_number" value="<?php echo get_user_meta($user->ID,'phone_number',true); ?>" placeholder="<?php _e('Phone Number', 'tutor'); ?>">
                </div>
            </div>
        </div>

        <div class="tutor-form-row">
            <div class="tutor-form-col-12">
                <div class="tutor-form-group">
                    <label>
                        <?php _e('Bio', 'tutor'); ?>
                    </label>
                    <textarea name="tutor_profile_bio"><?php echo strip_tags(get_user_meta($user->ID,'_tutor_profile_bio',true)); ?></textarea>
                </div>
            </div>
        </div>

        <div class="tutor-form-row">
            <div class="tutor-form-col-6">

                <div class="tutor-form-group">
                    <label for="display_name"><?php _e( 'Display name publicly as', 'tutor' ); ?></label>

                    <select name="display_name" id="display_name">
                        <?php
                        $public_display                     = array();
                        $public_display['display_nickname'] = $user->nickname;
                        $public_display['display_username'] = $user->user_login;

                        if ( ! empty( $user->first_name ) ) {
                            $public_display['display_firstname'] = $user->first_name;
                        }

                        if ( ! empty( $user->last_name ) ) {
                            $public_display['display_lastname'] = $user->last_name;
                        }

                        if ( ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {
                            $public_display['display_firstlast'] = $user->first_name . ' ' . $user->last_name;
                            $public_display['display_lastfirst'] = $user->last_name . ' ' . $user->first_name;
                        }

                        if ( ! in_array( $user->display_name, $public_display ) ) { // Only add this if it isn't duplicated elsewhere
                            $public_display = array( 'display_displayname' => $user->display_name ) + $public_display;
                        }

                        $public_display = array_map( 'trim', $public_display );
                        $public_display = array_unique( $public_display );

                        foreach ( $public_display as $id => $item ) {
                            ?>
                            <option <?php selected( $user->display_name, $item ); ?>><?php echo $item; ?></option>
                            <?php
                        }
                        ?>
                    </select>

                    <p><small><?php _e('The display name is shown in all public fields, such as the author name, instructor name, student name, and name that will be printed on the certificate.', 'tutor'); ?></small> </p>

                </div>

            </div>

        </div>

        <?php do_action('tutor_profile_edit_before_social_media', $user); ?>

        <?php
        $tutor_user_social_icons = tutor_utils()->tutor_user_social_icons();
        foreach ($tutor_user_social_icons as $key => $social_icon){
            ?>
            <div class="tutor-form-row">
                <div class="tutor-form-col-12">
                    <div class="tutor-form-group">
                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($social_icon['label']); ?></label>
                        <input type="text" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" value="<?php echo get_user_meta($user->ID,$key,true); ?>" placeholder="<?php echo esc_html($social_icon['placeholder']); ?>">
                    </div>
                </div>
            </div>
            <?php
        }

        ?>

        <div class="tutor-form-row">
            <div class="tutor-form-col-12">
                <div class="tutor-form-group tutor-profile-form-btn-wrap">
                    <button type="submit" name="tutor_register_student_btn" value="register" class="tutor-button"><?php _e('Update Profile', 'tutor'); ?></button>
                </div>
            </div>
        </div>



        <?php do_action('tutor_profile_edit_input_after'); ?>

    </form>

    <?php do_action('tutor_profile_edit_form_after'); ?>

</div>