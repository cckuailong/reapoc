<?php
$AffHomeMsgNotRegistered = get_option(WPAM_PluginConfig::$AffHomeMsgNotRegistered);
if(empty($AffHomeMsgNotRegistered)){ //just to make sure the option was added correctly during update/new installation
printf( __( 'This is the affiliates section of this store. You are not currently an affiliate of this store. If you wish to become one, please <a href="%s"/>apply</a>.', 'affiliates-manager' ), $this->viewData['registerUrl'] );    
}
else{
    echo wpautop($AffHomeMsgNotRegistered);
}
