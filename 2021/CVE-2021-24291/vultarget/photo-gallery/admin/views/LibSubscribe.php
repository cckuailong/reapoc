<?php
$admin_data = wp_get_current_user();

$user_first_name = get_user_meta($admin_data->ID, "first_name", true);
$user_last_name = get_user_meta($admin_data->ID, "last_name", true);

$name = $user_first_name || $user_last_name ? $user_first_name . " " . $user_last_name : $admin_data->data->user_login;
$email_subscribe = get_option("bwg_subscribe_email");
$prefix = BWG()->prefix; // Current plugin prefix.
$plugin_url = BWG()->plugin_url; // Current plugin URL.
$menu_slug = "galleries_" . BWG()->prefix; // Current plugin slug.
$title_text = __( "Hey! I know how hard and time-consuming creating a well-structured gallery can be.", $prefix ); // Message Box text.
?>
<script>
    jQuery(document).on("ready", function () {
        jQuery("#tenweb_subscribe_submit").on("click", function () {
            var error = 0;
            var inputs = {
                "user_name" : "<?php _e("Name is required.", $prefix); ?>",
                "user_email" : "<?php _e("Please enter a valid email.", $prefix ); ?>"
            };
            for (var i in inputs) {
                var input =  jQuery("#<?php echo $prefix; ?>_" + i);
                if (input.val() == "" || (i == "user_email" && !tenWebSubscrineIsEmail(input.val()))) {
                    input.closest("p").addClass("error");
                    input.closest("p").find(".error_msg").html(inputs[i]);
                    error++;
                }
                else {
                    input.closest("p").removeClass("error");
                    input.closest("p").find(".error_msg").html("");
                }
            }
            if (error == 0 ) {
                jQuery(this).closest("form").submit();
            } else {
                return false;
            }
        });
    });
    function tenWebSubscrineIsEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
</script>
<div id="tenweb_new_subscribe" class="tenweb_subscribe">
    <div class="tenweb_subscribe_container">
        <div class="tenweb_subscribe_content clear">
            <div id="tenweb_logo_div">
                <a id="tenweb_logo"></a>
            </div>
            <div class="left">
                <div class="founder"><img src="<?php echo $plugin_url; ?>/images/subscribe/founder.png" class="founder_img" alt="Arto"></div>
                <div class="founder_info">
                    <h4>Arto</h4>
                    <p>10Web Founder</p>
                </div>
            </div>
            <div class="right">
                <div id="tenweb_title">
                    <p><?php echo $title_text ?></p>
                    <h2><?php _e( "So, I handcrafted a step-by-step ebook to make it super easy for you. Enjoy ", $prefix ); ?><img src="<?php echo $plugin_url; ?>/images/subscribe/smile.png"></h2>     
                </div>
                <div id="tablet_book">
                    <img src="<?php echo $plugin_url; ?>/images/subscribe/ebook.png">
                </div>
                <form id="tenweb_form" method="get" action="admin.php?page=<?php echo $prefix; ?>_subscribe">
                    <p>
                        <label for="user_name"><?php _e( "NAME", $prefix ); ?> <span>*</span></label>
                        <input type="text" name="<?php echo $prefix; ?>_user_name"  id="<?php echo $prefix; ?>_user_name" placeholder="Fill Your Name" value="<?php echo $name; ?>">
                        <span class='error_msg'></span>
                    </p>
                    <p>
                        <label for="user_name"><?php _e( "EMAIL ADDRESS", $prefix ); ?> <span>*</span></label>
                        <input type="text" name="<?php echo $prefix; ?>_user_email"  id="<?php echo $prefix; ?>_user_email" placeholder="Fill Your Email" value="<?php echo $admin_data->data->user_email; ?>">
                        <span class='error_msg'></span>
                    </p>
                    <div class="form_desc"><?php _e( "Keep in mind that submitting this form will allow 10Web to store your name, email, and site URL. Being GDPR-compliant, we won’t ever share your info with third parties...", $prefix ); ?></div>
                    <div id="form_buttons">
                        <input type="hidden" name="<?php echo $prefix; ?>_sub_action" value="allow" />
                        <input type="hidden" name="page" value="<?php echo $prefix . '_subscribe' ; ?>" />
                        <input type="button" id="tenweb_subscribe_submit" value="RECEIVE EBOOK" />
                        <a href="<?php echo "admin.php?page=" . $prefix . "_subscribe&" . $prefix . "_sub_action=skip" ;?>" class="skip more" ><?php _e( "Skip", $prefix ); ?></a>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<?php if ($email_subscribe !== false && isset($_GET[$prefix . "_sub_action"]) && $_GET[$prefix . "_sub_action"] == "allow") : ?>
<div id="tenweb_subscribe_popup" class="subscribed">
    <div class="subscribe_popup_content">
        <div class="subscribe_email"><?php echo $email_subscribe; ?></div>
        <h3>We've just sent you the eBook!</h3>
        <p class="bold">If you are a Gmail user check:</p>
        <p>Primary, Promotions, Social, Updates or Forums.</p>
        <img src="<?php echo $plugin_url; ?>/images/subscribe/popup_img.png">
        <p class="bold">Warning: also check your spam or junk!</p>
        <p><b>1.</b> Select the message and click Not Spam.</p>
        <p><b>2.</b> Recheck all inbox tabs.</p>
        <a href="<?php echo admin_url('admin.php?page=' . $menu_slug); ?>" class="got_it">GOT IT</a>
    </div>
</div>
<?php endif; ?>
