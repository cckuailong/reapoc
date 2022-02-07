<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Add_Ons Class
 * 
 * This is used to display SJB Add-ons listing.
 * 
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.3.2
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin
 * @author      PressTigers <support@presstigers.com> 
 */

class Simple_Job_Board_Add_Ons {

    /**
     * Marketplace API URL.
     *
     * @since    2.3.2
     * @access   public
     * @var      string    $api_url    Marketplace API URL.
     */
    public $api_url;

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct() {
        // Initialize Marketplace API URL
        $this->api_url = 'https://market.presstigers.com/wc-api/v2/products';

        // Action - Add Settings Menu
        add_action('admin_menu', array($this, 'admin_menu'), 13);

    }

    /**
     * Add Add-ons Page Under Job Board Menu.
     * 
     * @since   2.3.2
     */
    public function admin_menu() {
        add_submenu_page('edit.php?post_type=jobpost', esc_html__('Simple Job Board Add-ons', 'simple-job-board'), esc_html__('Add-ons', 'simple-job-board'), 'manage_options', 'sjb-add-ons', array($this, 'sjb_add_ons'));
    }

    /**
     * Simple Job Board Add-ons
     * 
     * @Since   2.3.2
     */
    public function sjb_add_ons() {
        ?>
        <div class="sjb-wrap container-fluid">
            <h1><?php echo esc_html__('Simple Job Board Add-ons', 'simple-job-board'); ?></h1>
            <?php
            $add_on_response = $this->sjb_get_items();
            // Print error if error, otherwise print information
            if (is_wp_error($add_on_response)) {
                echo '<div id="message" class="error notice notice-error">';
                echo '<p>The following error occurred when contacting Simple Job Board server: ' . wp_strip_all_tags($add_on_response->get_error_message()) . '</p>';
                echo '</div>';
            } else {
                $add_on_body = json_decode($add_on_response);
                $add_on_items = $add_on_body->products;
                ?>

                <!-- List SJB Add-ons --> 
                <div class="add-ons">
                    <div class="row">
                        <div class="products">
                            <?php
                            // Retrive Add-ons Reuired Data
                            foreach ($add_on_items as $item) {
                                if ("Simple Plugin Integration" !== $item->title &&
                                        "Plugin Integration With Extended Support" !== $item->title &&
                                        in_array('Simple Job Board', $item->categories)
                                ) {
                                    $url = explode('/', $item->permalink);
                                    $home_url = $url[0] . '//' . $url[2];
                                    ?>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 product">
                                        <article>
                                            <a href="<?php echo esc_url($item->permalink); ?>" target="_blank">
                                                <img title="<?php echo esc_attr($item->title); ?>" alt="<?php echo esc_attr($item->title); ?>" class="img-responsive" src="<?php echo esc_url($item->images[0]->src); ?>">
                                                <h3><?php echo esc_attr($item->title); ?></h3>
                                            </a>
                                            <?php echo $item->short_description; ?>
                                            <div class="price add-to-cart">
                                                <span><b><?php echo '$' . $item->price; ?></b></span>
                                                <a href="<?php echo esc_url($home_url . "/cart/?add-to-cart=" . $item->id); ?>" class="button button-primary cart-btn" target="_blank"><?php echo __('Buy Now', 'simple-job-board'); ?></a>
                                            </div>
                                        </article>
                                    </div>
                                    <?php
                                }
                            }
                            ?>

                            <!-- Contact Us Link -->
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 product">
                                <article>
                                    <a href="<?php echo esc_url("https://market.presstigers.com/contact-us/"); ?>" target="_blank">
                                        <img title="SJB customizations" alt="Contact for add-on." class="img-responsive" src="<?php echo esc_url(untrailingslashit(plugins_url(basename(plugin_dir_path(__DIR__)), basename(__DIR__))) . '/admin/images/add-on.jpg'); ?>" >
                                        <h3><?php echo esc_html__('Unable to locate required add-on?', 'simple-job-board'); ?></h3>
                                    </a>
                                    <a href="<?php echo esc_url("https://market.presstigers.com/contact-us/"); ?>" class="button button-primary add-on-contact" target="_blank"><?php echo __('Contact Us', 'simple-job-board'); ?></a>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    /**
     * Get SJB Items.
     *
     * @return  string|WP_Error Returns the contents of the response on success|WP_Error on failure
     */
    public function sjb_get_items() {
        
        // Https Authentication args
        $params = array(
            'consumer_key' => 'ck_0fbca498c2fe9491ce5cfcdbc2a03d2b396153c7',
            'consumer_secret' => 'cs_66dafc2cb72361dd98cf37cb08ec5508eb49cc97',
        );

        // Generate the URL
        $url = esc_url_raw($this->api_url) . '?' . http_build_query($params);

        // Make API request
        $response = wp_remote_post(
                $url, array(
            'method' => 'GET',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => $params,
            'cookies' => array(),
                )
        );

        // Check the response code
        $response_code = wp_remote_retrieve_response_code($response);
        
        $response_message = wp_remote_retrieve_response_message($response);

        if (200 != $response_code && !empty($response_message)) {
            return new WP_Error($response_code, $response_message);
        } elseif (200 != $response_code) {
            return new WP_Error($response_code, esc_html__('Unknown error occurred'));
        } else {
            return wp_remote_retrieve_body($response);
        }
    }

    /**
     * Generate OAuth Signature:
     *
     * @since 2.3.2
     *
     * @param   array   $params         Query Parameters (including oauth_*)
     * @param   string  $http_method    e.g. GET
     * @return  string  signature
     */
    public function generate_oauth_signature($params) {
        $base_request_uri = rawurlencode($this->api_url);
        if (isset($params['filter'])) {
            $filters = $params['filter'];
            unset($params['filter']);
            foreach ($filters as $filter => $filter_value) {
                $params['filter[' . $filter . ']'] = $filter_value;
            }
        }

        // Normalize Parameter & Sort them
        $params = $this->normalize_parameters($params);
        uksort($params, 'strcmp');

        // Form Query String
        $query_params = array();
        foreach ($params as $param_key => $param_value) {
            $query_params[] = $param_key . '%3D' . $param_value; // join with equals sign
        }

        $query_string = implode('%26', $query_params); // join with ampersand
        $string_to_sign = 'GET&' . $base_request_uri . '&' . $query_string;

        return base64_encode(hash_hmac('SHA256', $string_to_sign, 'cs_7ad8e2e0db0cbd1eeaa2095f2090cbce626a8758', TRUE));
    }

    /**
     * Normalize each parameter by assuming each parameter may have already been
     * encoded, so attempt to decode, and then re-encode according to RFC 3986
     *
     * @since 2.3.2
     * 
     * @param   array   $parameters Un-normalized pararmeters
     * @return  array   Normalized Parameters
     */
    private function normalize_parameters($parameters) {
        $normalized_parameters = array();
        foreach ($parameters as $key => $value) {
            // Percent Symbols (%) Must be Double-encoded
            $key = str_replace('%', '%25', rawurlencode(rawurldecode($key)));
            $value = str_replace('%', '%25', rawurlencode(rawurldecode($value)));

            $normalized_parameters[$key] = $value;
        }
        return $normalized_parameters;
    }

}

new Simple_Job_Board_Add_Ons();