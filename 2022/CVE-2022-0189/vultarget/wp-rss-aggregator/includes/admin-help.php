<?php

use Aventura\Wprss\Core\Licensing\License\Status as License_Status;

/**
     * Checks if the HS becaon is enabled or not.
     *
     * @since 4.12.1
     *
     * @return bool True if enabled, false if not.
     */
    function wprss_is_help_beacon_enabled() {
        return (int) get_option('wprss_hs_beacon_enabled', 1) === 1;
    }

    /**
     * Build the Help page
     *
     * @since 4.2
     */
    function wprss_help_page_display() {
        ?>

        <div class="wrap">
            <h2><?php _e( 'Help & Support', 'wprss' ); ?></h2>
            <?php do_action('wpra/help_page/after_title') ?>
            <h3><?php _e( 'Knowledge Base', 'wprss' ) ?></h3>
            <?php
                printf(
                    wpautop(
                        __( 'In the <a href="%s">WP RSS Aggregator knowledge base</a> you will find comprehensive details and tutorials on how to use the core plugin and all the add-ons.

                            There are also some videos to help you make a quick start to setting up and enjoying this plugin.',
                            'wprss'
                        )
                    ),
                    esc_attr('https://kb.wprssaggregator.com/')
                );
            ?>
            <h3><?php _e( 'Frequently Asked Questions (FAQ)', 'wprss' ) ?></h3>
            <?php
                printf(
                    wpautop(
                        __(
                            'If after going through the knowledge base you still have questions, please take a look at the <a href="%s">FAQ section</a>. We set this up purposely to answer the most commonly asked questions from our users.',
                            'wprss'
                        )
                    ),
                    esc_attr('https://kb.wprssaggregator.com/category/359-faqs')
                )
            ?>

            <?php
            if ( wprss_licensing_get_manager()->licenseWithStatusExists( License_Status::VALID ) ) {
                wprss_premium_help_display();
            } else {
                wprss_free_help_display();
            }
            ?>
            <h3><?= __('Built-in Help Beacon', 'wprss') ?></h3>
            <form method="POST">
                <p>
                    <?= __('The help beacon is an interactive button that appears on the bottom-right section of WP RSS Aggregator admin pages.', 'wprss'); ?>
                    <?= __('It provides access to our extensive knowledge base where you can find the answers to the most commonly asked questions.', 'wprss'); ?>
                </p>
                <p>
                    <?= __('The beacon only works on WP RSS Aggregator admin pages and does not track your mouse clicks and/or keyboard input.', 'wprss'); ?>
                </p>

                <?php if (wprss_is_help_beacon_enabled()): ?>
                    <p><?= __('The support beacon is currently <b>enabled</b>.', 'wprss'); ?></p>
                    <button type="submit" name="wprss_hs_beacon_enabled" value="0" class="button button-secondary">
                        <?= __('Disable support beacon', 'wprss'); ?>
                    </button>
                <?php else: ?>
                    <p>
                        <?= __('By enabling the help beacon, you are consenting to this data collection.', 'wprss'); ?>
                    </p>
                    <button type="submit" name="wprss_hs_beacon_enabled" value="1" class="button button-primary">
                        <?= __('Enable support beacon', 'wprss'); ?>
                    </button>
                <?php endif; ?>

                <?php wp_nonce_field('wprss_hs_beacon_enabled'); ?>
            </form>
        </div>
        <?php
        do_action('wpra/help_page/bottom');
    }

    // Handler to update the HS beacon enabled option
    add_action('init', function () {
        if (!is_admin()) {
            return;
        }

        $enabled = filter_input(INPUT_POST, 'wprss_hs_beacon_enabled', FILTER_VALIDATE_INT);

        if ($enabled !== null) {
            check_admin_referer('wprss_hs_beacon_enabled');
            update_option('wprss_hs_beacon_enabled', $enabled);
        }
    });

    /**
     * Print the premium help section, linking to the contact us page on the site.
     *
     * @since 4.11.3
     */
    function wprss_premium_help_display() {
        printf('<h3>%s</h3>', __('Premium Support', 'wprss'));
        printf(
            __(
                'Contact us <a href="%s" target="%s=">here</a> for pre-sales and premium support.',
                'wprss'
            ),
            esc_attr("https://www.wprssaggregator.com/contact/"),
            esc_attr("wpra-premium-contact-us-form")
        );
    }

    /**
     * Print the premium help section with inline support form.
     *
     * (Currently unused)
     *
     * @since 4.7
     */
    function wprss_premium_help_support_form() {
        // Addon and license object, both detected in the below algorithm that searches for a
        // premium addon that is activated with a valid license
        $addon = null;
        $license = null;
        // Get license statuses option
        $statuses = get_option( 'wprss_settings_license_statuses', array() );
        // Iterate all statuses
        foreach ( $statuses as $_key => $_value ) {
            // If not a license status key, continue to next
            $_keyPos = strpos($_key, '_license_status');
            if ( $_keyPos === FALSE ) {
                continue;
            }

            // If the status is not valid, continue to next
            if ($_value !== 'valid') {
                continue;
            }

            // Get the addon ID
            $_addonId = substr( $_key, 0, $_keyPos );
            // Get the license
            $_license = wprss_licensing_get_manager()->checkLicense( $_addonId, 'ALL' );
            // If the license is not null
            if ($_license !== null) {
                // Save its details
                $addon = $_addonId;
                $license = $_license;
                // And stop iterating
                break;
            }
        }

        // If we didn't find an add-on with a valid license, show the free help text.
        if ( $addon === null || $license === null ) {
            wprss_free_help_display();
            return;
        }

        // Get the full license info so we can prefill the name and email
        $customer_name = is_object($license) ? esc_attr($license->customer_name) : '';
        $customer_email = is_object($license) ? esc_attr($license->customer_email) : '';

        echo '<h3>' . __('Email Support', 'wprss') . '</h3>';
        echo wpautop(__("If you still can't find an answer to your query after reading the documentation and going through the FAQ, just fill out the support request form below. We'll be happy to help you out.", 'wprss'));

        ?>

        <form method="post">
            <table>
                <tr>
                    <td><strong><?= __('From: ', 'wprss'); ?></strong></td>
                    <td>
                        <input
                            type='text'
                            name='support-name'
                            value="<?= $customer_name ?>"
                            placeholder='<?= __('Name', 'wprss'); ?>'
                            style='width:100%;'
                        />
                    </td>
                    <td>
                        <input
                            type='text'
                            name='support-email'
                            value="<?= $customer_email ?>"
                            placeholder='<?= __('Email', 'wprss'); ?>'
                            style='width:100%;'
                        />
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;">
                        <small><?php _e('Replies will be sent to this email address.'); ?></small>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input
                            type='text'
                            name='support-subject'
                            placeholder='<?= __('Subject', 'wprss'); ?>'
                            style='width:100%;'>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <textarea
                            name='support-message'
                            rows='10'
                            cols='80'
                            placeholder='<?= __('Message', 'wprss'); ?>'></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <strong><?= __('Attachments', 'wprss') ?>: </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><input type='checkbox' name='support-include-log' value='checked' checked>
                        <?= __('WP RSS Aggregator log file', 'wprss') ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><input type='checkbox' name='support-include-sys' value='checked' checked>
                        <?= __('WordPress debugging information', 'wprss'); ?>
                    </td>
                </tr>
            </table>
        </form>

        <div style='line-height:2.3em; margin-top:10px;'>
            <button id='send-message-btn' class='button button-primary'>
                <?= __('Send Message', 'wprss'); ?>
            </button>
            <div id='support-error'></div>
        </div>

        <?php
    }

    /**
     * Print the free help section with link to forums.
     *
     * @since 4.7
     */
    function wprss_free_help_display() {
        echo '<h3>' . __( 'Support Forums', 'wprss' ) . '</h3>';
        printf(
            wpautop(
                __( 'Users of the free version of WP RSS Aggregator can ask questions on the <a href="%s">support forum</a>.', 'wprss' )
            ),
            'https://wordpress.org/support/plugin/wp-rss-aggregator'
        );
    }


    add_action( 'wp_ajax_wprss_ajax_send_premium_support', 'wprss_ajax_send_premium_support' );
    /**
     * Handles the AJAX request to send the support form. Returns a JSON status.
     *
     * @since 4.7
     */
    function wprss_ajax_send_premium_support() {
        $ret = array();

        // Validate the form fields that were submitted and send any errors.
        $error = wprss_validate_support_request();
        if ($error !== FALSE) {
            $ret['error'] = $error;
            echo json_encode($ret);
            die();
        }

        // Create the email content.
        $subject = sanitize_text_field($_GET['support-subject']);
        $message = wprss_create_support_message();
        $headers  = wprss_create_support_headers();

        // Send the email.
        $sent = wp_mail( "support@wprssaggregator.com", $subject, $message, $headers );

        // NB, the retval is a best-guess about email sending. According to the WP Codex it
        // doesn't mean the user received the email, it "only means that the method used
        // was able to  process the request without any errors."
        if ($sent === FALSE) {
            $ret['error'] = sprintf(
                __(
                    'There was an error sending the form. Please use the <a href="%s" target="_blank">contact form on our site.</a>',
                    'wprss'
                ),
                esc_attr('https://www.wprssaggregator.com/contact/')
            );
            $ret['message'] = $message;
        } else {
            $ret['status'] = 'OK';
        }

        echo json_encode($ret);
        die();
    }


    /**
     * Ensures that all support form fields have been filled out. Returns TRUE
     *
     * @since 4.7
     * @return FALSE when all fields are valid, or a string containing an error they aren't.
     */
    function wprss_validate_support_request() {
        $fields = [
            'support-name',
            'support-email',
            'support-subject',
            'support-message'
        ];

        // Ensure that each required field is present and filled out.
        foreach ($fields as $field) {
            $value = filter_input(INPUT_GET, $field);
            if (empty($value)) {
                $fieldName = explode('-', $field)[1];
                $fieldName = ucfirst($fieldName);

                return sprintf(
                    __('Please fill out all the fields in the form, including the <strong>%s</strong> field.', 'wprss'),
                    $fieldName
                );
            }
        }

        // Ensure the email is of a valid format.
        $email = filter_input(INPUT_GET, 'support-email');
        if (!is_email($email)) {
            return __('Please enter a valid email address.', 'wprss');
        }

        return false;
    }


    /**
     * Creates and returns the support request email's message body.
     *
     * @since 4.7
     */
    function wprss_create_support_message() {
        // Get the WP RSS Aggregator log.
        $log = 'Customer did not send log';
        if ($_GET['support-include-log'] === 'true') {
            $log = wprss_get_log();
        }

        // Get the system information.
        $sys_info = 'Customer did not send system information';
        if ($_GET['support-include-sys'] === 'true') {
            ob_start();
            wprss_print_system_info();
            $sys_info = ob_get_contents();
            ob_end_clean();
        }

        // Get the license keys.
        $keys = json_encode(get_option('wprss_settings_license_keys', []), JSON_PRETTY_PRINT);

            // Get the message they entered.
        $message  = sanitize_text_field($_GET['support-message']);

        // Remove any generated system data that may be present from previous form submission attempts.
        $idx = strpos($message, "----------------------------------------------");
        if ($idx !== FALSE) {
            $message  = substr($message, 0, $idx);
        }

        // Append the generated system data.
        $message .= "\n\n----------------------------------------------\n";
        $message .= "\nLicense Information:\n" . $keys;
        $message .= "\n\n\nError Log:\n" . $log;
        $message .= "\n\n\nSystem Information:\n" . $sys_info . "\n";

        return apply_filters('wprss_support_message', $message);
    }


    /**
     * Creates and returns the support request email's headers.
     *
     * @since 4.7
     */
    function wprss_create_support_headers() {
        $headers  = "From: no-reply@wprssaggregator.com\r\n";
        $headers .= "Reply-to: " . sanitize_text_field($_GET['support-name']) . " <" . sanitize_email($_GET['support-email']) . ">\r\n";

        return apply_filters('wprss_support_headers', $headers);
    }


