<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$product_id = tutor_utils()->get_course_product_id();
$download = new EDD_Download( $product_id );

if ($download->ID) {
	?>
    <div class="tutor-course-purchase-box">
		<?php
        echo edd_get_purchase_link( array( 'download_id' => $download->ID ) );
		?>
    </div>
	<?php
}else{
	?>
    <p class="tutor-alert-warning">
		<?php _e('Please make sure that your EDD product exists and valid for this course', 'tutor'); ?>
    </p>
	<?php
}