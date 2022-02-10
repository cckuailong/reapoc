<div class="wrap">

	<h2>PayPal Mass Pay</h2>
	<h3>Payment Failed</h3>

	<div class="wpam-error-tip">
		PayPal rejected your mass payment. Error details:<br/>
		<?php foreach ($this->viewData['response']->getErrors() as $error) { ?>
			<br /><strong>Code:</strong> <?php echo $error->getCode()?><br/>
			<strong>Message:</strong> <?php echo $error->getLongMessage()?><br/>
			<strong>Severity:</strong> <?php echo $error->getSeverityCode()?><br/>
		<?php } ?>
	</div>

</div>