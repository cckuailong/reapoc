<?php

/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$is_instructor = tutor_utils()->is_instructor();
if ($is_instructor) {
?>
    <?php
    $user_id = get_current_user_id();
    $instructor_status = get_user_meta($user_id, '_tutor_instructor_status', true);

    ?>

    <div class="tutor-instructor-pending-wrapper">
        <div class="tutor-alert <?php echo ($instructor_status == 'pending' ? 'tutor-alert-info' : ($instructor_status == 'approved' ? 'tutor-alert-success' : ($instructor_status == 'blocked' ? 'tutor-alert-danger' : ''))); ?>">

            <?php
            if ($instructor_status == 'pending') {
                _e('Your application will be reviewed and the results will be sent to you by email.', 'tutor');
            } else if ($instructor_status == 'approved') {
                _e('Your application has been accepted. Further necessary details have been sent to your registered email account.', 'tutor');
            } else if ($instructor_status == 'blocked') {
                _e('You have been blocked from being an instructor.', 'tutor');
            }
            ?>
        </div>

        <div class="tutor-instructor-pending-content">
            <img src="<?php echo esc_url(tutor()->url . 'assets/images/new-user.png') ?>" alt="<?php _e('New User', 'tutor') ?>">
            <div class="tutor-instructor-thankyou-wrapper">
                <div class="tutor-instructor-thankyou-text">
                    <h2>
                        <?php
                        if ($instructor_status == 'pending') {
                            _e('Thank you for registering as an instructor! ', 'tutor');
                        } else if ($instructor_status == 'approved') {
                            _e('Congratulations! You are now registered as an instructor.', 'tutor');
                        } else if ($instructor_status == 'blocked') {
                            _e('Unfortunately, your instructor status has been removed.', 'tutor');
                        }
                        ?>
                    </h2>
                </div>
                <div class="tutor-instructor-extra-text">
                    <p>
                        <?php
                        if ($instructor_status == 'pending') {
                            _e('We\'ve received your application, and we will review it soon. Please hang tight!', 'tutor');
                        } else if ($instructor_status == 'approved') {
                            _e('Start building your first course today and let your eLearning journey begin.', 'tutor');
                        } else if ($instructor_status == 'blocked') {
                            _e('Please contact the site administrator for further information.', 'tutor');
                        }
                        ?>
                    </p>
                </div>

                <a class="tutor-button" href="<?php echo esc_url(tutor_utils()->tutor_dashboard_url()) ?>">
                    <?php _e('Go to Dashboard', 'tutor'); ?>
                </a>
            </div>
        </div>
    </div>

<?php } else {
    tutor_load_template('dashboard.instructor.apply_for_instructor');
} ?>