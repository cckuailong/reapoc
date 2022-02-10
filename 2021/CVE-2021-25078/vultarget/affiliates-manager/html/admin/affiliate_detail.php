<?php
$model = $this->viewData['affiliate'];
$user = $this->viewData['user'];
?>
<style type="text/css">
	.ui-progressbar-value {
		background-image: url(<?php echo WPAM_URL . "/images/pbar-ani.gif"?>);
		height: 22px;
	}

</style>

<script type="text/javascript">

	jQuery(document).ready(function() {

		var transactionType;

		jQuery("#tabs").tabs({
			cookie: {
				  name: 'wpam_detail_tab'
			}
		});


		function approveConfirmClicked() {
			var bountyType = jQuery("#_ddBountyType").val();
			var bountyAmount = jQuery("#_txtBountyAmount").val();

			doJsonRequest(
				{
					'handler' : 'approveApplication',
					'affiliateId' : <?php echo $model->affiliateId?>,
					'bountyType' : bountyType,
					'bountyAmount' : bountyAmount
				},
				jsonFinished
			);
		}

		function validateApproveForm()
		{
			var val = jQuery("#_txtBountyAmount").val();

			if (isNaN(val) || jQuery.trim(val).length == 0)
			{
				jQuery("label[for=_txtBountyAmount]").css('color', '#f00');
				return false;
			}
			jQuery("label[for=_txtBountyAmount]").css('color', '#000');
			return true;
		}

		function markLabelBad(inputId)
		{
			jQuery("label[for=" + inputId + "]").addClass('wpam_form_error');
		}
		function markLabelOk(inputId)
		{
			jQuery("label[for=" + inputId + "]").removeClass('wpam_form_error');
		}

		function validateAdjustmentForm()
		{
			var val = jQuery("#txtAdjustmentAmount").val();
			if (isNaN(val) || jQuery.trim(val).length == 0)
			{
				markLabelBad('txtAdjustmentAmount');
				return false;
			}
			markLabelOk('txtAdjustmentAmount');
			return true;
		}

		function validatePayoutForm()
		{
			var val = jQuery("#txtPayoutAmount").val();
			if (isNaN(val) || jQuery.trim(val).length == 0)
			{
				markLabelBad('txtPayoutAmount');
				return false;
			}
			markLabelOk('txtPayoutAmount');
			return true;
		}


		function jsonFinished(data)
		{
			if (data['status'] == 'OK')
			{
				location.reload();
			}
			else
			{
				jQuery("#errorMsg").html(data['message']);
				jQuery("#dialog-error").dialog('open');
				jQuery("#dialog-loading").dialog('close');
			}
		}

		function showLoad()
		{
			jQuery("#dialog-loading").dialog("open");
			jQuery("#progressbar").show();

			jQuery(".ui-dialog-titlebar").hide();
		}

		function showConfirmDialog(message, confirmText, confirmCallback)
		{
			var buttons = new Object;
			var cancelText = "<?php _e( 'Cancel', 'affiliates-manager' ) ?>";
			buttons[cancelText] = function() { jQuery(this).dialog('close'); };
			buttons[confirmText] = function() {
				jQuery(this).dialog('close');
				confirmCallback();
			};
			jQuery("#confirmMessage").html(message);
			jQuery("#dialog-confirm").dialog('option', 'buttons', buttons);
			jQuery("#dialog-confirm").dialog('open');
		}

		function doJsonRequest(args, successCallback)
		{
			args.action = 'wpam-ajax_request';
			jQuery.getJSON(ajaxurl, args, successCallback);
			showLoad();
		}


		function declinedBlockReapplyClicked() {

		}

		function declinedNoBlockReapplyClicked() {

		}
		function declineConfirmClicked() {
			jQuery('#dialog-confirm').dialog('option', 'buttons', [
				{
				  text : '<?php echo sprintf( __( 'YES, Block all applications from %s', 'affiliates-manager' ), $model->email ) ?>',
				  click : function() {
					doJsonRequest({
						handler : 'blockApplication',
						affiliateId : <?php echo $model->affiliateId?>
					}, jsonFinished);
				  }
				},
				{
				  text: '<?php _e( 'NO, They may re-apply', 'affiliates-manager' ) ?>',
				  click : function() {
					doJsonRequest({
						handler : 'declineApplication',
						affiliateId : <?php echo $model->affiliateId?>
					}, jsonFinished);
				  }
				} ]
			);
			jQuery("#confirmMessage").html("<?php _e( 'Do you want to block all future applications from this email address, or allow them to sign up at a later date?', 'affiliates-manager' ) ?>");
			jQuery("#dialog-confirm").dialog('open');



		}

		function activateConfirmClicked()
		{
			doJsonRequest({
				handler : 'activateAffiliate',
				affiliateId : <?php echo $model->affiliateId?>
			}, jsonFinished);
		}

		function deactivateConfirmClicked() {
			doJsonRequest({
				handler : 'deactivateAffiliate',
				affiliateId : <?php echo $model->affiliateId?>
			}, jsonFinished);
		}

		function applyAdjustmentConfirmClicked() {
			var adjustmentAmount = jQuery("#txtAdjustmentAmount").val();
			var adjustmentDescription = jQuery("#txtAdjustmentDescription").val();
			doAddTransaction('adjustment', adjustmentAmount, adjustmentDescription);
		}

		function applyPayoutConfirmClicked(amount) {
			var adjustmentDescription = '<?php _e( 'Payout', 'affiliates-manager' ) ?>';
			doAddTransaction('payout', amount, adjustmentDescription);
		}

		function doAddTransaction(type, amount, description)
		{
			doJsonRequest({
				handler: 'addTransaction',
				affiliateId: <?php echo $model->affiliateId?>,
				amount: amount,
				description: description,
				type: type
			}, jsonFinished)
		}


		jQuery("#dialog-confirm").dialog({
			resizable: false,
			height: 300,
			width: 400,
			modal:true,
			draggable: true,
			autoOpen: false
		});
		jQuery("#dialog-error").dialog({
			resizable: false,
			height: 250,
			autoOpen: false,
			modal: true,
			draggable: false,
			buttons: [ {
				  text : '<?php _e( 'OK', 'affiliates-manager' ) ?>',
				  click : function() { jQuery(this).dialog('close'); }
			} ]
		});
		jQuery("#dialog-loading").dialog({
			resizable: false,
			height: 250,
			width: 500,
			closeOnEscape: false,

			modal:true,
			draggable:false,
			autoOpen:false
		});


                //Add Cancel / OK buttons
                jQuery( '#dialog-approveForm table' ).after( '<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix" style="border:none"><div class="ui-dialog-buttonset"><button type="button" name="ok" class="ok ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text"><?php _e( 'OK', 'affiliates-manager' ) ?></span></button><button type="button" name="cancel" class="cancel ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text"><?php _e( 'Cancel', 'affiliates-manager' ) ?></span></button></div></div>' );

                //Close thickbox on cancel
                jQuery('#dialog-approveForm button.cancel').click(function(){
                        tb_remove();
                });

                //Move on if "OK"
                jQuery('#dialog-approveForm button.ok').click(function(){
                    if (validateApproveForm())
					{
                                            tb_remove();
						showConfirmDialog(
							'<?php _e( 'Are you sure you wish to approve this affiliate?', 'affiliates-manager' ) ?>',
							'<?php _e( 'Yes, approve.', 'affiliates-manager' ) ?>',
							approveConfirmClicked
						);
					}
                });


		function getFormattedMoneyHtml(money)
		{
			var formattedMoney = '<?php echo WPAM_MoneyHelper::getDollarSign() ?>' + money.formatMoney(2,thousandsSeparator,decimalPoint); // "$3,543.76"
			var amountSpan = jQuery("<span>").css("font-size", "1.5em").html(formattedMoney);

			if (money < 0)
				amountSpan.addClass("negativeMoney");
			else
				amountSpan.addClass("positiveMoney");

			return jQuery('<div>').append(amountSpan.clone()).remove().html();
		}

		jQuery("#dialog-apply-adjustment").dialog({
			resizable: false,
			height: 300,
			width: 500,
			modal: true,
			draggable: true,
			autoOpen: false,
			buttons: [
				{
				  text: '<?php _e( 'Cancel', 'affiliates-manager' ) ?>',
				  click : function() { jQuery(this).dialog('close'); }
				},
				{
				  text: '<?php _e( 'Apply', 'affiliates-manager' ) ?>',
				  click : function() {
					if (validateAdjustmentForm())
					{
						var amount = parseFloat(jQuery("#txtAdjustmentAmount").val());
						var currentBalance = <?php echo $this->viewData['accountStanding']?>;
						var newBalance = currentBalance + amount;

						showConfirmDialog(
							'<?php _e( 'Are you sure you wish to apply an adjustment of ', 'affiliates-manager' ) ?>' + getFormattedMoneyHtml(amount) + '<?php _e( ' to this account? The new balance will be ', 'affiliates-manager' ) ?>' + getFormattedMoneyHtml(newBalance),
							'<?php _e( 'Yes, apply it.', 'affiliates-manager' ) ?>',
							applyAdjustmentConfirmClicked
						);
					}
				  }
			    } ]
		});

		jQuery("#dialog-apply-payout").dialog({
			resizable: false,
			height: 300,
			width: 500,
			modal: true,
			draggable: true,
			autoOpen: false,
			buttons: [
				{
				  text : '<?php _e( 'Cancel', 'affiliates-manager' ) ?>',
				  click : function() { jQuery(this).dialog('close'); }
				},
				{
				  text : '<?php _e( 'Apply', 'affiliates-manager' ) ?>',
				  click : function() {
					if (validatePayoutForm())
					{
						var selected = jQuery("input[name=payoutAmountType]:checked").val();
						var amount;

						if (selected == 'currentBalance')
						{
							amount = <?php echo $this->viewData['accountStanding'] * -1?>;
						}
						else
						{
							amount = parseFloat(jQuery("#txtPayoutAmount").val()) * -1;
						}

						var currentBalance = <?php echo $this->viewData['accountStanding']?>;
						var newBalance = currentBalance + amount;

						showConfirmDialog(
							'<?php _e( 'Are you sure you wish to apply a payout of ', 'affiliates-manager' ) ?>' + getFormattedMoneyHtml(amount*-1) + '<?php _e( ' to this account? The new balance will be ', 'affiliates-manager' ) ?>' + getFormattedMoneyHtml(newBalance),
							'<?php _e( 'Yes, apply it.', 'affiliates-manager' ) ?>',
							function() { applyPayoutConfirmClicked(amount); }
						);
					}
				}
			} ]
		});

		jQuery("input[name=payoutAmountType]").change(function() {
			var selected = jQuery("input[name=payoutAmountType]:checked").val();

			if (selected == 'currentBalance')
			{
				jQuery("#txtPayoutAmount").hide();
			}
			else if (selected == 'otherAmount')
			{
				jQuery("#txtPayoutAmount").show();
			}
		});

		jQuery("#applyAdjustmentButton").click(function() {
			jQuery("#txtAdjustmentAmount").val("0");
			jQuery("#dialog-apply-adjustment").dialog('open');
		});
		jQuery("#applyPayoutButton").click(function() {
			jQuery("#dialog-apply-payout").dialog('open');
		});
/*
		jQuery("#approvebutton").click(function() {
			jQuery("#dialog-approveForm").dialog('open');
		});
*/
		jQuery("#declinebutton").click(function() {
			showConfirmDialog('<?php _e( 'Are you sure you wish to decline this affiliate?', 'affiliates-manager' ) ?>', '<?php _e( 'Yes, DECLINE.', 'affiliates-manager' ) ?>', declineConfirmClicked);
		});

		jQuery("#blockButton").click(function() {
			showConfirmDialog('<?php _e( 'Are you sure you wish to block this affiliate?', 'affiliates-manager' ) ?>', '<?php _e( 'Yes, BLOCK.', 'affiliates-manager' ) ?>', function() {
				doJsonRequest({
					handler : 'blockApplication',
					affiliateId : <?php echo $model->affiliateId?>
				}, jsonFinished);

			});
		});

		jQuery("#unblockButton").click(function() {
			showConfirmDialog("<?php _e( 'Are you sure you wish to unblock this affiliate?<br>User will become DECLINED.', 'affiliates-manager' ) ?>", '<?php _e( 'Yes, UNBLOCK.', 'affiliates-manager' ) ?>', function() {
				doJsonRequest({
					handler : 'declineApplication',
					affiliateId : <?php echo $model->affiliateId?>
				}, jsonFinished);

			});

		});

		jQuery("#activateButton").click(function() {
			showConfirmDialog('<?php _e( 'Are you sure you wish to activate this affiliate?', 'affiliates-manager' ) ?>', '<?php _e( 'Yes, ACTIVATE.', 'affiliates-manager' ) ?>', activateConfirmClicked);
		});

		jQuery("#deactivateButton").click(function() {
			showConfirmDialog('<?php _e( 'Are you sure you wish to deactivate this affiliate?', 'affiliates-manager' ) ?>', '<?php _e( 'Yes, DEACTIVATE.', 'affiliates-manager' ) ?>', deactivateConfirmClicked);
		});

		jQuery("#_ddBountyType").change(function() {
			var type = jQuery(this).val();
			if (type == 'fixed')
			{
				jQuery('#_lblBountyAmount').html(currencyL10n.fixedLabel);
			}
			else if (type == 'percent')
			{
				jQuery('#_lblBountyAmount').html(currencyL10n.percentLabel);
			}
		})
	});
