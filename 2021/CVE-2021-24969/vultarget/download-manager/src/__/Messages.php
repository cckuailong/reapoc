<?php
namespace WPDM\__;

class Messages {

    public $template = "blank";

    public static function fullPage($title, $msg, $type = 'error'){
        include Template::locate("message.php", __DIR__.'/views');
        die();
    }

    public static function message($msg, $die = 0, $style = 'embed'){
        if(is_array($msg)) {
            if($style === 'modal')
                $message = "<script>WPDM.bootAlert('{$msg['title']}', '<div class=\'text-{$msg['type']}\'>{$msg['message']}</div>')</script>";
            else if($style === 'notify')
                $message = "<script>WPDM.notify('<strong>{$msg['title']}</strong><br/>{$msg['message']}', '{$msg['type']}', 'top-right')</script>";
            else
                $message = "<div class='w3eden'><div class='alert alert-{$msg['type']}' data-title='{$msg['title']}'>{$msg['message']}</div></div>";
        }
        else {
            if($style === 'mpdal')
                $message = "<script>WPDM.bootAlert('Attention Please!', '{$msg}')</script>";
            else if($style === 'notify')
                $message = "<script>WPDM.notify('{$msg}', 'info', 'top-right')</script>";
            else
                $message = $msg;
        }
        if($die==-1) return $message;
        if($die==0)
            echo $message;
        if($die==1) {
            $content = "<div style='display: table;vertical-align: middle;height: 90%;position: absolute;width: 90%;margin-left: 5%;'>
                        <div style='text-align: center;height: 100%;display: table-cell;vertical-align: middle'>
                        <div style='max-width: 70% !important;display: inline-block;font-size: 13pt'>
                            $message
                        </div></div></div>";
            include Template::locate("blank.php", __DIR__.'/views');
            die();
        }
        return true;
    }

    public static function error($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Error!';
        $msg['type'] = 'danger';
        $msg['icon'] = 'exclamation-triangle';
        return self::Message($msg, $die, $style);
    }

    public static function warning($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Warning!';
        $msg['type'] = 'warning';
        $msg['icon'] = 'exclamation-circle';
        return self::Message($msg, $die, $style);
    }

    public static function info($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Attention!';
        $msg['type'] = 'info';
        $msg['icon'] = 'info-circle';
        return self::Message($msg, $die, $style);
    }

    public static function success($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Awesome!';
        $msg['type'] = 'success';
        $msg['icon'] = 'check-circle';
        return self::Message($msg, $die, $style);
    }

    public static function decode_html($html){
        $html = htmlspecialchars_decode($html);
        $html = html_entity_decode($html, ENT_QUOTES);
        $html = stripslashes_deep($html);
        return $html;
    }

    public static function download_limit_exceeded($ID = null){
        $message = get_option("__wpdm_download_limit_exceeded");
        $message = self::decode_html($message);
        $message = wpdm_escs($message);
        $message = trim($message) !== '' ? $message : __( "Download Limit Exceeded!", "download-manager" );
        return $message;
    }

    public static function login_required($ID = null){
        $message = get_option("wpdm_login_msg");
        $message = self::decode_html($message);
        $message = wpdm_escs($message);
        $message = trim($message) !== '' ? $message : WPDM()->user->login->modalLoginFormBtn(['class' => 'btn btn-danger', 'label' => '<i class="fas fa-lock mr-3"></i>'.__( "Login", "download-manager" )]);
        return $message;
    }

    public static function permission_denied($ID = null){
        $message = get_option("__wpdm_permission_denied_msg");
        $message = self::decode_html($message);
        $message = wpdm_escs($message);
        $message = trim($message) !== '' ? $message : WPDM()->ui->button('<i class="fas fa-lock mr-3"></i>'.__( "Access Denied", "download-manager" ), ['class' => 'btn btn-danger']);
        return $message;
    }
}
