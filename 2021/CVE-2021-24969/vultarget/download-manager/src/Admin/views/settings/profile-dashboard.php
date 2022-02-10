<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "User Dashboard" , "download-manager" ); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <label for="__wpdm_user_dashboard"><?php echo __( "Dashboard Page" , "download-manager" ); ?></label><br/>
            <?php wp_dropdown_pages(array('name' => '__wpdm_user_dashboard', 'id' => '__wpdm_user_dashboard', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_user_dashboard'))) ?><br/>
            <em class="note"><?php printf(__( "The page where you used short-code %s" , "download-manager" ),'<input style="width: 165px" readonly="readonly" type="text" value="[wpdm_user_dashboard]" class="txtsc">'); ?></em>
        </div>

        <div class="form-group">
            
        </div>

    </div>
</div>

