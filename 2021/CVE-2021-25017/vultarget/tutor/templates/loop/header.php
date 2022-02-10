<?php
/**
 * @package TutorLMS/Templates
 * @version 1.5.8
 */

?>

<div class="tutor-course-header">
	<?php
	tutor_course_loop_thumbnail();

	$course_id = get_the_ID();
	?>
    <div class="tutor-course-loop-header-meta">
		<?php
        $is_wishlisted = tutor_utils()->is_wishlisted($course_id);
        $has_wish_list = '';
        if ($is_wishlisted){
	        $has_wish_list = 'has-wish-listed';
        }

        $action_class = '';
        if ( is_user_logged_in()){
            $action_class = apply_filters('tutor_wishlist_btn_class', 'tutor-course-wishlist-btn');
        }else{
            $action_class = apply_filters('tutor_popup_login_class', 'cart-required-login');
        }

		echo '<span class="tutor-course-loop-level">'.get_tutor_course_level().'</span>';
		echo '<span class="tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line '.$action_class.' '.$has_wish_list.' " data-course-id="'.$course_id.'"></a> </span>';
		?>
    </div>
</div>