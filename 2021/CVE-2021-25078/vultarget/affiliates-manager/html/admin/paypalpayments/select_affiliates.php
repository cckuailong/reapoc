<?php
$currency = WPAM_MoneyHelper::getDollarSign();
?>
<script type="text/javascript">
jQuery(function($) {
	$('#checkall').change(function() {
		if ($(this).is(':checked'))
			$("input[type=checkbox][name^=chkAffiliate]").not('[disabled]').attr('checked','checked');
		else
			$("input[type=checkbox][name^=chkAffiliate]").removeAttr('checked');

		$("input[type=checkbox][name^=chkAffiliate]").trigger('change');
	});
	$('input[type=checkbox][name^=chkAffiliate]').change(function() {
		if ($(this).is(':checked')) {
			$(this).closest('tr').addClass('row_selected');
		}
		else {
			$(this).closest('tr').removeClass('row_selected');
		}
		updateTotals();
	});

	$('input[name^=txtAffiliatePaymentAmount]').change(function() {

		var val;
		if (isNaN($(this).val()))
			val = 0;
		else
			val = Number($(this).val());

		$(this).val(val.toFixed(2));
		updateTotals();
	});

	var dates = $( "#from, #to" ).datepicker({
		numberOfMonths: 2,
		onSelect: function( selectedDate ) {
			var option = this.id == "from" ? "minDate" : "maxDate",
			instance = $( this ).data( "datepicker" ),
			date = $.datepicker.parseDate(
				instance.settings.dateFormat ||
				$.datepicker._defaults.dateFormat,
				selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});	

	function updateTotals()
	{
		var elems = $('input[name^=txtAffiliatePaymentAmount]').filter(function() { return $(this).closest('tr').find('input[type=checkbox]').is(':checked'); });
		var total = 0;
		var fees = 0;
		
		elems.each(function() {
			var transaction = Number($(this).val());
			total += transaction;
			fees += Math.min(transaction*0.02, 1.00);
		});

		$('#subTotalCell').html('<?php echo $currency; ?>' + total.toFixed(2));
		$('#paypalFeeCell').html('<?php echo $currency; ?>' + fees.toFixed(2));
		$('#totalCell').html('<?php echo $currency; ?>' + (total+fees).toFixed(2));

		if (total.toFixed(2) <= 0.00)
		{
			$('#btnSubmit').attr('disabled','disabled');
		}
		else
		{
			$('#btnSubmit').removeAttr('disabled');
		}

	}

	$('#reset').click(function() {
		$('#to').val('');
		$('#from').val('');
		$('#dateRange').submit();
	});
	
	updateTotals();
});
</script>
<style type="text/css">
	.row_selected {
		background-color: #efe;
	}
	.row_unselected {
		background-color: #eee;
	}
	.row_disabled {
		color: #aaa;
	}
	.row_inactive {
		background-color: #fee;
	}
</style>


<div class="wrap">
	<h2><?php _e( 'PayPal Mass Pay', 'affiliates-manager' ) ?></h2>
	<h3><?php _e( 'Select Affiliates to Pay', 'affiliates-manager' ) ?></h3>


	<p><?php echo sprintf( __( 'Not showing %s affiliates that do not have a PayPal account on file.', 'affiliates-manager' ), $this->viewData['notShownCount'] ) ?></p>
	<div style="width: 800px;">
	<form method="post" id="dateRange">
        <?php wp_nonce_field('wpam_payments_select_aff_date_range_nonce'); ?>
		<div>
<p><strong><?php _e( 'Date Range:', 'affiliates-manager' ) ?></strong> 
<label for="from"><?php _e( 'From Date', 'affiliates-manager' ) ?></label>
<input type="text" id="from" name="from" value="<?php echo $this->viewData['from']; ?>"/>
<label for="to"><?php _e( 'To Date', 'affiliates-manager' ) ?></label>
<input type="text" id="to" name="to" value="<?php echo $this->viewData['to']; ?>"/>
			 <input type="submit" name="apply" value="<?php _e( 'Apply', 'affiliates-manager' ) ?>" />
			 <input type="button" name="clear" value="<?php _e( 'Clear', 'affiliates-manager' ) ?>" id="reset" />
			 </p>
		</div>
	</form>
	    <form method="POST" action="<?php echo admin_url('admin.php?page=wpam-payments&step=review_affiliates')?>">
                <?php wp_nonce_field('wpam_payments_review_affiliates_nonce'); ?>
		<table class="widefat" style="width: 800px">
			<thead><tr>
				<th width="10" style="padding: 0"><input type="checkbox" id="checkall" checked="checked" /></th>
				<th width="25"><?php _e( 'AID', 'affiliates-manager' ) ?></th>
				<th width="100"><?php _e( 'First Name', 'affiliates-manager' ) ?></th>
				<th width="100"><?php _e( 'Last Name', 'affiliates-manager' ) ?></th>
				<th width="100"><?php _e( 'Company', 'affiliates-manager' ) ?></th>
				<th width="auto"><?php _e( 'PayPal E-Mail', 'affiliates-manager' ) ?></th>
				<th width="50"><?php _e( 'Balance', 'affiliates-manager' ) ?></th>
				<th width="100"><?php _e( 'Payment Amount', 'affiliates-manager' ) ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ($this->viewData['affiliates'] as $affiliate) {?>
																		 
				<tr <?php
				if ( $affiliate->status == 'inactive' )
					echo 'class="row_inactive"';
				elseif ( $affiliate->balance <= 0 )
					echo 'class="row_disabled"';
				elseif ( $affiliate->balance < $this->viewData['minPayout'] )
					echo 'class="row_unselected"';
				else		
					echo 'class="row_selected"'; ?>>
				
					<td><input type="checkbox" name="chkAffiliate[<?php echo $affiliate->affiliateId?>]" <?php
						if ( $affiliate->balance >= $this->viewData['minPayout'] ) {
							echo 'checked="checked"';
						} elseif( $affiliate->balance <= 0 ) {
							echo 'disabled="disabled"';
						}
					 ?>>
					 </td>
					<td><?php echo $affiliate->affiliateId?></td>
					<td><?php echo $affiliate->firstName?></td>
					<td><?php echo $affiliate->lastName?></td>
					<td><?php echo $affiliate->companyName?></td>
					<td style="font-weight: bold"><?php echo $affiliate->paypalEmail?></td>
					<td style="text-align: right"><?php echo wpam_format_money($affiliate->balance)?></td>
					<td><input type="text" name="txtAffiliatePaymentAmount[<?php echo $affiliate->affiliateId?>]" value="<?php echo $affiliate->balance?>" <?php
						if ($affiliate->balance <= 0) {
							echo 'disabled="disabled"';
						}
					?>></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<div>
			<table class="totalsTable" style="position: relative; width: 400px; left: 400px; border: 1px solid #aaa;">
				<tr>
					<td style="width: 250px;">Sub-Total</td>
					<td style="width: 150px;" class="moneyCell" id="subTotalCell"><?php echo wpam_format_money(0, false); ?></td>
				</tr>
				<tr>
					<td><?php echo sprintf( __( 'PayPal Fee<br /><small>2%% per payment, max %s1 per payment</small>', 'affiliates-manager' ), $currency ) ?></td>
					<td class="moneyCell" id="paypalFeeCell"><?php echo wpam_format_money(0, false); ?></td>
				</tr>
				<tr class="totalSeparatorRow"><td colspan="2"></td> </tr>
				<tr class="totalRow">
					<th><?php _e( 'Estimated Total', 'affiliates-manager' ) ?></th>
					<th class="moneyCell" id="totalCell"><?php echo wpam_format_money(0, false); ?></th>
				</tr>
			</table>
			<div style="float: right; margin: 10px;">
				<input type="submit" class="button-primary" id="btnSubmit" name="btnSubmit" value="<?php _e( 'Continue with these payments', 'affiliates-manager' ) ?>" />
			</div>
		</div>
		</form>
	</div>
</div>