</script>

<div id="dialog-approveForm" style="display:none">
	<h2>
		<?php _e( 'Approve Application', 'affiliates-manager' ) ?>
	</h2>

	<table>
		<tr>
			<td>
				<label for="_ddBountyType"><?php _e( 'Bounty Type', 'affiliates-manager' ) ?></label>
			</td>
			<td>
				<select id="_ddBountyType" name="ddBountyType">
					<option value="percent"><?php _e( 'Percentage of Sales', 'affiliates-manager' ) ?></option>
					<option value="fixed"><?php _e( 'Fixed Amount per Sale', 'affiliates-manager' ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label id="_lblBountyAmount" for="_txtBountyAmount"><?php _e( 'Bounty Rate (% of Sale)', 'affiliates-manager' ) ?></label>
			</td>
			<td>
				<input type="text" id="_txtBountyAmount" name="txtBountyAmount" size="5"/>
			</td>
		</tr>
	</table>

</div>


<div id="dialog-loading" style="display:none">
	<div style="text-align: center"><?php _e( 'Updating, please wait ... ', 'affiliates-manager' ) ?></div><br />
	<div id="progressbar" class="ui-progressbar-value">

	</div>
</div>
<div id="dialog-confirm" title="<?php _e( 'Are you sure?', 'affiliates-manager' ) ?>" style="display: none">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><span id="confirmMessage"><?php _e( 'approve', 'affiliates-manager' ) ?></span></p>
</div>
<div id="dialog-error" title="<?php _e( 'Error', 'affiliates-manager' ) ?>" style="display: none">
	<p><?php _e( 'ERROR:', 'affiliates-manager' ) ?> <span id="errorMsg"></span></p>
</div>

<div id="dialog-apply-adjustment" title="<?php _e( 'Apply Adjustment ', 'affiliates-manager' ) ?>" style="display: none">
	<table>
		<tbody>
		<tr>
			<td width="150"><label for="txtAdjustmentAmount"><?php _e( 'Amount ', 'affiliates-manager' ) ?></label></td>
			<td>
				<input type="text" id="txtAdjustmentAmount" name="txtAdjustmentAmount" size="10"/>
			</td>
		</tr>
		<tr>
			<td><label for="txtAdjustmentDescription"><?php _e( 'Description ', 'affiliates-manager' ) ?></label></td>
			<td>
				<input type="text" id="txtAdjustmentDescription" name="txtDescription" size="30" />
			</td>
		</tr>
		</tbody>
	</table>
</div>

<div id="dialog-apply-payout" title="<?php _e( 'Apply Payout ', 'affiliates-manager' ) ?>" style="display: none">
	<table>
		<tbody>
		<tr>
			<td width="150" style="vertical-align:top"><label for="txtAdjustmentAmount"><?php _e( 'Payout Amount ', 'affiliates-manager' ) ?></label></td>
			<td>
				<input name="payoutAmountType" type="radio" id="rbPayoutCurrentBalance" value="currentBalance" checked="checked" />
				<label for="rbPayoutCurrentBalance"><?php _e( 'Current balance', 'affiliates-manager' ) ?> (<?php echo WPAM_MoneyHelper::getDollarSign(), $this->viewData['accountStanding']?>)</label><br />

				<input name="payoutAmountType" type="radio" id="rbPayoutOtherAmount" value="otherAmount">
				<label for="rbPayoutOtherAmount"><?php _e( 'Other amount', 'affiliates-manager' ) ?></label><br>
				<input type="text" id="txtPayoutAmount" name="txtPayoutAmount" size="10" style="display: none;" value="<?php echo sprintf("%01.2f", $this->viewData['accountStanding'])?>"/>
			</td>
		</tr>
		</tbody>
	</table>
</div>

<div class="wrap">
<h2><?php _e( 'Affiliate:', 'affiliates-manager' ) ?> <?php echo $model->firstName . " " . $model->lastName?></h2>

<br /><br/>

<div id="tabs">
    <ul>
        <li><a href="#fragment-1"><span><?php _e( 'Overview', 'affiliates-manager' ) ?></span></a></li>
        <li><a href="#fragment-2"><span><?php _e( 'Account Finances', 'affiliates-manager' ) ?></span></a></li>
		<li><a href="#fragment-3"><span><?php _e( 'Information', 'affiliates-manager' ) ?></span></a></li>
        <li><a href="#fragment-4"><span><?php _e( 'Affiliate Links', 'affiliates-manager' ) ?></span></a></li>
<?php if (get_option (WPAM_PluginConfig::$AffEnableImpressions)) { ?>
        <li><a href="#fragment-5"><span><?php _e( 'Impressions', 'affiliates-manager' ) ?></span></a></li>
<?php } ?>
    </ul>
    <div id="fragment-1">
		<div class="buttonsBar">
<?php
if ($model->isPending())
{
?>
				<a class="button-secondary thickbox" id="approvebutton" href="#TB_inline?height=300&width=500&inlineId=dialog-approveForm"><?php _e( 'Approve', 'affiliates-manager' ) ?></a>
				<a id="declinebutton" class="button-secondary"><?php _e( 'Decline', 'affiliates-manager' ) ?></a>
<?php } else if ($model->isConfirmed() || $model->isInactive()) { ?>
				<a class="button-secondary" id="activateButton"><?php _e( 'Activate', 'affiliates-manager' ) ?></a>
<?php } else if ($model->isActive()) { ?>
				<a class="button-secondary" id="deactivateButton"><?php _e( 'Dectivate', 'affiliates-manager' ) ?></a>
<?php } else if ($model->isBlocked()) { ?>
				<a class="button-secondary" id="unblockButton"><?php _e( 'Unblock', 'affiliates-manager' ) ?></a>
<?php } else if ($model->isDeclined()) { ?>
				<a class="button-secondary" id="blockButton"><?php _e( 'Block', 'affiliates-manager' ) ?></a>
<?php } ?>
		</div>
		<table class="widefat">
			<thead>
				<tr>
					<th colspan="2"><?php _e( 'General', 'affiliates-manager' ) ?></th>
				</tr>
			</thead>
			<tbody>
				<tr><td width="200"><?php _e( 'Date Applied', 'affiliates-manager' ) ?></td><td><?php echo date("m/d/Y H:i:s", $model->dateCreated)?></td></tr>
				<tr><td><?php _e( 'Affiliate Status', 'affiliates-manager' ) ?></td><td><span class="status_<?php echo $model->status?>"><?php echo wpam_format_status( $model->status ) ?></span><br /><?php echo wpam_get_status_desc($model->status)?></td></tr>
				<tr>
					<td width="200">
						<?php _e( 'Account Standing', 'affiliates-manager' ) ?>
					</td>
					<td>
						<div style="margin: 5px; font-size: 2.0em;">
							<?php echo wpam_format_money($this->viewData['accountStanding'])?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>





		<br /><br />

		<?php
		if ($model->isActive() || $model->isConfirmed() || $model->isApproved())
		{
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th colspan="2"><?php _e( 'Payment Details', 'affiliates-manager' ) ?></th>
				</tr>
			</thead>
			<tbody>
		<tr><td width="200"><?php _e( 'Bounty Type', 'affiliates-manager' ) ?></td><td><?php echo $model->getBountyType() ?></td></tr>
				<tr><td><?php _e( 'Bounty Amount', 'affiliates-manager' ) ?></td><td><?php echo $model->bountyAmount?></td></tr>
			<?php if ($model->isConfirmed() || $model->isActive()) { ?>
			<tr><td><?php _e( 'Payment Method', 'affiliates-manager' ) ?></td><td><?php echo $model->getPaymentMethod() ?></td></tr>
				<?php if ($model->paymentMethod === 'paypal') { ?>
					<tr><td><?php _e( 'Paypal E-Mail', 'affiliates-manager' ) ?></td><td><?php echo $model->paypalEmail?></td></tr>
				<?php } else if ($model->paymentMethod === 'check') { ?>
					<tr><td><?php _e( 'Make Check Out To', 'affiliates-manager' ) ?></td><td><?php echo $model->nameOnCheck?></td></tr>
				<?php } ?>
			<?php } ?>
			</tbody>
		</table>
		<br/><br/>

		<table class="widefat">
			<thead>
				<tr>
					<th colspan="2"><?php _e( 'Statistics', 'affiliates-manager' ) ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (get_option (WPAM_PluginConfig::$AffEnableImpressions)) { ?>
				<tr><td width="200"><?php _e( 'Impressions', 'affiliates-manager' ) ?></td><td><?php echo $this->viewData['impressionCount'] ?></td></tr>
				<?php } ?>
				<tr><td width="200"><?php _e( 'Visits', 'affiliates-manager' ) ?></td><td><?php echo $this->viewData['visitCount'] ?></td></tr>
				<tr><td width="200"><?php _e( 'Purchases', 'affiliates-manager' ) ?></td><td><?php echo $this->viewData['purchaseCount'] ?></td></tr>
			</tbody>
		</table>
		<br/><br/>

		<?php }?>

    </div>
	<div id="fragment-2">
		<div class="accountSummaryBox">
			<div style="font-size: 1.5em">
				<?php _e( 'Balance:', 'affiliates-manager' ) ?> <?php echo wpam_format_money($this->viewData['accountStanding'])?>
			</div>
			<div style="margin-left: 5px; margin-top: 20px; padding: 5px; border: 1px solid gray; width: 300px;">
				<?php _e( 'Earnings:', 'affiliates-manager' ) ?> <?php echo wpam_format_money($this->viewData['accountCredits'])?><br/>
				<?php _e( 'Payments:', 'affiliates-manager' ) ?> <?php echo wpam_format_money($this->viewData['accountDebits'])?><br/>
				<?php _e( 'Adjustments:', 'affiliates-manager' ) ?> <?php echo wpam_format_money($this->viewData['accountAdjustments'])?><br/>
			</div>
		</div>
		<div class="buttonsBar">
			<a id="applyPayoutButton" class="button-secondary"><?php _e( 'Apply Payout', 'affiliates-manager' ) ?></a>
			<a id="applyAdjustmentButton" class="button-secondary"><?php _e( 'Apply Manual Adjustment', 'affiliates-manager' ) ?></a>
		</div>
	  <?php include WPAM_BASE_DIRECTORY . '/html/transaction_table.php'; ?>
  </div>
    <div id="fragment-3">
	  <?php include WPAM_BASE_DIRECTORY . '/html/contact_info.php'; ?>
    </div>
	<div id="fragment-4">
		<div class="creativesBox">
		<table class="widefat">
			<thead>
			<tr>
				<th width="50"><?php _e( 'Creative', 'affiliates-manager' ) ?></th>
				<th width="25"><?php _e( 'Type', 'affiliates-manager' ) ?></th>
				<th width="200"><?php _e( 'Link', 'affiliates-manager' ) ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $this->viewData['creatives'] as $creative ) {
				$linkBuilder = new WPAM_Tracking_TrackingLinkBuilder($model, $creative);
				$link = $linkBuilder->getImpressionHtmlSnippet();
				?>
			<tr class="creative-<?php echo $creative->status?>">
				<td><?php echo $creative->name?></td>
				<td><?php echo $creative->type?></td>
				<td><input type="text" size="50" value='<?php echo htmlentities( $link, ENT_QUOTES ) ?>' /></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
  </div>
</div>

<?php if (get_option (WPAM_PluginConfig::$AffEnableImpressions)) { ?>
    <div id="fragment-5">
	  <?php include WPAM_BASE_DIRECTORY . '/html/impressions_table.php'; ?>
    </div>
<?php } ?>

</div>
