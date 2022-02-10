<?php
$massPayment = $this->viewData['pplog'];
$affiliates = $this->viewData['affiliates'];
?>
		
<div class="wrap">
	<h2><?php _e('PayPal Mass Pay', 'affiliates-manager');?></h2>
	<h3><?php _e('Payment Detail', 'affiliates-manager');?></h3>

	<table class="widefat" style="width: 700px">

		<thead>
		<tr><th width="150">&nbsp;</th><th width="500">&nbsp;</th></tr>
		</thead>

		<tr><th><?php _e('Database ID', 'affiliates-manager');?></th><td><?php echo $massPayment->paypalLogId?></td></tr>
		<tr><th><?php _e('Date Occurred', 'affiliates-manager');?></th><td><?php echo date("m/d/Y H:i:s",$massPayment->dateOccurred)?></td></tr>
		<tr><th><?php _e('PayPal Timestamp', 'affiliates-manager');?></th><td><?php echo date("m/d/Y H:i:s", $massPayment->responseTimestamp)?></td></tr>
		<tr><th><?php _e('PayPal Correlation ID', 'affiliates-manager');?></th> <td><?php echo $massPayment->correlationId?></td></tr>
		<tr><th><?php _e('Amount', 'affiliates-manager');?></th><td><?php echo ($massPayment->amount)?></td></tr>
		<tr><th><?php _e('Fee', 'affiliates-manager');?></th><td><?php echo ($massPayment->fee)?></td></tr>
		<tr><th><?php _e('Total Amount', 'affiliates-manager');?></th><td><?php echo ($massPayment->totalAmount)?></td></tr>
		<tr class="transaction-<?php echo $massPayment->status?>"><th>Status</th><td><?php echo $massPayment->status?></td></tr>
		<?php if ($massPayment->status == 'pending') {?>
			<tr>
				<th style="vertical-align: top"><?php _e('Reconciliation', 'affiliates-manager');?></th>
				<td>
					<div style="margin-left: 25px; margin-top: 25px;">
						<a class="button-secondary" href="<?php echo admin_url('admin.php?page=wpam-payments&step=reconcile_manual&id='.$massPayment->paypalLogId)?>"><?php _e('Manually reconcile payments ... ', 'affiliates-manager');?></a><br/><br/>
						<a class="button-secondary" href="<?php echo admin_url('admin.php?page=wpam-payments&step=reconcile_with_file&id='.$massPayment->paypalLogId)?>"><?php _e('Reconcile using PayPal Mass Payment results file ... ', 'affiliates-manager');?></a><br/><br/>
					</div>

				</td>
			</tr>
		<?php } else if ($massPayment->status == 'failed') { ?>
			<tr>
				<th style="vertical-align:top"><?php _e('Errors', 'affiliates-manager');?></th>
				<td>
				<?php foreach ($massPayment->errors as $error) { ?>
					<strong><?php _e('Code:', 'affiliates-manager');?></strong> <?php echo $error->getCode()?><br/>
					<strong><?php _e('Message:', 'affiliates-manager');?></strong> <?php echo $error->getLongMessage()?><br/>
					<strong><?php _e('Severity:', 'affiliates-manager');?></strong> <?php echo $error->getSeverityCode()?><br/>
					<br/>
				<?php } ?>
				</td>
			</tr>
		<?php } ?>


	</table>

	<h3><?php _e('Associated Transactions', 'affiliates-manager');?></h3>
	<table class="widefat" style="width: auto">
		<thead>
		<tr>

			<th width="25"><?php _e('ID', 'affiliates-manager');?></th>
			<th width="100"><?php _e('Date Occurred', 'affiliates-manager');?></th>
			<th width="150"><?php _e('Affiliate', 'affiliates-manager');?></th>
			<th width="150"><?php _e('PayPal E-Mail', 'affiliates-manager');?></th>
			<th width="100"><?php _e('Status', 'affiliates-manager');?></th>
			<th><?php _e('Description', 'affiliates-manager');?></th>
			<th width="100"><?php _e('Amount', 'affiliates-manager');?></th>
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
		</tr>
		<?php } ?>

		</tbody>
	</table>
</div>