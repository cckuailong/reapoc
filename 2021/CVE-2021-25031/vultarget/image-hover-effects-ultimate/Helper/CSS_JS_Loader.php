<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * @author $biplob018
 */
trait CSS_JS_Loader {

    public function admin_css_loader() {
        $this->admin_css();
        $this->admin_js();
    }

    public function admin_css() {
        $this->loader_font_familly_validation(['Bree+Serif', 'Source+Sans+Pro']);
        wp_enqueue_style('oxilab-image-hover-bootstrap', OXI_IMAGE_HOVER_URL . 'assets/backend/css/bootstrap.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('font-awsome.min', OXI_IMAGE_HOVER_URL . 'assets/frontend/css/font-awsome.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxilab-admin-css', OXI_IMAGE_HOVER_URL . '/assets/backend/css/admin.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function admin_js() {
        wp_enqueue_script("jquery");
        wp_enqueue_script('oxilab-popper', OXI_IMAGE_HOVER_URL . '/assets/backend/js/popper.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('oxilab-bootstrap', OXI_IMAGE_HOVER_URL . '/assets/backend/js/bootstrap.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_localize_script('oxilab-bootstrap', 'ImageHoverUltimate', array(
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    public function admin_home() {
        wp_enqueue_script("jquery");
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery.dataTables.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/jquery.dataTables.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('dataTables.bootstrap.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/dataTables.bootstrap.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function admin_elements_frontend_loader() {
        $this->admin_css_loader();
        wp_enqueue_script("jquery");
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_style('jquery.coloring-pick.min.js', OXI_IMAGE_HOVER_URL . '/assets/backend/css/jquery.coloring-pick.min.js.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('jquery.coloring-pick.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/jquery.coloring-pick.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('jquery.minicolors', OXI_IMAGE_HOVER_URL . '/assets/backend/css/minicolors.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('jquery.minicolors', OXI_IMAGE_HOVER_URL . '/assets/backend/js/minicolors.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('nouislider', OXI_IMAGE_HOVER_URL . '/assets/backend/css/nouislider.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('nouislider', OXI_IMAGE_HOVER_URL . '/assets/backend/js/nouislider.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('fontawesome-iconpicker', OXI_IMAGE_HOVER_URL . '/assets/backend/css/fontawesome-iconpicker.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('fontawesome-iconpicker', OXI_IMAGE_HOVER_URL . '/assets/backend/js/fontawesome-iconpicker.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('jquery.coloring-pick.min.js', OXI_IMAGE_HOVER_URL . '/assets/backend/css/jquery.coloring-pick.min.js.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('jquery.coloring-pick.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/jquery.coloring-pick.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('jquery.conditionize2.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/jquery.conditionize2.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('select2.min', OXI_IMAGE_HOVER_URL . '/assets/backend/css/select2.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('select2.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/select2.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('jquery.serializejson.min', OXI_IMAGE_HOVER_URL . '/assets/backend/js/jquery.serializejson.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('jquery.fontselect', OXI_IMAGE_HOVER_URL . '/assets/backend/css/jquery.fontselect.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('oxi-image-hover-addons-vendor', OXI_IMAGE_HOVER_URL . '/assets/backend/js/vendor.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->admin_media_scripts();
    }

    /**
     * Admin Media Scripts.
     * Most of time using into Style Editing Page
     *
     * @since 9.3.0
     */
    public function admin_media_scripts() {
        wp_enqueue_media();
        wp_register_script('oxi-image-hover_media_scripts', OXI_IMAGE_HOVER_URL . '/assets/backend/js/media-uploader.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('oxi-image-hover_media_scripts');
    }

    public function str_replace_first($from, $to, $content) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $content, 1);
    }

    public function loader_font_familly_validation($data = []) {
        foreach ($data as $value) {
            wp_enqueue_style('' . $value . '', 'https://fonts.googleapis.com/css?family=' . $value . '');
        }
    }

}
