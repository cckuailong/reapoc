<?php
//Renders the all payment transactions
?>

<div class="swpm-grey-box">
    <?php echo SwpmUtils::_('All the payments/transactions of your members are recorded here.'); ?>
</div>

<div class="postbox">
    <h3 class="hndle"><label for="title">Search Transaction</label></h3>
    <div class="inside">
        <?php echo SwpmUtils::_('Search for a transaction by using email or name'); ?>
        <br /><br />
        <form method="post" action="">
            <input name="swpm_txn_search" type="text" size="40" value="<?php echo isset($_POST['swpm_txn_search']) ? esc_attr($_POST['swpm_txn_search']) : ''; ?>"/>
            <input type="submit" name="swpm_txn_search_btn" class="button" value="<?php echo SwpmUtils::_('Search'); ?>" />
        </form>
    </div></div>

<?php
include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/admin-includes/class.swpm-payments-list-table.php');
//Create an instance of our package class...
$payments_list_table = new SWPMPaymentsListTable();

//Check if an action was performed
if (isset($_REQUEST['action'])) { //Do list table form row action tasks
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_txn') { //Delete link was clicked for a row in list table
        $record_id = sanitize_text_field($_REQUEST['id']);
        $record_id = absint($record_id);
        $payments_list_table->delete_record($record_id);
        $success_msg = '<div id="message" class="updated"><p><strong>';
        $success_msg .= SwpmUtils::_('The selected entry was deleted!');
        $success_msg .= '</strong></p></div>';
        echo $success_msg;
    }
}

//Fetch, prepare, sort, and filter our data...
$payments_list_table->prepare_items();
?>
<form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
    <!-- Now we can render the completed list table -->
    <?php $payments_list_table->display(); ?>
</form>

<p class="submit">
    <a href="admin.php?page=simple_wp_membership_payments&tab=add_new_txn" class="button"><?php echo SwpmUtils::_('Add a Transaction Manually'); ?></a>
</p>