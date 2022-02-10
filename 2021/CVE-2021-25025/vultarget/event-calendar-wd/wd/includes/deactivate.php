<?php
if (!defined('ABSPATH')) {
    exit;
}

class TenWebLibDeactivate
{
    ////////////////////////////////////////////////////////////////////////////////////////
    // Events                                                                             //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Constants                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Variables                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////
    public $deactivate_reasons = array();
    public $config;
    // Reason IDs
    const REASON_PLUGIN_IS_HARD_TO_USE_TECHNICAL_PROBLEMS = "reason_plugin_is_hard_to_use_technical_problems";
    const REASON_FREE_VERSION_IS_LIMITED                  = "reason_free_version_limited";
    const REASON_PRO_EXPENSIVE                            = "reason_premium_expensive";
    const REASON_UPGRADING_TO_PAID_VERSION                = "reason_upgrading_to_paid_version";
    const REASON_TEMPORARY_DEACTIVATION                   = "reason_temporary_deactivation";

    ////////////////////////////////////////////////////////////////////////////////////////
    // Constructor & Destructor                                                           //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function __construct($config = array())
    {
        $this->config = $config;
        $wd_options = $this->config;

        $this->deactivate_reasons = array(
            1 => array(
                'id'   => self::REASON_PLUGIN_IS_HARD_TO_USE_TECHNICAL_PROBLEMS,
                'text' => __('Technical problems / hard to use', $wd_options->prefix),
            ),
            2 => array(
                'id'   => self::REASON_FREE_VERSION_IS_LIMITED,
                'text' => __('Free version is limited', $wd_options->prefix),
            ),
            3 => array(
                'id'   => self::REASON_UPGRADING_TO_PAID_VERSION,
                'text' => __('Upgrading to paid version', $wd_options->prefix),
            ),
            4 => array(
                'id'   => self::REASON_TEMPORARY_DEACTIVATION,
                'text' => __('Temporary deactivation', $wd_options->prefix),
            ),
        );

        add_action('admin_footer', array($this, 'add_deactivation_feedback_dialog_box'));
        add_action('admin_init', array($this, 'submit_and_deactivate'));


        // add_action('admin_enqueue_scripts', array($this, 'scripts'));
        // Just enqueue styles/scripts and they will be in the footer.
        $this->scripts();

    }
    ////////////////////////////////////////////////////////////////////////////////////////
    // Public Methods                                                                     //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function add_deactivation_feedback_dialog_box()
    {
        $deactivate_reasons = $this->deactivate_reasons;
        $wd_options = $this->config;

        ?>
        <script>
            jQuery(document).ready(function () {
                tenwebReady("<?php echo $wd_options->prefix; ?>");
            });
        </script>
        <?php

        $deactivate_url =
            add_query_arg(
                array(
                    'action'   => 'deactivate',
                    'plugin'   => plugin_basename($wd_options->plugin_main_file),
                    '_wpnonce' => wp_create_nonce('deactivate-plugin_' . plugin_basename($wd_options->plugin_main_file))
                ),
                admin_url('plugins.php')
            );

        require($wd_options->wd_dir_templates . '/display_deactivation_popup.php');
    }


    public function scripts()
    {
        $wd_options = $this->config;
        wp_enqueue_style('tenweb-deactivate-popup', $wd_options->wd_url_css . '/deactivate_popup.css', array(), get_option($wd_options->prefix . "_version"));
        wp_enqueue_script('tenweb-deactivate-popup', $wd_options->wd_url_js . '/deactivate_popup.js', array(), get_option($wd_options->prefix . "_version"));

        $admin_data = wp_get_current_user();
        wp_localize_script('tenweb-deactivate-popup', $wd_options->prefix . 'WDDeactivateVars', array(
            "prefix"           => $wd_options->prefix,
            "deactivate_class" => $wd_options->prefix . '_deactivate_link',
            "email"            => $admin_data->data->user_email,
            "plugin_wd_url"    => $wd_options->plugin_wd_url,
        ));


    }

    public function submit_and_deactivate()
    {
        $wd_options = $this->config;
        if (isset($_POST[$wd_options->prefix . "_submit_and_deactivate"])) {

            if ($_POST[$wd_options->prefix . "_submit_and_deactivate"] == 2 || $_POST[$wd_options->prefix . "_submit_and_deactivate"] == 3) {
                //$api = new TenWebLibApi( $wd_options );

                $data = array();

                $data["reason"] = isset($_POST[$wd_options->prefix . "_reasons"]) ? $_POST[$wd_options->prefix . "_reasons"] : "";
                $data["site_url"] = site_url();
                $data["product_id"] = $wd_options->plugin_id;

                $data["additional_details"] = isset($_POST[$wd_options->prefix . "_additional_details"]) ? $_POST[$wd_options->prefix . "_additional_details"] : "";
                $admin_data = wp_get_current_user();
                $data["email"] = isset($_POST[$wd_options->prefix . "_email"]) ? $_POST[$wd_options->prefix . "_email"] : $admin_data->data->user_email;
                $user_first_name = get_user_meta($admin_data->ID, "first_name", true);
                $user_last_name = get_user_meta($admin_data->ID, "last_name", true);

                $data["name"] = $user_first_name || $user_last_name ? $user_first_name . " " . $user_last_name : $admin_data->data->user_login;

                $response = wp_remote_post(TEN_WEB_LIB_DEACTIVATION_URL, array(
                        'method'      => 'POST',
                        'timeout'     => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'headers'     => array("Accept" => "application/x.10webcore.v1+json"),
                        'body'        => $data,
                        'cookies'     => array()
                    )
                );

                $response_body = (!is_wp_error($response) && isset($response["body"])) ? json_decode($response["body"], true) : null;
                /*if (is_array($response_body) && $response_body["body"]["msg"] == "Access") {

                }*/

            }
            if ($_POST[$wd_options->prefix . "_submit_and_deactivate"] == 2 || $_POST[$wd_options->prefix . "_submit_and_deactivate"] == 1) {
                $deactivate_url =
                    add_query_arg(
                        array(
                            'action'   => 'deactivate',
                            'plugin'   => plugin_basename($wd_options->plugin_main_file),
                            '_wpnonce' => wp_create_nonce('deactivate-plugin_' . plugin_basename($wd_options->plugin_main_file))
                        ),
                        admin_url('plugins.php')
                    );
                echo '<script>window.location.href="' . $deactivate_url . '";</script>';
            }

        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    // Getters & Setters                                                                  //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Private Methods                                                                    //
    ////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////
    // Listeners                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////

}

	
