<?php
$withdrawList = new \TUTOR\Withdraw_Requests_List();
$withdrawList->prepare_items();
?>


<div class="wrap">
	<h2><?php _e('Withdraw Requests', 'tutor'); ?></h2>

	<form id="withdrawals-filter" method="get">
		<input type="hidden" name="page" value="<?php echo \TUTOR\Withdraw_Requests_List::WITHDRAW_REQUEST_LIST_PAGE; ?>" />
		<?php
		$withdrawList->search_box(__('Search', 'tutor'), 'withdrawals');
		$withdrawList->display(); ?>
	</form>
</div>