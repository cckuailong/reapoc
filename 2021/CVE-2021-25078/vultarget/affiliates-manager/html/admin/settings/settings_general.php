<?php
    $cookie_duration_documentation = "https://wpaffiliatemanager.com/cookie-duration-use/";
?>
<script type="text/javascript">
jQuery(function($){

	var dialog = {
		resizable: false,
		height: 500,
		width: 500,
		autoOpen: false,
		modal: true,
		draggable: false,
		buttons: [ {
			  text : 'OK',
			  click : function() { $(this).dialog('close'); }
		} ]
	};
	
	$("#tnc_help").dialog(dialog);

	$("#tncInfo").click(function()
	{
		$("#tnc_help").dialog('open');
	});

	$("#cookie_help").dialog(dialog);

	$("#cookieInfo").click(function()
    {
		$("#cookie_help").dialog('open');
    });
	
	$("#email_help").dialog(dialog);

	$(".emailInfo").click(function()
    {
		$("#email_help").dialog('open');
    });

	$("#imp_help").dialog(dialog);

	$("#impInfo").click(function()
	{
		$("#imp_help").dialog('open');
	});
});
</script>

<div id="tnc_help" style="display: none;">
	This is the terms and conditions template that will be used when confirming new affiliates.
	You should review these.  There are special tokens through-out this document that will be replaced
	with special values:<br /><br/>
	<ul style="margin-left: 30px;">
		<li><strong>[site name]</strong>: The name of your site will go here.</li>
		<li><strong>[site url]</strong>: The URL to your website will be placed here.</li>
		<li><strong>[terms url]</strong>: This will be a permanent link to the terms & conditions.</li>
		<li><strong>[payout minimum]</strong>: Will be replaced with the minimum payout amount.</li>
	</ul>
</div>
	 
<div id="cookie_help" style="display: none;">
        <p>
        When a user comes to your site via an affiliate link, a cookie will be set in the users web browser.
        Normally, with a setting of zero (0), the affiliation only lasts as long as the user keeps their web
        browser open.  That is, if they don't purchase something and instead close their browser, the
        affiliation ends.
</p>
<p>
       Setting this to a value other than zero, will allow the cookie to stay around for that many days.  This would
       give credit to the affiliate if a user arrived at your site via an affiliate link, closed their browser, but came
       back later to your site by typing in the URL (rather than visiting through an affiliate link) and purchased something
       (within the specified number of days).
       </p>
       <p>In the affiliate industry a 15-30 day cookie duration is pretty standard. The longer the duration, the more attractive your program is to affiliates.
       </p>
</div>

<div id="email_help" style="display: none;">
        <p>
        The Affiliate Manager sends emails to new affiliates notifying them of their approval status.
By default, WordPress sends these messages as:</p>
<p>WordPress &lt;wordpress@[sitename].com&gt;
<p>You may choose to override the name & address with something more suitable. 
These addresses will only affect emails going to affiliates regarding their approval status.
       </p>
</div>

<div id="imp_help" style="display: none;">
        <p>
        Enabling impressions will cause creative views to be tracked, even if they are not clicked on.
This is done by adding pixel tracking code to affiliate links, which records the affiliate and which
creative was viewed. This can be useful for determining creative click through rates, but can add a
significant burden to the server as far as additional requests and data storage (depending on the
frequency of creative views), which in turn may slow down page loads where a creative is shown.
	</p>
</div>

