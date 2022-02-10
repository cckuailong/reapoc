<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Carousel\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects2 extends Public_Render {

    public function public_jquery() {
        wp_enqueue_script('oxi-image-carousel-flipster.min.js', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/jquery.flipster.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'oxi-image-carousel-flipster.min.js';
    }

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-carousel-flipster.min.css', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/jquery.flipster.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-style-2', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/style-2.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function render() {
        echo '<div class="oxi-addons-container ' . $this->WRAPPER . ' oxi-image-hover-wrapper-' . (array_key_exists('carousel_register_style', $this->style) ? $this->style['carousel_register_style'] : '') . '" id="' . $this->WRAPPER . '">
                 <div class="oxi-addons-row">
                    <ul class="flip-items oxi-addons-col-edit">';
        $this->default_render($this->style, $this->child, $this->admin);
        echo '   
                    </ul>
                </div>
              </div>';
    }

    public function public_column_render($col) {
        $column = 1;
        if (count(explode('-lg-', $col)) == 2) :
            $column = explode('-lg-', $col)[1];
        elseif (count(explode('-md-', $col)) == 2) :
            $column = explode('-md-', $col)[1];
        elseif (count(explode('-sm-', $col)) == 2) :
            $column = explode('-sm-', $col)[1];
        endif;
        if ($column == 12) :
            return 1;
        elseif ($column == 6) :
            return 2;
        elseif ($column == 4) :
            return 3;
        elseif ($column == 3) :
            return 4;
        elseif ($column == 2) :
            return 6;
        else :
            return 12;
        endif;
    }

    public function default_render($style, $child, $admin) {
        if (!array_key_exists('carousel_register_style', $style) && $style['carousel_register_style'] < 1) :
            echo '<p>Kindly Select Image Effects Frist to Extend Carousel.</p>';
            return;
        endif;
        $styledata = $this->wpdb->get_row($this->wpdb->prepare('SELECT * FROM ' . $this->parent_table . ' WHERE id = %d ', $style['carousel_register_style']), ARRAY_A);

        if (!is_array($styledata)) :
            echo '<p> Style Data not found. Kindly Check Carousel & Slider <a href="https://www.oxilabdemos.com/image-hover/docs/hover-extension/carousel-slider/">Documentation</a>.</p>';
            return;
        endif;
        $files = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM $this->child_table WHERE styleid = %d", $style['carousel_register_style']), ARRAY_A);
        $StyleName = explode('-', ucfirst($styledata['style_name']));
        $cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . $StyleName[0] . '\Render\Effects' . $StyleName[1];
        new $cls($styledata, $files, 'request');


        $col = json_decode(stripslashes($styledata['rawdata']), true);
        $lap = $this->public_column_render($col['oxi-image-hover-col-lap']);
        $tab = $this->public_column_render($col['oxi-image-hover-col-tab']);
        $mobile = $this->public_column_render($col['oxi-image-hover-col-mob']);

        $prev = $this->font_awesome_render($style['carousel_left_arrow']);
        $next = $this->font_awesome_render($style['carousel_right_arrow']);
        $start = '';

        $effect = $style['carousel_effect'];
        $autoplayspeed = !empty($style['carousel_autoplay_speed']) ? $style['carousel_autoplay_speed'] : 2000;
        $autoplay = ($style['carousel_autoplay'] == 'yes') ? $autoplayspeed : 'false';
        $fadein = $style['carousel_fadeIn'];
        if ($style['carousel_center_mode'] == 'yes') :
            $start = 'center';
        else :
            $start = $style['carousel_start_number'];
        endif;
        $pause_on_hover = ($style['carousel_pause_on_hover'] == 'yes') ? 'true' : 'false';
        $infinite = ($style['carousel_infinite'] == 'yes') ? 'true' : 'false';
        $touch = ($style['carousel_touch'] == 'yes') ? 'true' : 'false';
        $click = ($style['carousel_click'] == 'yes') ? 'true' : 'false';
        $arrows = ($style['carousel_show_arrows'] == 'yes') ? 'custom' : '';

        $jquery = '(function ($) {
            var flipContainer = $(".' . $this->WRAPPER . ' .oxi-addons-row"),
            flipItem = flipContainer.find(".oxi-image-hover-style");
            $(flipItem).each(function() { 
                var NewElement = $("<li />");
                $.each(this.attributes, function(i, attrib){
                    $(NewElement).attr(attrib.name, attrib.value);
                });
                $(this).replaceWith(function () {
                    return $(NewElement).append($(this).contents());
                });
            
            });
            $(flipContainer).flipster({
                style: "' . $effect . '",
                start: "' . $start . '",
                fadeIn: ' . $fadein . ',
                loop: ' . $infinite . ',
                autoplay: ' . $autoplay . ',
                pauseOnHover: ' . $pause_on_hover . ',
                spacing: ' . (isset($style['carousel_flipster_spacing']) ? $style['carousel_flipster_spacing'] : 0) . ',
                click: ' . $click . ',
                scrollwheel: false,
                tocuh: ' . $touch . ',
                nav: false,
                buttons: "' . $arrows . '",
                buttonPrev: \'<div class="oxi_carousel_arrows oxi_carousel_prev">' . $prev . '</div>\',
                buttonNext: \'<div class="oxi_carousel_arrows oxi_carousel_next">' . $next . '</div>\'
            });
        })(jQuery);';
        wp_add_inline_script($this->JSHANDLE, $jquery);
    }

}
