<?php

/**
 * Utilities of plugin
 *
 * @author cmshelplive
 */
class RM_Utilities {

    public $instance;
    public $table_name_for;
    public static $script_handle;

    public function __construct() {
        $script_handle = array();
    }

    public function __wakeup() {
        
    }

    public function __clone() {
        
    }

    public static function get_instance() {
        if (!isset(self::$instance) && !( self::$instance instanceof RM_Utilities )) {
            self::$instance = new RM_Utilities();
        }

        return self::$instance;
    }

    /**
     * Redirect user to a url or post permalink with some delay
     * 
     * @param string $url 
     * @param boolean $is_post      if set true url will not be used. will redirect the user to $post_id
     * @param int $post_id          ID of the post on which user will be redirected
     * @param boolean/int $delay    Delay in redirection(in ms) or default 5s is used if set true
     */
    public static function redirect($url = '', $is_post = false, $post_id = 0, $delay = false) {

        if ($is_post && $post_id > 0) {
            $url = get_permalink($post_id);
        }

        if (headers_sent() || $delay) {
            if (defined('RM_AJAX_REQ'))
                $prefix = 'parent.';
            else
                $prefix = '';

            $string = '<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">';
            if ($delay === true) {
                $string .= "window.setTimeout(function(){" . $prefix . "window.location.href = '" . $url . "';}, 5000);";
            } elseif ((int) $delay) {
                $string .= "window.setTimeout(function(){" . $prefix . "window.location.href = '" . $url . "';}, " . (int) $delay . ");";
            } else {
                $string .= $prefix . 'window.location = "' . $url . '"';
            }

            $string .= '</script></pre>';

            echo $string;
        } else {
            if (isset($_SERVER['HTTP_REFERER']) AND ( $url == $_SERVER['HTTP_REFERER']))
                wp_redirect($_SERVER['HTTP_REFERER']);
            else
                wp_redirect($url);

            exit;
        }
    }

    public static function user_role_dropdown($placeholder = false, $formatted = false) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::user_role_dropdown($placeholder, $formatted);
        }
        $roles = array();
        if ($placeholder)
            $roles[null] = RM_UI_Strings::get('PH_USER_ROLE_DD');

        if (!function_exists('get_editable_roles'))
            require_once ABSPATH . 'wp-admin/includes/user.php';

        $user_roles = get_editable_roles();
        foreach ($user_roles as $key => $value) {
            $roles[$key] = $value['name'];
        }
        return $roles;
    }

    public static function wp_pages_dropdown($args = null) {
        $wp_pages = array('Select page');
        if ($args === null)
            $args = array(
                'depth' => 0,
                'child_of' => 0,
                'selected' => 0,
                'echo' => 1,
                'name' => 'page_id',
                'id' => null, // string
                'class' => null, // string
                'show_option_none' => null, // string
                'show_option_no_change' => null, // string
                'option_none_value' => null, // string
            );

        $pages = get_pages($args);
        foreach ($pages as $page) {
            if (!$page->post_title) {
                $page->post_title = "#$page->ID (no title)";
            }
            $wp_pages[$page->ID] = $page->post_title;
        }

        return $wp_pages;
    }

    public static function merge_object($args, $defaults = null) {
        if ($args instanceof stdClass)
            if (is_object($defaults))
                foreach ($defaults as $key => $default)
                    if (!isset($args->$key))
                        $args->$key = $default;

        return $args;
    }

    public static function get_field_types($include_widgets= true,$form_type=1) {
        $field_types = array(
             null => 'Select A Field',
            'Textbox' => __('Text','custom-registration-form-builder-with-submission-manager'),
            'Select' => __('Drop Down','custom-registration-form-builder-with-submission-manager'),
            'Radio' => __('Radio Button','custom-registration-form-builder-with-submission-manager'),
            'Textarea' => __('Textarea','custom-registration-form-builder-with-submission-manager'),
            'Checkbox' => __('Checkbox','custom-registration-form-builder-with-submission-manager'),
            'jQueryUIDate' => __('Date','custom-registration-form-builder-with-submission-manager'),
            'Email' => __('Email','custom-registration-form-builder-with-submission-manager'),
            'Number' => __('Number','custom-registration-form-builder-with-submission-manager'),
            'Country' => __('Country','custom-registration-form-builder-with-submission-manager'),
            'Timezone' => __('Timezone','custom-registration-form-builder-with-submission-manager'),
            'Terms' => __('T&C Checkbox','custom-registration-form-builder-with-submission-manager'),
            'File' => __('File Upload','custom-registration-form-builder-with-submission-manager'),
            'Price' => __('Product','custom-registration-form-builder-with-submission-manager'),
            'Repeatable' => __('Repeatable Text','custom-registration-form-builder-with-submission-manager'),
            'Repeatable_M' => __('Repeatable Text','custom-registration-form-builder-with-submission-manager'),
            'Map' => __('Map','custom-registration-form-builder-with-submission-manager'),
            'Address' => __('Address','custom-registration-form-builder-with-submission-manager'),
            'Fname' => __('First Name','custom-registration-form-builder-with-submission-manager'),
            'Lname' => __('Last Name','custom-registration-form-builder-with-submission-manager'),
            'BInfo' => __('Biographical Info','custom-registration-form-builder-with-submission-manager'),
            'Phone' => __('Phone Number','custom-registration-form-builder-with-submission-manager'),
            'Mobile' => __('Mobile Number','custom-registration-form-builder-with-submission-manager'),
            'Password' => __('Password','custom-registration-form-builder-with-submission-manager'),
            'Nickname' => __('Nick Name','custom-registration-form-builder-with-submission-manager'),
            'Bdate' => __('Birth Date','custom-registration-form-builder-with-submission-manager'),
            'SecEmail' => __('Secondary Email','custom-registration-form-builder-with-submission-manager'),
            'Gender' => __('Gender','custom-registration-form-builder-with-submission-manager'),
            'Language' => __('Language','custom-registration-form-builder-with-submission-manager'),
            'Facebook' => __('Facebook','custom-registration-form-builder-with-submission-manager'),
            'Twitter' => __('Twitter','custom-registration-form-builder-with-submission-manager'),
            'Google' => __('Google+','custom-registration-form-builder-with-submission-manager'),
            'Linked' => __('LinkedIn','custom-registration-form-builder-with-submission-manager'),
            'Youtube' => __('YouTube','custom-registration-form-builder-with-submission-manager'),
            'ImageV'=> __('Image Widget','custom-registration-form-builder-with-submission-manager'),
            'VKontacte' => __('VKontacte','custom-registration-form-builder-with-submission-manager'),
            'Instagram' => __('Instagram','custom-registration-form-builder-with-submission-manager'),
            'Skype' => __('Skype ID','custom-registration-form-builder-with-submission-manager'),
            'SoundCloud' => __('Sound Cloud','custom-registration-form-builder-with-submission-manager'),
            'Time' => __('Time','custom-registration-form-builder-with-submission-manager'),
            'Image' => __('Image Upload','custom-registration-form-builder-with-submission-manager'),
            'Shortcode' => __('Shortcode','custom-registration-form-builder-with-submission-manager'),
            'Multi-Dropdown' => __('Multi-Dropdown','custom-registration-form-builder-with-submission-manager'),
            'Rating' => __('Rating','custom-registration-form-builder-with-submission-manager'),
            'Website' => __('Website','custom-registration-form-builder-with-submission-manager'),
            'Custom' => __('Custom Field','custom-registration-form-builder-with-submission-manager'),
            'Hidden' =>__('Hidden Field','custom-registration-form-builder-with-submission-manager'),
            'PriceV' => __('Total Price Widget','custom-registration-form-builder-with-submission-manager'),
            'MapV'=> __('Map','custom-registration-form-builder-with-submission-manager'),
            'SubCountV'=> __('Submission Count','custom-registration-form-builder-with-submission-manager'),
            'Form_Chart'=> __('Form Data Chart','custom-registration-form-builder-with-submission-manager'),
            'FormData' => __('Form Data','custom-registration-form-builder-with-submission-manager'),
            'Feed'=> __('Registration Feed','custom-registration-form-builder-with-submission-manager'),
            'Username' =>__('Account Username','custom-registration-form-builder-with-submission-manager'),
            'UserPassword' => __('Account Password','custom-registration-form-builder-with-submission-manager'),
            'Privacy' => __('Privacy Policy','custom-registration-form-builder-with-submission-manager'),
            'WCBilling' => __('Woocommerce Billing','custom-registration-form-builder-with-submission-manager'),
            'WCShipping' => __('Woocommerce Shipping','custom-registration-form-builder-with-submission-manager'),
            'WCBillingPhone' => __('WooCommerce Billing Phone','custom-registration-form-builder-with-submission-manager'),
            'ESign' => __('ESign','custom-registration-form-builder-with-submission-manager'),
        );
        if($form_type==0){
            $field_types['Feed']= __('Submission Feed','custom-registration-form-builder-with-submission-manager');
        }
        
        if($include_widgets){
            $field_types= array_merge($field_types,array('Timer'=>__('Timer','custom-registration-form-builder-with-submission-manager'),
                'RichText'=>__('Rich Text','custom-registration-form-builder-with-submission-manager'),
                'Divider'=>__('Divider','custom-registration-form-builder-with-submission-manager'),
                'Spacing'=>__('Spacing','custom-registration-form-builder-with-submission-manager'),
                'HTMLP'=>__('Paragraph','custom-registration-form-builder-with-submission-manager'),
                'HTMLH'=>__('Heading','custom-registration-form-builder-with-submission-manager'),
                'Link'=>__('Link','custom-registration-form-builder-with-submission-manager'),
                'YouTubeV'=>__('YouTube Video','custom-registration-form-builder-with-submission-manager'),
                "Iframe"=>__('Embed Iframe','custom-registration-form-builder-with-submission-manager')));
        }
        return $field_types;
    }
    
    public static function get_widget_types() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_widget_types();
        }
    }

    public static function after_login_redirect($user) {
        $redirect_to = get_permalink();
        if(is_wp_error($user) || empty($user)){
            return false;
        }
        $login_service = new RM_Login_Service();
        $red = $login_service->get_redirections();
        if ($red['redirection_type'] == 'common') {
            $is_admin = user_can($user->ID, 'manage_options');
            if (!empty($red['redirection_link']) && $is_admin && !empty($red['admin_redirection_link'])) {
                $redirect_to = admin_url();
            } else if (!empty($red['redirection_link'])) {
                $redirect_to = get_permalink($red['redirection_link']);
            }
        } else if ($red['redirection_type'] == 'role_based' && !empty($user)) {
            $user_meta = get_userdata($user->ID);
            $user_roles = $user_meta->roles;
            if (!empty($red['role_based_login_redirection'])) {
                foreach ($user_roles as $role) {
                    $role= strtolower(str_replace(' ', '', $role));
                    if (in_array($role, $red['role_based_login_redirection'])) {
                        if (!empty($red[$role . '_login_redirection'])) {
                            $redirect_to = get_permalink($red[$role . '_login_redirection']);
                            break;
                        }
                    }
                }
            }
        } else {
            $gopts = new RM_Options;
            $redirect_to = $gopts->get_value_of("post_submission_redirection_url");
            $enforce_admin_redirect_to_dashboard = ($gopts->get_value_of("redirect_admin_to_dashboard_post_login") == "yes");

            if (!$redirect_to){
                $redirect_to="";
            } else {
                    if ($enforce_admin_redirect_to_dashboard && isset($user->roles) && is_array($user->roles)) {
                    if (in_array('administrator', $user->roles)) {
                        return admin_url();
                    }
                }

                switch ($redirect_to) {
                    case "__current_url":
                        if ($GLOBALS['pagenow'] === 'wp-login.php'){
                           $redirect_to= admin_url();     
                        } else {
                            $test = get_permalink(); //* Won't work from a widget!!*
                            if (!$test){
                                $redirect_to= "__current_url";
                            } else
                                $redirect_to= $test;
                        }
                        break;
                    case "__home_page":
                        $redirect_to= get_home_url();
                        break;
                    case "__dashboard":
                        $redirect_to= admin_url();
                        break;

                    default: $redirect_to = home_url("?p=".$redirect_to); 
                }
            } 
        }
        $redirect_to= apply_filters('rm_login_redirection',$redirect_to,$user);
        return $redirect_to;
    }

    public static function get_current_url() {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);
        $query = '';
        if (!empty($parts['query'])) {
            // drop known fb params
            $params = explode('&', $parts['query']);
            $retained_params = array();
            foreach ($params as $param) {
                $retained_params[] = $param;
            } if (!empty($retained_params)) {
                $query = '?' . implode($retained_params, '&');
            }
        }        // use port if non default
        $port = isset($parts['port']) &&
                (($protocol === 'http://' && $parts['port'] !== 80) ||
                ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }

    public static function get_forms_dropdown($service) {
        if(defined('REGMAGIC_ADDON')) {
            $forms = $service->get_all('FORMS', $offset = 0, $limit = 0, $column = '*', $sort_by = 'form_id', $descending = true);
        } else {
            $forms = $service->get_all('FORMS', $offset = 0, $limit = 0, $column = '*', $sort_by = 'created_on', $descending = true);
        }
        $form_dropdown_array = array();
        if ($forms)
            foreach ($forms as $form)
                $form_dropdown_array[$form->form_id] = $form->form_name;
        return $form_dropdown_array;
    }

    public static function get_paypal_field_types($service) {
        $pricing_fields = $service->get_all('PAYPAL_FIELDS', $offset = 0, $limit = 999999, $column = '*');
        //var_dump($pricing_fields);
        $field_dropdown_array = array();
        if ($pricing_fields)
            foreach ($pricing_fields as $field)
                $field_dropdown_array[$field->field_id] = $field->name;
        else
            $field_dropdown_array[null] = RM_UI_Strings::get('MSG_CREATE_PRICE_FIELD');

        return $field_dropdown_array;
    }

    public static function send_email($to, $data) {
        /*
         * Function to send email
         */
    }

    public static function trim_array($var) {
        if (is_array($var) || is_object($var))
            foreach ($var as $key => $var_)
                if (is_array($var))
                    $var[$key] = self::trim_array($var_);
                else
                    $var->$key = self::trim_array($var_);
        else
            $var = trim($var);

        return $var;
    }

    public static function escape_array($var) {
        if (is_array($var) || is_object($var))
            foreach ($var as $key => $var_)
                if (is_array($var))
                    $var[$key] = self::escape_array($var_);
                else
                    $var->$key = self::escape_array($var_);
        else
            $var = addslashes($var);

        return $var;
    }

    public static function strip_slash_array($var) {
        if (is_array($var) || is_object($var))
            foreach ($var as $key => $var_)
                if (is_array($var))
                    $var[$key] = self::strip_slash_array($var_);
                else
                    $var->$key = self::strip_slash_array($var_);
        else
            $var = stripslashes($var);

        return $var;
    }

    public static function get_current_time($time = null) {
        if (!is_numeric($time))
            return gmdate('Y-m-d H:i:s');
        else
            return gmdate('Y-m-d H:i:s', $time);
    }

    public static function create_submission_page() {
        global $wpdb;

        $submission_page = array(
            'post_type' => 'page',
            'post_title' => __('Submissions','custom-registration-form-builder-with-submission-manager'),
            'post_status' => 'publish',
            'post_name' => 'submissions',
            'post_content' => '[RM_Front_Submissions]'
        );

        $page_id = get_option('rm_option_front_sub_page_id');

        if ($page_id) {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Front_Submissions]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[CRF_Submissions]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
        } else {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Front_Submissions]%\" AND `post_status`='publish'");
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[CRF_Submissions]%\" AND `post_status`='publish'");
        }

        if (!$post) {
            $page_id = wp_insert_post($submission_page);
            update_option('rm_option_front_sub_page_id', $page_id);
        } else {
            if ($page_id != $post)
                update_option('rm_option_front_sub_page_id', $post);
        }
    }

    public static function create_recovery_page() {
        global $wpdb;
        $shortcode= '[RM_password_recovery]';
	$sql = 'SELECT ID FROM '.$wpdb->posts.' WHERE (post_type = "page" or post_type="post") AND post_status="publish" AND post_content LIKE "%' . $shortcode . '%"';
	$id = $wpdb->get_var($sql);
        if(empty($id)){
            $recovery_page = array(
            'post_type' => 'page',
            'post_title' => __('Password Recovery','custom-registration-form-builder-with-submission-manager'),
            'post_status' => 'publish',
            'post_content' => '[RM_password_recovery]'
            );
            $page_id = wp_insert_post($recovery_page);
            if(!empty($page_id)){
                $login_service = new RM_Login_Service();
                $options= $login_service->get_recovery_options();
                $options['recovery_page']= $page_id;
                $login_service->update_recovery_options($options);
            }
        }
    }
    
    public static function create_login_page() {
        global $wpdb;

        $submission_page = array(
            'post_type' => 'page',
            'post_title' => __('Login','custom-registration-form-builder-with-submission-manager'),
            'post_status' => 'publish',
            'post_name' => 'login',
            'post_content' => '[RM_Login]'
        );

        $page_id = get_option('rm_option_front_login_page_id');

        if ($page_id) {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
        } else {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish'");
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish'");
        }

        if (!$post) {
            $page_id = wp_insert_post($submission_page);
            update_option('rm_option_front_login_page_id', $page_id);
        } else {
            if ($page_id != $post)
                update_option('rm_option_front_login_page_id', $post);
        }
    }

    public static function get_class_name_for($model_identifier) {
        $prefix = 'RM_';
        $class_name = $prefix . self::ucwords(strtolower($model_identifier));
        return $class_name;
    }

    public static function ucwords($string, $delimiter = " ") {
        if ($delimiter != " ") {
            $str = str_replace($delimiter, " ", $string);
            $str = ucwords($str);
            $str = str_replace(" ", $delimiter, $str);
        } elseif ($delimiter == " ")
            $str = ucwords($string);

        return $str;
    }

    public static function convert_to_unix_timestamp($mysql_timestamp) {
        return strtotime($mysql_timestamp);
    }

    public static function convert_to_mysql_timestamp($unix_timestamp) {
        return date("Y-m-d H:i:s", $unix_timestamp);
    }

    public static function create_pdf($html = null, $header_data = array('logo' => null, 'header_text' => null, 'title' => ''), $outputconf = array('name' => 'rm_submission.pdf', 'type' => 'D')) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::create_pdf($html, $header_data, $outputconf);
        }
        ob_start();
        require_once plugin_dir_path(dirname(__FILE__)) . 'external/tcpdf_min/tcpdf.php';