/**
 * Encapsulates features for providing inline help in the admin interface.
 *
 * The following filters are introduced:
 *
 * - `wprss_help_default_options` - The default options to be extended.
 *
 *	1.	The array of options
 *
 * - `wprss_help_template_path` - The path of template retrieved by WPRSS_Help::get_template().
 *
 *	1. The path to the template.
 *  2. The array of variables passed.
 *
 * - `wprss_help_template_vars` - The variables for the template, received by WPRSS_Help::get_template().
 *
 *	1. The variables array.
 *	2. The path to the template, filtered by `wprss_help_template_path`.
 *
 * - `wprss_help_tooltip_options` - Options that are in effect when adding tooltips with WPRSS_Help::add_tooltip().
 * - `wprss_help_tooltip_handle_html_options` - Options that are in effect when retrieving tooltip handle HTML with WPRSS_Help::wprss_help_tooltip_handle_html_options.
 *
 *
 * Also, the following options are available:
 *
 * - `tooltip_id_prefix` - The HTML element ID prefix that will be used for tooltips.
 * - `tooltip_handle_text` - The text that will appear inside the handle HTML elements.
 * - `tooltip_handle_class` - The CSS class that will be assigned to tooltip handles.
 * - `tooltip_content_class` - The CSS class that will be assigned to tooltip content HTML elements.
 * - `enqueue_tooltip_content` - Whether or not content is to be enqueued, instead of being output directly.
 *
 *	1. The absolute path to the core plugin directory
 */
