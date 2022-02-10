<?php
/*
Asset Manager for WordPress Download Manager
Author: Shahjada
Version: 1.0.0
*/

namespace WPDM\AssetManager;

use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\Messages;
use WPDM\__\Session;
use WPDM\__\Template;
use WPDM\__\FileSystem;

define('WPDMAM_NONCE_KEY', 'r2pj@|k5.|;B1?n9MqB)%<w2Yz|XZx(alt@Aoc|~,|93lei|wR.R9~5X4D$ZH&*7U}Ot');


class AssetManager
{
    private static $instance;
    private $dir, $url, $root;
    private $mime_type;

    public static function getInstance()
    {
    	if (self::$instance === null) {
            self::$instance = new self;
            self::$instance->dir = dirname(__FILE__);
            self::$instance->url = WP_PLUGIN_URL . '/' . basename(self::$instance->dir);
            self::$instance->actions();
            //print_r($_SESSION);
        }
        return self::$instance;
    }

    public static function root($path = '')
    {
        global $current_user;
        $root = current_user_can(WPDM_ADMIN_CAP) ? rtrim(get_option('_wpdm_file_browser_root'), '/') . '/' : UPLOAD_DIR . $current_user->user_login . '/';
        $_root = str_replace("\\", "/", $root);
        if ($path !== '') $root .= $path;
        $root = str_replace(array("./", "../"), "", $root);
        $root = str_replace("\\", "/", $root);
        if ($path !== '' && !strstr($root, $_root)) return "[INVALID_PATH]";
        if (is_dir($root)) $root = rtrim($root, "/") . "/";
        if ($path === '' && !file_exists($root)) {
            @mkdir($root, 0775, true);
            \WPDM\__\FileSystem::blockHTTPAccess($root);

        }
        return $root;
    }

    private function actions()
    {

        add_action('init', array($this, 'assetPicker'), 1);
        add_action('init', array($this, 'download'), 1);

        //add_action('wp_ajax_wpdm_fm_file_upload', array($this,'uploadFile'));
        add_action('wp_ajax_wpdm_mkdir', array($this, 'mkDir'));
        add_action('wp_ajax_wpdm_newfile', array($this, 'newFile'));
        add_action('wp_ajax_wpdm_scandir', array($this, 'scanDir'));
        add_action('wp_ajax_wpdm_createzip', array($this, 'createZip'));
        add_action('wp_ajax_wpdm_unzipit', array($this, 'unZip'));
        add_action('wp_ajax_wpdm_openfile', array($this, 'openFile'));
        add_action('wp_ajax_wpdm_filesettings', array($this, 'fileSettings'));
        add_action('wp_ajax_wpdm_unlink', array($this, 'deleteItem'));
        add_action('wp_ajax_wpdm_rename', array($this, 'renameItem'));
        add_action('wp_ajax_wpdm_savefile', array($this, 'saveFile'));
        add_action('wp_ajax_wpdm_copypaste', array($this, 'copyItem'));
        add_action('wp_ajax_wpdm_cutpaste', array($this, 'moveItem'));
        add_action('wp_ajax_wpdm_addcomment', array($this, 'addComment'));
        add_action('wp_ajax_wpdm_newsharelink', array($this, 'addShareLink'));
        add_action('wp_ajax_wpdm_getlinkdet', array($this, 'getLinkDet'));
        add_action('wp_ajax_wpdm_updatelink', array($this, 'updateLink'));
        add_action('wp_ajax_wpdm_deletelink', array($this, 'deleteLink'));

		//add_action('wpdm_asset_viewer_head', [$this, 'enqueueScripts']);

        add_action('wpdm_after_upload_file', array($this, 'upload'), 1);
        //add_action('wp_enqueue_scripts', array($this,'siteScripts'));
        add_action('admin_enqueue_scripts', array($this, 'adminScripts'));

        //add_shortcode('wpdm_asset_manager', array($this,'_assetManager'));
        add_shortcode('wpdm_asset', array($this, 'wpdmAsset'));

        //add_filter('wpdm_frontend', array($this,'frontendFileManagerTab'));

        if (is_admin()) {
            add_action('admin_menu', array($this, 'adminMenu'), 1);
        }

    }

	function dequeueScripts() {
		global $wp_scripts;
		$wp_scripts->queue = array();
	}


	function dequeueStyles() {
		global $wp_styles;
		$wp_styles->queue = array();
	}