<table class="form-table">
	<tr>
		<th width="200">
			<label for="txtMinimumPayout">
				<?php _e('Minimum Payout Amount', 'affiliates-manager'); ?>
			</label>
		</th>
		<td>
			<input type="text" size="30" name="txtMinimumPayout" id="txtMinimumPayout" value="<?php echo $this->viewData['request']['txtMinimumPayout']?>" />
		</td>
	</tr>
	<tr>
    	<th width="200">
            <label for="txtCookieExpire"><?php _e('Cookie Duration (days)', 'affiliates-manager');?></label>
			<img id="cookieInfo" style="cursor: pointer;" src="<?php echo WPAM_URL . "/images/info_icon.png"?>" />
        </th>
        <td>
        	<input type="text" size="30" name="txtCookieExpire" id="txtCookieExpire" value="<?php echo $this->viewData['request']['txtCookieExpire']?>" />
                <span><?php printf(__('<a target="_blank" href="%s">Read more on cookie duration here</a>', 'affiliates-manager'), esc_url($cookie_duration_documentation));?></span>
        </td>
	</tr>
 	<tr>
    	<th width="200">
            <label for="txtEmailName"><?php _e('Email name', 'affiliates-manager');?></label>
			<img class="emailInfo" style="cursor: pointer;" src="<?php echo WPAM_URL . "/images/info_icon.png"?>" />
        </th>
        <td>
        	<input type="text" size="30" name="txtEmailName" id="txtEmailName" value="<?php echo $this->viewData['request']['txtEmailName']?>" />
			<span><?php _e('(Leave blank to use WordPress default)', 'affiliates-manager');?></span>
        </td>
	</tr>
 	<tr>
    	<th width="200">
            <label for="txtEmailAddress"><?php _e('Email address', 'affiliates-manager');?></label>
			<img class="emailInfo" style="cursor: pointer;" src="<?php echo WPAM_URL . "/images/info_icon.png"?>" />
        </th>
        <td>
        	<input type="text" size="30" name="txtEmailAddress" id="txtEmailAddress" value="<?php echo $this->viewData['request']['txtEmailAddress']?>" />
			<span><?php _e('(Leave blank to use WordPress default)', 'affiliates-manager');?></span>
        </td>
	</tr>
        
        <tr>
		<th width="200">
			<label for="autoaffapprove">
				<?php _e('Automatically approve a new affiliate', 'affiliates-manager');?>
			</label>
		</th>
		<td>
			<input type="checkbox" id="autoaffapprove" name="autoaffapprove" <?php
			if ($this->viewData['request']['autoaffapprove'])
				echo 'checked="checked"';
			?>/>
		</td>
	</tr>
        
        <tr>
        <th>
                <label for="affBountyType"><?php _e( 'Bounty Type', 'affiliates-manager' ) ?></label>
        </th>
        <td>
                <select id="affBountyType" name="affBountyType">
                        <option value="percent" <?php echo ($this->viewData['request']['affBountyType'] == 'percent' ? 'selected="selected"' : '')?>><?php _e( 'Percentage of Sales', 'affiliates-manager' ) ?></option>
                        <option value="fixed" <?php echo ($this->viewData['request']['affBountyType'] == 'fixed' ? 'selected="selected"' : '')?>><?php _e( 'Fixed Amount per Sale', 'affiliates-manager' ) ?></option>
                </select>
        </td>
        </tr>
        
        <tr>
                <th>
                        <label id="lblaffBountyAmount" for="affBountyAmount"><?php _e( 'Bounty Rate (% of Sale) / Amount', 'affiliates-manager' ) ?></label>
                </th>
                <td>
                        <input type="text" id="affBountyAmount" name="affBountyAmount" size="5" value="<?php echo $this->viewData['request']['affBountyAmount']?>" />
                </td>
        </tr>
        
        <tr>
                <th>
                        <label id="lblaffCurSymbol" for="affCurrencySymbol"><?php _e( 'Currency Symbol', 'affiliates-manager' ) ?></label>
                </th>
                <td>
                        <input type="text" id="affCurrencySymbol" name="affCurrencySymbol" size="5" value="<?php echo $this->viewData['request']['affCurrencySymbol']?>" />
                </td>
        </tr>
        
        <tr>
                <th>
                        <label id="lblaffCurCode" for="affCurrencyCode"><?php _e( 'Currency Code', 'affiliates-manager' ) ?></label>
                </th>
                <td>
                        <input type="text" id="affCurrencyCode" name="affCurrencyCode" size="5" value="<?php echo $this->viewData['request']['affCurrencyCode']?>" />
                </td>
        </tr>
        
        <tr>
		<th width="200">
			<label for="doNotRecordZeroAmtCommission">
				<?php _e('Do Not Record Zero Amount Commission', 'affiliates-manager');?>
			</label>
		</th>
		<td>
			<input type="checkbox" id="doNotRecordZeroAmtCommission" name="doNotRecordZeroAmtCommission" <?php
			if ($this->viewData['request']['doNotRecordZeroAmtCommission'])
				echo 'checked="checked"';
			?>/>
		</td>
	</tr>       

        <tr>
                <th width="200">
                        <label id="lblchkImpressions" for="chkImpressions"><?php _e( 'Enable Impressions', 'affiliates-manager' ) ?></label>
			<img id="impInfo" style="cursor: pointer;" src="<?php echo WPAM_URL . "/images/info_icon.png"?>" />
                </th>
                <td>
                        <input type="checkbox" id="chkImpressions" name="chkImpressions"<?php if($this->viewData['request']['chkImpressions']=='1') echo ' checked="checked"'; ?> value="1"/>
                </td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><label for="enable_debug"><?php echo __('Enable Debug','affiliates-manager'); ?></label></th>
        <td>
        <input name="enable_debug" type="checkbox"<?php if($this->viewData['request']['enable_debug']=='1') echo ' checked="checked"'; ?> value="1"/>
        <p class="description"><?php _e('If checked, debug output will be written to log files. This is useful for troubleshooting post payment failures.', 'affiliates-manager');?></p>
        <p class="description"><?php _e('You can check the debug log file by clicking on the link below (The log file can be viewed using any text editor):', 'affiliates-manager');?></p>
        
        <p><a href="<?php echo WPAM_URL.'/logs/wpam-log.txt'; ?>" target="_blank">wpam-log.txt</a></p>    
        <div class="submit">
            <input type="submit" name="wpam_reset_logfile" class="button" style="color:red" value="<?php _e('Reset Debug Log file', 'affiliates-manager');?>"/> 
            <p class="descripiton"><?php _e("Use it to reset the affiliate manager plugin's log file.", 'affiliates-manager');?></p>
        </div>
        </td></tr>
        
</table>