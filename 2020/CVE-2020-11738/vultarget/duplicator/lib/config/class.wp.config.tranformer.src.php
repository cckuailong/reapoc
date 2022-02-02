<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!class_exists('WPConfigTransformer')) {
    require_once(dirname(__FILE__).'/class.wp.config.tranformer.php');
}

if (!class_exists('WPConfigTransformerSrc')):

    /**
     * Transforms a wp-config.php file.
     */
    class WPConfigTransformerSrc extends WPConfigTransformer
    {

        /**
         * Instantiates the class with a valid wp-config.php scr text
         *
         * @param string $wp_config_path Path to a wp-config.php file.
         */
        public function __construct($wp_config_src)
        {
            // Normalize the newline to prevent an issue coming from OSX
            $this->wp_config_src = str_replace(array("\n\r", "\r"), array("\n", "\n"), $wp_config_src);
        }

        public function getSrc()
        {
            return $this->wp_config_src;
        }

        /**
         * Checks if a config exists in the wp-config.php src
         *
         * @throws Exception If the wp-config.php file is empty.
         * @throws Exception If the requested config type is invalid.
         *
         * @param string $type Config type (constant or variable).
         * @param string $name Config name.
         *
         * @return bool
         */
        public function exists($type, $name)
        {
            $this->wp_configs = $this->parse_wp_config($this->wp_config_src);

            if (!isset($this->wp_configs[$type])) {
                throw new Exception("Config type '{$type}' does not exist.");
            }

            return isset($this->wp_configs[$type][$name]);
        }

        /**
         * Get the value of a config in the wp-config.php src
         *
         * @param string $type Config type (constant or variable).
         * @param string $name Config name.
         *
         * @return array
         */
        public function get_value($type, $name, $get_real_value = true)
        {
            $this->wp_configs = $this->parse_wp_config($this->wp_config_src);

            if (!isset($this->wp_configs[$type])) {
                throw new Exception("Config type '{$type}' does not exist.");
            }

            // Duplicator Extra
            $val = $this->wp_configs[$type][$name]['value'];
            if ($get_real_value) {
                return self::getRealValFromVal($val);
            } else {
                return $val;
            }
        }

        /**
         * update wp_config_src
         *
         * @param string $contents
         * @return boolean
         */
        protected function save($contents)
        {
            $this->wp_config_src = $contents;
            return true;
        }
    }
    
endif;
