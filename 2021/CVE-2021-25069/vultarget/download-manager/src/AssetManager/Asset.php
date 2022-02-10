<?php

namespace WPDM\AssetManager;


use WPDM\__\__;
use WPDM\__\FileSystem;
use WPDM\__\Template;

class Asset
{
    var $ID;
    var $activities = array();
    var $comments = array();
    var $access = array();
    var $links = array();
    var $metadata = array();
    var $path;
    var $preview;
    var $name;
    var $type;
    var $size;
    var $temp_download_url;
    var $dbtable;

    function __construct($path = null){
        global $wpdb;
        $this->dbtable = "{$wpdb->prefix}ahm_assets";
        if($path)
            $this->init($path);
    }

    public function init($path){
        global $wpdb;
        if(!$this->get($path)) {
            $wpdb->insert($this->dbtable, array('path' => $path));
            $this->ID = $wpdb->insert_id;
            $this->path = $path;
            $this->preview = self::preview($this->path);
            $this->name = basename($this->path);
            $this->type = is_dir($this->path)?'dir':'file';
            $this->size = wpdm_file_size($this->path);
            $this->temp_download_url = home_url("/?asset={$this->ID}&key=".wp_create_nonce($this->path));
        }
        return $this;
    }

    function get($path_or_id){
        global $wpdb;
        $id = (int)$path_or_id;
        $idcond = $id > 0 ? " or ID = '$id'":"";
        $assetmeta = $wpdb->get_row("select * from {$wpdb->prefix}ahm_assets where path = '{$path_or_id}' {$idcond}");
        if ($assetmeta){
            $this->ID = $assetmeta->ID;
            $this->activities = json_decode($assetmeta->activities);
            $this->comments = json_decode($assetmeta->comments);
            $this->access = json_decode($assetmeta->access);
            $this->links = $this->getLinks();
            $this->metadata = json_decode($assetmeta->metadata);
            $this->path = $assetmeta->path;
            $this->preview = self::preview($this->path);
            $this->name = basename($this->path);
            $this->type = is_dir($this->path)?'dir':'file';
            $this->size = wpdm_file_size($this->path);
            $this->temp_download_url = home_url("/?asset={$this->ID}&key=".wp_create_nonce($this->path));
            return $this;
        }
        return null;
    }

    function resolveKey($key){
        global $wpdb;
        $key = esc_sql(esc_attr($key));
        $asset_link = $wpdb->get_row("select * from {$wpdb->prefix}ahm_asset_links where asset_key='$key'");
        $asset_id = $asset_link->asset_ID;
        $assetmeta = $wpdb->get_row("select * from {$wpdb->prefix}ahm_assets where ID = '{$asset_id}'");
        if ($assetmeta){
            $this->ID = $assetmeta->ID;
            $this->activities = json_decode($assetmeta->activities);
            $this->comments = json_decode($assetmeta->comments);
            $this->access = json_decode($asset_link->access);
            $this->links = $this->getLinks();
            $this->metadata = json_decode($assetmeta->metadata);
            $this->path = $assetmeta->path;
            $this->preview = self::preview($this->path);
            $this->name = basename($this->path);
            $this->type = is_dir($this->path)?'dir':'file';
            $this->size = wpdm_file_size($this->path);
            $this->temp_download_url = home_url("/?asset={$this->ID}&key=".wp_create_nonce($this->path));
            return $this;
        }
        return null;
    }

    function newComment($comment, $user){
        if(!is_array($this->comments)) $this->comments = array();
        $userdata = get_userdata($user);
        $avatar = get_avatar($user);
        $comment = array( 'comment' => $comment, 'user' => $user, 'username' => $userdata->user_login, 'name' => $userdata->display_name, 'email' => $userdata->user_email, 'avatar' => $avatar, 'time' => time() );
        array_unshift($this->comments, $comment);
        return $this;
    }

    function newLink($access = array('roles' => array('guest'), 'users' => array())){
        global $wpdb;
        $asset_key = uniqid();
        $wpdb->insert("{$wpdb->prefix}ahm_asset_links", array('asset_ID' => $this->ID, 'asset_key' => $asset_key, 'access' => serialize($access)));
        $this->links = $this->getLinks();
        return $this;
    }

    function getLinks(){
        global $wpdb;
        $links = $wpdb->get_results("select * from {$wpdb->prefix}ahm_asset_links where asset_ID = '{$this->ID}'");
        foreach ($links as &$link){
            $link->url = home_url('/wpdm-asset/'.$link->asset_key);
            $link->access = json_decode($link->access);
        }

        return $links;
    }

    public static function getLink($ID){
        global $wpdb;
        $link = $wpdb->get_row("select * from {$wpdb->prefix}ahm_asset_links where ID = '{$ID}'");
        $link->url = home_url('/wpdm-asset/'.$link->asset_key);
        $link->access = json_decode($link->access);
        if(!is_array($link->access)) $link->access = array();
        if(!isset($link->access['roles'])) $link->access['roles'] = array();
        if(!isset($link->access['users'])) $link->access['users'] = array();

        return $link;
    }

    public static function updateLink($data, $ID){
        global $wpdb;
        return $wpdb->update("{$wpdb->prefix}ahm_asset_links", $data, array('ID' => $ID));
    }

    public static function deleteLink($ID){
        global $wpdb;
        return $wpdb->delete("{$wpdb->prefix}ahm_asset_links", array('ID' => $ID));
    }

    function newActivity($activity, $user){
        if(!is_array($this->activities)) $this->activities = array();
        $this->activities[] = array( 'activity' => $activity, 'user' => $user, 'time' => time() );
        return $this;
    }

