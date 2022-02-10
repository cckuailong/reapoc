<?php
if(!defined('ABSPATH')) die();
$uid = isset($params['user'])?$params['user']:get_current_user_id();
$myfavs = maybe_unserialize(get_user_meta($uid, '__wpdm_favs', true));
$template = isset($params['template'])?$params['template']:'link-template-calltoaction4';
$cols = 6;
?>
<div class="row">
    <?php if(is_array($myfavs)) foreach ($myfavs as $fav){

        if(wpdm_user_has_access($fav)){
            ?>
            <div class="col-md-<?php echo $cols; ?>"><?php echo WPDM()->package->fetchTemplate($template, array('ID' => $fav)); ?></div>
        <?php }} ?>
</div>
