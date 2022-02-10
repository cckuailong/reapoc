<?php
//v2.02.06
if (!function_exists('zing_support_us')) {
    function zing_support_us($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false) {
        if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
        if (get_option('cc_whmcs_bridge_sso_license_key')) $donations=false;
        ?>
        <?php if (!get_option('cc_whmcs_bridge_sso_active')) {?>
            <div class="cc-support-us">
                <h3><i class="fa fa-rocket"></i> Discover WHMCS Bridge Pro</h3><br/>
                <h4><i class="fa fa-code"></i> Short codes: </h4><p>Shortcodes for all of your product info, domain lookups and more (<a href="https://i-plugins.com/shortcodes" target="_blank">details here</a>).</p><br/><br/>
                <h4><i class="fa fa-bolt"></i> Supercharge your bridge with caching: </h4><p>Cache CSS and JS files from WHMCS locally on your WordPress for up to 30 days to cut down on load times and resource usage.</p><br/><br/>
                <h4><i class="fa fa-sign-in"></i> Single sign on: </h4><p>thanks to the single sign-on feature, your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.</p><br /><br />
                <h4><i class="fa fa-language"></i> Multi-lingual WHMCS support: </h4><p>fully integrated with qtranslate.</p><br /><br />
                <h4><i class="fa fa-wifi"></i> IP address resolution 'patch': </h4><p>shows your customer's IP address instead of your server's IP address during sign up.</p><br /><br />
                <h4><i class="fa fa-pencil-square-o"></i> Choose your WHMCS portal: </h4><p>Use any template you'd like, even custom templates.</p><br /><br />
                <h4><i class="fa fa-link"></i> Pretty permalinks: </h4><p>display links like http://www.mysite.tld/clientarea/ rather than http://www.mysite.tld/?ccce=clientarea. Also supports knowledgebase, announcement and download links.</p><br /><br />
                <div style="text-align:center; width:100%"><a href="http://i-plugins.com/whmcs-bridge-wordpress-plugin/" target="_blank"><img src="<?php echo plugins_url().'/whmcs-bridge/images/buy_now.png'?>" /></a></div>
            </div>
        <?php }?>

        <div class="cc-support-us">
            <h3><i class="fa fa-ambulance"></i> Get Help!</h3>
            Visit the public <a href="http://wordpress.org/support/plugin/whmcs-bridge" target="_blank">forums</a><br/>
            View our <a href="http://i-plugins.com/whmcs/knowledgebase.php?action=displaycat&catid=1021" target="_blank">knowledgebase</a><br/>
            Pro users can open a <a href="http://i-plugins.com/whmcs-bridge/?ccce=submitticket" target="_blank">support ticket</a>
        </div>

        <div class="cc-support-us">
            <h3><i class="fa fa-wordpress"></i> Support us by rating our plugin on WordPress</h3>
            <a href="http://wordpress.org/extend/plugins/<?php echo $wpPluginName;?>" alt="Rate our plugin">
                <img src="<?php echo $pluginUrl?>images/5-stars-125pxw.png" />
            </a>
            <?php
            $option=$wpPluginName.'-support-us';
            if (get_option($option) == '') {
                update_option($option,time());
            } elseif (isset($_REQUEST['support-us']) && ($_REQUEST['support-us'] == 'hide')) {
                update_option($option,time()+7776000);
            } else {
                if ((time() - get_option($option)) > 1209600) { //14 days
                    if ($donations) echo "<div id='zing-warning' style='background-color:red;color:white;font-size:large;margin:20px;padding:10px;'>Looks like you've been using this plugin for quite a while now. Have you thought about showing your appreciation through a small donation?<br /><br /><a href='http://i-plugins.com/donations'><img src='https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif' /></a><br /><br />If you already made a donation, you can <a href='?page=".$adminLink."&support-us=hide'>hide</a> this message.</div>";
                }
            }
            ?>
        </div>
        <div class="cc-support-us">
            <h3><i class="fa fa-twitter"></i> News Feed</h3>
            <a width="300" height="400" class="twitter-timeline" width="300" href="https://twitter.com/iPluginsNews" data-widget-id="529962276762050560" data-chrome="nofooter transparent">Tweets by @iPluginsNews</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>
    <?php
    }
}
?>