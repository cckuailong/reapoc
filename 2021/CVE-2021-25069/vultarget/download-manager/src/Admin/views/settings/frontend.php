

                 <div class="panel panel-default">
                     <div class="panel-heading"><?php echo __( "Front-end Settings" , "download-manager" ); ?></div>
                     <div class="panel-body">


                         <div class="form-group">
                             <label for="__wpdm_login_url"><?php echo __( "Login Page" , "download-manager" ); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_login_url', 'id' => '__wpdm_login_url', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_login_url'))) ?>
                             <label class="ttip" style="margin-top: 2px" title="Only available with the pro version"><input disabled="disabled" style="margin: 0 3px 0 5px" value="1" type="checkbox" /> <?php _e("Clean login page", "download-manager");  ?></label><br/>
                             <em class="note"><?php printf(__( "The page where you used short-code %s" , "download-manager" ),'<input style="width: 145px" readonly="readonly" type="text" value="[wpdm_login_form]" class="txtsc">'); ?></em><br/>
                             <label style="margin-top: 2px"><input type="hidden" name="__wpdm_modal_login" value="0"><input <?php checked(1, get_option('__wpdm_modal_login', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_modal_login" type="checkbox" /> <?php _e("Enable modal login form", "download-manager");  ?></label>
                             <hr/>
                         </div>

                         <div class="form-group">
                             <label for="__wpdm_register_url"><?php echo __( "Register Page" , "download-manager" ); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_register_url', 'id' => '__wpdm_register_url', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_register_url'))) ?>
                             <label class="ttip" style="margin-top: 2px" title="Only available with the pro version"><input disabled="disabled" style="margin: 0 3px 0 5px" value="1" type="checkbox" /> <?php _e("Clean signup page", "download-manager");  ?></label><br/>
                             <em class="note"><?php printf(__( "The page where you used the short-code %s" , "download-manager" ),'<input style="width: 135px" readonly="readonly" type="text" value="[wpdm_reg_form]" class="txtsc">'); ?></em>
                             <label style="margin-top: 2px;display: block"><input type="hidden" name="__wpdm_signup_email_verify" value="0"><input <?php checked(1, get_option('__wpdm_signup_email_verify', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_signup_email_verify" type="checkbox" /> <?php _e("Enable email verification on signup", "download-manager");  ?></label>
                             <label style="margin-top: 2px;display: block"><input type="hidden" name="__wpdm_signup_autologin" value="0"><input <?php checked(1, get_option('__wpdm_signup_autologin', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_signup_autologin" type="checkbox" /> <?php _e("Login automatically after signup is completed", "download-manager");  ?></label>
                         </div>
                         <hr/>


                         <div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_rss_feed_main" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_rss_feed_main'),1); ?> value="1" name="__wpdm_rss_feed_main"><?php _e( "Include Packages in Main RSS Feed" , "download-manager" ); ?></label><br/>
                             <em><?php printf(__( "Check this option if you want to show wpdm packages in your main <a target=\"_blank\" href=\"%s\">RSS Feed</a>" , "download-manager" ), get_bloginfo('rss_url')); ?></em>
                             <br/>

                         </div>

                         <?php do_action("wpdm_settings_frontend_general"); ?>


                     </div>
                 </div>

                 <?php

                 include dirname(__FILE__).'/profile-dashboard.php';

                 ?>

                 <?php do_action("wpdm_settings_frontend"); ?>


<style> legend{ font-weight: 800; } fieldset#cpi legend input { margin: 0 !important; }</style>
