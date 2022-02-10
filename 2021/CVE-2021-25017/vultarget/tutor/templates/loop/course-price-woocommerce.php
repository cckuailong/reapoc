<?php

/**
 * Course loop price
 *
 * @since v.1.0.0
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */
?>

<div class="tutor-course-loop-price">
    <?php
    $course_id = get_the_ID();
    $is_public = get_post_meta( $course_id, '_tutor_is_public_course', true )=='yes';
    $enroll_btn = '<div  class="tutor-public-course-start-learning">' . apply_filters( 'tutor_course_restrict_new_entry', '<a href="'. get_the_permalink(). '">' . __('Get Enrolled', 'tutor') . '</a>' ) . '</div>';
    $default_price = apply_filters('tutor-loop-default-price', ($is_public ? '' : __('Free', 'tutor') ));
    $price_html = '<div class="price"> '.$default_price.$enroll_btn. '</div>';
    if (tutor_utils()->is_course_purchasable()) {
	    $enroll_btn = tutor_course_loop_add_to_cart(false);

	    $product_id = tutor_utils()->get_course_product_id($course_id);
	    $product    = wc_get_product( $product_id );

	    if ( $product ) {
		    $price_html = '<div class="price"> '.$product->get_price_html() . apply_filters( 'tutor_course_restrict_new_entry', $enroll_btn ) . ' </div>';
	    }
    }

    if( $is_public ) {
        $price_html = '<div class="price">
            ' . __('Free', 'tutor') . '
            <a class="tutor-public-course-start-learning" href="'. get_the_permalink(). '">
                ' . __('Start Learning ', 'tutor') . '
            </a></div>';
    }

    echo $price_html;
    ?>
</div>