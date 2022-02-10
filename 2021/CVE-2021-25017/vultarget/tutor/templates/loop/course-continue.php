<?php

/**
 * Course loop continue when enrolled
 *
 * @since v.1.7.4
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.7.4
 */
?>

<div class="tutor-course-loop-price">
    <?php
    $course_id = get_the_ID();
    $enroll_btn = '<div  class="tutor-loop-cart-btn-wrap"><a href="'. get_the_permalink(). '">'.__('Continue Course', 'tutor'). '</a></div>';
    $default_price = apply_filters('tutor-loop-default-price', __('Free', 'tutor'));
    $price_html = '<div class="price"> '.$default_price.$enroll_btn. '</div>';
    if (tutor_utils()->is_course_purchasable()) {
        
	    $product_id = tutor_utils()->get_course_product_id($course_id);
	    $product    = wc_get_product( $product_id );

	    if ( $product ) {
		    $price_html = '<div class="price"> '.$product->get_price_html().$enroll_btn.' </div>';
	    }
    }
    echo $price_html;
    ?>
</div>