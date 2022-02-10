<?php
if (!defined('ABSPATH')) {
    exit;
}

class TenWebLibSubscribe
{
    // //////////////////////////////////////////////////////////////////////////////////////
    // Events                                                                              //
    // //////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////////////
    // Constants //
    // //////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////////////
    // Variables //
    // //////////////////////////////////////////////////////////////////////////////////////
    public $config;
    // //////////////////////////////////////////////////////////////////////////////////////
    // Constructor & Destructor //
    // //////////////////////////////////////////////////////////////////////////////////////
    public function __construct($config = array())
    {
        $this->config = $config;
        add_action('admin_init', array($this, 'after_subscribe'));
    }
    // //////////////////////////////////////////////////////////////////////////////////////
    // Public Methods //
    // //////////////////////////////////////////////////////////////////////////////////////

    public function subscribe_scripts()
    {
        $wd_options = $this->config;
        wp_register_script('subscribe_js', $wd_options->wd_url_js . '/subsribe.js');
        wp_enqueue_script('subscribe_js');

    }

    public function subscribe_styles()
    {
        $wd_options = $this->config;
        wp_enqueue_style($wd_options->prefix . 'subscribe', $wd_options->wd_url_css . '/subscribe.css');

    }

    public function subscribe_display_page()
    {
        $wd_options = $this->config;
        require_once($wd_options->wd_dir_templates . "/display_subscribe.php");
    }

    public function after_subscribe()
    {
        $wd_options = $this->config;
        if (isset($_GET[$wd_options->prefix . "_sub_action"])) {

            if ($_GET[$wd_options->prefix . "_sub_action"] == "allow") {
                //$api = new TenWebLibApi($wd_options);
                $all_plugins = array();
                $plugins = get_plugins();
                foreach ($plugins as $slug => $data) {
                    $plugin = array(
                        "Name"      => $data["Name"],
                        "PluginURI" => $data["PluginURI"],
                        "Author"    => $data["Author"],
                        "AuthorURI" => $data["AuthorURI"]
                    );
                    $all_plugins[$slug] = $plugin;
                }

                $data = array();
                $data["wp_site_url"] = site_url();

                $admin_data = wp_get_current_user();

                $user_first_name = get_user_meta($admin_data->ID, "first_name", true);
                $user_last_name = get_user_meta($admin_data->ID, "last_name", true);

                $data["name"] = $user_first_name || $user_last_name ? $user_first_name . " " . $user_last_name : $admin_data->data->user_login;

                $data["email"] = $admin_data->data->user_email;
                $data["wp_version"] = get_bloginfo('version');
                $data["product_id"] = $wd_options->plugin_id;
                $data["all_plugins"] = json_encode($all_plugins);


                $response = wp_remote_post(TEN_WEB_LIB_SUBSCRIBE_URL, array(
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

                if (is_array($response_body) && $response_body["body"]["msg"] == "ok") {

                }

            }
            if (get_option($wd_options->prefix . "_subscribe_done") != 1) {
                update_option($wd_options->prefix . "_subscribe_done", 1);
            } else {
                add_option($wd_options->prefix . "_subscribe_done", "1", '', 'no');
            }

            wp_safe_redirect($wd_options->after_subscribe);
        }

    }
    // //////////////////////////////////////////////////////////////////////////////////////
    // Getters & Setters //
    // //////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////////////
    // Private Methods //
    // //////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////////////
    // Listeners //
    // //////////////////////////////////////////////////////////////////////////////////////
}