    function assetPicker()
    {
		//$this->dequeueScripts();
		//$this->dequeueStyles();
        global $wp_query;
        if (wpdm_query_var('assetpicker', 'int') === 1) {
            if(!current_user_can('access_server_browser')) Messages::fullPage("Error", esc_attr__( 'You are not authorized to access this page', 'download-manager' ), 'error');
            http_response_code(200);
            include Template::locate("asset-manager-picker.php", __DIR__.'/views');
            die();
        }
    }


    function siteScripts()
    {
        global $post;

        if (is_single() && !has_shortcode($post->post_content, '[wpdm_asset_manager]')) return;

        $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/plain'));
        wp_localize_script('jquery', 'wpdmcm_settings', $cm_settings);

        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-autocomplete');
    }


    function adminScripts($hook)
    {
        if ($hook !== 'wpdmpro_page_wpdm-asset-manager') return;

        $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/plain'));
        wp_localize_script('jquery', 'wpdmcm_settings', $cm_settings);

        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-autocomplete');

    }

    public function download()
    {
        if (isset($_REQUEST['asset']) && isset($_REQUEST['key'])) {
            $asset = new Asset();
            $asset->get(wpdm_query_var('asset', 'int'));
            if (wp_verify_nonce($_REQUEST['key'], $asset->path))
                $asset->download();
            else
                \WPDM\__\Messages::error(apply_filters('wpdm_asset_download_link_expired_message', __("Download link is expired! Go back and Refresh the page to regenerate download link", "download-manager")), 1);
            die();
        }
        if (isset($_REQUEST['wpdmfmdl']) && is_user_logged_in()) {
            global $current_user;
            $file = AssetManager::root(Crypt::decrypt(wpdm_query_var('wpdmfmdl')));
            if (!$file) \WPDM\__\Messages::error("File Not Found!", 1);
            \WPDM\__\FileSystem::downloadFile($file, wp_basename($file), 10240, false, ['play' => wpdm_query_var('play')]);
            die();
        }
    }

    public static function getDir()
    {
        return self::$instance->dir;
    }

    public static function getUrl()
    {
        return self::$instance->url;
    }