// create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Registration Magic');
        $pdf->SetTitle('Submission');
        $pdf->SetSubject(__('PDF for Submission','custom-registration-form-builder-with-submission-manager'));
        $pdf->SetKeywords('submission,pdf,print');

// set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006', PDF_HEADER_STRING);
        $pdf->SetHeaderData('', '', $header_data['title'], '');

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont('courier', '', 10);

// add a page
        $pdf->AddPage();

        //var_dump(htmlentities(ob_get_contents()));die;
// output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
        $pdf->lastPage();
        if (ob_get_contents()) {
            ob_end_clean();
        }

//Close and output PDF document
        $pdf->Output('rm_submission.pdf', 'D');
    }

    public static function create_json_for_chart($string_label, $numeric_label, array $dataset) {
        $data_table = new stdClass;
        $data_table->cols = array();
        $data_table->rows = array();
        $data_table->cols = array(
            // Labels for your chart, these represent the column titles
            // Note that one column is in "string" format and another one is in "number" format as pie chart only require "numbers" for calculating percentage and string will be used for column title
            (object) array('label' => $string_label, 'type' => 'string'),
            (object) array('label' => $numeric_label, 'type' => 'number')
        );

        $rows = array();

        foreach ($dataset as $name => $value) {
            $temp = array();
            // the following line will be used to slice the Pie chart
            $temp[] = (object) array('v' => (string) $name);

            // Values of each slice
            $temp[] = (object) array('v' => (int) $value);
            $rows[] = (object) array('c' => $temp);
        }
        $data_table->rows = $rows;
        $json_table = json_encode($data_table);
        return $json_table;
    }

    public static function HTMLToRGB($htmlCode) {
        if ($htmlCode[0] == '#')
            $htmlCode = substr($htmlCode, 1);

        if (strlen($htmlCode) == 3) {
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }

        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);

        return $b + ($g << 0x8) + ($r << 0x10);
    }

    public static function RGBToHSL($RGB) {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;

        $r = ((float) $r) / 255.0;
        $g = ((float) $g) / 255.0;
        $b = ((float) $b) / 255.0;

        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);

        $l = ($maxC + $minC) / 2.0;

        if ($maxC == $minC) {
            $s = 0;
            $h = 0;
        } else {
            if ($l < .5) {
                $s = ($maxC - $minC) / ($maxC + $minC);
            } else {
                $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
            }
            if ($r == $maxC)
                $h = ($g - $b) / ($maxC - $minC);
            if ($g == $maxC)
                $h = 2.0 + ($b - $r) / ($maxC - $minC);
            if ($b == $maxC)
                $h = 4.0 + ($r - $g) / ($maxC - $minC);

            $h = $h / 6.0;
        }

        $h = (int) round(255.0 * $h);
        $s = (int) round(255.0 * $s);
        $l = (int) round(255.0 * $l);

        return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
    }

    public static function send_mail($email) {
        add_action('phpmailer_init', 'RM_Utilities::config_phpmailer');

        $success = true;

        if (!$email->to)
            return false;

        //Just in case if data has not been supplied, set proper default values so email function does not fail.
        $exdata = property_exists($email, 'exdata') ? $email->exdata : null;
        //Checking using isset instead of property_exists as we do not want to get null value getting passed as attachments.
        $attachments = isset($email->attachments) ? $email->attachments : array();

        if (is_array($email->to)) {
            foreach ($email->to as $to) {

                if (!self::rm_wp_mail($email->type, $to, $email->subject, $email->message, $email->header, $exdata, $attachments))
                    $success = false;
            }
        } else
            $success = self::rm_wp_mail($email->type, $email->to, $email->subject, $email->message, $email->header, $exdata, $attachments);

        return $success;
    }

    //Sends a generic mail to a given address.
    public static function quick_email($to, $sub, $body, $mail_type = RM_EMAIL_GENERIC, array $extra_params = null) {
        $params = new stdClass;
        $params->type = $mail_type;
        $params->to = $to;
        $params->subject = $sub;
        $params->message = $body;

        //Add exra params if available
        if ($extra_params) {
            foreach ($extra_params as $param_name => $param_value)
                $params->$param_name = $param_value;
        }

        RM_Email_Service::quick_email($params);
    }

    public static function rm_wp_mail($mail_type, $to, $subject, $message, $header, $additional_data = null, $attachments = array()) {

        $mails_not_to_be_saved = array(RM_EMAIL_USER_ACTIVATION_ADMIN,
            RM_EMAIL_PASSWORD_USER,
            RM_EMAIL_POSTSUB_ADMIN,
            /* RM_EMAIL_NOTE_ADDED, */
            RM_EMAIL_TEST);
        $sent_res = wp_mail($to, $subject, $message, $header, $attachments);
        $was_sent_successfully = $sent_res ? 1 : 0;

        $sent_on = gmdate('Y-m-d H:i:s');
        if (!in_array($mail_type, $mails_not_to_be_saved)) {
            $form_id = null;
            $exdata = null;

            if (is_array($additional_data) && count($additional_data) > 0) {
                if (isset($additional_data['form_id']))
                    $form_id = $additional_data['form_id'];
                if (isset($additional_data['exdata']))
                    $exdata = $additional_data['exdata'];
            }
            $row_data = array('type' => $mail_type, 'to' => $to, 'sub' => htmlspecialchars($subject), 'body' => htmlspecialchars($message), 'sent_on' => $sent_on, 'headers' => $header, 'form_id' => $form_id, 'exdata' => $exdata, 'was_sent_success' => $was_sent_successfully);
            $fmts = array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d');

            RM_DBManager::insert_row('SENT_EMAILS', $row_data, $fmts);
        }
        return $sent_res;
    }

