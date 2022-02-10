<?php
if (!defined('ABSPATH')) {
    exit;
}
class Chaty_affiliate_program {

    public $plugin = "Chaty";

    public function __construct() {
        add_action("wp_ajax_".$this->plugin."_affiliate_program", array($this, "affiliate_program"));

        add_action('admin_notices', array($this, 'admin_notices'));
    }

    public function affiliate_program() {
        if(current_user_can("manage_options")) {
            $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);
            $days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_STRING);
            if (!empty($nonce) && wp_verify_nonce($nonce, $this->plugin . "_affiliate_program")) {
                if ($days == -1) {
                    add_option($this->plugin . "_hide_affiliate_box", "1");
                } else {
                    $date = date("Y-m-d", strtotime("+" . $days . " days"));
                    update_option($this->plugin . "_show_affiliate_box_after", $date);
                }
            }
            die;
        }
    }

    public function admin_notices() {
        if(current_user_can("manage_options")) {
            $is_hidden = get_option($this->plugin."_hide_affiliate_box");
            if($is_hidden !== false) {
                return;
            }
            $date_to_show = get_option($this->plugin."_show_affiliate_box_after");
            if($date_to_show === false || empty($date_to_show)) {
                $date = date("Y-m-d", strtotime("+5 days"));
                update_option($this->plugin."_show_affiliate_box_after", $date);
                return;
            }
            $current_date = date("Y-m-d");
            if($current_date < $date_to_show) {
                return;
            }
            ?>
            <style>
                .premio-affiliate p a {
                    display: inline-block;
                    float: right;
                    text-decoration: none;
                    color: #999999;
                    position: absolute;
                    right: 12px;
                    top: 12px;
                }
                .premio-affiliate p a:hover, .premio-affiliate p a:focus {
                    color: #333333;
                }
                .premio-affiliate .button span {
                    display: inline-block;
                    line-height: 27px;
                    font-size: 16px;
                }
                .premio-affiliate {
                    padding: 1px 100px 12px 12px;
                    margin: 15px 15px 2px;
                    position: relative;
                }
                .affiliate-popup {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 10001;
                    background: rgba(0,0,0,0.65);
                    top: 0;
                    left: 0;
                    display: none;
                }
                .affiliate-popup-content {
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
                .affiliate-title {
                    padding: 0 0 10px 0;
                    font-weight: bold;
                }
                .affiliate-options a {
                    display: block;
                    margin: 5px 0 5px 0;
                    color: #333;
                    text-decoration: none;
                }
                .affiliate-options a.dismiss {
                    color: #999;
                }
                .affiliate-options a:hover, .affiliate-options a:focus {
                    color: #0073aa;
                }
                button.close-affiliate-popup {
                    position: absolute;
                    top: 5px;
                    right: 0;
                    border: none;
                    background: transparent;
                    cursor: pointer;
                }
                a.button.button-primary.affiliate-btn {
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
            </style>
            <div class="notice notice-info chaty-notice premio-affiliate <?php echo $this->plugin ?>-premio-affiliate">
                <p>Hi there, you've been using Chaty for a while now. Do you know that <b><?php echo $this->plugin ?></b> has an affiliate program? Join now and get <b>25% lifetime commission</b> <a href="javascript:;" class="dismiss-btn"><span class="dashicons dashicons-no-alt"></span> Dismiss</a></p>
                <div class="clear clearfix"></div>
                <a class="button button-primary affiliate-btn" target="_blank" href=" https://premio.io/affiliates/?utm_source=inapp&plugin=chaty&domain=<?php echo $_SERVER['HTTP_HOST'] ?>">Tell me more <span class="dashicons dashicons-arrow-right-alt"></span></a>
            </div>
            <div class="affiliate-popup">
                <div class="affiliate-popup-content">
                    <button class="close-affiliate-popup"><span class="dashicons dashicons-no-alt"></span></button>
                    <div class="affiliate-title">Would you like us to remind you about this later?</div>
                    <div class="affiliate-options">
                        <a href="javascript:;" data-days="3">Remind me in 3 days</a>
                        <a href="javascript:;" data-days="10">Remind me in 10 days</a>
                        <a href="javascript:;" data-days="-1" class="dismiss">Don't remind me about this</a>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function(){
                    jQuery(document).on("click", ".premio-affiliate p a.dismiss-btn", function(){
                        jQuery(".affiliate-popup").show();
                    });
                    jQuery(document).on("click", ".close-affiliate-popup", function(){
                        jQuery(".affiliate-popup").hide();
                    });
                    jQuery(document).on("click", ".affiliate-options a", function(){
                        var dataDays = jQuery(this).attr("data-days");
                        jQuery(".affiliate-popup").hide();
                        jQuery(".<?php echo $this->plugin ?>-premio-affiliate").hide();
                        jQuery.ajax({
                            url: "<?php echo admin_url("admin-ajax.php") ?>",
                            data: "action=<?php echo esc_attr($this->plugin) ?>_affiliate_program&days="+dataDays+"&nonce=<?php echo esc_attr(wp_create_nonce($this->plugin."_affiliate_program")) ?>",
                            type: "post",
                            success: function() {
                                jQuery(".affiliate-popup").remove();
                                jQuery(".<?php echo $this->plugin ?>-premio-affiliate").remove();
                            }
                        });
                    });
                });
            </script>
            <?php
        }
    }
}
$Chaty_affiliate_program = new Chaty_affiliate_program();