    public function adminMenu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __("Asset Manager", 'download-manager'), __('Asset Manager', 'download-manager'), 'access_server_browser', 'wpdm-asset-manager', array($this, '_assetManager'));
    }

    static function mimeType($file)
    {
        $contenttype = wp_check_filetype($file);
        $contenttype = $contenttype['type'];
        if (!$contenttype) {
            $file = explode(".", $file);
            $contenttype = "unknown/" . end($file);
        }
        return $contenttype;
    }

    function mkDir()
    {
        global $current_user;
        if (isset($_REQUEST['__wpdm_mkdir']) && !wp_verify_nonce($_REQUEST['__wpdm_mkdir'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_mkdir');
        if (!current_user_can('upload_files') || !current_user_can('access_server_browser')) die('Error! Unauthorized Access.');
        $root = AssetManager::root();
        $relpath = Crypt::decrypt(wpdm_query_var('path'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        $name = wpdm_query_var('name', 'filename');
        mkdir($path . $name);
        wp_send_json(array('success' => true, 'path' => $path . $name));
    }

    function newFile()
    {
        global $current_user;
        if (isset($_REQUEST['__wpdm_newfile']) && !wp_verify_nonce($_REQUEST['__wpdm_newfile'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_newfile');
        if (!current_user_can('upload_files') || !current_user_can('access_server_browser')) die('Error! Unauthorized Access.');
        $root = AssetManager::root();
        $relpath = Crypt::decrypt(wpdm_query_var('path'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));

        $name = wpdm_query_var('name');
        //Check file is in allowed types
        if (WPDM()->fileSystem->isBlocked($name)) wp_send_json(array('success' => false, 'message' => __("Error! FileType is not allowed.", "download-manager")));

        $ret = file_put_contents($path . $name, '');
        if ($ret !== false)
            wp_send_json(array('success' => true, 'filepath' => $path . $name));
        else
            wp_send_json(array('success' => false, 'filepath' => $path . $name));

    }

    function scanDir()
    {
        if (!isset($_REQUEST['__wpdm_scandir']) || !wp_verify_nonce($_REQUEST['__wpdm_scandir'], NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(NONCE_KEY, '__wpdm_scandir');
        //if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));
        if (!current_user_can('upload_files') || !current_user_can('access_server_browser')) die('Error! Unauthorized Access.');
        global $current_user;
        $root = AssetManager::root();
        $relpath = Crypt::decrypt(wpdm_query_var('path'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        $items = scandir($path, SCANDIR_SORT_ASCENDING);
        if(!is_array($items)) $items = [];
        $items = array_diff($items, ['.', '..']);
        $_items = [];
        $_dirs = [];
        update_user_meta(get_current_user_id(), 'working_dir', $path);
        foreach ($items as $item) {

            $item_label = $item;
            $item_label = esc_attr($item_label);
            //$item_label = strlen($item_label) > 30 ? substr($item_label, 0, 15) . "..." . substr($item_label, strlen($item_label) - 15) : $item_label;
            $ext = explode('.', $item);
            $ext = end($ext);
            $icon = \WPDM\__\FileSystem::fileTypeIcon($ext);
            $type = is_dir($path . $item) ? 'dir' : 'file';
            $note = is_dir($path . $item) ? (count(scandir($path . $item)) - 2) . ' items' : number_format((filesize($path . $item) / 1024), 2) . ' KB';
            $rpath = str_replace($root, "", $path . $item);
            $wp_rel_path = str_replace(UPLOAD_DIR, '', $path . $item);
            $wp_rel_path = str_replace(ABSPATH, '', $wp_rel_path);
            $_rpath = Crypt::encrypt($rpath);
            if ($type === 'dir') {
                $_dirs[] = array('item_label' => $item_label, 'item' => $item, 'icon' => $icon, 'type' => $type, 'note' => $note, 'path' => $_rpath, 'id' => md5($rpath));
            } else {
                $contenttype = function_exists('mime_content_type') ? mime_content_type($path . $item) : self::mimeType($item);
                $_items[] = array('item_label' => $item_label, 'item' => $item, 'icon' => $icon, 'type' => $type, 'contenttype' => $contenttype, 'note' => $note, 'path_on' => $path . $item, 'wp_rel_path' => $wp_rel_path, 'path' => $_rpath, 'id' => md5($rpath));
            }

        }

        $allitems = $_dirs;
        foreach ($_items as $_item) {
            $allitems[] = $_item;
        }
        $parts = explode("/", $relpath);
        $breadcrumb[] = "<i class='fa fa-hdd color-purple'></i><a href='#' class='media-folder' data-path=''>" . __("Home", "download-manager") . "</a>";
        $topath = array();
        foreach ($parts as $part) {
            $topath[] = $part;
            $rpath = Crypt::encrypt(implode("/", $topath));
            $breadcrumb[] = "<a href='#' class='media-folder' data-path='{$rpath}'>" . esc_attr($part) . "</a>";
        }
        $breadcrumb = implode("<i class='fa fa-folder-open'></i>", $breadcrumb);
        if ((int)wpdm_query_var('dirs') === 1)
            wp_send_json($_dirs);
        else
            wp_send_json(array('success' => true, 'items' => $allitems, 'breadcrumb' => $breadcrumb, 'root' => $root, WPDM_ADMIN_CAP => current_user_can(WPDM_ADMIN_CAP), 'roles' => $current_user->roles));
        die();
    }

    function createZip()
    {
        if (!isset($_REQUEST['__wpdm_createzip']) || !wp_verify_nonce($_REQUEST['__wpdm_createzip'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_createzip');
        //if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("<b>Unauthorized Action!</b><br/>Execution is cancelled by the system.", "download-manager")));
        global $current_user;
        $root = AssetManager::root();
        $relpath = Crypt::decrypt(wpdm_query_var('dir_path'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        $zipped = FileSystem::zipDir($path);
        rename($zipped, untrailingslashit($path) . ".zip");
        wp_send_json(array('success' => true, 'zipped' => untrailingslashit($path) . ".zip", 'refresh' => true));
        die();
    }

    function unZip(){
        if (!isset($_REQUEST['__wpdm_unzipit']) || !wp_verify_nonce($_REQUEST['__wpdm_unzipit'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_unzipit');
        //if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("<b>Unauthorized Action!</b><br/>Execution is cancelled by the system.", "download-manager")));
        global $current_user;
        $root = AssetManager::root();
        $relpath = Crypt::decrypt(wpdm_query_var('dir_path'));
        $path = AssetManager::root($relpath);
        if (!$path || FileSystem::mime_type($path) !== 'application/zip') wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        FileSystem::unZip($path);
        wp_send_json(array('success' => true, 'refresh' => true));
        die();
    }

    function deleteItem()
    {

        __::isAuthentic('__wpdm_unlink', WPDMAM_NONCE_KEY, WPDM_ADMIN_CAP, true);

        $relpath = Crypt::decrypt(wpdm_query_var('delete'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        if (is_dir($path))
            $this->rmDir($path);
        else
            unlink($path);

        Asset::delete($path);

        die($path);
    }

    function openFile()
    {
        if (!isset($_REQUEST['__wpdm_openfile']) || !wp_verify_nonce($_REQUEST['__wpdm_openfile'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_openfile');
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));
        $relpath = Crypt::decrypt(wpdm_query_var('file'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        if (file_exists($path) && is_file($path)) {
            $cid = uniqid();
            Session::set($cid, $path);
            $type = function_exists('mime_content_type') ? mime_content_type($path) : self::mimeType($path);
            $ext = explode(".", $path);
            $ext = end($ext);
            $ext = strtolower($ext);

            if (strstr("__{$type}", "text/") || in_array($ext, array('txt', 'csv', 'css', 'html', 'log')))
                wp_send_json(array('content' => file_get_contents($path), 'id' => $cid));
            else if (strstr("__{$type}", "svg"))
                wp_send_json(array('content' => '', 'embed' => file_get_contents($path), 'id' => $cid));
            else {
                $file = Crypt::decrypt(wpdm_query_var('file'));
                $file = basename($file);
                $fetchurl = home_url("/?wpdmfmdl=" . wpdm_query_var('file'));
                if (strstr("__{$type}", "image/")) {
                    $embed_code = "<img src='$fetchurl' />";
                    wp_send_json(array('content' => '', 'embed' => $embed_code, 'id' => $cid));
                }
                if (strstr("__{$type}", "audio/")) {
                    $embed_code = do_shortcode("[audio src='{$fetchurl}&file={$file}']");
                    wp_send_json(array('content' => '', 'embed' => $embed_code, 'id' => $cid));
                }
                if (strstr("__{$type}", "video/")) {
                    $embed_code = do_shortcode("[video src='{$fetchurl}&file={$file}']");
                    wp_send_json(array('content' => '', 'embed' => $embed_code, 'id' => $cid));
                }
                if ($type === 'application/pdf') {
                    //$embed_code = do_shortcode("[video src='{$fetchurl}&file={$file}']");
                    $embed_code = "<iframe style='width: 100%;height: 100%;' src='{$fetchurl}&file={$file}&play=1'></iframe><style>#filecontent_alt{ padding: 0 !important; overflow: hidden; }</style>";
                    wp_send_json(array('content' => '', 'embed' => $embed_code, 'id' => $cid));
                }
            }


        } else {
            wp_send_json(array('content' => 'Failed to open file!', 'id' => uniqid()));
            die();
        }

    }

    function fileSettings()
    {

        if (!isset($_REQUEST['__wpdm_filesettings']) || !wp_verify_nonce($_REQUEST['__wpdm_filesettings'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_filesettings');
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Access.", "download-manager")));
        $relpath = Crypt::decrypt(wpdm_query_var('file'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        if (file_exists($path)) {
            $asset = new Asset($path);
            wp_send_json($asset);
        } else {
            wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
            die();
        }

    }


    function addComment()
    {
        if (!isset($_REQUEST['__wpdm_addcomment']) || !wp_verify_nonce($_REQUEST['__wpdm_addcomment'], NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(NONCE_KEY, '__wpdm_addcomment');
        if (!is_user_logged_in()) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $asset_id = wpdm_query_var('assetid', 'int');
        $asset = new Asset();
        $asset->get($asset_id)->newComment(wpdm_query_var('comment', 'txts'), get_current_user_id())->save();
        wp_send_json($asset->comments);
    }

    function addShareLink()
    {
        if (!isset($_REQUEST['__wpdm_newsharelink']) || !wp_verify_nonce($_REQUEST['__wpdm_newsharelink'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_newsharelink');
        if (!current_user_can('access_server_browser')) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $asset_ID = wpdm_query_var('asset', 'int');
        $asset = new Asset();
        $asset->get($asset_ID)->newLink(wpdm_query_var('access'))->save();
        wp_send_json($asset->links);
    }

    function getLinkDet()
    {
        if (!isset($_REQUEST['__wpdm_getlinkdet']) || !wp_verify_nonce($_REQUEST['__wpdm_getlinkdet'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_getlinkdet');
        if (!current_user_can('access_server_browser')) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $link_ID = wpdm_query_var('linkid', 'int');
        $link = Asset::getLink($link_ID);
        wp_send_json($link);
    }

    function updateLink()
    {
        if (!isset($_REQUEST['__wpdm_updatelink']) || !wp_verify_nonce($_REQUEST['__wpdm_updatelink'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_updatelink');
        if (!current_user_can('access_server_browser')) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $link_ID = wpdm_query_var('ID', 'int');
        $access = wpdm_query_var('access');
        if (!isset($access['roles'])) $access['roles'] = array();
        if (!isset($access['users'])) $access['users'] = array();
        $link = Asset::updateLink(array('access' => json_encode($access)), $link_ID);
        wp_send_json(array('success' => $link));
    }

    function deleteLink()
    {
        if (!isset($_REQUEST['__wpdm_deletelink']) || !wp_verify_nonce($_REQUEST['__wpdm_deletelink'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_deletelink');
        if (!current_user_can('access_server_browser')) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $link_ID = wpdm_query_var('linkid', 'int');
        $link = Asset::deleteLink($link_ID);
        wp_send_json(array('success' => $link));

    }

    function saveFile()
    {
        if (!isset($_REQUEST['__wpdm_savefile']) || !wp_verify_nonce($_REQUEST['__wpdm_savefile'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_savefile');
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $ofilepath = Session::get(wpdm_query_var('opened'));
        $relpath = Crypt::decrypt(wpdm_query_var('file'));
        $path = AssetManager::root($relpath);
        if (!$path) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));

        if (WPDM()->fileSystem->isBlocked($path)) wp_send_json(array('success' => false, 'message' => __("Error! FileType is not allowed.", "download-manager")));

        if (file_exists($path) && is_file($path)) {
            $content = stripslashes_deep($_POST['content']);
            file_put_contents($path, $content);
            wp_send_json(array('success' => true, 'message' => 'Saved Successfully.', 'type' => 'success'));
        } else {
            wp_send_json(array('success' => false, 'message' => __("Error! Couldn't open file ( $path ).", "download-manager")));
        }

    }

    function renameItem()
    {
        if (!isset($_REQUEST['__wpdm_rename']) || !wp_verify_nonce($_REQUEST['__wpdm_rename'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_rename');
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));
        global $current_user;
        $asset = new Asset();
        $asset->get(wpdm_query_var('assetid', 'int'));
        $root = AssetManager::root();
        $oldpath = $asset->path;
        $newpath = dirname($asset->path) . '/' . str_replace(array("/", "\\", "\"", "'"), "_", wpdm_query_var('newname'));

        if (WPDM()->fileSystem->isBlocked(wpdm_query_var('newname'))) wp_send_json(array('success' => false, 'message' => __("Error! FileType is not allowed.", "download-manager")));

        if (!strstr($newpath, $root)) die('Error!' . $newpath . " -- " . $root);
        rename($oldpath, $newpath);
        $asset->updatePath($newpath);
        wp_send_json($asset);
    }

    function moveItem()
    {
        if (!isset($_REQUEST['__wpdm_cutpaste']) || !wp_verify_nonce($_REQUEST['__wpdm_cutpaste'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_cutpaste');
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));

        $opath = explode("|||", wpdm_query_var('source'));
        $olddir = Crypt::decrypt($opath[0]);
        $file = end($opath);

        //Check file is in allowed types
        if (WPDM()->fileSystem->isBlocked($file)) wp_send_json(array('success' => false, 'message' => __("Error! FileType is not allowed.", "download-manager")));


        $oldpath = AssetManager::root($olddir . '/' . $file);
        $newpath = AssetManager::root(Crypt::decrypt(wpdm_query_var('dest'))) . $file;
        if (!$oldpath) wp_send_json(array('success' => false, 'message' => __("Invalid source path", "download-manager")));
        if (!$newpath) wp_send_json(array('success' => false, 'message' => __("Invalid destination path", "download-manager")));
        rename($oldpath, $newpath);

        $asset = new Asset();
        $asset = $asset->get($oldpath);
        if ($asset)
            $asset->updatePath($newpath);

        wp_send_json(array('success' => true, 'message' => __("File moved successfully", "download-manager")));
    }

    function copyItem()
    {
        if (!isset($_REQUEST['__wpdm_copypaste']) || !wp_verify_nonce($_REQUEST['__wpdm_copypaste'], WPDMAM_NONCE_KEY)) wp_send_json(array('success' => false, 'message' => __("Error! Session Expired. Try refreshing page.", "download-manager")));
        check_ajax_referer(WPDMAM_NONCE_KEY, '__wpdm_copypaste');
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! You are not authorized to execute this action.", "download-manager")));
        global $current_user;
        $root = AssetManager::root();
        $opath = explode("|||", wpdm_query_var('source'));
        $olddir = Crypt::decrypt($opath[0]);
        $file = end($opath);
        $oldpath = AssetManager::root($olddir . '/' . $file);
        $newpath = AssetManager::root(Crypt::decrypt(wpdm_query_var('dest'))) . $file;
        if (!strstr($oldpath, $root)) wp_send_json(array('success' => false, 'message' => __("Invalid source path", "download-manager")));
        if (!strstr($newpath, $root)) wp_send_json(array('success' => false, 'message' => __("Invalid destination path", "download-manager")));

        //Check file is in allowed types
        if (WPDM()->fileSystem->isBlocked($newpath)) wp_send_json(array('success' => false, 'message' => __("Error! FileType is not allowed.", "download-manager")));

        copy($oldpath, $newpath);

        wp_send_json(array('success' => true, 'message' => __("File copied successfully", "download-manager")));
    }

    function rmDir($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->rmDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    function copyDir($src, $dst)
    {
        $src = realpath($src);
        $dir = opendir($src);

        $dst = realpath($dst) . '/' . basename($src);
        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    function frontendFileManagerTab($tabs)
    {
        $tabs['asset-manager'] = array('label' => 'Asset Manager', 'callback' => array($this, '_assetManager'), 'icon' => 'fa fa-copy');
        return $tabs;
    }

    function _assetManager()
    {

        include Template::locate("asset-manager-ui.php", __DIR__.'/views');

    }

    /**
     * Shortcode processor for [wpdm_asset ...$params]
     * @param $params
     * @return bool|mixed|string
     */
    function wpdmAsset($params)
    {
        if (!isset($params['id'])) return \WPDM\__\Messages::error(__("Asset not found!", "download-manager"), -1);
        $path_or_id = (int)$params['id'];
        $asset = new Asset();
        $asset->get($path_or_id);
        ob_start();
        include Template::locate("embed-asset.php", __DIR__.'/views');
        $content = ob_get_clean();
        return $content;
    }

    function upload($file)
    {
        if (isset($_REQUEST['__wpdmfm_upload']) && wp_verify_nonce($_REQUEST['__wpdmfm_upload'], NONCE_KEY)) {
            $working_dir = get_user_meta(get_current_user_id(), 'working_dir', true);
            $root = AssetManager::root();
            if (!strstr($working_dir, $root)) wp_send_json(array('success' => false));
            if ($working_dir != '') {
                $dest = $working_dir . basename($file);
                rename($file, $dest);
                wp_send_json(array('success' => true, 'src' => $file, 'file' => $dest));
            } else
                wp_send_json(array('success' => false));
        }
    }

    /**
     * Extract zip
     */
    function extract()
    {
        $relpath = Crypt::decrypt(wpdm_query_var('zipfile'));
        $zipfile = AssetManager::root($relpath);
        $reldest = Crypt::decrypt(wpdm_query_var('zipdest'));
        if ($reldest == '') $reldest = dirname($zipfile);
        $zipdest = AssetManager::root($reldest);
        if (!current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __("Error! Only Administrator can execute this operation.", "download-manager")));
        if (!$zipfile || !stristr($zipfile, '.zip')) wp_send_json(array('success' => false, 'message' => __("Error! Unauthorized Path.", "download-manager")));
        if (!$zipdest) wp_send_json(array('success' => false, 'message' => __("Error! Invalid Destination Path.", "download-manager")));
        if (!class_exists('\ZipArchive')) wp_send_json(array('success' => false, 'message' => __('Please activate "zlib" in your server to perform zip operations', 'download-manager')));
        $zip = new \ZipArchive();
        if ($zip->open($zipfile) === TRUE) {
            $zip->extractTo($zipdest);
            $zip->close();
            wp_send_json(array('success' => true, 'message' => __("Unzipped successfully.", "download-manager")));
        } else {
            wp_send_json(array('success' => false, 'message' => __("Error! Couldn't open the zip file.", "download-manager")));
        }
    }

}