// format date string
    public static function localize_time($date_string, $dateformatstring = null, $advanced = false, $is_timestamp = false) {

        if ($is_timestamp) {
            $date_string = gmdate('Y-m-d H:i:s', $date_string);
        }

        if (!$dateformatstring) {
            $df = get_option('date_format', null) ?: 'd M Y';
            $tf = get_option('time_format', null) ?: 'h:ia';
            $dateformatstring = $df . ' @ ' . $tf;
        }

        return get_date_from_gmt($date_string, $dateformatstring);
    }

    public static function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'csv' => 'text/csv; charset=utf-8',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        $arr = explode('.', $filename);
        $ext = array_pop($arr);
        $ext = strtolower($ext);
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } else {
            return 'application/octet-stream';
        }
    }

    public static function config_phpmailer($phpmailer) {
        $options = new RM_Options;

        if ($options->get_value_of('enable_smtp') == 'yes') {
            $phpmailer->isSMTP();
            $phpmailer->SMTPDebug = 0;
            $phpmailer->Host = $options->get_value_of('smtp_host');
            $phpmailer->SMTPAuth = $options->get_value_of('smtp_auth') == 'yes' ? true : false;
            $phpmailer->Port = $options->get_value_of('smtp_port');
            $phpmailer->Username = $options->get_value_of('smtp_user_name');
            $phpmailer->Password = $options->get_value_of('smtp_password');
            $phpmailer->SMTPSecure = ($options->get_value_of('smtp_encryption_type') == 'enc_tls') ? 'tls' : (($options->get_value_of('smtp_encryption_type') == 'enc_ssl') ? 'ssl' : '' );
            $phpmailer->From = $options->get_value_of('smtp_senders_email');  
        }
        else
        {
            $phpmailer->From = $options->get_value_of('senders_email');
        }
        $phpmailer->FromName = $options->get_value_of('senders_display_name');
        if (empty($phpmailer->AltBody))
            $phpmailer->AltBody = self::html_to_text_email($phpmailer->Body);

        return;
    }

    public static function check_smtp() {

        $options = new RM_Options;

        $bckup = $options->get_all_options();

        $email = isset($_POST['test_email']) ? $_POST['test_email'] : null;

        $options->set_values(array(
            'enable_smtp' => 'yes',
            'smtp_host' => isset($_POST['smtp_host']) ? $_POST['smtp_host'] : null,
            'smtp_auth' => isset($_POST['SMTPAuth']) ? $_POST['SMTPAuth'] : null,
            'smtp_port' => isset($_POST['Port']) ? $_POST['Port'] : null,
            'smtp_user_name' => isset($_POST['Username']) ? $_POST['Username'] : null,
            'smtp_password' => isset($_POST['Password']) ? $_POST['Password'] : null,
            'smtp_encryption_type' => isset($_POST['SMTPSecure']) ? $_POST['SMTPSecure'] : null,
            'senders_email' => isset($_POST['From']) ? $_POST['From'] : null,
            'senders_display_name' => isset($_POST['FromName']) ? $_POST['FromName'] : null
        ));
        if (!$email) {
            echo 'blank_email ' . RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_REQUIRED_MESSAGE');
            $options->set_values($bckup);
            die;
        }

        $test_email = new stdClass();
        $test_email->type = RM_EMAIL_TEST;
        $test_email->to = $email;
        $test_email->subject = __('Test SMTP Connection','custom-registration-form-builder-with-submission-manager');
        $test_email->message = __('Test', 'custom-registration-form-builder-with-submission-manager');
        $test_email->header = '';
        $test_email->attachments = array();
        if (self::send_mail($test_email))
            echo RM_UI_Strings::get('LABEL_SMTP_SUCCESS_MESSAGE');
        else
            echo RM_UI_Strings::get('LABEL_SMTP_FAIL_MESSAGE');

        $options->set_values($bckup);
        die;
    }

    public static function check_wordpress_default_mail() {

        $options = new RM_Options;

        $bckup = $options->get_all_options();

        $to = isset($_POST['test_email']) ? $_POST['test_email'] : null;
        $message = isset($_POST['message']) ? $_POST['message'] : null;
        $from = isset($_POST['From']) ? $_POST['From'] : null;
        $headers = "From:" . $from;

        if (!$to) {
            echo 'blank_email ' . RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_REQUIRED_MESSAGE');

            die;
        }
        if (wp_mail($to, __('Test Mail','custom-registration-form-builder-with-submission-manager'), $message, $headers)) {
            echo RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_SUCCESS_MESSAGE');
        } else {
            echo RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_FAIL_MESSAGE');
        }
        die;
    }

    public static function disable_review_banner() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::disable_review_banner();
        }
    }
    
    public static function handle_rating_operations() {
        $type = $_POST['type'];
        $data = $_POST['info'];
        $options = new RM_Options;
        $service = new RM_Services;
        $events = $options->get_value_of('review_events');
        if ($type == 'remind') {
            $events['event'] = $service->get_review_event();
            $events['status']['flag'] = 'remind';
            $events['status']['time'] = date('Y-m-d');
        } elseif ($type == 'wordpress') {
            $events['event'] = $service->get_review_event();
            $events['status']['flag'] = 'reviewed';
            $events['status']['time'] = date('Y-m-d');
        } elseif ($type == 'rating') {
            $events['rating'] = $data;
        } elseif ($type == 'feedback') {
            $events['event'] = $service->get_review_event();
            $events['status']['flag'] = 'feedback';
            $events['status']['time'] = date('Y-m-d');
        } else {
            
        }
        $options->set_value_of('review_events', $events);
        die;
    }

    public static function disable_newsletter_banner() {
        global $rm_env_requirements;

        if ($rm_env_requirements & RM_REQ_EXT_CURL) {
            require_once RM_EXTERNAL_DIR . "Xurl/rm_xurl.php";

            $xurl = new RM_Xurl("https://registrationmagic.com/subscribe_to_newsletter/");

            if (function_exists('is_multisite') && is_multisite()) {
                $nl_sub_mail = get_site_option('admin_email');
            } else {
                $nl_sub_mail = get_option('admin_email');
            }

            $user = get_user_by('email', $nl_sub_mail);
            $req_arr = array('sub_email' => $nl_sub_mail, 'fname' => $user->first_name, 'lname' => $user->last_name);

            $xurl->post($req_arr);
        }
        if (function_exists('is_multisite') && is_multisite()) {
            update_site_option('rm_option_newsletter_subbed', 1);
        } else {
            update_option('rm_option_newsletter_subbed', 1);
        }

        wp_die();
    }

    public static function is_ssl() {
        //return true;
        return is_ssl();
    }

    //More reliable check for write permission to a directory than the php native is_writable.
    public static function is_writable_extensive_check($path) {
        //NOTE: use a trailing slash for folders!!!
        if ($path[strlen($path) - 1] == '/') // recursively return a temporary file path
            return self::is_writable_extensive_check($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path))
            return self::is_writable_extensive_check($path . '/' . uniqid(mt_rand()) . '.tmp');
        // check tmp file for read/write capabilities
        $rm = file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false)
            return false;
        fclose($f);
        if (!$rm)
            unlink($path);
        return true;
    }

    //Check for fatal errors with which can not continue.
    public static function fatal_errors() {
        global $rm_env_requirements;
        global $regmagic_errors;
        $fatality = false;
        $error_msgs = array();

        //Now check for any other remaining errors that might be originally in the global variable
        if(is_array($regmagic_errors)){
            foreach ($regmagic_errors as $err) {
                if (!$err->should_cont) {
                    $fatality = true;
                    break;
                }
            }
        }
        

        if (!($rm_env_requirements & RM_REQ_EXT_SIMPLEXML)) {
            $regmagic_errors[RM_ERR_ID_EXT_SIMPLEXML] = (object) array('msg' => RM_UI_Strings::get('CRIT_ERR_XML'), 'should_cont' => false); //"PHP extension SimpleXML is not enabled on server. This plugin cannot function without it.";
            $fatality = true;
        }

        if (!($rm_env_requirements & RM_REQ_PHP_VERSION)) {
            $regmagic_errors[RM_ERR_ID_PHP_VERSION] = (object) array('msg' => RM_UI_Strings::get('CRIT_ERR_PHP_VERSION'), 'should_cont' => false); //"This plugin requires atleast PHP version 5.3. Cannot continue.";
            $fatality = true;
        }

        if (!($rm_env_requirements & RM_REQ_EXT_CURL)) {
            $regmagic_errors[RM_ERR_ID_EXT_CURL] = (object) array('msg' => RM_UI_Strings::get('RM_ERROR_EXTENSION_CURL'), 'should_cont' => true);
        }

        if (!($rm_env_requirements & RM_REQ_EXT_ZIP)) {
            $regmagic_errors[RM_ERR_ID_EXT_ZIP] = (object) array('msg' => RM_UI_Strings::get('RM_ERROR_EXTENSION_ZIP'), 'should_cont' => true);
        }


        return $fatality;
    }

    public static function rm_error_handler($errno, $errstr, $errfile, $errline) {
        global $regmagic_errors;

        var_dump($errno);
        var_dump($errstr);

        return true;
    }

    public static function is_banned_ip($ip_to_check, $format) {
        if ($format === null)
            return false;

        //compare directly in case of ipv6 ban pattern
        if ((bool) filter_var($format, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if ($ip_to_check == $format)
                return true;
            else
                return false;
        }

        $matchrx = '/[0-9.]/';
        
        /*
        $gen_regex = array('[0-2]', '[0-9]', '[0-9]', '\.',
            '[0-2]', '[0-9]', '[0-9]', '\.',
            '[0-2]', '[0-9]', '[0-9]', '\.',
            '[0-2]', '[0-9]', '[0-9]');

        for ($i = 0; $i < strlen($format); $i++) {
            if ($format[$i] == '?' || $format[$i] == '.')
                $matchrx .= $gen_regex[$i];
            else
                $matchrx .= $format[$i];
        }

        $matchrx .= '/';
        */

        if (preg_match($matchrx, $ip_to_check) === 1 && $ip_to_check === $format)
            return true;
        else
            return false;
    }

    public static function is_banned_email($email_to_check, $format) {
        if (!$format)
            return false;

        $matchrx = '/';

        $gen_regex = array('?' => '.',
            '*' => '.*',
            '.' => '\.'
        );

        $formatlen = strlen($format);

        for ($i = 0; $i < $formatlen; $i++) {
            if ($format[$i] == '?' || $format[$i] == '.' || $format[$i] == '*')
                $matchrx .= $gen_regex[$format[$i]];
            else
                $matchrx .= $format[$i];
        }

        $matchrx .= '/';

        //Following check is employed instead preg_match so that partial matches
        //will not get selected unless user specifies using wildcard '*'.      
        $test = preg_replace($matchrx, '', $email_to_check);

        if ($test == '')
            return true;
        else
            return false;
    }
    
    public static function is_username_reserved($username_to_check) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::is_username_reserved($username_to_check);
        }
    }

    public static function enc_str($string) {
        if (function_exists('mcrypt_encrypt')) {
            $key = 'A Terrific tryst with tyranny';
            $iv = @mcrypt_create_iv(
                            mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM
            );

            $encrypted = @base64_encode($iv . mcrypt_encrypt(
                                    MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), $string, MCRYPT_MODE_CBC, $iv
                            )
            );
        } else { //Using open SSL
            $key = self::get_enc_key();
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            $encrypted = base64_encode($iv . $hmac . $ciphertext_raw);
        }

        return $encrypted;
    }

    public static function dec_str($string) {
        if (function_exists('mcrypt_encrypt')) {
            $key = 'A Terrific tryst with tyranny';

            $data = base64_decode($string);
            $iv = @substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

            $decrypted = @rtrim(
                            mcrypt_decrypt(
                                    MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv
                            ), "\0"
            );
        } else {
            $key = self::get_enc_key();
            $c = base64_decode($string);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
                return $decrypted;
            }
        }


        return $decrypted;
    }

    public static function get_enc_key() {
        return "e0cb6eecb9ff1b6397ff";
    }

    public static function link_activate_user() {
        $req = $_GET['user'];

        $user_service = new RM_User_Services();

        $req_deco = self::dec_str($req);

        $user_data = json_decode($req_deco);

        if(defined('REGMAGIC_ADDON')) {
            echo '<!DOCTYPE html>
                    <html>
                    <head>
                      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      <meta http-equiv="Content-Style-Type" content="text/css">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                      <title></title>
                      <meta name="Generator" content="Cocoa HTML Writer">
                      <meta name="CocoaVersion" content="1404.34">
                        <link rel="stylesheet" type="text/css" href="' . RM_BASE_URL . 'admin/css/style_rm_admin.css">
                        <link rel="stylesheet" type="text/css" href="' . RM_ADDON_BASE_URL . 'admin/css/style_rm_admin.css">
                    </head>
                    <body class="rmajxbody">
            <div class="rmagic">';
        } else {
            echo '<!DOCTYPE html>
                    <html>
                    <head>
                      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      <meta http-equiv="Content-Style-Type" content="text/css">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                      <title></title>
                      <meta name="Generator" content="Cocoa HTML Writer">
                      <meta name="CocoaVersion" content="1404.34">
                        <link rel="stylesheet" type="text/css" href="' . RM_BASE_URL . 'admin/css/style_rm_admin.css">
                    </head>
                    <body class="rmajxbody">
            <div class="rmagic">';
        }

        echo '<div class="rm_user_activation_msg">';

        if ($user_data->activation_code == get_user_meta($user_data->user_id, 'rm_activation_code', true)) {
            if (!delete_user_meta($user_data->user_id, 'rm_activation_code')) {
                echo '<div class="rm_fail_del">' . RM_UI_Strings::get('ACT_AJX_FAILED_DEL') . '</div>';
                die;
            }

            if ($user_service->activate_user_by_id($user_data->user_id)) {
                $users = array($user_data->user_id);
                $user_service->notify_users($users, 'user_activated');
                echo '<h1 class="rm_user_msg_ajx">' . RM_UI_Strings::get('ACT_AJX_ACTIVATED') . '</h1>';
                $user = get_user_by('id', $user_data->user_id);
                echo '<div class = rm_user_info><div class="rm_field_cntnr"><div class="rm_user_label">' . RM_UI_Strings::get('LABEL_USER_NAME') . ' : </div><div class="rm_label_value">' . $user->user_login . '</div></div><div class="rm_field_cntnr"><div class="rm_user_label">' . RM_UI_Strings::get('LABEL_USEREMAIL') . ' : </div><div class="rm_label_value">' . $user->user_email . '</div></div></div>';
                echo '<div class="rm_user_msg_ajx">' . RM_UI_Strings::get('ACT_AJX_ACTIVATED2') . '</div>';
            } else
                echo '<div class="rm_not_authorized_ajax rm_act_fl">' . RM_UI_Strings::get('ACT_AJX_ACTIVATE_FAIL') . '</div>';
        } else
            echo '<div class="rm_not_authorized_ajax">' . RM_UI_Strings::get('ACT_AJX_NO_ACCESS') . '</div>';

        echo '</div></div></html></body>';
        /* ?>
          <button type="button" onclick="window.location.reload()">Retry</button>
          <button type="button" onclick="window.history.back()">GO BACK</button>
          <?php */
        die;
    }

    public static function html_to_text_email($html) {
        $html = str_replace('<br>', "\r\n", $html);
        $html = str_replace('<br/>', "\r\n", $html);
        $html = str_replace('</br>', "\r\n", $html);

        $html = strip_tags($html);
        $html = html_entity_decode($html);
        return trim($html);
    }
    
    public static function get_language_array() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_language_array();
        }
    }

    public static function set_default_form() {
        check_ajax_referer( 'rm_formflow', 'rm_ajaxnonce' );
       
        if (isset($_POST['rm_def_form_id']) && current_user_can('manage_options')) {
            $gopts = new RM_Options;
            $gopts->set_value_of('default_form_id', $_POST['rm_def_form_id']);
        }
        die;
    }

    public static function unset_default_form() {
        if (isset($_POST['rm_def_form_id'])) {
            $gopts = new RM_Options;
            $gopts->set_value_of('default_form_id', null);
        }
        die;
    }
    
    public static function get_validations_array() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_validations_array();
        }
    }

    //One time login
    public static function safe_login() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::safe_login();
        }
        if (isset($_SESSION['RM_SLI_UID'])) {
            $user_status_flag = get_user_meta($_SESSION['RM_SLI_UID'], 'rm_user_status', true);
            if ($user_status_flag === '0' || $user_status_flag === '') {
                wp_set_auth_cookie($_SESSION['RM_SLI_UID']);
                wp_set_current_user($_SESSION['RM_SLI_UID']);
            }
            unset($_SESSION['RM_SLI_UID']);
        }
    }
    
    public static function rm_is_prov_login_active($user_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::rm_is_prov_login_active($user_id);
        }
    }

    //Loads scripts without wp_enque_script for ajax calls.
    public static function enqueue_external_scripts($handle, $src = false, $deps = array(), $ver = false, $in_footer = false) {
        if (!defined('RM_AJAX_REQ')) {
            if (!wp_script_is($handle, 'enqueued')) {
                if (wp_script_is($handle, 'registered'))
                    wp_enqueue_script($handle);
                else
                    wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
            }
        }elseif (!isset(self::$script_handle[$handle])) {
            self::$script_handle[$handle] = $src;
            return '<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript" src="' . $src . '"></script></pre>';
        }
    }
    
    public static function localize_script($handle_key, $name, $value) {
        if (!defined('RM_AJAX_REQ')) {
            wp_localize_script($handle_key, $name, $value);
        } else {
            $js_code = "";
            if(is_array($value)) {
                $js_code = "const $name = {";
                foreach ($value as $index => $val) {
                    $js_code .= "$index:'$val',";
                }
                $js_code .= "};";
            } else {
                $js_code = "var $name = $val;";
            }
            return '<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">'. $js_code .'</script></pre>';
        }
    }
    
    public function get_state() {
        $user= wp_get_current_user();
        $state= '';
        if(!empty($user->ID)){
            $type= $_REQUEST['type'];
            if($type=='billing')
                $state= get_user_meta($user->ID, 'billing_state', true);
            else if($type=='shipping')
                $state= get_user_meta($user->ID, 'shipping_state', true);
                
        } 
        $country_arr = RM_Utilities::get_countries();
        $country_code = str_replace(array($country_arr[$_REQUEST['country']],'[',']'),'',$_REQUEST['country']);
        
        $woo_countries = new WC_Countries();
        //$countries = $woo_countries->get_allowed_countries();
        $states = $woo_countries->get_states($country_code);
        if(empty($states)){
            return '';
        }
        $state_arr = '';
        foreach($states as $key=>$value){
            if($state==$key)
                $selected= 'selected';
            else
                $selected='';
            $state_arr .= '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
        }
        echo $state_arr;
        exit;
    }

    /*
     * Loads all the data requires in JS 
     * It will allow to use language strings in JS
     */

    public static function load_admin_js_data() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::load_admin_js_data();
        }
        $data = new stdClass();
        echo json_encode($data);
        die;
    }

    public static function load_js_data() {
        $data = new stdClass();

        // Validation message override
        $data->validations = array();
        $data->validations['required'] = RM_UI_Strings::get("VALIDATION_REQUIRED");
        $data->validations['email'] = RM_UI_Strings::get("INVALID_EMAIL");
        $data->validations['url'] = RM_UI_Strings::get("INVALID_URL");
        $data->validations['pattern'] = RM_UI_Strings::get("INVALID_FORMAT");
        $data->validations['number'] = RM_UI_Strings::get("INVALID_NUMBER");
        $data->validations['digits'] = RM_UI_Strings::get("INVALID_DIGITS");
        $data->validations['maxlength'] = RM_UI_Strings::get("INVALID_MAXLEN");
        $data->validations['minlength'] = RM_UI_Strings::get("INVALID_MINLEN");
        $data->validations['max'] = RM_UI_Strings::get("INVALID_MAX");
        $data->validations['min'] = RM_UI_Strings::get("INVALID_MIN");

        echo json_encode($data);
        wp_die();
    }

    public static function save_submit_label() {
        $form_id = $_POST['form_id'];
        $label = $_POST['label'];

        $form = new RM_Forms;
        $form->load_from_db($form_id);
        $form->form_options->form_submit_btn_label = $label;
        $form->update_into_db();
        echo "changed";
        die;
    }

    public static function update_tour_state($tour_id, $state) {
        $gopts = new RM_Options;

        $existing_tour = $gopts->get_value_of('tour_state');

        if (is_array($existing_tour)) {
            $existing_tour[$tour_id] = strtolower($state);
        } else {
            $existing_tour = array($tour_id => strtolower($state));
        }
        $gopts->set_value_of('tour_state', $existing_tour);
    }

    public static function has_taken_tour($tour_id) {
        $gopts = new RM_Options;

        $existing_tour = $gopts->get_value_of('tour_state');

        if (isset($existing_tour[$tour_id]))
            return ($existing_tour[$tour_id] == 'taken');
        else
            return false;
    }

    public static function update_tour_state_ajax() {
        $tour_id = $_POST['tour_id'];
        $state = $_POST['state'];

        self::update_tour_state($tour_id, $state);
        wp_die();
    }

    public static function process_field_options($value) {
        $p_options = array();

        if (!is_array($value))
            $tmp_options = explode(',', $value);
        else
            $tmp_options = $value;

        foreach ($tmp_options as $val) {
            $val = trim($val);
            $val = trim($val, "|");
            $t = explode("|", $val);

            if (count($t) <= 1 || trim($t[1]) === "")
                $p_options[$val] = $val;
            else
                $p_options[trim($t[1])] = trim($t[0]);
        }

        return $p_options;
    }

    public static function get_lable_for_option($field_id, $opt_value) {
        $rmf = new RM_Fields;
        if (!$rmf->load_from_db($field_id))
            return $opt_value;

        //Return same value if it is not a multival field
        if (!in_array($rmf->field_type, array('Checkbox', 'Radio', 'Select')))
            return $opt_value;

        $val = $rmf->get_field_value();
        $p_opts = self::process_field_options($val);

        if (!is_array($opt_value)) {
            if (isset($p_opts[$opt_value]))
                return $p_opts[$opt_value];
            else
                return $opt_value;
        }
        else {
            $tmp = array();
            foreach ($opt_value as $val) {
                if (isset($p_opts[$val]))
                    $tmp[] = $p_opts[$val];
                else
                    $tmp[] = $val;
            }
            return $tmp;
        }
    }

    //Print nested array like vars as html table.
    public static function var_to_html($variable) {
        $html = "";

        if (is_array($variable) || is_object($variable)) {
            $html .= "<table style='border:none; padding:3px; width:100%; margin: 0px;'>";
            if (count($variable) === 0)
                $html .= "empty";
            foreach ($variable as $k => $v) {
                $html .= '<tr><td style="background-color:#F0F0F0; vertical-align:top; min-width:100px;">';
                $html .= '<strong>' . $k . "</strong></td><td>";
                $html .= self::var_to_html($v);
                $html .= "</td></tr>";
            }

            $html .= "</table>";
            return $html;
        }

        $html .= $variable ? $variable : "NULL";
        return $html;
    }

    public static function is_date_valid() {
        $date = $_POST['date'];

        try {
            $test = new DateTime($date);
            echo "VALID";
        } catch (Exception $e) {
            echo "INVALID";
        }

        wp_die();
    }

    public function handel_fb_subscribe() {
        $gopts = new RM_Options;
        $gopts->set_value_of('has_subbed_fb_page', 'yes');
        wp_die();
    }

    //Methods to simplify one-time-action option handeling
    public static function update_action_state($act_id, $state) {
        $gopts = new RM_Options;

        $one_time_actions = $gopts->get_value_of('one_time_actions');

        if (is_array($one_time_actions)) {
            $one_time_actions[$act_id] = $state;
        } else {
            $one_time_actions = array($act_id => $state);
        }
        $gopts->set_value_of('one_time_actions', $one_time_actions);
    }

    public static function has_action_occured($act_id) {
        $gopts = new RM_Options;

        $one_time_actions = $gopts->get_value_of('one_time_actions');

        if (isset($one_time_actions[$act_id]))
            return $one_time_actions[$act_id];
        else
            return false;
    }

    public static function get_allowed_conditional_fields() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_allowed_conditional_fields();
        }
        return array('Textbox', 'Select', 'Radio', 'Checkbox', 'jQueryUIDate', 'Email', 'Number', 'Country', 'Website',
            'Language', 'Timezone', 'Fname', 'Lname', 'Phone', 'Mobile', 'Nickname', 'Bdate', 'Gender', 'Custom', 'Repeatable', 'Password', 'Terms');
    }

    public static function get_fields_dropdown($config = array()) {
        $service = new RM_Services();
        $fields = $service->get_all_form_fields($config['form_id']);
        $options = '';
        if (isset($config['full']))
            $options .= '<select name="' . $config['name'] . '" id="' . (isset($config['id']) ? $config['id'] : $config['name']) . '">';
        if ($fields)
            foreach ($fields as $field) {
                if (!empty($config['exclude']) && in_array($field->field_id, $config['exclude']))
                    continue;
                if (!empty($config['inc_by_type']) && !in_array($field->field_type, $config['inc_by_type']))
                    continue;
                if (!empty($config['ex_by_type']) && in_array($field->field_type, $config['ex_by_type']))
                    continue;
                if (isset($config['def']) && $field->field_id == $config['def'])
                    $options .= '<option selected value="' . $field->field_id . '">' . $field->field_label . '</option>';
                else
                    $options .= '<option value="' . $field->field_id . '">' . $field->field_label . '</option>';
            }
        if (isset($config['full']))
            $options .= '</select>';
        return $options;
    }

    public static function update_action_state_ajax() {
        $act_id = $_POST['action_id'];
        //Pass 'state' as string "true" or "false".
        $state = ($_POST['state'] == 'true');

        self::update_action_state($act_id, $state);
        wp_die();
    }

    public static function get_allowed_cond_op($config = array()) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_allowed_cond_op($config);
        }
        return array(
            'Equals' => '==', 'Not equals' => '!=', 'Less than or equals' => '<=',
            'Less than' => '<', 'Greater than' => '>', 'Greater than or equals' => '>=',
            'Contains' => 'in',
            'Empty' => '_blank', 'Not Empty' => '_not_blank'
        );
    }

    public static function get_cond_op_dd($config = array()) {
        $operators = self::get_allowed_cond_op();
        $options = '';
        if (isset($config['full']))
            $options .= '<select name="' . $config['name'] . '" id="' . (isset($config['id']) ? $config['id'] : $config['name']) . '">';

        foreach ($operators as $key => $op) {
            if (isset($config['def']) && $op == $config['def'])
                $options .= '<option selected value="' . $op . '">' . $key . '</option>';
            else
                $options .= '<option value="' . $op . '">' . $key . '</option>';
        }
        if (isset($config['full']))
            $options .= '</select>';
        return $options;
    }

    public static function pdf_excluded_widgets() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::pdf_excluded_widgets();
        }
        return array("Spacing", "HTMLCustomized", "HTML", "Timer", "Iframe","UserPassword");
    }

    public static function csv_excluded_widgets(){
        return array("HTMLH","Spacing","HTMLCustomized","HTML","Timer","HTMLP","Divider","Spacing","RichText","Link","YouTubeV","Iframe",'PriceV','SubCountV',"MapV","Form_Chart","FormData","Feed","ImageV","UserPassword","Username");
    }
    
    public static function submission_manager_excluded_fields(){
        return array('File','Spacing','Divider','HTMLH','HTMLP','RichText','Timer','YouTubeV',"Link","Iframe",'HTMLCustomized','ImageV','PriceV','SubCountV',"MapV","Form_Chart","FormData","Feed","UserPassword","Username");
    }

    public static function extract_youtube_embed_src($string) {
        return preg_replace(
                "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "$2", $string
        );
    }

    public static function extract_vimeo_embed_src($string) {

        return (int) substr(parse_url($string, PHP_URL_PATH), 1);
    }

    public static function check_src_type($string) {
        if (strpos($string, 'youtube') > 0) {
            return 'youtube';
        } elseif (strpos($string, 'vimeo') > 0) {
            return 'vimeo';
        } else {
            return 'unknown';
        }
    }

    public static function get_usa_states() {
        return array(
            'AL' => __('Alabama','custom-registration-form-builder-with-submission-manager'),
            'AK' => __('Alaska','custom-registration-form-builder-with-submission-manager'),
            'AZ' => __('Arizona','custom-registration-form-builder-with-submission-manager'),
            'AR' => __('Arkansas','custom-registration-form-builder-with-submission-manager'),
            'AA' => __('Armed Forces America','custom-registration-form-builder-with-submission-manager'),
            'AE' => __('Armed Forces Europe','custom-registration-form-builder-with-submission-manager'),
            'AP' => __('Armed Forces Pacific','custom-registration-form-builder-with-submission-manager'),
            'CA' => __('California','custom-registration-form-builder-with-submission-manager'),
            'CO' => __('Colorado','custom-registration-form-builder-with-submission-manager'),
            'CT' => __('Connecticut','custom-registration-form-builder-with-submission-manager'),
            'DE' => __('Delaware','custom-registration-form-builder-with-submission-manager'),
            'DC' => __('District Of Columbia','custom-registration-form-builder-with-submission-manager'),
            'FL' => __('Florida','custom-registration-form-builder-with-submission-manager'),
            'GA' => __('Georgia','custom-registration-form-builder-with-submission-manager'),
            'HI' => __('Hawaii','custom-registration-form-builder-with-submission-manager'),
            'ID' => __('Idaho','custom-registration-form-builder-with-submission-manager'),
            'IL' => __('Illinois','custom-registration-form-builder-with-submission-manager'),
            'IN' => __('Indiana','custom-registration-form-builder-with-submission-manager'),
            'IA' => __('Iowa','custom-registration-form-builder-with-submission-manager'),
            'KS' => __('Kansas','custom-registration-form-builder-with-submission-manager'),
            'KY' => __('Kentucky','custom-registration-form-builder-with-submission-manager'),
            'LA' => __('Louisiana','custom-registration-form-builder-with-submission-manager'),
            'ME' => __('Maine','custom-registration-form-builder-with-submission-manager'),
            'MD' => __('Maryland','custom-registration-form-builder-with-submission-manager'),
            'MA' => __('Massachusetts','custom-registration-form-builder-with-submission-manager'),
            'MI' => __('Michigan','custom-registration-form-builder-with-submission-manager'),
            'MN' => __('Minnesota','custom-registration-form-builder-with-submission-manager'),
            'MS' => __('Mississippi','custom-registration-form-builder-with-submission-manager'),
            'MO' => __('Missouri','custom-registration-form-builder-with-submission-manager'),
            'MT' => __('Montana','custom-registration-form-builder-with-submission-manager'),
            'NE' => __('Nebraska','custom-registration-form-builder-with-submission-manager'),
            'NV' => __('Nevada','custom-registration-form-builder-with-submission-manager'),
            'NH' => __('New Hampshire','custom-registration-form-builder-with-submission-manager'),
            'NJ' => __('New Jersey','custom-registration-form-builder-with-submission-manager'),
            'NM' => __('New Mexico','custom-registration-form-builder-with-submission-manager'),
            'NY' => __('New York','custom-registration-form-builder-with-submission-manager'),
            'NC' => __('North Carolina','custom-registration-form-builder-with-submission-manager'),
            'ND' => __('North Dakota','custom-registration-form-builder-with-submission-manager'),
            'OH' => __('Ohio','custom-registration-form-builder-with-submission-manager'),
            'OK' => __('Oklahoma','custom-registration-form-builder-with-submission-manager'),
            'OR' => __('Oregon','custom-registration-form-builder-with-submission-manager'),
            'PA' => __('Pennsylvania','custom-registration-form-builder-with-submission-manager'),
            'RI' => __('Rhode Island','custom-registration-form-builder-with-submission-manager'),
            'SC' => __('South Carolina','custom-registration-form-builder-with-submission-manager'),
            'SD' => __('South Dakota','custom-registration-form-builder-with-submission-manager'),
            'TN' => __('Tennessee','custom-registration-form-builder-with-submission-manager'),
            'TX' => __('Texas','custom-registration-form-builder-with-submission-manager'),
            'UT' => __('Utah','custom-registration-form-builder-with-submission-manager'),
            'VT' => __('Vermont','custom-registration-form-builder-with-submission-manager'),
            'VA' => __('Virginia','custom-registration-form-builder-with-submission-manager'),
            'WA' => __('Washington','custom-registration-form-builder-with-submission-manager'),
            'WV' => __('West Virginia','custom-registration-form-builder-with-submission-manager'),
            'WI' => __('Wisconsin','custom-registration-form-builder-with-submission-manager'),
            'WY' => __('Wyoming','custom-registration-form-builder-with-submission-manager'),
        );
    }

    public static function get_canadian_provinces() {
        return array(
            'AB' => __('Alberta','custom-registration-form-builder-with-submission-manager'),
            'BC' => __('British Columbia','custom-registration-form-builder-with-submission-manager'),
            'MB' => __('Manitoba','custom-registration-form-builder-with-submission-manager'),
            'NB' => __('New Brunswick','custom-registration-form-builder-with-submission-manager'),
            'NL' => __('Newfoundland and Labrador','custom-registration-form-builder-with-submission-manager'),
            'NT' => __('Northwest Territories','custom-registration-form-builder-with-submission-manager'),
            'NS' => __('Nova Scotia','custom-registration-form-builder-with-submission-manager'),
            'NU' => __('Nunavut','custom-registration-form-builder-with-submission-manager'),
            'ON' => __('Ontario','custom-registration-form-builder-with-submission-manager'),
            'PE' => __('Prince Edward Island','custom-registration-form-builder-with-submission-manager'),
            'QC' => __('QuÃ©bec','custom-registration-form-builder-with-submission-manager'),
            'SK' => __('Saskatchewan','custom-registration-form-builder-with-submission-manager'),
            'YT' => __('Yukon','custom-registration-form-builder-with-submission-manager')
      );
    }

    public static function get_countries() {
        $list= array(
            null => RM_UI_Strings::get("LABEL_SELECT_COUNTRY"),
            "Afghanistan[AF]" => __("Afghanistan","custom-registration-form-builder-with-submission-manager"),
            "Aland Islands[AX]" => __("Aland Islands","custom-registration-form-builder-with-submission-manager"),
            "Albania[AL]" => __("Albania","custom-registration-form-builder-with-submission-manager"),
            "Algeria[DZ]" => __("Algeria","custom-registration-form-builder-with-submission-manager"),
            "American Samoa[AS]" => __("American Samoa","custom-registration-form-builder-with-submission-manager"),
            "Andorra[AD]" => __("Andorra","custom-registration-form-builder-with-submission-manager"),
            "Angola[AO]" => __("Angola","custom-registration-form-builder-with-submission-manager"),
            "Anguilla[AI]" => __("Anguilla","custom-registration-form-builder-with-submission-manager"),
            "Antarctica[AQ]" => __("Antarctica","custom-registration-form-builder-with-submission-manager"),
            "Antigua and Barbuda[AG]" => __("Antigua and Barbuda","custom-registration-form-builder-with-submission-manager"),
            "Argentina[AR]" => __("Argentina","custom-registration-form-builder-with-submission-manager"),
            "Armenia[AM]" => __("Armenia","custom-registration-form-builder-with-submission-manager"),
            "Aruba[AW]" => __("Aruba","custom-registration-form-builder-with-submission-manager"),
            "Australia[AU]" => __("Australia","custom-registration-form-builder-with-submission-manager"),
            "Austria[AT]" => __("Austria","custom-registration-form-builder-with-submission-manager"),
            "Azerbaijan[AZ]" => __("Azerbaijan","custom-registration-form-builder-with-submission-manager"),
            "Bahamas, The[BS]" => __("Bahamas, The","custom-registration-form-builder-with-submission-manager"),
            "Bahrain[BH]" => __("Bahrain","custom-registration-form-builder-with-submission-manager"),
            "Bangladesh[BD]" => __("Bangladesh","custom-registration-form-builder-with-submission-manager"),
            "Barbados[BB]" => __("Barbados","custom-registration-form-builder-with-submission-manager"),
            "Belarus[BY]" => __("Belarus","custom-registration-form-builder-with-submission-manager"),
            "Belgium[BE]" => __("Belgium","custom-registration-form-builder-with-submission-manager"),
            "Belize[BZ]" => __("Belize","custom-registration-form-builder-with-submission-manager"),
            "Benin[BJ]" => __("Benin","custom-registration-form-builder-with-submission-manager"),
            "Bermuda[BM]" => __("Bermuda","custom-registration-form-builder-with-submission-manager"),
            "Bhutan[BT]" => __("Bhutan","custom-registration-form-builder-with-submission-manager"),
            "Bolivia[BO]" => __("Bolivia","custom-registration-form-builder-with-submission-manager"),
            "Bosnia and Herzegovina[BA]" => __("Bosnia and Herzegovina","custom-registration-form-builder-with-submission-manager"),
            "Botswana[BW]" => __("Botswana","custom-registration-form-builder-with-submission-manager"),
            "Bouvet Island[BV]" => __("Bouvet Island","custom-registration-form-builder-with-submission-manager"),
            "Brazil[BR]" => __("Brazil","custom-registration-form-builder-with-submission-manager"),
            "British Indian Ocean Territory[IO]" => __("British Indian Ocean Territory","custom-registration-form-builder-with-submission-manager"),
            "Brunei Darussalam[BN]" => __("Brunei Darussalam","custom-registration-form-builder-with-submission-manager"),
            "Bulgaria[BG]" => __("Bulgaria","custom-registration-form-builder-with-submission-manager"),
            "Burkina Faso[BF]" => __("Burkina Faso","custom-registration-form-builder-with-submission-manager"),
            "Burundi[BI]" => __("Burundi","custom-registration-form-builder-with-submission-manager"),
            "Cambodia[KH]" => __("Cambodia","custom-registration-form-builder-with-submission-manager"),
            "Cameroon[CM]" => __("Cameroon","custom-registration-form-builder-with-submission-manager"),
            "Canada[CA]" => __("Canada","custom-registration-form-builder-with-submission-manager"),
            "Cape Verde[CV]" => __("Cape Verde","custom-registration-form-builder-with-submission-manager"),
            "Cayman Islands[KY]" => __("Cayman Islands","custom-registration-form-builder-with-submission-manager"),
            "Central African Republic[CF]" => __("Central African Republic","custom-registration-form-builder-with-submission-manager"),
            "Chad[TD]" => __("Chad","custom-registration-form-builder-with-submission-manager"),
            "Chile[CL]" => __("Chile","custom-registration-form-builder-with-submission-manager"),
            "China[CN]" => __("China","custom-registration-form-builder-with-submission-manager"),
            "Christmas Island[CX]" => __("Christmas Island","custom-registration-form-builder-with-submission-manager"),
            "Cocos (Keeling) Islands[CC]" => __("Cocos (Keeling) Islands","custom-registration-form-builder-with-submission-manager"),
            "Colombia[CO]" => __("Colombia","custom-registration-form-builder-with-submission-manager"),
            "Comoros[KM]" => __("Comoros","custom-registration-form-builder-with-submission-manager"),
            "Congo[CG]" => __("Congo","custom-registration-form-builder-with-submission-manager"),
            "Congo, The Democratic Republic Of The[CD]" => __("Congo, The Democratic Republic Of The","custom-registration-form-builder-with-submission-manager"),
            "Cook Islands[CK]" => __("Cook Islands","custom-registration-form-builder-with-submission-manager"),
            "Costa Rica[CR]" => __("Costa Rica","custom-registration-form-builder-with-submission-manager"),
            "Cote D'ivoire[CI]" => __("Cote D'ivoire","custom-registration-form-builder-with-submission-manager"),
            "Croatia[HR]" => __("Croatia","custom-registration-form-builder-with-submission-manager"),
            "Cuba[CU]" => __("Cuba","custom-registration-form-builder-with-submission-manager"),
            "Cyprus[CY]" => __("Cyprus","custom-registration-form-builder-with-submission-manager"),
            "Czech Republic[CZ]" => __("Czech Republic","custom-registration-form-builder-with-submission-manager"),
            "Denmark[DK]" => __("Denmark","custom-registration-form-builder-with-submission-manager"),
            "Djibouti[DJ]" => __("Djibouti","custom-registration-form-builder-with-submission-manager"),
            "Dominica[DM]" => __("Dominica","custom-registration-form-builder-with-submission-manager"),
            "Dominican Republic[DO]" => __("Dominican Republic","custom-registration-form-builder-with-submission-manager"),
            "Ecuador[EC]" => __("Ecuador","custom-registration-form-builder-with-submission-manager"),
            "Egypt[EG]" => __("Egypt","custom-registration-form-builder-with-submission-manager"),
            "El Salvador[SV]" => __("El Salvador","custom-registration-form-builder-with-submission-manager"),
            "Equatorial Guinea[GQ]" => __("Equatorial Guinea","custom-registration-form-builder-with-submission-manager"),
            "Eritrea[ER]" => __("Eritrea","custom-registration-form-builder-with-submission-manager"),
            "Estonia[EE]" => __("Estonia","custom-registration-form-builder-with-submission-manager"),
            "Ethiopia[ET]" => __("Ethiopia","custom-registration-form-builder-with-submission-manager"),
            "Falkland Islands (Malvinas)[FK]" => __("Falkland Islands (Malvinas)","custom-registration-form-builder-with-submission-manager"),
            "Faroe Islands[FO]" => __("Faroe Islands","custom-registration-form-builder-with-submission-manager"),
            "Fiji[FJ]" => __("Fiji","custom-registration-form-builder-with-submission-manager"),
            "Finland[FI]" => __("Finland","custom-registration-form-builder-with-submission-manager"),
            "France[FR]" => __("France","custom-registration-form-builder-with-submission-manager"),
            "French Guiana[GF]" => __("French Guiana","custom-registration-form-builder-with-submission-manager"),
            "French Polynesia[PF]" => __("French Polynesia","custom-registration-form-builder-with-submission-manager"),
            "French Southern Territories[TF]" => __("French Southern Territories","custom-registration-form-builder-with-submission-manager"),
            "Gabon[GA]" => __("Gabon","custom-registration-form-builder-with-submission-manager"),
            "Gambia, The[GM]" => __("Gambia, The","custom-registration-form-builder-with-submission-manager"),
            "Georgia[GE]" => __("Georgia","custom-registration-form-builder-with-submission-manager"),
            "Germany[DE]" => __("Germany","custom-registration-form-builder-with-submission-manager"),
            "Ghana[GH]" => __("Ghana","custom-registration-form-builder-with-submission-manager"),
            "Gibraltar[GI]" => __("Gibraltar","custom-registration-form-builder-with-submission-manager"),
            "Greece[GR]" => __("Greece","custom-registration-form-builder-with-submission-manager"),
            "Greenland[GL]" => __("Greenland","custom-registration-form-builder-with-submission-manager"),
            "Grenada[GD]" => __("Grenada","custom-registration-form-builder-with-submission-manager"),
            "Guadeloupe[GP]" => __("Guadeloupe","custom-registration-form-builder-with-submission-manager"),
            "Guam[GU]" => __("Guam","custom-registration-form-builder-with-submission-manager"),
            "Guatemala[GT]" => __("Guatemala","custom-registration-form-builder-with-submission-manager"),
            "Guernsey[GG]" => __("Guernsey","custom-registration-form-builder-with-submission-manager"),
            "Guinea[GN]" => __("Guinea","custom-registration-form-builder-with-submission-manager"),
            "Guinea-Bissau[GW]" => __("Guinea-Bissau","custom-registration-form-builder-with-submission-manager"),
            "Guyana[GY]" => __("Guyana","custom-registration-form-builder-with-submission-manager"),
            "Haiti[HT]" => __("Haiti","custom-registration-form-builder-with-submission-manager"),
            "Heard Island and the McDonald Islands[HM]" => __("Heard Island and the McDonald Islands","custom-registration-form-builder-with-submission-manager"),
            "Holy See[VA]" => __("Holy See","custom-registration-form-builder-with-submission-manager"),
            "Honduras[HN]" => __("Honduras","custom-registration-form-builder-with-submission-manager"),
            "Hong Kong[HK]" => __("Hong Kong","custom-registration-form-builder-with-submission-manager"),
            "Hungary[HU]" => __("Hungary","custom-registration-form-builder-with-submission-manager"),
            "Iceland[IS]" => __("Iceland","custom-registration-form-builder-with-submission-manager"),
            "India[IN]" => __("India","custom-registration-form-builder-with-submission-manager"),
            "Indonesia[ID]" => __("Indonesia","custom-registration-form-builder-with-submission-manager"),
            "Iraq[IQ]" => __("Iraq","custom-registration-form-builder-with-submission-manager"),
            "Iran[IR]" => __("Iran","custom-registration-form-builder-with-submission-manager"),
            "Ireland[IE]" => __("Ireland","custom-registration-form-builder-with-submission-manager"),
            "Isle Of Man[IM]" => __("Isle Of Man","custom-registration-form-builder-with-submission-manager"),
            "Israel[IL]" => __("Israel","custom-registration-form-builder-with-submission-manager"),
            "Italy[IT]" => __("Italy","custom-registration-form-builder-with-submission-manager"),
            "Jamaica[JM]" => __("Jamaica","custom-registration-form-builder-with-submission-manager"),
            "Japan[JP]" => __("Japan","custom-registration-form-builder-with-submission-manager"),
            "Jersey[JE]" => __("Jersey","custom-registration-form-builder-with-submission-manager"),
            "Jordan[JO]" => __("Jordan","custom-registration-form-builder-with-submission-manager"),
            "Kazakhstan[KZ]" => __("Kazakhstan","custom-registration-form-builder-with-submission-manager"),
            "Kenya[KE]" => __("Kenya","custom-registration-form-builder-with-submission-manager"),
            "Kiribati[KI]" => __("Kiribati","custom-registration-form-builder-with-submission-manager"),
            "Korea, Republic Of[KR]" => __("Korea, Republic Of","custom-registration-form-builder-with-submission-manager"),
            "Kosovo[KS]" => __("Kosovo","custom-registration-form-builder-with-submission-manager"),
            "Kuwait[KW]" => __("Kuwait","custom-registration-form-builder-with-submission-manager"),
            "Kyrgyzstan[KG]" => __("Kyrgyzstan","custom-registration-form-builder-with-submission-manager"),
            "Lao People's Democratic Republic[LA]" => __("Lao People's Democratic Republic","custom-registration-form-builder-with-submission-manager"),
            "Latvia[LV]" => __("Latvia","custom-registration-form-builder-with-submission-manager"),
            "Lebanon[LB]" => __("Lebanon","custom-registration-form-builder-with-submission-manager"),
            "Lesotho[LS]" => __("Lesotho","custom-registration-form-builder-with-submission-manager"),
            "Liberia[LR]" => __("Liberia","custom-registration-form-builder-with-submission-manager"),
            "Libya[LY]" => __("Libya","custom-registration-form-builder-with-submission-manager"),
            "Liechtenstein[LI]" => __("Liechtenstein","custom-registration-form-builder-with-submission-manager"),
            "Lithuania[LT]" => __("Lithuania","custom-registration-form-builder-with-submission-manager"),
            "Luxembourg[LU]" => __("Luxembourg","custom-registration-form-builder-with-submission-manager"),
            "Macao[MO]" => __("Macao","custom-registration-form-builder-with-submission-manager"),
            "Macedonia, The Former Yugoslav Republic Of[MK]" => __("Macedonia, The Former Yugoslav Republic Of","custom-registration-form-builder-with-submission-manager"),
            "Madagascar[MG]" => __("Madagascar","custom-registration-form-builder-with-submission-manager"),
            "Malawi[MW]" => __("Malawi","custom-registration-form-builder-with-submission-manager"),
            "Malaysia[MY]" => __("Malaysia","custom-registration-form-builder-with-submission-manager"),
            "Maldives[MV]" => __("Maldives","custom-registration-form-builder-with-submission-manager"),
            "Mali[ML]" => __("Mali","custom-registration-form-builder-with-submission-manager"),
            "Malta[MT]" => __("Malta","custom-registration-form-builder-with-submission-manager"),
            "Marshall Islands[MH]" => __("Marshall Islands","custom-registration-form-builder-with-submission-manager"),
            "Martinique[MQ]" => __("Martinique","custom-registration-form-builder-with-submission-manager"),
            "Mauritania[MR]" => __("Mauritania","custom-registration-form-builder-with-submission-manager"),
            "Mauritius[MU]" => __("Mauritius","custom-registration-form-builder-with-submission-manager"),
            "Mayotte[YT]" => __("Mayotte","custom-registration-form-builder-with-submission-manager"),
            "Mexico[MX]" => __("Mexico","custom-registration-form-builder-with-submission-manager"),
            "Micronesia, Federated States Of[FM]" => __("Micronesia, Federated States Of","custom-registration-form-builder-with-submission-manager"),
            "Moldova, Republic Of[MD]" => __("Moldova, Republic Of","custom-registration-form-builder-with-submission-manager"),
            "Monaco[MC]" => __("Monaco","custom-registration-form-builder-with-submission-manager"),
            "Mongolia[MN]" => __("Mongolia","custom-registration-form-builder-with-submission-manager"),
            "Montenegro[ME]" => __("Montenegro","custom-registration-form-builder-with-submission-manager"),
            "Montserrat[MS]" => __("Montserrat","custom-registration-form-builder-with-submission-manager"),
            "Morocco[MA]" => __("Morocco","custom-registration-form-builder-with-submission-manager"),
            "Mozambique[MZ]" => __("Mozambique","custom-registration-form-builder-with-submission-manager"),
            "Myanmar[MM]" => __("Myanmar","custom-registration-form-builder-with-submission-manager"),
            "Namibia[NA]" => __("Namibia","custom-registration-form-builder-with-submission-manager"),
            "Nauru[NR]" => __("Nauru","custom-registration-form-builder-with-submission-manager"),
            "Nepal[NP]" => __("Nepal","custom-registration-form-builder-with-submission-manager"),
            "Netherlands[NL]" => __("Netherlands","custom-registration-form-builder-with-submission-manager"),
            "Netherlands Antilles[AN]" => __("Netherlands Antilles","custom-registration-form-builder-with-submission-manager"),
            "New Caledonia[NC]" => __("New Caledonia","custom-registration-form-builder-with-submission-manager"),
            "New Zealand[NZ]" => __("New Zealand","custom-registration-form-builder-with-submission-manager"),
            "Nicaragua[NI]" => __("Nicaragua","custom-registration-form-builder-with-submission-manager"),
            "Niger[NE]" => __("Niger","custom-registration-form-builder-with-submission-manager"),
            "Nigeria[NG]" => __("Nigeria","custom-registration-form-builder-with-submission-manager"),
            "Niue[NU]" => __("Niue","custom-registration-form-builder-with-submission-manager"),
            "Norfolk Island[NF]" => __("Norfolk Island","custom-registration-form-builder-with-submission-manager"),
            "Northern Mariana Islands[MP]" => __("Northern Mariana Islands","custom-registration-form-builder-with-submission-manager"),
            "Norway[NO]" => __("Norway","custom-registration-form-builder-with-submission-manager"),
            "Oman[OM]" => __("Oman","custom-registration-form-builder-with-submission-manager"),
            "Pakistan[PK]" => __("Pakistan","custom-registration-form-builder-with-submission-manager"),
            "Palau[PW]" => __("Palau","custom-registration-form-builder-with-submission-manager"),
            "Palestinian Territories[PS]" => __("Palestinian Territories","custom-registration-form-builder-with-submission-manager"),
            "Panama[PA]" => __("Panama","custom-registration-form-builder-with-submission-manager"),
            "Papua New Guinea[PG]" => __("Papua New Guinea","custom-registration-form-builder-with-submission-manager"),
            "Paraguay[PY]" => __("Paraguay","custom-registration-form-builder-with-submission-manager"),
            "Peru[PE]" => __("Peru","custom-registration-form-builder-with-submission-manager"),
            "Philippines[PH]" => __("Philippines","custom-registration-form-builder-with-submission-manager"),
            "Pitcairn[PN]" => __("Pitcairn","custom-registration-form-builder-with-submission-manager"),
            "Poland[PL]" => __("Poland","custom-registration-form-builder-with-submission-manager"),
            "Portugal[PT]" => __("Portugal","custom-registration-form-builder-with-submission-manager"),
            "Puerto Rico[PR]" => __("Puerto Rico","custom-registration-form-builder-with-submission-manager"),
            "Qatar[QA]" => __("Qatar","custom-registration-form-builder-with-submission-manager"),
            "Reunion[RE]" => __("Reunion","custom-registration-form-builder-with-submission-manager"),
            "Romania[RO]" => __("Romania","custom-registration-form-builder-with-submission-manager"),
            "Russian Federation[RU]" => __("Russian Federation","custom-registration-form-builder-with-submission-manager"),
            "Rwanda[RW]" => __("Rwanda","custom-registration-form-builder-with-submission-manager"),
            "Saint Barthelemy[BL]" => __("Saint Barthelemy","custom-registration-form-builder-with-submission-manager"),
            "Saint Helena[SH]" => __("Saint Helena","custom-registration-form-builder-with-submission-manager"),
            "Saint Kitts and Nevis[KN]" => __("Saint Kitts and Nevis","custom-registration-form-builder-with-submission-manager"),
            "Saint Lucia[LC]" => __("Saint Lucia","custom-registration-form-builder-with-submission-manager"),
            "Saint Martin[MF]" => __("Saint Martin","custom-registration-form-builder-with-submission-manager"),
            "Saint Pierre and Miquelon[PM]" => __("Saint Pierre and Miquelon","custom-registration-form-builder-with-submission-manager"),
            "Saint Vincent and The Grenadines[VC]" => __("Saint Vincent and The Grenadines","custom-registration-form-builder-with-submission-manager"),
            "Samoa[WS]" => __("Samoa","custom-registration-form-builder-with-submission-manager"),
            "San Marino[SM]" => __("San Marino","custom-registration-form-builder-with-submission-manager"),
            "Sao Tome and Principe[ST]" => __("Sao Tome and Principe","custom-registration-form-builder-with-submission-manager"),
            "Saudi Arabia[SA]" => __("Saudi Arabia","custom-registration-form-builder-with-submission-manager"),
            "Senegal[SN]" => __("Senegal","custom-registration-form-builder-with-submission-manager"),
            "Serbia[RS]" => __("Serbia","custom-registration-form-builder-with-submission-manager"),
            "Seychelles[SC]" => __("Seychelles","custom-registration-form-builder-with-submission-manager"),
            "Sierra Leone[SL]" => __("Sierra Leone","custom-registration-form-builder-with-submission-manager"),
            "Singapore[SG]" => __("Singapore","custom-registration-form-builder-with-submission-manager"),
            "Slovakia[SK]" => __("Slovakia","custom-registration-form-builder-with-submission-manager"),
            "Slovenia[SI]" => __("Slovenia","custom-registration-form-builder-with-submission-manager"),
            "Solomon Islands[SB]" => __("Solomon Islands","custom-registration-form-builder-with-submission-manager"),
            "Somalia[SO]" => __("Somalia","custom-registration-form-builder-with-submission-manager"),
            "South Africa[ZA]" => __("South Africa","custom-registration-form-builder-with-submission-manager"),
            "South Georgia and the South Sandwich Islands[GS]" => __("South Georgia and the South Sandwich Islands","custom-registration-form-builder-with-submission-manager"),
            "Spain[ES]" => __("Spain","custom-registration-form-builder-with-submission-manager"),
            "Sri Lanka[LK]" => __("Sri Lanka","custom-registration-form-builder-with-submission-manager"),
            "Sudan[SD]" => __("Sudan","custom-registration-form-builder-with-submission-manager"),
            "Suriname[SR]" => __("Suriname","custom-registration-form-builder-with-submission-manager"),
            "Svalbard and Jan Mayen[SJ]" => __("Svalbard and Jan Mayen","custom-registration-form-builder-with-submission-manager"),
            "Swaziland[SZ]" => __("Swaziland","custom-registration-form-builder-with-submission-manager"),
            "Sweden[SE]" => __("Sweden","custom-registration-form-builder-with-submission-manager"),
            "Switzerland[CH]" => __("Switzerland","custom-registration-form-builder-with-submission-manager"),
            "Taiwan[TW]" => __("Taiwan","custom-registration-form-builder-with-submission-manager"),
            "Tajikistan[TJ]" => __("Tajikistan","custom-registration-form-builder-with-submission-manager"),
            "Tanzania, United Republic Of[TZ]" => __("Tanzania, United Republic Of","custom-registration-form-builder-with-submission-manager"),
            "Thailand[TH]" => __("Thailand","custom-registration-form-builder-with-submission-manager"),
            "Timor-leste[TL]" => __("Timor-leste","custom-registration-form-builder-with-submission-manager"),
            "Togo[TG]" => __("Togo","custom-registration-form-builder-with-submission-manager"),
            "Tokelau[TK]" => __("Tokelau","custom-registration-form-builder-with-submission-manager"),
            "Tonga[TO]" => __("Tonga","custom-registration-form-builder-with-submission-manager"),
            "Trinidad and Tobago[TT]" => __("Trinidad and Tobago","custom-registration-form-builder-with-submission-manager"),
            "Tunisia[TN]" => __("Tunisia","custom-registration-form-builder-with-submission-manager"),
            "Turkey[TR]" => __("Turkey","custom-registration-form-builder-with-submission-manager"),
            "Turkmenistan[TM]" => __("Turkmenistan","custom-registration-form-builder-with-submission-manager"),
            "Turks and Caicos Islands[TC]" => __("Turks and Caicos Islands","custom-registration-form-builder-with-submission-manager"),
            "Tuvalu[TV]" => __("Tuvalu","custom-registration-form-builder-with-submission-manager"),
            "Uganda[UG]" => __("Uganda","custom-registration-form-builder-with-submission-manager"),
            "Ukraine[UA]" => __("Ukraine","custom-registration-form-builder-with-submission-manager"),
            "United Arab Emirates[AE]" => __("United Arab Emirates","custom-registration-form-builder-with-submission-manager"),
            "United Kingdom[GB]" => __("United Kingdom","custom-registration-form-builder-with-submission-manager"),
            "United States[US]" => __("United States","custom-registration-form-builder-with-submission-manager"),
            "United States Minor Outlying Islands[UM]" => __("United States Minor Outlying Islands","custom-registration-form-builder-with-submission-manager"),
            "Uruguay[UY]" => __("Uruguay","custom-registration-form-builder-with-submission-manager"),
            "Uzbekistan[UZ]" => __("Uzbekistan","custom-registration-form-builder-with-submission-manager"),
            "Vanuatu[VU]" => __("Vanuatu","custom-registration-form-builder-with-submission-manager"),
            "Venezuela[VE]" => __("Venezuela","custom-registration-form-builder-with-submission-manager"),
            "Vietnam[VN]" => __("Vietnam","custom-registration-form-builder-with-submission-manager"),
            "Virgin Islands, British[VG]" => __("Virgin Islands, British","custom-registration-form-builder-with-submission-manager"),
            "Virgin Islands, U.S.[VI]" => __("Virgin Islands, U.S.","custom-registration-form-builder-with-submission-manager"),
            "Wallis and Futuna[WF]" => __("Wallis and Futuna","custom-registration-form-builder-with-submission-manager"),
            "Western Sahara[EH]" => __("Western Sahara","custom-registration-form-builder-with-submission-manager"),
            "Yemen[YE]" => __("Yemen","custom-registration-form-builder-with-submission-manager"),
            "Zambia[ZM]" => __("Zambia","custom-registration-form-builder-with-submission-manager"),
            "Zimbabwe[ZW]" => __("Zimbabwe","custom-registration-form-builder-with-submission-manager")        
          );
        $list = apply_filters('rm_country_list',$list);
        return $list;
    }

    public static function get_formdata_widget_html($field_id) {
        $field = new RM_Fields();
        $field->load_from_db($field_id);

        $class = $field->field_options->field_css_class;
        $html = "<div class='rmrow'><div class='fdata-row'>";
        $form_name = '';
        $form = new RM_Forms();
        $form->load_from_db($field->get_form_id());
        $stats = new RM_Analytics_Service();
        $stats_data = $stats->calculate_form_stats($field->get_form_id());
        $options = array("nu_form_views" => array("nu_views_text_before", "nu_views_text_after"),
            "nu_submissions" => array("nu_sub_text_before", "nu_sub_text_after"),
            "sub_limits" => array("sub_limit_text_before", "sub_limit_text_after"),
            "sub_date_limits" => array("sub_date_limit_text_before", "sub_date_limit_text_after"),
            "last_sub_rec" => array("ls_text_before", "ls_text_after"));

        foreach ($options as $key => $values) {
            $value = '';
            if (!empty($field->field_options->{$key}) && $field->field_options->{$key}) {
                if ($key == 'nu_form_views') {
                    $value = $stats_data->total_entries;
                } else if ($key == 'nu_submissions') {
                    $value = $stats_data->successful_submission;
                } else if ($key == "sub_limits") {
                    $fo = $form->form_options;
                    $value = $fo->form_submissions_limit;
                } else if ($key == "sub_date_limits") {
                    $limit_type = empty($field->field_options->sub_limit_ind) ? 'date' : $field->field_options->sub_limit_ind;
                    $fo = $form->form_options;
                    if ($form->get_form_should_auto_expire()) {
                        if (!empty($fo->form_expiry_date)) {
                            if ($limit_type == "days") {
                                $diff = strtotime($fo->form_expiry_date) - time();
                                if ($diff > 0) {
                                    $value = floor($diff / (60 * 60 * 24)) . ' Days ';
                                }
                            } else {
                                $value = $fo->form_expiry_date;
                            }
                        }
                    }
                } else if ($key == "last_sub_rec") {
                    $submission = RM_DBManager::get_last_submission();
                    if (!empty($submission)) {
                        $visited_on = strtotime($submission->submitted_on);
                        if (!empty($visited_on)) {
                            $visited_on = self::convert_to_mysql_timestamp(strtotime($submission->submitted_on));
                            $visited_on = self::localize_time($visited_on, 'd M Y, h:ia');
                            $value = $visited_on;
                        }
                    }
                }
                $html .= $field->field_options->{$values[0]} . " <span>$value</span> " . $field->field_options->{$values[1]} . '<br>';
            }
        }
        if ($field->field_options->show_form_name) {
            $html .= '<div class="rm-form-name"><h3>' . $form->get_form_name() . '</h3></div>';
        }

        if ($field->field_options->form_desc) {
            $html .= '<div class="rm-form-name">' . $form->form_options->form_description . '</div>';
        }

        $html .= "</div></div>";
        return $html;
    }

    public static function get_feed_widget_html($field_id) {
        $field= new RM_Fields();
        $field->load_from_db($field_id);
        
        $class=  $field->field_options->field_css_class;
        $limit=  (int)$field->field_options->max_items>0 ? $field->field_options->max_items : 5;
        $html = "<div class='rmrow $class'>";
        $initial='';
        $form_id= $field->get_form_id();
        $form= new RM_Forms();
        $form->load_from_db($form_id);

        if($form->get_form_type()==1){
            $user_repo= new RM_User_Repository();
            $users= $user_repo->get_users_for_front(array("form_id"=>$form_id,'limit'=>$limit));

            if(is_array($users) && count($users)>0){
            foreach($users as $user){
                if(empty($user->ID))
                    continue;
                $initial='';
                $value= $field->field_value;
                if($value=="user_login"){
                    $initial= $user->user_login;
                } else if($value=="first_name"){
                    $initial= get_user_meta($user->ID, "first_name", true);
                    $initial= empty($initial) ? $user->display_name : $initial;
                } else if($value=="last_name"){
                     $initial= get_user_meta($user->ID, "last_name", true);
                     $initial= empty($initial) ? $user->display_name : $initial;
                } else if($value=="custom"){
                    $initial= $field->field_options->custom_value;
                } else if($value=="display_name"){
                    $initial= $user->display_name;
                } else if($value=='in_last_name'){
                    $first_name= get_user_meta($user->ID, "first_name", true);
                    $last_name= get_user_meta($user->ID, "last_name", true);
                    if(empty($first_name) && empty($last_name)){
                        $initial= $user->display_name;
                    } else{
                        $first_initial= !empty($first_name) ? strtoupper($first_name[0]) : '';
                        $initial= $first_initial.' '.ucwords($last_name);
                    }
                }
                else if($value=="both_names"){
                    $first_name= get_user_meta($user->ID, "first_name", true);
                    $last_name= get_user_meta($user->ID, "last_name", true);
                    if(empty($first_name) && empty($last_name)){
                        $initial= $user->display_name;
                    } else
                    $initial= $first_name.' '.$last_name;
                }
                $html .= "<div class='rm-rgfeed'>";
                 if($field->field_options->show_gravatar){
                    $html .= "<span class='rm-avatar'>".get_avatar($user->user_email)."</span>";
                }
                $html .="<div class='rm-rgfeed-user-info'> <span class='rm-rgfeed-user'>$initial </span>";
                if(!$field->field_options->hide_date){
                    if(empty($user->user_registered)){
                        $submission= RM_DBManager::get_submissions_for_user($user->user_email,1);
                        if(!empty($submission)){
                           $html .= RM_UI_Strings::get("LABEL_UNREGISTERED_SUB")." <b>".self::format_on_time($submission[0]->submitted_on)."</b>";
                        }
                        
                    } else {
                        $html .= RM_UI_Strings::get("LABEL_REGISTERED_ON")." <b>".self::format_on_time($user->user_registered)."</b>";
                    }
                }
                else{
                    if(empty($user->user_registered)){
                        $submission= RM_DBManager::get_submissions_for_user($user->user_email,1);
                        if(!empty($submission)){
                           $html .= RM_UI_Strings::get("LABEL_UNREGISTERED_SUB");
                        }
                    } else{
                        $html .= RM_UI_Strings::get("LABEL_REGISTERED_ON");
                    }
                }
      
                
                if(!$field->field_options->hide_country){ 
                    $submissions= RM_DBManager::get_latest_submission_for_user($user->user_email,array($form_id));
                    if(!empty($submissions) && is_array($submissions))
                    {
                        $data= maybe_unserialize($submissions[0]->data);
                        $country='';
                        $country_field= RM_DBManager::get_field_by_type($form_id,'Country');
                        if(!empty($country_field) && isset($data[$country_field->field_id])){
                            $country= $data[$country_field->field_id]->value;
                            preg_match("/\[[A-Z]{2}\]/", $country,$matches);
                            if(!empty($matches)){
                                preg_match("/[A-Z]{2}/",$matches[0],$matches);
                                if(!empty($matches)){
                                    $flag= strtolower($matches[0]);
                                    $country_name= str_replace("["."$matches[0]"."]", '', $country);
                                    $country= '<b>'.$country_name.'</b> <img class="rm_country_flag" src="'.RM_IMG_URL.'flag/16/'.$flag.'.png" />';
                                }
                                
                            }     
                        }
                        if(!empty($country))
                             $html .= " from $country ";
                    }
                }
                $html .=" </div></div>";
            }
            } 
        } else {
            $submissions= RM_DBManager::get_submissions_for_form($form_id,$limit,0,'*','submitted_on',true);
            
            $value= $field->field_value;
            if($value=='custom'){
                $initial= $field->field_options->custom_value.' ';
            }
            else
            {
                $initial= ' User ';
            }
            foreach($submissions as $submission){ 
                $data= maybe_unserialize($submission->data);
                $html .= "<div class='rm-rgfeed'> ";
            
                  if($field->field_options->show_gravatar){
                    $html .= "<span class='rm-avatar'>".get_avatar($submission->user_email)."</span>";
                }
                    $html .="<div class='rm-rgfeed-user-info'><span class='rm-rgfeed-user'>$initial</span>";
                if(!$field->field_options->hide_date){
                    $html .= RM_UI_Strings::get("LABEL_SUBMITTED_ON")." <b>". self::format_on_time($submission->submitted_on)."</b>";
                }
                if(!$field->field_options->hide_country){
                    $data= maybe_unserialize($submission->data);
                    $country='';
                    $country_field= RM_DBManager::get_field_by_type($form_id,'Country');
                    if(!empty($country_field) && isset($data[$country_field->field_id])){
                        $country= $data[$country_field->field_id]->value;   
                        preg_match("/\[[A-Z]{2}\]/",$country,$matches);
                            if(!empty($matches)){
                                preg_match("/[A-Z]{2}/",$matches[0],$matches);
                                if(!empty($matches)){
                                    $flag= strtolower($matches[0]);
                                    $country_name= str_replace("["."$matches[0]"."]", '', $country);
                                    $country = '<b>'.$country_name.'</b> <img class="rm_country_flag" src="'.RM_IMG_URL.'flag/16/'.$flag.'.png" />';
                                }
                                
                        }  
                    }
                    if(!empty($country))
                         $html .= " from $country";
                }
                $html .="</div> </div>";
              
            }
        }
      $html .= "</div>";
      return $html;
    }

    public static function format_on_time($t) {
        $ts = strtotime($t);
        if ($ts >= strtotime("today"))
            return date('g:i A', $ts) . ' today';
        else if ($ts >= strtotime("yesterday"))
            return date('g:i A', $ts) . ' yesterday';
        else {
            $on = self::convert_to_mysql_timestamp($ts);
            $on = self::localize_time($on, 'd M Y, h:i A');
            return $on;
        }
    }
    
     public static function get_form_expiry_message($form_id){
         $service= new RM_Services();
         $form= new RM_Forms();
         $form->load_from_db($form_id);
         $expiry_details = $service->get_form_expiry_stats($form);
         $exp_str='';
         if (!empty($expiry_details) && $expiry_details->state !== 'perpetual')
          {
           if ($expiry_details->state === 'expired')
            $exp_str .= '<div class="rm-formcard-expired">' .__("Expired", 'custom-registration-form-builder-with-submission-manager'). '</div>';
           else
            {
                switch ($expiry_details->criteria)
                {
                    case 'both':
                        $message = sprintf(RM_UI_Strings::get('EXPIRY_DETAIL_BOTH'), ($expiry_details->sub_limit - $expiry_details->remaining_subs), $expiry_details->sub_limit, $expiry_details->remaining_days);
                        $exp_str .= '<div class="rm-formcard-expired"><span class="rm_sandclock"></span>' . $message . '</div>';
                        break;
                    case 'subs':
                        $total = $expiry_details->sub_limit;
                        $rem = $expiry_details->remaining_subs;
                        $wtot = 100;
                        $rem = ($rem * 100) / $total;
                        $done = 100 - $rem;
                        $message = sprintf(RM_UI_Strings::get('EXPIRY_DETAIL_SUBS'), ($expiry_details->sub_limit - $expiry_details->remaining_subs), $expiry_details->sub_limit);
                        $exp_str .= '<div class="rm-formcard-expired"><span class="rm_sandclock"></span>' . $message . '</div>';
                        break;

                    case 'date':
                        $message = sprintf(RM_UI_Strings::get('EXPIRY_DETAIL_DATE'), $expiry_details->remaining_days);
                        $exp_str .= '<div class="rm-formcard-expired"><span class="rm_sandclock"></span>' . $message . '</div>';
                        break;
                }
            } 
            }
            return $exp_str;
    }
    
    public static function get_acc_verification_link($user_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_acc_verification_link($user_id);
        }
    }
    
    public static function js_error_messages() {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::js_error_messages();
        }
    }
    
    public static function setup_login_form_options(){
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'fields'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'fields','value'=>'{"form_fields":[{"username_accepts":"username","field_label":"Username","placeholder":"Enter Username","input_selected_icon_codepoint":"","icon_fg_color":"CBFFC2","icon_bg_color":"FFFFFF","icon_bg_alpha":"0.5","icon_shape":"square","field_css_class":"","field_type":"username"},{"field_label":"Password","placeholder":"Enter Password","input_selected_icon_codepoint":"","icon_fg_color":"FFFFFF","icon_bg_color":"FFFFFF","icon_bg_alpha":"0.5","icon_shape":"square","field_css_class":"test","field_type":"password"}]}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'redirections'));
        if($count==0){
            $redirect_login = (get_option('rm_option_post_submission_redirection_url'))?get_option('rm_option_post_submission_redirection_url'):"";
            $redirect_admin = (get_option('rm_option_redirect_admin_to_dashboard_post_login'))?1:0;
            $redirect_logout = (get_option('rm_option_post_logout_redirection_page_id'))?get_option('rm_option_post_logout_redirection_page_id'):"";
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'redirections','value'=>'{"redirection_type":"common","redirection_link":"'.$redirect_login.'","admin_redirection_link":'.$redirect_admin.',"logout_redirection":"'.$redirect_logout.'","role_based_login_redirection":[],"administrator_login_redirection":"","administrator_logout_redirection":"","editor_login_redirection":"","editor_logout_redirection":"","author_login_redirection":"","author_logout_redirection":"","contributor_login_redirection":"","contributor_logout_redirection":"","subscriber_login_redirection":"","subscriber_logout_redirection":"","translator_login_redirection":"","translator_logout_redirection":"","customer_login_redirection":"","customer_logout_redirection":""}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'validations'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'validations','value'=>'{"un_error_msg":"The login credentials you entered are incorrect. Please try again.","pass_error_msg":"The login credentials you entered are incorrect. Please try again.","sub_error_msg":"You are not authorized to view the contents of this page. Please log in to view the submissions.","en_recovery_link":1,"en_failed_user_notification":0,"en_failed_admin_notification":0,"en_captcha":0,"allowed_failed_attempts":3,"allowed_failed_duration":60,"en_ban_ip":0,"allowed_attempts_before_ban":6,"allowed_duration_before_ban":60,"ban_type":"temp","ban_duration":1440,"ban_error_msg":"<div style=\"font-weight: 400;\" class=\"rm-failed-ip-error\">Your IP has been banned by the Admin due to repeated failed login attempts.<\/div>","notify_admin_on_ban":1}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'recovery'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'recovery','value'=>'{"en_pwd_recovery":1,"recovery_link_text":"Lost your password?","recovery_page":""}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'auth'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'auth','value'=>'{"otp_type":"numeric","en_two_fa":0,"otp_length":6,"otp_expiry_action":"regenerate","otp_expiry":10,"otp_regen_success_msg":"A new OTP was successfully sent to your email address!","otp_regen_text":"Re-generate OTP","otp_exp_msg":"Sorry, your OTP has expired. You can re-generate OTP using link below.","otp_exp_restart_msg":"Sorry, your OTP has expired. You need to login again to proceed.","otp_field_label":"Enter Your OTP","msg_above_otp":"We emailed you a one-time-password (OTP) to your registered email address. Please enter it below to complete the login process.","en_resend_otp":1,"otp_resend_text":"Did not received OTP? Resend it","otp_resent_msg":"OTP was resent successfully to your email address!","otp_resend_limit":"3","allowed_incorrect_attempts":5,"invalid_otp_error":"The OTP you entered is incorrect.","apply_on":"all","disable_two_fa_for_admin":1,"enable_two_fa_for_roles":[]}'), array('%s','%s'));
        }
        
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'email_templates'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'email_templates','value'=>'{"failed_login_err":"<span style=\"font-weight: 400;\">There was a failed login attempt using your account username\/ password {{username}} on our site {{sitename}} from IP {{Login_IP}} on {{login_time}}. If you have forgotten your password, you can easily reset it by visiting login page on our site. <\/span>\r\n\r\n&nbsp;\r\n\r\n<span style=\"font-weight: 400;\">If you think it was an unauthorized login attempt, please contact site admin immediately.<\/span>","otp_message":"<span style=\"font-weight: 400;\">Here is your one-time-password (OTP) for logging into {{site_name}}. The OTP will automatically expire after {{OTP_expiry}} minutes.<\/span>\r\n\r\n&nbsp;\r\n\r\n<span style=\"font-weight: 400;\">{{OTP}}<\/span>\r\n\r\n&nbsp;\r\n\r\n<span style=\"font-weight: 400;\">If you think it was an unauthorized login attempt, please contact site admin immediately. <\/span>\r\n\r\n&nbsp;\r\n\r\n<span style=\"font-weight: 400;\"><\/span>","failed_login_err_admin":"<span style=\"font-weight: 400;\">There was a failed login attempt using username\/ password {{username}} on your site {{sitename}} from IP {{Login_IP}} on {{login_time}}.<\/span>\r\n\r\n&nbsp;\r\n\r\n<span style=\"font-weight: 400;\">If you think this is an unauthorized login attempt<\/span><i><span style=\"font-weight: 400;\">, <\/span><\/i><span style=\"font-weight: 400;\">you can also immediately ban the IP by clicking <\/span><span style=\"font-weight: 400;\"><a href=\"'.admin_url().'admin.php?page=rm_options_security\">here</a><\/span><span style=\"font-weight: 400;\">. <\/span>\r\n\r\n<span style=\"font-weight: 400;\">You can managed the blocked IPs and\/ or usernames by visiting <\/span><span style=\"font-weight: 400;\"><a href=\"'.admin_url().'admin.php?page=rm_options_security\">this link</a><\/span> <i><span style=\"font-weight: 400;\">Global Settings \u2192 Security page link<\/span><\/i>","ban_message_admin":"<span style=\"font-weight: 400;\">There were multiple failed login attempts from IP {{login_IP}}. As a preset security measure, RegistrationMagic has blocked the IP. Here are the details of the ban:<\/span>\r\n\r\n&nbsp;\r\n\r\n<span style=\"font-weight: 400;\">Ban Period: {{ban_period}}<\/span>\r\n\r\n<span style=\"font-weight: 400;\">Failed Login Attempts: {{ban_trigger}}<\/span>\r\n\r\n<span style=\"font-weight: 400;\">If you think this IP is secure, you can lift the ban by clicking <\/span><span style=\"font-weight: 400;\"><a href=\"'.admin_url().'admin.php?page=rm_options_security\">here</a><\/span><span style=\"font-weight: 400;\">. <\/span>\r\n\r\n<span style=\"font-weight: 400;\">You can managed the blocked IPs and\/ or usernames by visiting <\/span><span style=\"font-weight: 400;\"><a href=\"'.admin_url().'admin.php?page=rm_options_security\">this link</a><\/span> <i><span style=\"font-weight: 400;\">Global Settings \u2192 Security page link<\/span><\/i>"}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'btn_config'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'btn_config','value'=>'{"register_btn":"Register","login_btn":"Login","align":"center","display_register":0}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'design'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'design','value'=>'{"style_form":"","style_textfield":"","style_btnfield":"","form_submit_btn_label":"Submit","style_section":"","form_id":"login","placeholder_css":""}'), array('%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'login_view'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'login_view','value'=>'{"display_user_avatar":1,"display_user_name":1,"display_greetings":1,"greetings_text":"Welcome","display_custom_msg":1,"custom_msg":"You are already logged in.","separator_bar_color":"DDDDDD","display_account_link":1,"account_link_text":"My Account","display_logout_link":1,"logout_text":"Logout"}'), array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'));
        }
        
        $count= RM_DBManager::count('LOGIN', array('m_key'=>'log_retention'));
        if($count==0){
            RM_DBManager::insert_row('LOGIN', array('m_key'=>'log_retention','value'=>'{"logs_retention":"records","no_of_records":1000,"no_of_days":7}'),array('%s','%s'));
        }
        
       
        
    }
    
    public static function random_number($length) {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }
    
    public static function is_user_online($user_to_check){
        // get the user activity the list
        $logged_in_users = get_transient('rm_user_online_status');

        $online = isset($logged_in_users[$user_to_check])
       && ($logged_in_users[$user_to_check] >  (time() - (15 * 60)));
        
        return $online;
    }
    
    public static function validate_username_characters($username,$form_id){
      $error= '';
      if(isset($username) && $username){
             $username= sanitize_text_field($username);
             $rm_service= new RM_Services();
             $field= $rm_service->get_primary_field_options('Username',$form_id);
             if(!isset($field->field_options))
                 return $error;
             $field_options= maybe_unserialize($field->field_options);
             if(is_array($field_options->username_characters)){
                  $expression_chars= array();        
                  foreach($field_options->username_characters as $scheme){
                      switch($scheme){
                          case 'alphabets': array_push ($expression_chars, 'a-zA-Z'); break;
                          case 'numbers': array_push ($expression_chars, '0-9'); break;
                          case 'underscores': array_push ($expression_chars, '_'); break;
                          case 'periods': array_push ($expression_chars, '.'); break;
                      }
 
                  }
                  if(!empty($expression_chars)){
                    $expression= implode('', $expression_chars);   
                    $expression= "/^[$expression]+$/";
                    if(!preg_match($expression, $username)){
                          $error= str_replace('{{allowed_characters}}',implode(',', $field_options->username_characters),$field_options->invalid_username_format);
                    }
                  }
                   
             }
         }
     return $error;    
  }
  
  public static function is_username_hidden($form_id){
      $form= new RM_Forms();
      $form->load_from_db($form_id);
      $form_options= $form->get_form_options();
      return isset($form_options->hide_username) ? $form_options->hide_username : false;
      
      /*
      $username_field = RM_DBManager::get_field_by_type($form_id, 'Username');
      if(empty($username_field))
          return true;
      
      return false;*/
  }  
  
   public static function sync_username_hide_option($form_id){
      $username_field = RM_DBManager::get_field_by_type($form_id, 'Username');
      $form_model= new RM_Forms();
      $form_model->load_from_db($form_id);
      if($form_model->get_form_type()!=RM_REG_FORM)
              return;
      $form_options= $form_model->get_form_options();
      if(empty($username_field)){
            $form_options->hide_username= 1;
       }
       else{
           $form_options->hide_username= 0;
       }
        $form_model->set_form_options($form_options);
        $form_model->update_into_db();
  }
  
    public static function get_password_regex($pw_rests) {
        $min_len = 0;
        $max_len = '';
        if(is_array($pw_rests->selected_rules)){
            if (in_array('PWR_MINLEN', $pw_rests->selected_rules) && isset($pw_rests->min_len) && $pw_rests->min_len)
                $min_len = $pw_rests->min_len;
            
            if (in_array('PWR_MAXLEN', $pw_rests->selected_rules) && isset($pw_rests->max_len) && $pw_rests->max_len)
                $max_len = $pw_rests->max_len;
        }
        
        $regex = '[A-Za-z\d\W+]{' . $min_len . ',' . $max_len . '}';
        
        if(is_array($pw_rests->selected_rules)){
            if (in_array('PWR_UC', $pw_rests->selected_rules))
                $regex = '(?=.*[A-Z])' . $regex;
            if (in_array('PWR_NUM', $pw_rests->selected_rules))
                $regex = '(?=.*\d)' . $regex;
            if (in_array('PWR_SC', $pw_rests->selected_rules))
                $regex = '(?=.*\W+)' . $regex;
        }   

        return $regex;
    }
    
    public static function check_access_control($factrl, $request) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::check_access_control($factrl, $request);
        }
    }
    
    public static function sync_hide_option_with_fields($form_id){
      $service= new RM_Services();
      $form_model= new RM_Forms();
      $form_model->load_from_db($form_id);
      $has_primary_fields= $service->has_primary_fields($form_id);
      if($form_model->get_form_type()==RM_REG_FORM){
          if(!$has_primary_fields){
              $service->add_primary_fields($form_id);
          }
      }
      else
      {
          if($has_primary_fields){
            $username_field = RM_DBManager::get_field_by_type($form_id, 'Username');
            if(!empty($username_field)){
                $service->remove($username_field->field_id, 'FIELDS', array());
            }
            
            $password_field = RM_DBManager::get_field_by_type($form_id, 'UserPassword');
            if(!empty($password_field)){
                $service->remove($password_field->field_id, 'FIELDS', array());
            }
          }
      }
      
  }
  
  public static function get_country_dial_codes(){
	return array("AF"=> "+93",
	"AL"=> "+355",
	"DZ"=> "+213",
	"AS"=> "+1",
	"AD"=> "+376",
	"AO"=> "+244",
	"AI"=> "+1",
	"AG"=> "+1",
	"AR"=> "+54",
	"AM"=> "+374",
	"AW"=> "+297",
	"AU"=> "+61",
	"AT"=> "+43",
	"AZ"=> "+994",
	"BH"=> "+973",
	"BD"=> "+880",
	"BB"=> "+1",
	"BY"=> "+375",
	"BE"=> "+32",
	"BZ"=> "+501",
	"BJ"=> "+229",
	"BM"=> "+1",
	"BT"=> "+975",
	"BO"=> "+591",
	"BA"=> "+387",
	"BW"=> "+267",
	"BR"=> "+55",
	"IO"=> "+246",
	"VG"=> "+1",
	"BN"=> "+673",
	"BG"=> "+359",
	"BF"=> "+226",
	"MM"=> "+95",
	"BI"=> "+257",
	"KH"=> "+855",
	"CM"=> "+237",
	"CA"=> "+1",
	"CV"=> "+238",
	"KY"=> "+1",
	"CF"=> "+236",
	"TD"=> "+235",
	"CL"=> "+56",
	"CN"=> "+86",
	"CO"=> "+57",
	"KM"=> "+269",
	"CK"=> "+682",
	"CR"=> "+506",
	"CI"=> "+225",
	"HR"=> "+385",
	"CU"=> "+53",
	"CY"=> "+357",
	"CZ"=> "+420",
	"CD"=> "+243",
	"DK"=> "+45",
	"DJ"=> "+253",
	"DM"=> "+1",
	"DO"=> "+1",
	"EC"=> "+593",
	"EG"=> "+20",
	"SV"=> "+503",
	"GQ"=> "+240",
	"ER"=> "+291",
	"EE"=> "+372",
	"ET"=> "+251",
	"FK"=> "+500",
	"FO"=> "+298",
	"FM"=> "+691",
	"FJ"=> "+679",
	"FI"=> "+358",
	"FR"=> "+33",
	"GF"=> "+594",
	"PF"=> "+689",
	"GA"=> "+241",
	"GE"=> "+995",
	"DE"=> "+49",
	"GH"=> "+233",
	"GI"=> "+350",
	"GR"=> "+30",
	"GL"=> "+299",
	"GD"=> "+1",
	"GP"=> "+590",
	"GU"=> "+1",
	"GT"=> "+502",
	"GN"=> "+224",
	"GW"=> "+245",
	"GY"=> "+592",
	"HT"=> "+509",
	"HN"=> "+504",
	"HK"=> "+852",
	"HU"=> "+36",
	"IS"=> "+354",
	"IN"=> "+91",
	"ID"=> "+62",
	"IR"=> "+98",
	"IQ"=> "+964",
	"IE"=> "+353",
	"IL"=> "+972",
	"IT"=> "+39",
	"JM"=> "+1",
	"JP"=> "+81",
	"JO"=> "+962",
	"KZ"=> "+7",
	"KE"=> "+254",
	"KI"=> "+686",
	"XK"=> "+381",
	"KW"=> "+965",
	"KG"=> "+996",
	"LA"=> "+856",
	"LV"=> "+371",
	"LB"=> "+961",
	"LS"=> "+266",
	"LR"=> "+231",
	"LY"=> "+218",
	"LI"=> "+423",
	"LT"=> "+370",
	"LU"=> "+352",
	"MO"=> "+853",
	"MK"=> "+389",
	"MG"=> "+261",
	"MW"=> "+265",
	"MY"=> "+60",
	"MV"=> "+960",
	"ML"=> "+223",
	"MT"=> "+356",
	"MH"=> "+692",
	"MQ"=> "+596",
	"MR"=> "+222",
	"MU"=> "+230",
	"YT"=> "+262",
	"MX"=> "+52",
	"MD"=> "+373",
	"MC"=> "+377",
	"MN"=> "+976",
	"ME"=> "+382",
	"MS"=> "+1",
	"MA"=> "+212",
	"MZ"=> "+258",
	"NA"=> "+264",
	"NR"=> "+674",
	"NP"=> "+977",
	"NL"=> "+31",
	"AN"=> "+599",
	"NC"=> "+687",
	"NZ"=> "+64",
	"NI"=> "+505",
	"NE"=> "+227",
	"NG"=> "+234",
	"NU"=> "+683",
	"NF"=> "+672",
	"KP"=> "+850",
	"MP"=> "+1",
	"NO"=> "+47",
	"OM"=> "+968",
	"PK"=> "+92",
	"PW"=> "+680",
	"PS"=> "+970",
	"PA"=> "+507",
	"PG"=> "+675",
	"PY"=> "+595",
	"PE"=> "+51",
	"PH"=> "+63",
	"PL"=> "+48",
	"PT"=> "+351",
	"PR"=> "+1",
	"QA"=> "+974",
	"CG"=> "+242",
	"RE"=> "+262",
	"RO"=> "+40",
	"RU"=> "+7",
	"RW"=> "+250",
	"BL"=> "+590",
	"SH"=> "+290",
	"KN"=> "+1",
	"MF"=> "+590",
	"PM"=> "+508",
	"VC"=> "+1",
	"WS"=> "+685",
	"SM"=> "+378",
	"ST"=> "+239",
	"SA"=> "+966",
	"SN"=> "+221",
	"RS"=> "+381",
	"SC"=> "+248",
	"SL"=> "+232",
	"SG"=> "+65",
	"SK"=> "+421",
	"SI"=> "+386",
	"SB"=> "+677",
	"SO"=> "+252",
	"ZA"=> "+27",
	"KR"=> "+82",
	"ES"=> "+34",
	"LK"=> "+94",
	"LC"=> "+1",
	"SD"=> "+249",
	"SR"=> "+597",
	"SZ"=> "+268",
	"SE"=> "+46",
	"CH"=> "+41",
	"SY"=> "+963",
	"TW"=> "+886",
	"TJ"=> "+992",
	"TZ"=> "+255",
	"TH"=> "+66",
	"BS"=> "+1",
	"GM"=> "+220",
	"TL"=> "+670",
	"TG"=> "+228",
	"TK"=> "+690",
	"TO"=> "+676",
	"TT"=> "+1",
	"TN"=> "+216",
	"TR"=> "+90",
	"TM"=> "+993",
	"TC"=> "+1",
	"TV"=> "+688",
	"UG"=> "+256",
	"UA"=> "+380",
	"AE"=> "+971",
	"GB"=> "+44",
	"US"=> "+1",
	"UY"=> "+598",
	"VI"=> "+1",
	"UZ"=> "+998",
	"VU"=> "+678",
	"VA"=> "+39",
	"VE"=> "+58",
	"VN"=> "+84",
	"WF"=> "+681",
	"YE"=> "+967",
	"ZM"=> "+260",
	"ZW"=> "+263");
    } 
    
    public static function get_country_code($country){
        $code='';
        preg_match("/\[[A-Z]{2}\]/", $country, $matches);
        if (!empty($matches)) {
            preg_match("/[A-Z]{2}/", $matches[0], $matches);
            if (!empty($matches)) {
                $code = strtolower($matches[0]);
            }
        }  
        return $code;
    }
    
    public static function suspend_user(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      //print_r($_REQUEST['user_id']);die;
      //$user= wp_get_current_user();
      // Get user ID which you want to suspend      
      $id= $_REQUEST['user_id'];       
      $user_model= new RM_User($id);
      $user_model->deactivate_user();
      echo 'success';die;
  }
  
  public static function deactivate_rm_user(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      $id= absint($_REQUEST['user_id']);
      $user_model= new RM_User;
      $result= $user_model->deactivate_user($id);
      echo ($result) ? 'success' : 'fail';
      die;
  }
  
  public static function activate_rm_user(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      $id= $_REQUEST['user_id'];
      $user_model= new RM_User;
      $result = $user_model->activate_user($id);
      echo ($result) ? 'success' : 'fail';
      die;
  }
  
  public static function reset_password(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
      // Login to reset password
      self::quick_email($_REQUEST['user_email'], __("RM LOGIN PASSWORD CHANGED",'custom-registration-form-builder-with-submission-manager'), __("Here is your updated password: ",'custom-registration-form-builder-with-submission-manager').$random_password, RM_EMAIL_GENERIC,array('do_not_save'=>true));
      wp_set_password($random_password,$_REQUEST['user_id']);
      echo 'success';
      die;
  }
  
  public static function block_ip(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      
       $ip = $_REQUEST['user_ip']; // IP of the user                   
       $gopt=new RM_Options;
       $blocked_ips=$gopt->get_value_of('banned_ip');
       if(empty($blocked_ips))
           $blocked_ips=array($ip);
       else
           array_push ($blocked_ips, $ip);
      
       $gopt->set_value_of('banned_ip',$blocked_ips);
      echo 'success';
      die;
  }
  
  public static function unblock_ip(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      
       $ip = $_REQUEST['user_ip']; // IP of the user                   
       $submission_model= new RM_Submissions();
       $submission_model->unblock_ip($ip);
       echo 'success';
      die;
  }
  
  public function send_email_to_user(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      $mail_subject = 'RM LOGIN EMAIL';
      if(trim($_REQUEST['user_subject'])!=''){
          $mail_subject = $_REQUEST['user_subject'];
      }
      self::quick_email($_REQUEST['user_email'], $mail_subject, $_REQUEST['user_message'], RM_EMAIL_GENERIC,array('do_not_save'=>true));
      echo 'success';
      die;
  }
  
  public function rm_delete_data(){
      if(!is_super_admin()){
          wp_die('Operation not allowed');
      }
      $delete_count = 0;
      // Deleting all the options 
      $option_model= new RM_Options();
      $options= $option_model->get_all_options();
      $all= array_merge($options,array('automation_intro_time'=>'','db_version'=>'','ex_chronos_db_version'=>'','last_update_time'=>'','rm_version'=>''));
      foreach($all as $key=>$value){
          delete_option('rm_option_'.$key);
      }
      $delete_count = $delete_count + RM_DBManager::reset_all_tables();
      echo 'success-'.$delete_count;
      die;
  }
    
    public static function filter_success_msg($msg, $form_id, $sub_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::filter_success_msg($msg, $form_id, $sub_id);
        }
    }
    
    public static function get_form_redirection_url($form) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::get_form_redirection_url($form);
        }
    }
    
    public static function login_user_by_id($user_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Utilities_Addon::login_user_by_id($user_id);
        }
    }
    
    public static function comparison_page_link(){
        //$mgp = get_option('mgp_a_id');
        $mgp = self::get_current_theme_aff_id();
        $url = 'https://registrationmagic.com/comparison/';
        if(!empty($mgp))
            $url = add_query_arg('mgp',$mgp,$url);
        
        return $url;
    }
    
    public static function get_current_theme_aff_id(){
        $current_theme = self::get_current_theme_name();
        $set_of_themes = self::get_theme_list_obj();
        if(!empty($set_of_themes) && property_exists($set_of_themes,$current_theme))
            return intval($set_of_themes->$current_theme);
        else
            return false;
    }
    
    public static function get_current_theme_name(){
        $theme_obj = wp_get_theme();
        return $theme_obj->__get('title');
    }
    
    public static function get_theme_list_obj(){
        $file_path = RM_BASE_DIR . 'libs/theme-list.json';
        $raw_json = file_get_contents($file_path);
        if($raw_json != false) {
            $raw_json = utf8_encode($raw_json);
            return json_decode($raw_json);
        } else {
            return false;
        }
    }
    
    public static function get_formatted_price($price) {
        $gopts = new RM_Options;
        $curr_symbol = $gopts->get_currency_symbol();
        $curr_pos = $gopts->get_value_of('currency_symbol_position');
        
        if($curr_pos === 'before') {
            return $curr_symbol . $price;
        } else {
            return $price . $curr_symbol;
        }
    }
}
