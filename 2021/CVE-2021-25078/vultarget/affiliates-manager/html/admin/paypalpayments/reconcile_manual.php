<?php
$massPayment = $this->viewData['pplog'];
$affiliates = $this->viewData['affiliates'];
?>

<script type="text/javascript">
	jQuery(function($) {
		$("#mainform").submit(function() {
			var invalidEntries = $("select[name^=transactionStatus]").filter(function(){
				return ($(this).val() != 'success' && $(this).val() != 'failed');
			}).length;
			if (invalidEntries > 0)
			{
				alert("Please select a resolution for all transactions.");
				return false;
			}
			return true;
		});
	});
</script>
<div class="wrap">

	<h2>PayPal Mass Pay</h2>
	<h3>Manual Reconciliation</h3>
	<table class="widefat" style="width: 700px">

		<thead>
		<tr><th width="150">&nbsp;</th><th width="500">&nbsp;</th></tr>
		</thead>

		<tr><th>Database ID</th><td><?php echo $massPayment->paypalLogId?></td></tr>
		<tr><th>Date Occurred</th><td><?php echo date("m/d/Y H:i:s",$massPayment->dateOccurred)?></td></tr>
		<tr><th>PayPal Timestamp</th><td><?php echo date("m/d/Y H:i:s", $massPayment->responseTimestamp)?></td></tr>
		<tr><th>PayPal Correlation ID</th> <td><?php echo $massPayment->correlationId?></td></tr>
		<tr><th>Amount</th><td><?php echo ($massPayment->amount)?></td></tr>
		<tr><th>Fee</th><td><?php echo ($massPayment->fee)?></td></tr>
		<tr><th>Total Amount</th><td><?php echo ($massPayment->totalAmount)?></td></tr>
		<tr class="transaction-<?php echo $massPayment->status?>"><th>Status</th><td><?php echo $massPayment->status?></td></tr>
	</table>

	<h3>Please select the outcome for each transaction.</h3>

<form id="mainform" method="post" action="<?php echo admin_url('admin.php?page=wpam-payments&step=reconcile_manual&substep=confirm&id='.$massPayment->paypalLogId)?>">
        <?php wp_nonce_field('wpam_payments_reconcile_manual_nonce'); ?>
	<table class="widefat" style="width: auto">
		<thead>
		<tr>

			<th width="25">ID</th>
			<th width="100">Date Occurred</th>
			<th width="150">Affiliate</th>
			<th width="150">PayPal E-Mail</th>
			<th width="100">Status</th>
			<th>Description</th>
			<th width="100">Amount</th>
			<th width="150">Outcome</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($this->viewData['transactions'] as $transaction) {?>
		<?php $affiliate = $affiliates[$transaction->affiliateId]; ?>
		<tr class="transaction-<?php echo $transaction->status?>">
			<td><?php echo $transaction->transactionId?></td>
			<td><?php echo date("m/d/Y", $transaction->dateCreated)?></td>
			<td><?php echo $affiliate->firstName?> <?php echo $affiliate->lastName?></td>
			<td><?php echo $affiliate->paypalEmail?></td>
			<td><?php echo $transaction->status?></td>
			<td><?php echo $transaction->description?></td>
			<td style="text-align: right"><?php echo wpam_format_money($transaction->amount)?></td>
			<td>
				<select name="transactionStatus[<?php echo $transaction->transactionId?>]" style="width: 100px;">
					<option value="unset"></option>
					<option value="success" style="color: green; ">Success</option>
					<option value="failed" style="color: red;">Failed</option>
				</select>
			</td>

		</tr>
		<?php } ?>
		<tr>
			<td colspan="100" style="text-align: right; padding: 20px;">
				<input type="submit" value="<?php _e('Reconcile Mass Payment', 'affiliates-manager');?>" class="button-primary" id="btnSubmit" />
			</td>
		</tr>
		</tbody>
	</table>

</form>
</div>