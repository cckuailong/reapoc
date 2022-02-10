<?php
if (defined("CC_WHMCS_BRIDGE_SSO_PLUGIN") && file_exists(WP_PLUGIN_DIR.'/whmcs-bridge-sso/pages/sync.php'))
    require(WP_PLUGIN_DIR.'/whmcs-bridge-sso/pages/sync.php');
else
    echo 'Sync is a Pro feature.';
