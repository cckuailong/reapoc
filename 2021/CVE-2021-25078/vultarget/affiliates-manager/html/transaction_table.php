<script>
    jQuery(function($) {
        var dates = $("#from, #to").datepicker({
            numberOfMonths: 1,
            onSelect: function(selectedDate) {
                var option = this.id == "from" ? "minDate" : "maxDate",
                        instance = $(this).data("datepicker"),
                        date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
            }
        });


        $('#reset').click(function() {
            $('#to').val('');
            $('#from').val('');
            $('#dateRange').submit();
        });

    });
</script>
<div class="daterange-form">
    <form method="post" id="dateRange" class="pure-form">
        <p class="wpam-daterange-heading"><?php _e('Date Range:', 'affiliates-manager') ?></p>
        <div class="wpam-daterange-selection">
        <label for="from"><?php _e('From Date', 'affiliates-manager') ?></label>
        <input type="text" id="from" name="from" value="<?php echo $this->viewData['from']; ?>"/>
        <label for="to"><?php _e('To Date', 'affiliates-manager') ?></label>
        <input type="text" id="to" name="to" value="<?php echo $this->viewData['to']; ?>"/>
        </div>
        <div class="wpam-daterange-action-buttons">
        <input type="submit" name="apply" value="<?php _e('Apply', 'affiliates-manager') ?>" class="pure-button pure-button-primary" />
        <input type="button" name="clear" value="<?php _e('Clear', 'affiliates-manager') ?>" id="reset" class="pure-button" />
        </div>
    </form>
</div>

<table class="pure-table wpam-responsive-table">
    <thead>
        <tr>
            <th><?php _e('ID', 'affiliates-manager') ?></th>
            <th><?php _e('Type', 'affiliates-manager') ?></th>
            <th><?php _e('Date Occurred', 'affiliates-manager') ?></th>
            <th><?php _e('Status', 'affiliates-manager') ?></th>
            <th><?php _e('Description', 'affiliates-manager') ?></th>
            <th><?php _e('Reference ID', 'affiliates-manager') ?></th>
            <th><?php _e('Amount', 'affiliates-manager') ?></th>
            <?php if (!empty($this->viewData['showBalance'])): ?>
                <th><?php _e('Balance', 'affiliates-manager') ?></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->viewData['transactions'] as $transaction) { ?>
            <tr class="transaction-<?php echo $transaction->status ?>">
                <td data-column="<?php _e('ID', 'affiliates-manager') ?>"><?php echo $transaction->transactionId ?></td>
                <td data-column="<?php _e('Type', 'affiliates-manager') ?>"><?php echo $transaction->type ?></td>
                <td data-column="<?php _e('Date Occurred', 'affiliates-manager') ?>"><?php echo date("m/d/Y", $transaction->dateCreated) ?></td>
                <td data-column="<?php _e('Status', 'affiliates-manager') ?>"><?php echo $transaction->status ?></td>
                <td data-column="<?php _e('Description', 'affiliates-manager') ?>"><?php echo $transaction->description ?></td>
                <td data-column="<?php _e('Reference ID', 'affiliates-manager') ?>"><?php echo (empty($transaction->referenceId) ? '&nbsp;' : $transaction->referenceId) ?></td>
                <td data-column="<?php _e('Amount', 'affiliates-manager') ?>"><?php echo wpam_format_money($transaction->amount) ?></td>
                <?php if (isset($this->viewData['showBalance']) && $this->viewData['showBalance']): ?>
                    <td data-column="<?php _e('Balance', 'affiliates-manager') ?>"><?php echo wpam_format_money($transaction->balance) ?></td>
                <?php endif; ?>
            </tr>
        <?php } ?>

    </tbody>
</table>
<?php
if (!count($this->viewData['transactions'])):
    ?>
    <div class="daterange-form"><p><?php _e('No records found for the date range selected.', 'affiliates-manager') ?></p></div>
<?php endif; ?>
