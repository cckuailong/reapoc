<div class="wrap">

	<h2>PayPal Mass Pay</h2>
	<h3>Payment Submitted</h3>

	<div class="wpam-success-tip">
		<p>Success! Your Mass Payment was submitted to PayPal. Your reference ID is <strong><?php echo $this->viewData['response']->getCorrelationId()?></strong></p>
		<p>Please note that the payment has been applied to your affiliates as a <strong>PENDING</strong> payment.</p>
		<p>PayPal has not yet reported whether or not these payments were successful, and they need to be reconciled properly. You may <a href="<?php echo admin_url('admin.php?page=wpam-payments&step=view_payment_detail&id='.$this->viewData['ppLogId'])?>">view this pending payment here.</a></p>
	</div>
</div>