    function newMeta($name, $value){
        $this->metadata[] = array( $name => $value );
        return $this;
    }

    public static function preview($path){
        global $current_user;
        $ext = explode('.', $path);
        $ext = end($ext);
        $ext = strtolower($ext);
        $url = str_replace(ABSPATH, home_url('/'), $path);
        $accessible = ini_get('allow_url_fopen') && __::is_url($url) ? get_headers($url) : [403];
        $accessible = substr_count($accessible[0], '403') ? false : true;
        $relpath = str_replace(AssetManager::root(), "", $path);
        if(!$accessible)
            $url = home_url("/?wpdmfmdl={$relpath}");
        $image = array('png', 'jpg', 'jpeg');
        if(is_dir($path))
            return "<img style='padding:20px;width: 128px' src='".WPDM_BASE_URL."assets/images/folder.svg"."' alt='Preview' />";
        if(in_array($ext, $image))
            return "<img style='padding:20px;' src='".wpdm_dynamic_thumb($path, array(400, 0), false)."' alt='Preview' />";
        if($ext == 'svg')
            return file_get_contents($path);
        if($ext == 'mp3')
            return do_shortcode("[audio src='$url']");
        if($ext == 'mp4')
            return do_shortcode("[video src='$url']");

        $icon = \WPDM\__\FileSystem::fileTypeIcon($ext);

        return "<img src='$icon' style='padding: 20px;width: 128px'/>";

    }

    public static function view($path){
        global $current_user;
        $ext = explode('.', $path);
        $ext = end($ext);
        $ext = strtolower($ext);
        $url = str_replace(ABSPATH, home_url('/'), $path);
        $accessible = __::is_url($url) ? get_headers($url) : [403];
        $accessible = strstr($accessible[0], '403') ? false : true;
        $relpath = str_replace(AssetManager::root(), "", $path);
        $asset = new Asset($path);
        if(!$accessible)
            $url = $asset->temp_download_url;
        $image = array('png', 'jpg', 'jpeg');
        if(is_dir($path))
            return $asset->dirViewer();
        if(in_array($ext, $image))
            return "<div  class='wpdm-asset wpdm-asset-image'><img title='Click for fullscreen view' style='cursor: pointer' onclick='this.requestFullscreen();' src='{$asset->temp_download_url}' alt='Preview' /></div>";
        if($ext == 'svg')
            return "<div class='wpdm-asset wpdm-asset-svg'>".file_get_contents($path)."</div>";
        if($ext == 'mp3')
            return do_shortcode("<div class='wpdm-asset wpdm-asset-audio'>[audio src='$url']</div>");
        if($ext == 'mp4')
            return do_shortcode("<div class='wpdm-asset wpdm-asset-video'>[video src='$url']</div>");
        $mime = wp_check_filetype($path);
        if(strstr(wpdm_valueof($mime, 'type'), "text/")) {
            $class = str_replace("text/", "", wpdm_valueof($mime, 'type'));
            return "<pre class='wpdm-asset wpdm-asset-text'><code class='$class'>" . esc_attr(file_get_contents($path)) . "</code></pre>";
        }
        $icon = \WPDM\__\FileSystem::fileTypeIcon($ext);

        return "<img src='$icon' style='padding: 20px;width: 128px'/>";

    }

    function hasAccess(){
        global $current_user;
        $roles = isset($this->access->roles) && is_array($this->access->roles) ? $this->access->roles : array();
        $users = isset($this->access->users) && is_array($this->access->users) ? $this->access->users : array();
        if(current_user_can('manage_options')) return true;
        if(count(array_intersect($current_user->roles, $roles)) > 0) return true;
        if(in_array('guest', $roles)) return true;
        if(in_array($current_user->user_login, $users)) return true;
        return false;
    }

    function dirViewer(){
        $dirTree = $this->dirIterator($this->path);
        ob_start();
        include Template::locate("dir-viewer.php", __DIR__.'/views');
        $viewer = ob_get_clean();
        return $viewer;
    }

    private function dirIterator($path){
        $iterator = new \DirectoryIterator( $path );
        $data = [];
        foreach ( $iterator as $node )
        {
            if ( $node->isDir() && !$node->isDot() )
            {
                $data["_".md5($node->getPathname())] = [ 'path' => $node->getPathname(), 'type' => 'dir', 'items' => $this->dirIterator( $node->getPathname() ) , 'name' => $node->getFilename() ];
            }
            else if ( $node->isFile() )
            {
                $data["_".md5($node->getPathname())] = [ 'path' => $node->getPathname(), 'type' => 'file' , 'name' => $node->getFilename() ];
            }
        }
        return $data;
    }

    function updatePath($new_path){
        global $wpdb;
        $this->path = $new_path;
        $this->name = basename($new_path);
        $wpdb->update($this->dbtable, array('path' => $this->path ), array('ID' => $this->ID));
        return $this;
    }

    function save(){
        global $wpdb;
        $data = array(
          'activities' => json_encode($this->activities),
          'comments' => json_encode($this->comments),
          'access' => json_encode($this->access),
          'metadata' => json_encode($this->metadata),
        );
        $wpdb->update($this->dbtable, $data, array('ID' => $this->ID));
        return $this;
    }

    public static function delete($path_or_id){
        global $wpdb;
        $path_or_id = wpdm_sanitize_var($path_or_id);
        $id = (int)$path_or_id;
        return $wpdb->query("delete from {$wpdb->prefix}ahm_assets where ID='{$id}' or path='{$path_or_id}'");
    }

    function download(){
        \WPDM\__\FileSystem::downloadFile($this->path, wp_basename($this->name));
    }
}
