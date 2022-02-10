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

$instructorList = new \TUTOR\Question_Answers_List();
$instructorList->prepare_items();
?>

<div class="wrap">
	<h2><?php _e('Question & Answer', 'tutor'); ?></h2>

	<form id="question_answers-filter" method="get">
		<input type="hidden" name="page" value="<?php echo \TUTOR\Question_Answers_List::Question_Answer_PAGE; ?>" />
		<?php
		$instructorList->search_box(__('Search', 'tutor'), 'question_answers');
		$instructorList->display(); ?>
	</form>
</div>