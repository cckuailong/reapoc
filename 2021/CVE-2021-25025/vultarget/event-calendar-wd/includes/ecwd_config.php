<?php

class ECWD_Config {

    protected static $instance = null;
    private $file_name = 'ecwd_config.json';
    private $file_dir = '';
    private $response = array();
    private $config = array();
    private $show_config_submenu = false;
    public $is_writable = false;

    private function __construct() {
        $this->set_show_config_submenu();
        $this->set_file_path();        
        $this->get_file_data();
    }

    private function get_file_data() {
        if ($this->is_writable) {
            $file_content = file_get_contents($this->file_dir);
            if ($file_content != '') {
                $this->config = json_decode($file_content, true);
            } else {
                $this->set_default_configs();
                file_put_contents($this->file_dir, json_encode($this->config));
            }
        } else {
            $this->set_default_configs();
            $this->add_response('Writable chi ' . $this->file_dir);
        }
        $this->set_data_to_global();
    }

    private function set_default_configs() {
        $file_content = file_get_contents(ECWD_DIR . '/config/config.json');
        $this->config = json_decode($file_content, true);
        $this->import_from_settings();
    }

    private function add_response($response) {
        $this->response[] = $response;
    }

    private function set_file_path() {
        $upload_dir = wp_upload_dir();
        $dir = $upload_dir['basedir'] . '/wd_configs/';
        $this->file_dir = $dir . $this->file_name;
        if (!is_dir($dir)) {
		mkdir($dir, 0777,true);
		chmod($dir, 0777);  
		$fp = fopen($this->file_dir, 'w');

        }
	$this->is_writable = is_writable($dir);
    }

    private function set_show_config_submenu() {
        if (isset($_GET['ecwd_config'])) {
            if ($_GET['ecwd_config'] == 'on') {
                add_option('ecwd_config', 'on');
            } else if ($_GET['ecwd_config'] == 'off') {
                delete_option('ecwd_config');
                header('Location: '.ECWD_MENU_SLUG);
                die;
            }
        }
        $option = get_option('ecwd_config');
        if ($option !== false && $option == 'on') {
            $this->show_config_submenu = true;
        }
    }

    private function set_data_to_global() {
        global $ecwd_config;
        $ecwd_config = $this->config;
        $ecwd_config['show_config_submenu'] = $this->show_config_submenu;
    }

    public function update_conf_file() {
        $default_conf = file_get_contents(ECWD_DIR . '/config/config.json');
        $default_conf = json_decode($default_conf, true);
        if (count($default_conf) !== count($this->config) || true) {
            foreach ($default_conf as $id => $value) {
                if (!isset($this->config[$id])) {
                    $this->config[$id] = $value;
                }
            }
            $this->import_from_settings();
            file_put_contents($this->file_dir, json_encode($this->config));
        }
        $this->set_data_to_global();
    }

    private function import_from_settings() {
        $opt = get_option('ecwd_settings_general');
        if ($opt == false || !isset($opt['featured_image_for_themes'])) {
            return;
        }
        $this->config['featured_image_for_themes']['value'] = $opt['featured_image_for_themes'];
        if ($this->is_writable) {
            unset($opt['featured_image_for_themes']);
            update_option('ecwd_settings_general', $opt);
        }
    }

    public function save_new_config($new_config) {
        if ($this->is_writable) {
            foreach ($new_config as $id => $value) {
                $this->config[$id]['value'] = $value;
            }
            file_put_contents($this->file_dir, json_encode($this->config));
        }
    }

    public function get_config() {
        return $this->config;
    }

    public function get_response() {
        return $this->response;
    }

    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}
