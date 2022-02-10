<?php

/**
 * Warning!!!
 * Don't change any function from here
 *
 */

use WPDM\__\Messages;

global $stabs, $package, $wpdm_package;


function WPDM($global = null){
    if($global){
        global $$global;
        return $$global;
    }
    global $WPDM;
    return $WPDM;
}

/**
 * @param $tablink
 * @param $newtab
 * @param $func
 * @deprecated Deprecated from v4.2, use filter hook 'add_wpdm_settings_tab'
 * @usage Deprecated: From v4.2, use filter hook 'add_wpdm_settings_tab'
 */
function add_wdm_settings_tab($tablink, $newtab, $func)
{
    global $stabs;
    $stabs["{$tablink}"] = array('id' => $tablink, 'icon' => 'fa fa-cog', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=' . $tablink, 'title' => $newtab, 'callback' => $func);
}

/**
 * @param $tabid
 * @param $tabtitle
 * @param $callback
 * @param string $icon
 * @return array
 */
function wpdm_create_settings_tab($tabid, $tabtitle, $callback, $icon = 'fa fa-cog')
{
    return \WPDM\Admin\Menu\Settings::createMenu($tabid, $tabtitle, $callback, $icon);
}


/**
 * @usage Check user's download limit
 * @param $id
 * @return bool
 */
function wpdm_is_download_limit_exceed($id)
{
    return WPDM()->package->userDownloadLimitExceeded($id);
}


/**
 * @param (int|array) $package Package ID (INT) or Complete Package Data (Array)
 * @param string $ext
 * @return string|void
 */
function wpdm_download_url($package, $params = array())
{
    if (!is_array($package)) $package = intval($package);
    $id = is_int($package) ? $package : $package['ID'];
    return WPDM()->package->getDownloadURL($id, $params);
}


/**
 * @usage Check if a download manager category has child
 * @param $parent
 * @return bool
 */

function wpdm_cat_has_child($parent)
{
    $termchildren = get_term_children($parent, 'wpdmcategory');
    if (count($termchildren) > 0) return count($termchildren);
    return false;
}

/**
 * @usage Get category checkbox list
 * @param int $parent
 * @param int $level
 * @param array $sel
 */
function wpdm_cblist_categories($parent = 0, $level = 0, $sel = array())
{
    $cats = get_terms('wpdmcategory', array('hide_empty' => false, 'parent' => $parent));
    if (!$cats) $cats = array();
    if ($parent != '') echo "<ul>";
    foreach ($cats as $cat) {
        $id = $cat->slug;
        $pres = $level * 5;

        if (in_array($id, $sel))
            $checked = 'checked=checked';
        else
            $checked = '';
        echo "<li style='margin-left:{$pres}px;padding-left:0'><label><input id='c$id' type='checkbox' name='file[category][]' value='$id' $checked /> " . $cat->name . "</label></li>\n";
        wpdm_cblist_categories($cat->term_id, $level + 1, $sel);

    }
    if ($parent != '') echo "</ul>";
}

/**
 * @usage Get category dropdown list
 * @param string $name
 * @param string $selected
 * @param string $id
 * @param int $echo
 * @return string
 */
function wpdm_dropdown_categories($name = '', $selected = '', $id = '', $echo = 1)
{
    return wp_dropdown_categories(array('show_option_none' => __( "Select category" , "download-manager" ), 'hierarchical' => 1, 'show_count' => 0, 'orderby' => 'name', 'echo' => $echo, 'class' => 'form-control selectpicker', 'taxonomy' => 'wpdmcategory', 'hide_empty' => 0, 'name' => $name, 'id' => $id, 'selected' => $selected));

}


/**
 * @usage Post with cURL
 * @param $url
 * @param $data (array)
 * @param $headers (array)
 * @return bool|mixed|string
 */
function wpdm_remote_post($url, $data, $headers = [])
{

    $response = wp_remote_post($url, array(
            'method' => 'POST',
            'sslverify' => false,
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
            'body' => $data,
            'cookies' => array()
        )
    );
    $body = wp_remote_retrieve_body($response);
    return $body;
}

/**
 * @usage Get with cURL
 * @param $url
 * @param $headers (array)
 * @return bool|mixed|string
 */
function wpdm_remote_get($url, $headers = [])
{
    $content = "";
    $response = wp_remote_get($url, array('timeout' => 5, 'sslverify' => false, 'headers' => $headers));
    if (is_array($response)) {
        $content = $response['body'];
    } else
        $content = Messages::error($response->get_error_message(), -1);
    return $content;
}


function is_valid_license_key()
{
    $key = isset($_POST['_wpdm_license_key']) ? $_POST['_wpdm_license_key'] : get_option('_wpdm_license_key');
    if($key === '') return false;
    $post = isset($_POST['_wpdm_license_key']) ? 1 : 0;
    $domain = strtolower(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if($post === 0) {
        $data = unserialize(base64_decode(get_option("wpdm_{$domain}")));
        if (is_array($data)) {
            //$data = unserialize(base64_decode(file_get_contents(WPDM_CACHE_DIR . "wpdm_{$domain}")));
            if ($data[0] == md5($domain . $key) && $data[1] > time())
                return true;
            else
                delete_option("wpdm_{$domain}");
        }
        if ((int)get_option('__wpdm_nlc') > time()) return false;
    }
    $res = wpdm_remote_post('https://www.wpdownloadmanager.com/', array('wpdmLicense' => 'validate', 'domain' => $domain, 'licenseKey' => $key, 'productId' => 'wpdmpro'));
    $res = json_decode($res);
    delete_option('__wpdm_core_update_check');
    if ($res->status === 'VALID') {
        //file_put_contents(WPDM_CACHE_DIR . "wpdm_{$domain}", base64_encode(serialize(array(md5($domain . $key), strtotime("+180 days")))));
        update_option("wpdm_{$domain}", base64_encode(serialize(array(md5($domain . $key), $res->expire))), false);
        update_option("__wpdm_license_det", json_encode($res), false);
        update_option("__wpdm_nlc", $res->expire, false);
        return true;
    }
    update_option("__wpdm_nlc", strtotime('+30 days'), false);
    if (get_option('settings_ok') == '')
        update_option('settings_ok', strtotime('+30 days'), false);
    else {
        $page = isset($_GET['page']) ? $_GET['page'] : "";
        $time = (int)get_option('settings_ok');
        if ($time < time() && $page == 'settings' && (!isset($_GET['tab']) || $_GET['tab'] != 'license')) {
            die("<script>location.href='edit.php?post_type=wpdmpro&page=settings&tab=license';</script>");
        }
    }
    return false;
}

function wpdm_access_token(){
    $at = get_option("__wpdm_access_token", false);
    if($at)
        return $at;
    if(get_option('__wpdm_suname') != '') {
        $auth = array('login' => get_option('__wpdm_suname'), 'pass' => get_option('__wpdm_supass'));
        $auth = base64_encode(serialize($auth));
        $access_token = wpdm_remote_get('https://www.wpdownloadmanager.com/?wpdm_api_req=getAccessToken&__wpdm_auth=' . $auth);
        $access_token = json_decode($access_token);
        //wpdmdd($access_token);
        if (isset($access_token->access_token)) {
            update_option("__wpdm_access_token", $access_token->access_token, false);
            return $access_token->access_token;
        }
    }
    return '';
}


function wpdm_plugin_data($dir)
{
    $plugins = get_plugins();
    foreach ($plugins as $plugin => $data) {
        $data['plugin_index_file'] = $plugin;
        $plugin = explode("/", $plugin);
        if ($plugin[0] == $dir) return $data;
    }
    return false;
}

function wpdm_plugin_update_email($plugin_name, $version, $update_url)
{

    $admin_email = get_option('admin_email');
    $hash = "__wpdm_" . md5($plugin_name . $version);
    $sent = get_option($hash, false);
    if (!$sent) {

        $message = 'New version available. Please update your copy.<br/><table class="email" style="width: 100%" cellpadding="5px"><tr><th>Plugin Name</th><th>Version</th></tr><tr><td>' . $plugin_name . '</td><td>' . $version . '</td></tr></table><div style="padding-top: 10px;"><a style="display: block;text-align: center" class="button" href="' . $update_url . '">Update Now</a></div>';

        $params = array(
                'subject' => sprintf(__("[%s] Update Available"), $plugin_name, 'download-manager'),
            'to_email' => get_option('admin_email'),
            'from_name' => 'WordPress Download Manager',
            'from_email' => 'support@wpdownloadmanager.com',
            'message' => $message
        );

        \WPDM\__\Email::send("default", $params);
        update_option($hash, 1, false);

    }
}

function wpdm_ldetails($license_key = null){
    $lic_det = get_option('__wpdm_license_det');
    $lic_det = json_decode($lic_det);
    return $lic_det;
}

if (!isset($_REQUEST['P3_NOCACHE'])) {

    $files = scandir(dirname(__FILE__) . '/Modules/');
    foreach ($files as $file) {
        $tmpdata = explode(".", $file);
        if ($file != '.' && $file != '..' && !@is_dir($file) && end($tmpdata) == 'php')
            include(dirname(__FILE__) . '/Modules/' . $file);
    }
}

function wpdmpro_required(){
    ?>
    <div class="panel panel-default" style="position: relative;z-index: 99999999">
        <div class="panel-body">
            <div class="media">
                <div class="pull-right"><a href="https://www.wpdownloadmanager.com/pricing/" target="_blank" class="btn btn-primary btn-sm">Get WPDM Pro</a></div>
                <div class="media-body lead" style="line-height: 25px">This option is only available with the WordPress Download Manager Pro!</div>
            </div>
        </div>
    </div>
    <?php
}
