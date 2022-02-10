<?php
/**
 * @package @TUTOR
 * @since v.1.0.0
 */

if (isset($_GET['sub_page'])){
    $page = sanitize_text_field($_GET['sub_page']);
    include_once tutor()->path."views/pages/{$page}.php";
    return;
}
/**
 * Quiz attempt filters added
 * 
 * @since 1.9.5
 */
$search_filter	= isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
$course_filter	= isset( $_GET['course-id'] ) ? sanitize_text_field( $_GET['course-id'] ) : '';
$date_filter	= isset( $_GET['date'] ) ? sanitize_text_field( $_GET['date'] ) : '';
$order_filter	= isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : "ASC";

$quiz_attempt 	= new \TUTOR\Quiz_Attempts_List();
$quiz_attempt->prepare_items( $search_filter, $course_filter, $date_filter, $order_filter );

?>

<div class="wrap">
	<div>
		<h2><?php _e('Quiz Attempts', 'tutor'); ?></h2>
	</div>

	<form id="quiz_attempts-filter" method="get">
		<input type="hidden" name="page" value="<?php echo \TUTOR\Quiz_Attempts_List::QUIZ_ATTEMPT_PAGE; ?>" />
		<?php $quiz_attempt->display($enable_sorting_field_with_bulk_action = true); ?>
	</form>
</div>