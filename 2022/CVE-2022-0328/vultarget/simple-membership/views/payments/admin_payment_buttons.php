<?php
//Render the all payment buttons tab
?>

<div class="swpm-grey-box">
    <?php echo SwpmUtils::_('All the membership buttons that you created in the plugin are displayed here.'); ?>
</div>

<?php
include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/admin-includes/class.swpm-payment-buttons-list-table.php');
//Create an instance of our package class...
$payments_buttons_table = new SwpmPaymentButtonsListTable();

//Fetch, prepare, sort, and filter our data...
$payments_buttons_table->prepare_items();

?>

<form id="swpm-payment-buttons-filter" method="post" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">

    <input type="hidden" name="page" value="" />
    <!-- Now we can render the completed list table -->
    <?php $payments_buttons_table->display(); ?>
</form>

<p>
    <a href="admin.php?page=simple_wp_membership_payments&tab=create_new_button" class="button"><?php SwpmUtils::e('Create New Button'); ?></a>
</p>
