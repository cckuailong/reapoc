<?php

$allowed_sub_pages = array( 'add_new_instructor' );
$sub_page = sanitize_text_field( tutor_utils()->array_get('sub_page', $_GET, '') );

if ( is_string( $sub_page ) && in_array($sub_page, $allowed_sub_pages)){
	$include_file = tutor()->path."views/pages/{$sub_page}.php";
	if (file_exists($include_file)){
		include $include_file;
		return;
	}
}
/**
 * Quiz attempt filters added
 * 
 * @since 1.9.7
 */
$search_filter	= isset( $_GET['search'] ) ? $_GET['search'] : '';
$course_filter	= isset( $_GET['course-id'] ) ? $_GET['course-id'] : '';
$date_filter	= isset( $_GET['date'] ) ? $_GET['date'] : '';
$order_filter	= isset( $_GET['order'] ) ? $_GET['order'] : "ASC";

$instructorList = new \TUTOR\Instructors_List();
$instructorList->prepare_items($search_filter, $course_filter, $date_filter,$order_filter);
?>


<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Instructors', 'tutor'); ?></h1>
	<?php 
		if(get_option( 'users_can_register', false ) && current_user_can( 'manage_options' )) {
			?>
			<a href="<?php echo add_query_arg(array('sub_page' => 'add_new_instructor')); ?>" class="page-title-action">
				<i class="tutor-icon-plus"></i> <?php _e('Add New Instructor', 'tutor'); ?>
			</a>
			<?php
		}
	?>
    <hr class="wp-header-end">

    <form id="students-filter" method="get">
        <input type="hidden" name="page" value="<?php echo \TUTOR\Instructors_List::INSTRUCTOR_LIST_PAGE; ?>" />
		<?php
			//$instructorList->search_box(__('Search', 'tutor'), 'instructors');
			$instructorList->display($enable_sorting_field_with_bulk_action = true); 
		?>
    </form>
</div>