<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 2019-07-29 23:15
 */

if(!defined("ABSPATH")) die();
?>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "Author Dashboard", "download-manager" ) ?></div>
    <div class="panel-body">


        <div class="form-group">
            <label><?php echo __( "Allowed User Roles to Create Package From Front-end" , "download-manager" ); ?></label><br/>
            <select name="__wpdm_front_end_access[]" class="chzn-select role" multiple="multiple" id="fronend-ui-access" style="min-width: 450px">
                <?php

                $currentAccess = maybe_unserialize(get_option( '__wpdm_front_end_access', array()));
                $selz = '';

                ?>

                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                foreach( $roles as $role => $name ) {



                    if(  $currentAccess ) $sel = (in_array($role,$currentAccess))?'selected=selected':'';
                    else $sel = '';



                    ?>
                    <option value="<?php echo $role; ?>" <?php echo $sel  ?>> <?php echo $name; ?></option>
                <?php } ?>
            </select><br/>

        </div>

        <div class="form-group">
            <label><?php echo __( "Front-end Administrator Roles" , "download-manager" ); ?></label><br/>
            <select name="__wpdm_front_end_admin[]" class="chzn-select role" multiple="multiple" id="fronend-ui-admin" style="min-width: 450px">
                <?php

                $adminAccess = maybe_unserialize(get_option( '__wpdm_front_end_admin', array()));
                $selz = '';

                ?>

                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                foreach( $roles as $role => $name ) {



                    if(  $adminAccess ) $sel = (in_array($role,$adminAccess))?'selected=selected':'';
                    else $sel = '';



                    ?>
                    <option value="<?php echo $role; ?>" <?php echo $sel  ?>> <?php echo $name; ?></option>
                <?php } ?>
            </select><br/>
            <em class="note">
                Caution! All users from the selected user roles will be able to manage all ( their own and others ) packages from front-end
            </em>
        </div>

        <div class="form-group">
            <label for="__wpdm_author_dashboard"><?php echo __( "Author Dashboard Page" , "download-manager" ); ?></label><br/>
            <?php wp_dropdown_pages(array('name' => '__wpdm_author_dashboard', 'id' => '__wpdm_author_dashboard', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_author_dashboard'))) ?><br/>
            <em class="note"><?php printf(__( "The page where you used short-code %s" , "download-manager" ),'<input style="width: 120px" readonly="readonly" type="text" value="[wpdm_frontend]" class="txtsc">'); ?></em>
        </div>

        <?php do_action("wpdm_settings_frontend_author_dashboard"); ?>


    </div>
</div>
