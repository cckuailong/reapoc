<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
<h2><?php _e('WP File Manager Contribution', 'wp-file-manager')?></h2>
<?php /* Donation Form */ ?>
<div id="submitdiv" class="postbox" style="padding: 6px; margin-top:20px; border-left: 5px solid #0073aa;">  
    <form name="_xclick" action="https://www.paypal.com/yt/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="mandeep.singh@mysenseinc.com">
    <input type="hidden" name="item_name" value="WP File Manager - Donation">
    <input type="hidden" name="currency_code" value="USD">
    <table style="text-align:center">
<tbody>
<tr>
<th scope="row"><label for="default_email_category"><code>$</code></label></th>
<td>
 <input type="text" name="amount" value="" required="required" placeholder="Enter amount" class="regular-text ltr">
</td>
<td>
 <input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make Donations with Paypal">
</td>
</tr>
</tbody></table> 
<?php _e(apply_filters('the_content','<p class="description"><strong style="color:#006600">Please contribute some donation, to make plugin more stable. You can pay amount of your choice. :) ,Your contribution will help us to make WP File Manager Plugin more stable and more functional.</strong></p>'), 'wp-file-manager')?>  
    </form>
    </div>    
<?php /* End Donation Form */ ?>
</div>