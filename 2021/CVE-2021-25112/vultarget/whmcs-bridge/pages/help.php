<h2>The Bridge Page</h2>
<br/>

    <div class="alert info">
        A WHMCS front end page has been created on your WordPress site.<br/>
        This page is the main interaction page between WordPress and WHMCS.<br/><br/>
        The full url is:
            <a href="<?php echo cc_whmcs_bridge_home($home,$pid);?>"><code><?php echo cc_whmcs_bridge_home($home,$pid);?></code></a><br/><br>
        You can edit the page link by editing the page and changing the permalink.<br>
        <strong>Do not delete this page!</strong></p>
    </div>

<br/>

<h2>Pro Shortcodes</h2>
<p>
    Logged in status, cart totals, product links, page links, domain register forms and more.
    <br><a target="_blank" href="https://i-plugins.com/shortcodes/">Click here to view information on the shortcodes included in Pro from version 5.0.</a>
</p>


<h2>Check my installation</h2>
<p>
    <button class="button-positive" onclick="jQuery('#whmcs-check-results').html('');
                                    jQuery('#whmcs-check').show();
                                    jQuery.post(ajaxurl, { action: 'check_bridge', nonce: '<?= wp_create_nonce('whmcs_bridge_check_bridge') ?>' }, function(data) { jQuery('#whmcs-check-results').html(data); jQuery('#whmcs-check').hide(); }); return false;">
        Check for problems with my setup
    </button>
</p>

<div id="whmcs-check" style="display:none">Checking your setup, please wait...</div>
<div id="whmcs-check-results"></div>

<form method="post" style="display:none" id="whmcs-check-form">
    <input type="hidden" name="bridge_action" value="check"/>
</form>
<br/>

<?php
if (defined("CC_WHMCS_BRIDGE_SSO_PLUGIN") && file_exists(WP_PLUGIN_DIR.'/whmcs-bridge-sso/pages/help.php')):
    require(WP_PLUGIN_DIR.'/whmcs-bridge-sso/pages/help.php');
else:
    ?>

<?php endif ?>