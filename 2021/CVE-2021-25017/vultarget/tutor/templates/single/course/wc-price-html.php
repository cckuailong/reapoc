<?php
/**
 * Template for displaying price
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */


$is_purchasable = tutor_utils()->is_course_purchasable();
$price = apply_filters('get_tutor_course_price', null, get_the_ID());

if ($is_purchasable && $price){
    echo '<div class="price">'.$price.'</div>';
}else{
	?>
	<div class="price">
		<?php _e('Free', 'tutor'); ?>
	</div>
	<?php
}
?>