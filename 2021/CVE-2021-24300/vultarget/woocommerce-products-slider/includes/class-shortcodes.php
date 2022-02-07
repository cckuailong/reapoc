<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

if( ! class_exists( 'class_wcps_shortcodes' ) ) {
    class class_wcps_shortcodes{


        public function __construct(){

            add_shortcode('wcps', array($this, 'wcps_new_display'));
            add_shortcode('wcps_import', array($this, 'wcps_import'));

        }


        public function wcps_import($atts, $content = null )
        {
            $atts = shortcode_atts(
                array(
                    'id' => "",
                ), $atts
            );


            $file = wcps_plugin_url.'sample-data/wcps-layouts.xml';


            $html_obj = simplexml_load_string(file_get_contents($file));
            $channel = isset($html_obj->channel)? $html_obj->channel : array();
            $items = isset($channel->item)? $channel->item : array();

            //echo '<pre>'.var_export($channel->item, true).'</pre>';

            $item_count = 0;
            foreach ($items as $item):

                if($item_count > 1) return;

                $item_title = isset($item->title) ? (string)$item->title : '';

                $item_link = isset($item->link) ? (string)$item->link : '';
                $item_guid = isset($item->guid) ? (string)$item->guid : '';
                $item_description = isset($item->description) ? (string)$item->description : '';

                echo '<pre>'.var_export($item, true).'</pre>';



                $item_count++;
            endforeach;



        }

        public function wcps_new_display($atts, $content = null ) {
            $atts = shortcode_atts(
                array(
                    'id' => "",
                ), $atts
            );

            $html = '';
            $wcps_id = isset($atts['id']) ? $atts['id'] : '';

            $args = array('wcps_id'=> $wcps_id);

            ob_start();
            ?>
            <div id="wcps-container-<?php echo $wcps_id; ?>" class="wcps-container wcps-container-<?php echo $wcps_id; ?>">
                <?php
                do_action('wcps_slider_main', $args);
                ?>
            </div>
            <?php
            return ob_get_clean();
        }



    }
}
new class_wcps_shortcodes();