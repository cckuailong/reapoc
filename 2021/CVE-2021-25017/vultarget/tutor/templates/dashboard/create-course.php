<?php

/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */


if (!defined('ABSPATH'))
    exit;

global $post;

get_tutor_header(true);
do_action('tutor_load_template_before', 'dashboard.create-course', null);

$course_id = get_the_ID();
$can_publish_course = (bool) tutor_utils()->get_option('instructor_can_publish_course') || current_user_can('administrator');
?>

<?php
if (!tutor_utils()->can_user_edit_course(get_current_user_id(), $course_id)) {
    $args = array(
        'headline' => __( 'Permission Denied', 'tutor' ),
        'message' =>  __( 'You don\'t have the right to edit this course', 'tutor' ),
        'description' => __('Please make sure you are logged in to correct account', 'tutor'),
        'button' => array(
            'url' => get_permalink( $course_id ),
            'text' =>  __( 'View Course', 'tutor' )
        )
    );

    tutor_load_template('permission-denied', $args);
    return;
}
?>

<?php do_action('tutor/dashboard_course_builder_before'); ?>
<form action="" id="tutor-frontend-course-builder" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field(tutor()->nonce_action, tutor()->nonce); ?>

    <header class="tutor-dashboard-builder-header">
        <div class="tutor-container tutor-fluid">
            <div class="tutor-row tutor-align-items-center">
                <div class="tutor-col-auto">
                    <div class="tutor-dashboard-builder-header-left">
                        <div class="tutor-dashboard-builder-logo">
                            <?php $tutor_course_builder_logo_src = apply_filters('tutor_course_builder_logo_src', tutor()->url . 'assets/images/tutor-logo.png'); ?>
                            <img src="<?php echo esc_url($tutor_course_builder_logo_src); ?>" alt="">
                        </div>
                        <button type="submit" class="tutor-dashboard-builder-draft-btn" name="course_submit_btn" value="save_course_as_draft">
                            <!-- @TODO: Icon must be chenged -->
                            <i class="tutor-icon-save"></i>
                            <span><?php _e('Save', 'tutor'); ?></span>
                        </button>
                    </div>
                </div>
                <div class="tutor-col-auto">
                    <div class="tutor-dashboard-builder-header-right">
                        <a href="<?php the_permalink($course_id); ?>" target="_blank"><i class="tutor-icon-glasses"></i><?php _e('Preview', 'tutor'); ?></a>
                        <?php
                        if ($can_publish_course) {
                        ?>
                            <button class="tutor-button" type="submit" name="course_submit_btn" value="publish_course"><?php _e('Publish Course', 'tutor'); ?></button>
                        <?php
                        } else {
                        ?>
                            <button class="tutor-button" type="submit" name="course_submit_btn" value="submit_for_review"><?php _e('Submit for Review', 'tutor'); ?></button>
                        <?php
                        }
                        ?>
                        <a href="<?php echo tutor_utils()->tutor_dashboard_url(); ?>"> <?php _e('Exit', "tutor") ?></a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="tutor-frontend-course-builder-section">

        <div class="tutor-container">
            <div class="tutor-row">
                <div class="tutor-col-8">
                    <input type="hidden" value="tutor_add_course_builder" name="tutor_action" />
                    <input type="hidden" name="course_ID" id="course_ID" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" name="post_ID" id="post_ID" value="<?php echo get_the_ID(); ?>">

                    <!--since 1.8.0 alert message -->
                    <?php
                        $user_id = get_current_user_id();
                        $expires = get_user_meta( $user_id, 'tutor_frontend_course_message_expires', true );
                        $message = get_user_meta( $user_id, 'tutor_frontend_course_action_message', true );

                        if($message && $expires && $expires>time()) {
                            ?>
                            <div class="tutor-alert tutor-alert-info">
                                <?php echo $message; ?>
                            </div>
                            <?php
                        }

                        if($message || $expires) {
                            delete_user_meta( $user_id, 'tutor_frontend_course_message_expires' );
                            delete_user_meta( $user_id, 'tutor_frontend_course_action_message' );
                        }
                    ?>
                    <!--alert message end -->
                    <?php do_action('tutor/dashboard_course_builder_form_field_before'); ?>

                    <div class="tutor-course-builder-section tutor-course-builder-info">
                        <div class="tutor-course-builder-section-title">
                            <h3><i class="tutor-icon-down"></i><span><?php esc_html_e('Course Info', 'tutor'); ?></span></h3>
                        </div>
                        <!--.tutor-course-builder-section-title-->
                        <div class="tutor-course-builder-section-content">
                            <div id="tutor-frontend-course-title" class="tutor-frontend-builder-item-scope">
                                <div class="tutor-form-group">
                                    <label class="tutor-builder-item-heading">
                                        <?php _e('Course Title', 'tutor'); ?>
                                    </label>
                                    <input type="text" name="title" value="<?php echo get_the_title(); ?>" placeholder="<?php _e('ex. Learn photoshop CS6 from scratch', 'tutor'); ?>">
                                </div>
                            </div>
                            <!--.tutor-frontend-builder-item-scope-->

                            <div class="tutor-frontend-builder-item-scope">
                                <div class="tutor-form-group">
                                    <label> <?php _e('Description', 'tutor'); ?></label>
                                    <?php
                                    $editor_settings = array(
                                        'media_buttons' => false,
                                        'quicktags'     => false,
                                        'editor_height' => 150,
                                        'textarea_name' => 'content'
                                    );
                                    wp_editor($post->post_content, 'course_description', $editor_settings);
                                    ?>
                                </div>
                            </div>
                            <!--.tutor-frontend-builder-item-scope-->

                            <?php do_action('tutor/frontend_course_edit/after/description', $post) ?>

                            <div class="tutor-frontend-builder-item-scope">
                                <div class="tutor-form-group">
                                    <label>
                                        <?php _e('Choose a category', 'tutor'); ?>
                                    </label>
                                    <div class="tutor-form-field-course-categories">
                                        <?php //echo tutor_course_categories_checkbox($course_id);
                                        echo tutor_course_categories_dropdown($course_id, array('classes' => 'tutor_select2'));
                                        ?>
                                    </div>
                                </div>
                            </div>


                            <div class="tutor-frontend-builder-item-scope">
                                <div class="tutor-form-group">
                                    <label>
                                        <?php _e('Choose a tag', 'tutor'); ?>
                                    </label>
                                    <div class="tutor-form-field-course-tags">
                                        <?php //echo tutor_course_tags_checkbox($course_id);
                                        echo tutor_course_tags_dropdown($course_id, array('classes' => 'tutor_select2'));
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $monetize_by = tutils()->get_option('monetize_by');
                            if ($monetize_by === 'wc' || $monetize_by === 'edd') {
                                $course_price = tutor_utils()->get_raw_course_price(get_the_ID());
                                $currency_symbol = tutor_utils()->currency_symbol();

                                $_tutor_course_price_type = tutils()->price_type();
                            ?>
                                <div class="tutor-frontend-builder-item-scope tutor-frontend-builder-course-price">
                                    <label class="tutor-builder-item-heading">
                                        <?php _e('Course Price', 'tutor'); ?>
                                    </label>
                                    <div class="tutor-row tutor-align-items-center">
                                        <div class="tutor-col-auto">
                                            <label for="tutor_course_price_type_pro" class="tutor-styled-radio">
                                                <input id="tutor_course_price_type_pro" type="radio" name="tutor_course_price_type" value="paid" <?php checked($_tutor_course_price_type, 'paid'); ?>>
                                                <span></span>
                                                <div class="tutor-form-group">
                                                    <span class="tutor-input-prepand"><?php echo $currency_symbol; ?></span>
                                                    <input type="text" name="course_price" value="<?php echo $course_price->regular_price; ?>" placeholder="<?php _e('Set course price', 'tutor'); ?>">
                                                </div>
                                            </label>
                                        </div>
                                        <div class="tutor-col-auto">
                                            <label class="tutor-styled-radio">
                                                <input type="radio" name="tutor_course_price_type" value="free" <?php $_tutor_course_price_type ? checked($_tutor_course_price_type, 'free') : checked('true', 'true'); ?>>
                                                <span><?php _e('Free', "tutor") ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!--.tutor-frontend-builder-item-scope-->
                            <?php } ?>

                            <div class="tutor-frontend-builder-item-scope">
                                <div class="tutor-form-group">
                                    <label>
                                        <?php _e('Course Thumbnail', 'tutor'); ?>
                                    </label>
                                    <div class="tutor-form-field tutor-form-field-course-thumbnail tutor-thumbnail-wrap">
                                        <div class="tutor-row tutor-align-items-center">
                                            <div class="tutor-col-5">
                                                <div class="builder-course-thumbnail-img-src">
                                                    <?php
                                                    $builder_course_img_src = tutor()->url . 'assets/images/placeholder-course.jpg';
                                                    $_thumbnail_url = get_the_post_thumbnail_url($course_id);
                                                    $post_thumbnail_id = get_post_thumbnail_id($course_id);

                                                    if (!$_thumbnail_url) {
                                                        $_thumbnail_url = $builder_course_img_src;
                                                    }
                                                    ?>
                                                    <img src="<?php echo $_thumbnail_url; ?>" class="thumbnail-img" data-placeholder-src="<?php echo $builder_course_img_src; ?>">
                                                    <a href="javascript:;" class="tutor-course-thumbnail-delete-btn" style="display: <?php echo
                                                                                                                                        $post_thumbnail_id ? 'block' : 'none'; ?>;"><i class="tutor-icon-line-cross"></i></a>
                                                </div>
                                            </div>

                                            <div class="tutor-col-7">
                                                <div class="builder-course-thumbnail-upload-wrap">
                                                    <div><?php echo sprintf(__("Important Guideline: %1\$s 700x430 pixels %2\$s %3\$s File Support: %1\$s jpg, .jpeg,. gif, or .png %2\$s no text on the image.", "tutor"), "<strong>", "</strong>", "<br>") ?></div>
                                                    <input type="hidden" id="tutor_course_thumbnail_id" name="tutor_course_thumbnail_id" value="<?php echo $post_thumbnail_id; ?>">
                                                    <a href="javascript:;" class="tutor-course-thumbnail-upload-btn tutor-button bordered-button"><?php _e('Upload Image', 'tutor'); ?></a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php do_action('tutor/dashboard_course_builder_form_field_after', $post); ?>
                    <div class="tutor-form-row">
                        <div class="tutor-form-col-12">
                            <div class="tutor-form-group">
                                <div class="tutor-form-field tutor-course-builder-btn-group">
                                    <button type="submit" class="tutor-button" name="course_submit_btn" value="save_course_as_draft"><?php _e('Save course as draft', 'tutor'); ?></button>
                                    <?php if ($can_publish_course) { ?>
                                        <button class="tutor-button tutor-button-primary" type="submit" name="course_submit_btn" value="publish_course"><?php _e('Publish Course', 'tutor'); ?></button>
                                    <?php } else { ?>
                                        <button class="tutor-button tutor-button-primary" type="submit" name="course_submit_btn" value="submit_for_review"><?php _e('Submit for Review', 'tutor'); ?></button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--.tutor-col-8-->
                <div class="tutor-col-4">
                    <div class="tutor-course-builder-upload-tips">
                        <h3 class="tutor-course-builder-tips-title"><i class="tutor-icon-light-bulb"></i><?php _e('Course Upload Tips', 'tutor') ?></h3>
                        <ul>
                            <li><?php _e("Set the Course Price option or make it free.", 'tutor'); ?></li>
                            <li><?php _e("Standard size for the course thumbnail is 700x430.", 'tutor'); ?></li>
                            <li><?php _e("Video section controls the course overview video.", 'tutor'); ?></li>
                            <li><?php _e("Course Builder is where you create & organize a course.", 'tutor'); ?></li>
                            <li><?php _e("Add Topics in the Course Builder section to create lessons, quizzes, and assignments.", 'tutor'); ?></li>
                            <li><?php _e("Prerequisites refers to the fundamental courses to complete before taking this particular course.", 'tutor'); ?></li>
                            <li><?php _e("Information from the Additional Data section shows up on the course single page.", 'tutor'); ?></li>
                        </ul>
                    </div>
                </div>
                <!--.tutor-col-4-->
            </div>
            <!--.tutor-row-->
        </div>
    </div>
</form>
<?php do_action('tutor/dashboard_course_builder_after'); ?>


<?php
do_action('tutor_load_template_after', 'dashboard.create-course', null);
get_tutor_footer(true); ?>