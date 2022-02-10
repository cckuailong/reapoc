<?php
$massPayment = $this->viewData['pplog'];
$affiliates = $this->viewData['affiliates'];
?>

<script type="text/javascript">
	jQuery(function($) {
		$("#mainform").submit(function() {
			if ($("#resultsFile").val().length == 0)
			{
				alert("You must specify a file!");
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

	<h3>Please upload the results file.</h3>

	<?php if (isset($this->viewData['errorMsg'])) { ?>
	<div class="wpam-error-tip" style="margin: 20px;">
		<?php echo $this->viewData['errorMsg']?>
	</div>
	<?php } ?>

	<form id="mainform" enctype="multipart/form-data" method="POST" action="<?php echo admin_url('admin.php?id='.$massPayment->paypalLogId.'&page=wpam-payments&step=reconcile_with_file&substep=confirm')?>">
                <?php wp_nonce_field('wpam_payments_rwfc_nonce'); ?>
		<table class="widefat" style="width: 600px">
		<thead><tr><th colspan="2">&nbsp;</th></tr></thead>
		<tbody><tr><th width="150"><label for="resultsFile">Results File: </label></th><td><input id="resultsFile" type="file" name="resultsFile" /></td></tr>
			<tr><td style="text-align: right; padding: 20px" colspan="2">
				<input type="submit" name="submitButton" id="submitButton" value="<?php _e('Reconcile with this file', 'affiliates-manager');?>" class="button-primary"/>
			</td></tr></tbody>
		</table>

		<br />

	</form>

</div>