<?php
if (!defined('ABSPATH')) { exit; }
class Chaty_free_review_box {

    public $plugin_name = "Chaty";

    public $plugin_slug = "chaty";

    public function __construct() {

        add_action("wp_ajax_".$this->plugin_slug."_review_box", array($this, "affiliate_program"));

        add_action('admin_notices', array($this, 'admin_notices'));
    }

    public function affiliate_program() {
        if(current_user_can("manage_options")) {
            $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);
            $days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_STRING);
            if (!empty($nonce) && wp_verify_nonce($nonce, $this->plugin_slug . "_review_box")) {
                if ($days == -1) {
                    add_option($this->plugin_slug . "_hide_review_box", "1");
                } else {
                    $date = date("Y-m-d", strtotime("+" . $days . " days"));
                    update_option($this->plugin_slug . "_show_review_box_after", $date);
                }
            }
            die;
        }
    }

    public function admin_notices() {
        if(current_user_can("manage_options")) {
            $is_hidden = get_option($this->plugin_slug."_hide_review_box");
            if($is_hidden !== false) {
                return;
            }
            $current_count = get_option($this->plugin_slug."_show_review_box_after");
            if($current_count === false) {
                $date = date("Y-m-d", strtotime("+7 days"));
                add_option($this->plugin_slug."_show_review_box_after", $date);
                return;
            }
            $date_to_show = get_option($this->plugin_slug."_show_review_box_after");
            if($date_to_show !== false) {
                $current_date = date("Y-m-d");
                if($current_date < $date_to_show) {
                    return;
                }
            }
            ?>
            <style>
                .<?php echo $this->plugin_slug ?>-premio-review-box p a {
                    display: inline-block;
                    float: right;
                    text-decoration: none;
                    color: #999999;
                    position: absolute;
                    right: 12px;
                    top: 12px;
                }
                .<?php echo $this->plugin_slug ?>-premio-review-box p a:hover, .<?php echo $this->plugin_slug ?>-premio-review-box p a:focus {
                    color: #333333;
                }
                .<?php echo $this->plugin_slug ?>-premio-review-box .button span {
                    display: inline-block;
                    line-height: 27px;
                    font-size: 16px;
                }
                .<?php echo $this->plugin_slug ?>-review-box-popup {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 10001;
                    background: rgba(0,0,0,0.65);
                    top: 0;
                    left: 0;
                    display: none;
                }
                .<?php echo $this->plugin_slug ?>-review-box-popup-content {
                    background: #ffffff;
                    padding: 20px;
                    position: absolute;
                    max-width: 450px;
                    width: 100%;
                    margin: 0 auto;
                    top: 45%;
                    left: 0;
                    right: 0;
                    -webkit-border-radius: 5px;
                    -moz-border-radius: 5px;
                    border-radius: 5px;: ;
                }
                .<?php echo $this->plugin_slug ?>-review-box-title {
                    padding: 0 0 10px 0;
                    font-weight: bold;
                }
                .<?php echo $this->plugin_slug ?>-review-box-options a {
                    display: block;
                    margin: 5px 0 5px 0;
                    color: #333;
                    text-decoration: none;
                }
                .<?php echo $this->plugin_slug ?>-review-box-options a.dismiss {
                    color: #999;
                }
                .<?php echo $this->plugin_slug ?>-review-box-options a:hover, .affiliate-options a:focus {
                    color: #0073aa;
                }
                button.<?php echo $this->plugin_slug ?>-close-review-box-popup {
                    position: absolute;
                    top: 5px;
                    right: 0;
                    border: none;
                    background: transparent;
                    cursor: pointer;
                }
                a.button.button-primary.<?php echo $this->plugin_slug ?>-review-box-btn {
                    font-size: 14px;
                    background: #F51366;
                    color: #fff;
                    border: solid 1px #F51366;
                    border-radius: 3px;
                    line-height: 24px;
                    -webkit-box-shadow: 0 3px 5px -3px #333333;
                    -moz-box-shadow: 0 3px 5px -3px #333333;
                    box-shadow: 0 3px 5px -3px #333333;
                    text-shadow: none;
                }
                .notice.notice-info.chaty-notice {
                    display: block !important;
                    position: relative;
                    padding: 1px 30px 1px 12px;
                }
                .notice.notice-info.chaty-notice ul li {
                    margin: 0;
                }
                .notice.notice-info.chaty-notice ul li a {
                    color: #0073aa;
                    font-size: 14px;
                    text-decoration: underline;
                }
                .<?php echo $this->plugin_slug ?>-premio-review-box p {
                    display: inline-block;
                    line-height: 30px;
                    vertical-align: middle;
                    padding: 0 10px 0 0;
                }
                .<?php echo $this->plugin_slug ?>-premio-review-box p img {
                    width: 30px;
                    height: 30px;
                    display: inline-block;
                    margin: 0 10px;
                    vertical-align: middle;
                    border-radius: 15px;
                }
            </style>
            <div class="notice notice-info chaty-notice <?php echo $this->plugin_slug ?>-premio-review-box <?php echo $this->plugin_slug ?>-premio-review-box">
                <p>
                    Hi there, it seems like <b><?php echo $this->plugin_name ?></b> is bringing you some value, and that's pretty awesome! Can you please show us some love and rate <?php echo $this->plugin_name ?> on WordPress? It'll only take 2 minutes of your time, and will really help us spread the word
                    - <b>Gal Dubinski</b>, Co-founder <img width="30px" src="<?php echo esc_url(CHT_PLUGIN_URL."admin/assets/images/owner.png") ?>" />
                    <a href="javascript:;" class="dismiss-btn <?php echo $this->plugin_slug ?>-premio-review-dismiss-btn"><span class="dashicons dashicons-no-alt"></span></a>
                </p>
                <div class="clear clearfix"></div>
                <ul>
                    <li><a class="<?php echo $this->plugin_slug ?>-premio-review-box-hide-btn" href="https://wordpress.org/support/plugin/chaty/reviews/?filter=5" target="_blank">I'd love to help :)</a></li>
                    <li><a class="<?php echo $this->plugin_slug ?>-premio-review-box-future-btn" href="javascript:;">Not this time</a></li>
                    <li><a class="<?php echo $this->plugin_slug ?>-premio-review-box-hide-btn" href="javascript:;">I've already rated you</a></li>
                </ul>
            </div>
            <div class="<?php echo $this->plugin_slug ?>-review-box-popup">
                <div class="<?php echo $this->plugin_slug ?>-review-box-popup-content">
                    <button class="<?php echo $this->plugin_slug ?>-close-review-box-popup"><span class="dashicons dashicons-no-alt"></span></button>
                    <div class="<?php echo $this->plugin_slug ?>-review-box-title">Would you like us to remind you about this later?</div>
                    <div class="<?php echo $this->plugin_slug ?>-review-box-options">
                        <a href="javascript:;" data-days="3">Remind me in 3 days</a>
                        <a href="javascript:;" data-days="10">Remind me in 10 days</a>
                        <a href="javascript:;" data-days="-1" class="dismiss">Don't remind me about this</a>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function(){
                    jQuery("body").addClass("has-premio-box");
                    jQuery(document).on("click", ".<?php echo $this->plugin_slug ?>-premio-review-dismiss-btn, .<?php echo $this->plugin_slug ?>-premio-review-box-future-btn", function(){
                        jQuery(".<?php echo $this->plugin_slug ?>-review-box-popup").show();
                    });
                    jQuery(document).on("click", ".<?php echo $this->plugin_slug ?>-close-review-box-popup", function(){
                        jQuery(".<?php echo $this->plugin_slug ?>-review-box-popup").hide();
                    });
                    jQuery(document).on("click",".<?php echo $this->plugin_slug ?>-premio-review-box-hide-btn",function(){
                        jQuery(".<?php echo $this->plugin_slug ?>-review-box-options a:last").trigger("click");
                    });
                    jQuery(document).on("click", ".<?php echo $this->plugin_slug ?>-review-box-options a", function(){
                        var dataDays = jQuery(this).attr("data-days");
                        jQuery(".<?php echo $this->plugin_slug ?>-review-box-popup").remove();
                        jQuery(".<?php echo $this->plugin_slug ?>-premio-review-box").remove();
                        jQuery("body").removeClass("has-premio-box");
                        jQuery.ajax({
                            url: "<?php echo admin_url("admin-ajax.php") ?>",
                            data: "action=<?php echo esc_attr($this->plugin_slug) ?>_review_box&days="+dataDays+"&nonce=<?php echo esc_attr(wp_create_nonce($this->plugin_slug."_review_box")) ?>",
                            type: "post",
                            success: function() {
                                jQuery(".<?php echo $this->plugin_slug ?>-review-box-popup").remove();
                                jQuery(".<?php echo $this->plugin_slug ?>-premio-review-box").remove();
                            }
                        });
                    });
                });
            </script>
            <?php
        }
    }
}
$Chaty_free_review_box = new Chaty_free_review_box();