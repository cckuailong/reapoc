<?php

/**
 * Closed for Enrollment
 *
 * @since v.1.6.4
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 */

if (!defined('ABSPATH'))
    exit;

do_action('tutor_course/single/closed-enrollment/before');
?>

<div class="tutor-single-add-to-cart-box">
    <div class="tutor-course-enroll-wrap">
        <button type="button" class="tutor-button tutor-button-block" disabled="disabled">
            <span><?php _e('100% Booked', 'tutor'); ?></span>
            <?php _e('Closed for Enrollment', 'tutor'); ?>
        </button>
    </div>
</div>

<?php do_action('tutor_course/single/closed-enrollment/after'); ?>