class WPRSS_Help {
    static $_instance;

    protected $_options;

    protected $_enqueued_tooltip_content = [];

    protected $_tooltips = [];

    const OPTION_NAME = 'wprss_settings_help';
    const CODE_PREFIX = 'wprss_help_';
    const OVERRIDE_DEFAULT_PREFIX = '!';
    const HASHING_DELIMETER = '|';
    const OPTIONS_FILTER_SUFFIX = '_options';
    const TOOLTIP_DATA_KEY_ID = 'id';
    const TOOLTIP_DATA_KEY_TEXT = 'text';
    const TOOLTIP_DATA_KEY_OPTIONS = 'options';

    /**
     * Retrieve the singleton instance
     *
     * @return WPRSS_Help
     */
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            $class_name = __CLASS__; // Late static bindings not allowed
            self::$_instance = new $class_name();
        }

        return self::$_instance;
    }

    /**
     * @since 4.10
     */
    public static function init()
    {
        if (static::get_instance()->_isEnqueueScripts()) {
            add_action('admin_enqueue_scripts', [self::get_instance(), '_admin_enqueue_scripts']);
            add_action('admin_footer', [self::get_instance(), '_admin_footer']);
        }
    }

    /**
     * Determines if the admin scripts should get enqueued.
     *
     * @since 4.10
     *
     * @return bool True if admin scripts should be enqueued; false otherwise.
     */
    protected function _isEnqueueScripts()
    {
        return $this->_isWprssPage();
    }

    /**
     * Determines if the current page is related to WPRSS.
     *
     * @since 4.10
     *
     * @return bool True if the current page is related to WPRSS; false otherwise.
     */
    protected function _isWprssPage()
    {
        return wprss_is_wprss_page();
    }


    /**
     * Filters used:
     *
     * - `wprss_help_default_options`
     *
     * @param array $options Options that will overwrite defaults.
     */
    public function __construct( $options = array() ) {
        $defaults = apply_filters( 'wprss_help_default_options', array(
            'tooltip_id_prefix'				=> 'wprss-tooltip-',
            'tooltip_handle_text'			=> '',
            'tooltip_handle_class'			=> 'wprss-tooltip-handle', // Used in logic to identify handle elements
            'tooltip_handle_class_extra'	=> 'fa fa-question-circle', // Not used in logic
            'tooltip_content_class'			=> 'wprss-tooltip-content',
            'tooltip_class'					=> 'wprss-ui-tooltip', // Overrides default jQuery UI class
            'is_enqueue_tooltip_content'	=> '0',
            'tooltip_handle_template'		=> '%1$s/help-tooltip-handle.php',
            'tooltip_content_template'		=> '%1$s/help-tooltip-content.php',
            'admin_footer_js_template'		=> '%1$s/help-footer-js.php',
            'tooltip_not_found_handle_html'	=> '',
            'text_domain'					=> 'wprss'
        ));
        $db_options = $this->get_options_db();
        $this->_set_options( $this->array_merge_recursive_distinct( $db_options, $defaults ) );

        $this->_construct();
    }


    /**
     * Used for parameter-less extension of constructor logic
     */
    protected function _construct() {

    }


    /**
     * Return an option value, or the whole array of internal options.
     * These options are a product of the defaults, the database, and anything
     * set later on, applied on top of each other and overwriting in that order.
     *
     * @param null|string $key The key of the option to return.
     * @param null|mixed $default What to return if options with the specified key not found.
     * @return array|mixed|null The option value, or an array of options.
     */
    public function get_options($key = null, $default = null)
    {
        $options = $this->_options;

        if (is_null($key)) {
            return $options;
        }

        if (is_array($key)) {
            return $this->array_merge_recursive_distinct($options, $key);
        }

        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Set the value of an internal option or options.
     * Existing options will be overwritten. New options will be added.
     * Database options will not be modified.
     *
     * @param string|array $key The key of the option to set, or an array of options.
     * @param null|mixed $value The value of the option to set.
     *
     * @return WPRSS_Help This instance.
     */
    public function set_options($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_value) {
                $this->_set_options($_key, $_value);
            }

            return $this;
        }

        $this->_set_options($key, $value);
    }

    /**
     * Set an option value, or all options.
     * In latter case completely overrides the whole options array.
     *
     * @param string|array $key The key of the option to set, or the whole options array.
     * @param null|mixed $value Value of the option to set.
     *
     * @return WPRSS_Help This instance.
     */
    protected function _set_options($key, $value = null)
    {
        if (is_array($key)) {
            $this->_options = $key;
            return $this;
        }

        $this->_options[$key] = $value;
        return $this;
    }


    /**
     * Returns a WPRSS_Help option or options from the database.
     *
     * @param string $key The key of the option to return.
     * @param null|mixed $default What to return if option identified by $key is not found.
     * @return null|array|mixed The options or option value.
     */
    public function get_options_db($key = null, $default = null)
    {
        $options = (array) get_option(self::OPTION_NAME, []);

        if (is_null($key)) {
            return $options;
        }

        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Get content of a template.
     *
     * Filters used
     *
     * - `wprss_help_template_path`
     * - `wprss_help_template_vars`
     *
     * @param string $path Full path to the template
     * @param array $vars This will be passed to the template
     */
    public function get_template($path, $vars = [])
    {
        $vars = (array) $vars;

        // Entry points
        $path = apply_filters('wprss_help_template_path', $path, $vars);
        $vars = apply_filters('wprss_help_template_vars', $vars, $path);

        ob_start();
        include($path);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * This is called during the `admin_enqueue_scripts` action, and will
     * enqueue scripts needed for the backend.
     *
     * Filters used:
     *
     * - `wprss_help_admin_scripts`
     *
     * @return WPRSS_Help This instance.
     */
    public function _admin_enqueue_scripts()
    {
        if (!wprss_is_wprss_page()) {
            return $this;
        }

        $scripts = $this->apply_filters('admin_scripts', [
            'jquery-ui-tooltip' => [],
        ]);

        foreach ($scripts as $_handle => $_args) {
            // Allows numeric array with handles as values
            if (is_numeric($_handle)) {
                $_handle = $_args;
            }

            // Allows specifying null as value to simply enqueue handle
            if (empty($_args)) {
                $_args = [];
            }

            array_unshift($_args, $_handle);
            call_user_func_array('wp_enqueue_script', $_args);
        }

        return $this;
    }

    public function _admin_footer()
    {
        $html = $this->get_enqueued_tooltip_content_html() . "\n" . $this->get_admin_footer_js_html();

        // This should not be escaped!
        echo $this->apply_filters('admin_footer', $html);
    }

    public function is_overrides_default_prefix($string)
    {
        return strpos($string, self::OVERRIDE_DEFAULT_PREFIX) === 0;
    }

    /**
     * Hashes all the given values into a single hash.
     * Accepts an infinite number of parameters, all of which will be first
     * glued together by a separator, then hashed.
     * Non-scalar values will be serialized.
     *
     * @param mixed $value The value to hash.
     * @param mixed $argN Other values to hash.
     *
     * @return string The hash.
     */
    public function get_hash($value)
    {
        $args = func_get_args();
        $glue = self::HASHING_DELIMETER;

        $blob = '';
        foreach ($args as $_arg) {
            $blob .= is_scalar($_arg) ? $_arg : serialize($_arg);
            $blob .= $glue;
        }

        $blob = substr($blob, 0, -1);

        return sha1($blob);
    }

    /**
     * Get the class code prefix, or the specified prefixed with it.
     *
     * @param string $string A string to prefix.
     *
     * @return string The code prefix or the prefixed string.
     */
    public function get_code_prefix($string = '')
    {
        return self::CODE_PREFIX . (string) $string;
    }

    /**
     * Optionally prefix a string with the class code prefix, unless it
     * contains the "!" character in the very beginning, in which case it will
     * simply be removed.
     *
     * @param string $string The string to consider for prefixing.
     *
     * @return string The prefixed or clean string.
     */
    public function prefix($string)
    {
        return $this->is_overrides_default_prefix($string)
            ? substr($string, 1)
            : $this->get_code_prefix($string);
    }

    /**
     * Applies filters, but prefixes the filter name with 'wprss_help_',
     * unless '!' is specified as the first character of the filter.
     *
     * @param string $filter_name Name or "tag" of the filter.
     * @param mixed $subject The value to apply filters to.
     * @param mixed $argN ,.. Additional filter arguments
     *
     * @return mixed Result of filtering
     */
    public function apply_filters($filter_name, $subject, $argN = null)
    {
        $args = func_get_args();
        $args[0] = $this->prefix($filter_name);

        return call_user_func_array('apply_filters', $args);
    }

    /**
     * Applies a filters with the specified name to the options that were
     * applied on top of defaults.
     * The name will be prefixed with the class prefix 'wprss_help_', and
     * suffixed with '_options'.
     *
     * @param string $filter_name Name of the filter to apply to the options
     * @param array $options The options to filter
     * @param mixed $filter_argN ,.. Other filter arguments to be passed to filter
     */
    public function apply_options_filters($filter_name, $options = [], $filter_argN = null)
    {
        $args = func_get_args();

        // Adding suffix to filter name
        $args[0] = $filter_name . self::OPTIONS_FILTER_SUFFIX;

        // Applying default options
        $args[1] = $this->get_options($options);

        // Entry point. Order of args is already correct.
        return call_user_func_array([$this, 'apply_filters'], $args);
    }

    /**
     * Parses the tooltip handle template path for placeholders.
     *
     * Filters used:
     *
     * - `wprss_help_admin_footer_js_html_template`
     *
     * @param null|string $path Optional path to parse and retrieve. Default: value of the 'admin_footer_js_template'
     *     option.
     *
     * @return string Path to the template.
     */
    public function get_admin_footer_js_html_template($path = null)
    {
        // Default is from options
        if (is_null($path)) {
            $path = $this->get_options('admin_footer_js_template');
        }

        // Entry point
        $path = $this->apply_filters('admin_footer_js_html_template', $path);

        return $this->parse_path($path);
    }

    /**
     * Get the HTML of the JavaScript for the footer in Admin Panel.
     *
     * Filters used:
     *
     * - `wprss_help_admin_footer_js_html`
     *
     * @param array $options Any additional options to be used with defaults.
     *
     * @return string The HTML.
     */
    public function get_admin_footer_js_html($options = [])
    {
        $options = $this->apply_options_filters('admin_footer_js_html', $options);

        $templatePath = $this->get_admin_footer_js_html_template($options['admin_footer_js_template']);

        return $this->get_template($templatePath, $options);
    }

    /**
     * Parses the tooltip handle template path for placeholders.
     *
     * Filters used:
     *
     * - `wprss_help_tooltip_handle_html_template`
     *
     * @param null|string $path Optional path to parse and retrieve. Default: value of the 'tooltip_handle_template'
     *     option.
     *
     * @return string Path to the template.
     */
    public function get_tooltip_handle_html_template($path = null)
    {
        // Default is from options
        if (is_null($path)) {
            $path = $this->get_options('tooltip_handle_template');
        }

        // Entry point
        $path = $this->apply_filters('tooltip_handle_html_template', $path);

        return $this->parse_path($path);
    }

    /**
     * Get the HTML of the tooltip handle.
     *
     * Filters used:
     *
     * - `wprss_help_tooltip_handle_html_options`
     *
     * @param string $text Content of the tooltip text.
     * @param string $id ID of the tooltip.
     * @param array $options Any additional options to be used with defaults.
     *
     * @return string The HTML.
     */
    public function get_tooltip_handle_html($text, $id, $options = [])
    {
        $options = $this->apply_options_filters('tooltip_handle_html', $options, $text, $id);

        // Add template variables
        $options['tooltip_id'] = $id;
        $options['tooltip_text'] = $text;

        $templatePath = $this->get_tooltip_handle_html_template($options['tooltip_handle_template']);

        return $this->get_template($templatePath, $options);
    }

    /**
     * Parses the tooltip content template path for placeholders.
     *
     * Filters used:
     *
     * - `wprss_help_tooltip_content_html_template`
     *
     * @param null|string $path Optional path to parse and retrieve. Default: value of the 'tooltip_handle_template'
     *     option.
     *
     * @return string Path to the template.
     */
    public function get_tooltip_content_html_template($path = null)
    {
        // Default is from options
        if (is_null($path)) {
            $path = $this->get_options('tooltip_content_template');
        }

        // Entry point
        $path = $this->apply_filters('tooltip_content_html_template', $path);

        return $this->parse_path($path);
    }

    /**
     * Get the HTML of the tooltip content.
     *
     * Filters used:
     *
     * - `wprss_help_tooltip_content_html_options`
     *
     * @param string $text Content of the tooltip text.
     * @param string $id ID of the tooltip.
     * @param array $options Any additional options to be used with defaults.
     *
     * @return string The HTML.
     */
    public function get_tooltip_content_html($text, $id, $options = [])
    {
        $options = $this->apply_options_filters('tooltip_content_html', $options, $text, $id);

        // Add template variables
        $options['tooltip_id'] = $id;
        $options['tooltip_text'] = $text;

        $templatePath = $this->get_tooltip_content_html_template($options['tooltip_content_template']);

        return $this->get_template($templatePath, $options);
    }

    /**
     * Add tooltip and get tooltip HTML.
     * If $text is null, just get the HTML of tooltip with specified ID.
     * The `is_enqueue_tooltip_content` option determines whether to enqueue
     * the content, instead of outputting it after the handle.
     *
     * @param string $id ID for this tooltip
     * @param string|null $text Text of this tooltip. If null, tooltip will not be added, but only retrieved.
     * @param array|bool $options The options for this operation, or a boolean indicating whether or not content is to be enqueued
     * @return string The tooltip handle and, optionally, content.
     */
    public function tooltip($id, $text = null, $options = [])
    {
        $this->add_tooltip($id, $text, $options);
        return $this->do_tooltip($id);
    }

    /**
     * Add tooltips in a batch, with optionally prefixed ID.
     *
     * @param array $tooltips An array where key is tooltip ID and value is tooltip text.
     * @param string $prefix A prefix to add to all tooltip IDs.
     * @param array $options Arra of options for all the tooltips to add.
     *
     * @return \WPRSS_Help
     */
    public function add_tooltips($tooltips, $prefix = null, $options = [])
    {
        $prefix = (string) $prefix;
        if (!is_array($options)) $options = [];

        foreach ($tooltips as $_id => $_text) {
            $this->add_tooltip($prefix . $_id, $_text, $options);
        }

        return $this;
    }

    /**
     * Add a tooltip for later display.
     * Text and options will be replaced by existing text and options, if they
     * are empty, and a tooltip with the same ID is already registered.
     *
     * @param string $id The ID of this tooltip
     * @param string $text Text for this tooltip
     * @param array $options Options for this tooltip.
     *
     * @return WPRSS_Help This instance.
     */
    public function add_tooltip($id, $text = null, $options = [])
    {
        if ($tooltip = $this->get_tooltip($id)) {
            if (is_null($text)) {
                $text = isset($tooltip[self::TOOLTIP_DATA_KEY_TEXT])
                    ? $tooltip[self::TOOLTIP_DATA_KEY_TEXT]
                    : $text;
            }

            if (empty($options)) {
                $options = isset($tooltip[self::TOOLTIP_DATA_KEY_OPTIONS])
                    ? $tooltip[self::TOOLTIP_DATA_KEY_OPTIONS]
                    : $options;
            }
        }

        $this->set_tooltip($id, $text, $options);

        return $this;
    }

    /**
     * Set a tooltip, existing or not.
     *
     * @param string $id The ID of this tooltip
     * @param string $text Text for this tooltip
     * @param array $options Options for this tooltip.
     *
     * @return WPRSS_Help This instance.
     */
    public function set_tooltip($id, $text = null, $options = [])
    {
        $this->_tooltips[$id] = [
            self::TOOLTIP_DATA_KEY_ID => $id,
            self::TOOLTIP_DATA_KEY_TEXT => $text,
            self::TOOLTIP_DATA_KEY_OPTIONS => $options,
        ];

        return $this;
    }

    /**
     * Retrieve one tooltip, or an array containing all tooltips.
     *
     * @param string|null $id The ID of the tooltip to retrieve.
     * @param mixed|null $default What to return if tooltip with specified ID not found.
     *
     * @return array An array that contains the following indexes: 'id', 'text', 'options'. See {@link add_tooltip()}
     *     for details.
     */
    public function get_tooltip($id = null, $default = null)
    {
        if (is_null($id)) {
            return $this->_tooltips;
        }

        return $this->has_tooltip($id)
            ? $this->_tooltips[$id]
            : $default;
    }

    /**
     * Check whether a tooltip with the specified ID exists.
     *
     * @param string $id ID of the tooltip to check for.
     *
     * @return boolean True if a tooltip with the specified ID exists; false otherwise.
     */
    public function has_tooltip($id)
    {
        return isset($this->_tooltips[$id]);
    }

    /**
     * Get registered tooltip HTML.
     *
     * Filters used:
     *
     *  - `wprss_help_tooltip_options` - Filters options used for tooltip
     *
     * @param string $id ID for this tooltip
     * @param string $text Text of this tooltip
     * @param array|bool $options The options for this operation, or a boolean indicating whether or not content is to
     *     be enqueued
     *
     * @return string The tooltip handle and, optionally, content.
     */
    public function do_tooltip($id)
    {
        $options = $this->get_options();
        $tooltip = $this->get_tooltip($id);

        $text = !empty($tooltip[self::TOOLTIP_DATA_KEY_TEXT])
            ? $tooltip[self::TOOLTIP_DATA_KEY_TEXT]
            : null;

        if (!$tooltip || empty($text)) {
            return isset($options['tooltip_not_found_handle_html'])
                ? $options['tooltip_not_found_handle_html']
                : null;
        }

        $options = isset($tooltip[self::TOOLTIP_DATA_KEY_OPTIONS])
            ? $tooltip[self::TOOLTIP_DATA_KEY_OPTIONS]
            : null;

        if (!is_array($options)) {
            $options = ['is_enqueue_tooltip_content' => $options];
        }

        // Entry point
        $options = $this->apply_options_filters('tooltip', $options, $id, $text);

        // Get handle HTML
        $output = $this->get_tooltip_handle_html($text, $id, $options);

        if ($this->evaluate_boolean($options['is_enqueue_tooltip_content'])) {
            $this->enqueue_tooltip_content($text, $id, $options);
        } else {
            $output .= $this->get_tooltip_content_html($text, $id, $options);
        }

        return $output;
    }

    /**
     * Enqueue tooltip content to be displayed in another part of the page.
     *
     * @param string $text The text of the tooltip content to enqueue.
     * @param string $id ID of the tooltip, the content of which to enqueue.
     * @param array $options This tooltip's options.
     *
     * @return \WP_Error|\WPRSS_Help This instance, or error if enqueue method is invalid.
     */
    public function enqueue_tooltip_content($text, $id, $options = [])
    {
        $queue_method = $this->apply_filters('enqueue_tooltip_content_method', [$this, '_enqueue_tooltip_content'],
            $options, $id, $text);

        // "Error handling" WP style
        if (!is_callable($queue_method)) {
            $code = $this->prefix('invalid_queue_method');
            $msg = __('Could not enqueue tooltip content: the queue method is not a valid callable.', 'wprss');

            return new WP_Error($code, $msg, [
                'queue_method' => $queue_method,
                'text' => $text,
                'id' => $id,
                'options' => $options,
            ]);
        }

        call_user_func_array($queue_method, [$text, $id, $options]);

        return $this;
    }

    public function _enqueue_tooltip_content($text, $id, $options = [])
    {
        $hash = $this->get_hash($text, $id, $options);
        $this->_enqueued_tooltip_content[$hash] = [
            self::TOOLTIP_DATA_KEY_TEXT => $text,
            self::TOOLTIP_DATA_KEY_ID => $id,
            self::TOOLTIP_DATA_KEY_OPTIONS => $options,
        ];

        return $this;
    }

    public function get_enqueued_tooltip_content()
    {
        return $this->_enqueued_tooltip_content;
    }

    public function get_enqueued_tooltip_content_html()
    {
        $output = '';
        foreach ($this->get_enqueued_tooltip_content() as $_vars) {
            $options = is_array($_vars[self::TOOLTIP_DATA_KEY_OPTIONS])
                ? $_vars[self::TOOLTIP_DATA_KEY_OPTIONS]
                : [];

            $output = $this->get_tooltip_content_html(
                $_vars[self::TOOLTIP_DATA_KEY_ID],
                $_vars[self::TOOLTIP_DATA_KEY_ID],
                $options
            );
        }

        // This should not be escaped!
        echo $output;
    }

    /**
     * Check whether or not the given value is false.
     * False values are all {@link empty()} values, and also strings 'false' and 'no'.
     *
     * @param mixed $value The value to check.
     * @return boolean Whether or not the value is considered to be false.
     */
    public function evaluate_boolean( $value ) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Merge two arrays in an intuitive way.
     * Input arrays remain unchanged.
     *
     * @see http://php.net/manual/en/function.array-merge-recursive.php#92195
     *
     * @param array $array1 The array to merge.
     * @param array $array2 The array to merge into.
     *
     * @return array The merged array.
     */
    public function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Converts an array to a numeric array.
     * If $map is empty, assumes that the array keys are already in order.
     * If $map is a number, assumes it's the amount of elements to return.
     * If $map is an array, assumes it is the map of intended numeric indexes to their value in the input array.
     *
     * @param array $array The array to convert to a numeric array
     * @param false|null|array $map The map of the array indexes, or number of array elements to slice, or nothing.
     *
     * @return array The resulting numeric array.
     */
    public function array_to_numeric($array, $map = null)
    {
        $result = [];

        // If map is not an array, assume it's an indicator
        if (!is_array($map)) {
            $array = array_values($array);
        }

        // If map is empty, assume keys are in order
        if (empty($map)) {
            return $array;
        }

        // If map is a number, assume it's the amount of elements to return
        if (is_numeric($map)) {
            $map = intval($map);
            return array_slice($array, 0, $map);
        }

        foreach ($map as $_idx => $_key) {
            $result[$_idx] = $array[$_key];
        }

        return $result;
    }

    /**
     * Parses the template and replaces placeholders with their values.
     * This function uses {@see sprintf()} to format the template string using
     * the values provided in $data.
     * It is also possible for $data to be an associative array of key-value pairs.
     * To achieve the same result, a map can be provided, mapping data keys to
     * their placeholder positions.
     * If no map is provided,
     *
     * @param string $string The template string.
     * @param array $data The key-value pairs of template data.
     * @param false|null|array $map {@see array_to_numeric()} The template value map.
     *
     * @return string The parsed and modified template.
     */
    public function parse_template($string, $data, $map = null)
    {
        $data = $this->array_to_numeric($data, $map);
        array_unshift($data, $string);
        return call_user_func_array('sprintf', $data);
    }

    /**
     * Parses a path template specifically with WPRSS_Help path placeholders.
     *
     * Filters used (in order):
     *
     *  1. `parse_path_data_default`;
     *  2. `parse_path_data`;
     *  3. `parse_path_map`;
     *  4. `parse_path_path`.
     *
     * @see WPRSS_Help::parse_template()
     *
     * @param string $path The path to parse.
     * @param null|array $data Any additional data. Will be merged with defaults.
     * @param null|array $map The map for parsing.
     *
     * @return string The path with placeholders replaced
     */
    public function parse_path($path, $data = null, $map = null)
    {
        if (is_null($data)) {
            $data = [];
        }

        $defaults = $this->apply_filters('parse_path_data_default', [
            'wprss_templates_dir' => wprss_get_templates_dir(),
        ]);
        $data = $this->array_merge_recursive_distinct($data, $defaults);
        $data = $this->apply_filters('parse_path_data', $data, $path, $map);
        $map = $this->apply_filters('parse_path_map', $map, $data, $path);
        $path = $this->apply_filters('parse_path_path', $path, $data, $map);

        return $this->parse_template($path, $data, $map);
    }
}

WPRSS_Help::init();
