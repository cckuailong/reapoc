<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC envato class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_envato extends MEC_base
{
    /**
     * The plugin current version
     */
    public $current_version = MEC_VERSION;

    /**
     * The plugin ite, id
     */
    public $itemid = '17731780';

    /**
     * The plugin url
     */
    public $itemurl = '';

    /**
     * User for cashing directory
     */
    public $purchase_code = '';

    /**
     * Product name
     */
    public $product_name = '';
    
    /**
     * The plugin remote update path
     */
    public $update_path = '';

    /**
     * Plugin Slug
     */
    public $plugin_slug = MEC_BASENAME;

    public $options = '';
    public $license_status = '';

    /**
     * Plugin name
     */
    public $slug;

    public $main;
    public $factory;
    public $settings;

    /**
     *  MEC update constructor
     */
    public function __construct()
    {
        // Import MEC Main
        $this->main = $this->getMain();

        // Import MEC Factory
        $this->factory = $this->getFactory();

        // MEC Settings
        $this->options = get_option('mec_options');
        $this->license_status = get_option( 'mec_license_status');
        
        // Set user purchase code
        $this->set_purchase_code(isset($this->options['purchase_code']) ? $this->options['purchase_code'] : '');
        $this->set_product_name(isset($this->options['product_name']) ? $this->options['product_name'] : '');

        // Plugin Slug
        list($slice1, $slice2) = explode('/', $this->plugin_slug);
        $this->slug = str_replace('.php', '', $slice2);        
    }

    /**
     * Get API URL.
     * @author Webnus <info@webnus.biz>
     * @return string $api_url
     */
    public static function get_api_url()
    {
        if(ini_get('allow_url_fopen'))
        {
            if(get_headers('https://webnus.biz')[0] != 'HTTP/1.1 200 OK') $api_url = 'https://webnus.net/api';
            else $api_url = 'http://webnus.biz/webnus.net';
        } 
        else 
        {
            $api_url = 'http://webnus.biz/webnus.net';
        } 

        return $api_url;
    }

    /**
     * Set purchase code.
     * @author Webnus <info@webnus.biz>
     * @param string $purchase_code
     */
    public function set_purchase_code($purchase_code)
    {
        $this->purchase_code = $purchase_code;
    }

    /**
     * Set product_name.
     * @author Webnus <info@webnus.biz>
     * @param string $product_name
     */
    public function set_product_name($product_name)
    {
        $this->product_name = $product_name;
    }

    /**
     * Set update path.
     * @author Webnus <info@webnus.biz>
     * @param string $update_path
     */
    public function set_update_path($update_path)
    {
        $this->update_path = $update_path;
    }

    /**
     * GET purchase code.
     * @author Webnus <info@webnus.biz>
     */
    public function get_purchase_code()
    {
        return $this->purchase_code;
    }

    /**
     * GET product_name.
     * @author Webnus <info@webnus.biz>
     */
    public function get_product_name()
    {
        return $this->product_name;
    }

    /**
     * Get update path.
     * @author Webnus <info@webnus.biz>
     */
    public function get_update_path()
    {
        return $this->update_path;
    }

    /**
     * Initialize the auto update class
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // updating checking
        $this->factory->filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));

        // information checking
        $this->factory->filter('plugins_api', array($this, 'check_info'), 10, 3);
    }

    /**
     * Add our self-hosted autoupdate plugin to the filter transien
     * @author Webnus <info@webnus.biz>
     * @param object $transient
     * @return object
     */
    public function check_update($transient)
    {
        if(empty($transient->checked)) return $transient;

        // Get the remote version
        $version = (isset($this->get_MEC_info('version')->version) and !empty($this->get_MEC_info('version')->version)) ? json_decode(json_encode($this->get_MEC_info('version')->version), true) : get_option('mec_save_version_number');

        // Set mec update path
        $dl_link = (!empty($this->options['purchase_code']) && $this->license_status == 'active') ? $this->set_update_path($this->get_MEC_info('dl')) : NULL;

        // If a newer version is available, add the update
        if(version_compare($this->current_version, $version, '<'))
        {
            $obj = new stdClass();
            $obj->id = $this->itemid;
            $obj->slug = $this->slug;
            $obj->plugin = $this->plugin_slug;
            $obj->requires = '4.0';
            $obj->tested = '4.9';
            $obj->new_version = $version;
            $obj->url = $this->itemurl;
            $obj->package = $this->get_update_path();
            $obj->upgrade_notice = '';
            $obj->icons = array(
		        '1x' => 'https://ps.w.org/modern-events-calendar-lite/assets/icon-128x128.png',
		        '2x' => 'https://ps.w.org/modern-events-calendar-lite/assets/icon-128x128.png'
            );
            $obj->sections = array
            (
                'description' => 'Modern Events Calendar - Responsive Event Scheduler & Booking For WordPress',
                'changelog' => 'Modern Events Calendar - Responsive Event Scheduler & Booking For WordPress'
            );
            
            $transient->response[$this->plugin_slug] = $obj;
        }
        elseif(isset($transient->response[$this->plugin_slug]))
        {
            unset($transient->response[$this->plugin_slug]);
        }

        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     * @author Webnus <info@webnus.biz>
     */
    public function check_info($false, $action, $arg)
    {
        $dl_link = !is_null($this->get_MEC_info('dl')) ? $this->set_update_path($this->get_MEC_info('dl')) : NULL;
        $version = (isset($this->get_MEC_info('version')->version) and !empty($this->get_MEC_info('version')->version)) ? json_decode(json_encode($this->get_MEC_info('version')->version), true) : get_option('mec_save_version_number');
        $data_url = 'https://webnus.net/modern-events-calendar/addons-api/addons-api.json';
        
        if(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
        {
            $get_data = file_get_contents($data_url);
            if($get_data !== false AND !empty($get_data))
            {
                $obj = json_decode($get_data);
            }
        }
        elseif(function_exists('curl_version'))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $data_url);

            $result = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($result);
        }
        else $obj = '';

        $addons = '';
        if(!empty($obj))
        {
            $addons .= '<div class="mec-details-addons-container">';
            foreach($obj as $key => $value)
            {
                $addons .= '
                <div class="mec-details-addons-wrap">
                    <a href="https://webnus.net/modern-events-calendar/addons/" target="_blank"><img alt="'.esc_attr($value->name).'" src="'.$value->img.'" /></a>
                    <div class="mec-details-addons-title"><a href="https://webnus.net/modern-events-calendar/addons/" target="_blank"><span>'. esc_html__($value->name) .'</span></a></div>
                    <p>'. esc_html__($value->desc) .'</p>
                </div>';
            }

            $addons .= '</div>';
        }

        if(isset($arg->slug) and $arg->slug === $this->slug)
        {
            $information = $this->getRemote_information();
            $information = json_decode($information);
            
            $information->name = 'Modern Events Calendar';
            $information->slug = 'modern-events-calendar';
            $information->plugin_name = 'Modern Events Calendar';
            $information->version = $version;
            $information->download_link  = $this->get_update_path();
            $information->banners['low'] = 'https://ps.w.org/modern-events-calendar-lite/assets/banner-772x250.png?rev=1912767';
            $information->tested = '5.2.2';
            $information->active_installs = '100000';
            $information->upgrade_notice = '';
            $information->sections = (array) $information->sections;

            unset($information->sections['installation']);
            unset($information->sections['faq']);
            unset($information->sections['screenshots']);
            
            $information->sections['addons'] = $addons;
            return $information;
        }
        
        return false;
    }

    /**
	 * Get information about the remote version
	 * @return bool|object
	 */
	public function getRemote_information()
	{
		$request = wp_remote_post('https://api.wordpress.org/plugins/info/1.0/modern-events-calendar-lite.json', array( 'timeout' => 30 ));
		if(!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200)
		{
			return $request['body'];
		}

		return false;
    }
    
    /* Save version to database */
    public function mec_version_in_database() {
        $mec_save_version_date = get_option('mec_save_version_date');
        if (!$mec_save_version_date) {
            $JSON = wp_remote_retrieve_body(wp_remote_get(self::get_api_url() . '/plugin-api/version', array(
                'body' => null,
                'timeout' => '120',
                'redirection' => '10',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
            )));
            $JSON = json_decode($JSON);
            update_option('mec_save_version_number', $JSON->version);
            update_option('mec_save_version_date', date("Y-m-d"));
            return true;
        } else {
            if ( strtotime(date("Y-m-d")) > strtotime($mec_save_version_date) ) {
                $JSON = wp_remote_retrieve_body(wp_remote_get(self::get_api_url() . '/plugin-api/version', array(
                    'body' => null,
                    'timeout' => '120',
                    'redirection' => '10',
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
                )));
                $JSON = json_decode($JSON);
                update_option('mec_save_version_number', $JSON->version);
                update_option('mec_save_version_date', date("Y-m-d"));
                return true;
            }
            return false;
        }
    }

    /**
     * Return details from envato
     * @author Webnus <info@webnus.biz>
     * @param string $type
     * @return mixed
     */
    public function get_MEC_info($type = 'dl')
    {
        // setting the header for the rest of the api
        $code = $this->get_purchase_code();
        $product_name = $this->get_product_name();
        $url = get_home_url();

        if($type == 'remove') {
            $verify_url = 'https://webnus.net/api/remove?id='.$code;
        } elseif($type == 'dl') {
            $verify_url = self::get_api_url() . '/plugin-api/verify?item_name=' . urlencode($product_name) . '&id=' . $code . '&url=' . $url;
        } elseif($type == 'version') {
            if ( $this->mec_version_in_database() ) {
                $verify_url = self::get_api_url() . '/plugin-api/version';
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }

        $JSON = wp_remote_retrieve_body(wp_remote_get($verify_url, array(
            'body' => null,
            'timeout' => '120',
            'redirection' => '10',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
        )));
        
        if($JSON != '') return json_decode($JSON);
        else return false;
    }
}