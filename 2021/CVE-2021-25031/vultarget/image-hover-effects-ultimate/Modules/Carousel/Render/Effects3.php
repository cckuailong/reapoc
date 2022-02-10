<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Carousel\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects3 extends Public_Render {

    public function public_jquery() {
        wp_enqueue_script('oxi-image-carousel-swiper.min.js', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/swiper.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'oxi-image-carousel-swiper.min.js';
    }

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-carousel-swiper.min.css', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/swiper.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-style-3', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/style-3.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function render() {
        $arrow = '';
        $style = $this->style;
        $prev = $this->font_awesome_render($style['carousel_left_arrow']);
        $next = $this->font_awesome_render($style['carousel_right_arrow']);

        if (array_key_exists('carousel_show_arrows', $style) && $style['carousel_show_arrows'] == 'yes') {
            $arrow = '
                <div class="swiper-button-next  oxi_carousel_arrows  oxi_carousel_next oxi_carousel_next_' . $this->oxiid . '">
                    ' . $next . '
                </div>
                <div class="swiper-button-prev oxi_carousel_arrows oxi_carousel_prev oxi_carousel_prev_' . $this->oxiid . '">
                    ' . $prev . '
                </div>
            ';
        }

        echo '<div class="oxi-addons-container ' . $this->WRAPPER . ' oxi-image-hover-wrapper-' . (array_key_exists('carousel_register_style', $this->style) ? $this->style['carousel_register_style'] : '') . '" id="' . $this->WRAPPER . '">
                <div class="oxi-addons-row swiper-container oxi-addons-swiper-wrapper">
                    <div class="swiper-wrapper">';
        $this->default_render($this->style, $this->child, 'request');

        echo '   
                    </div>';
        if ($style['carousel_show_dots'] == 'yes') :
            echo '<div class="swiper-pagination oxi_carousel_dots oxi_carousel_dots_' . $this->oxiid . '"></div>';
        endif;
        if ($style['carousel_show_arrows'] == 'yes') {
            echo $arrow;
        }
        echo '
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

        $lap = $style['carousel_item-lap-size'];
        $tab = $style['carousel_item-tab-size'];
        $mobile = $style['carousel_item-mob-size'];

        $effects = $style['carousel_effect'];
        $autoplay = ($style['carousel_autoplay'] == 'yes') ? $style['carousel_autoplay_speed'] : '99999';
        $speed = !empty($style['carousel_speed']) ? $style['carousel_speed'] : 500;
        $pause_on_hover = ($style['carousel_pause_on_hover'] == 'yes') ? 'true' : 'false';
        $infinite = ($style['carousel_infinite'] == 'yes') ? 'true' : 'false';
        $adaptiveheight = ($style['carousel_adaptive_height'] == 'yes') ? 'true' : 'false';
        $grab_cursor = ($style['carousel_grab_cursor'] == 'yes') ? 'true' : 'false';
        $rtl = $style['carousel_direction'];

        $centeredSlides = ($effects == 'coverflow') ? 'true' : 'false';
        if ($effects == "coverflow" || $effects == "slide") {
            $lap = $lap;
            $tab = $tab;
            $mobile = $mobile;
        } elseif ($effects == "cube") {
            $lap = 1;
            $tab = 1;
            $mobile = 1;
        } else {
            $lap = "auto";
            $tab = "auto";
            $mobile = "auto";
        }

        $jquery = '(function ($) {
            var oxi_swiper_slider = $(".' . $this->WRAPPER . ' .oxi-addons-row");
            oxi_swiper_slider.find(".oxi-image-hover-style").removeClass().addClass("oxi-image-hover-style swiper-slide");
                if("' . $rtl . '" == "rtl"){
                    $(oxi_swiper_slider).prop("dir", "rtl");
                }
                var oxiSwiperSlider = new Swiper(oxi_swiper_slider, {
                    direction: "horizontal",
                    speed: ' . $speed . ',
                    effect: "' . $effects . '",
                    centeredSlides: ' . $centeredSlides . ',
                    grabCursor: ' . $grab_cursor . ',
                    autoHeight: ' . $adaptiveheight . ',
                    loop: ' . $infinite . ',
                    observer: true,
                    observeParents: true,
                    cubeEffect: {
                        shadow: false,
                        slideShadows: false,
                        shadowOffset: 0,
                        shadowScale: 0,
                    },
                    autoplay: {
                        delay: ' . $autoplay . '
                    },
                    pagination: {
                        el: ".oxi_carousel_dots_' . $this->oxiid . '",
                        clickable: true
                    },
                    navigation: {
                        nextEl: ".oxi_carousel_next_' . $this->oxiid . '",
                        prevEl: ".oxi_carousel_prev_' . $this->oxiid . '"
                    },
                    breakpoints: {
                        960: {
                            slidesPerView: "' . $lap . '",
                        },
                        600 : {
                            slidesPerView: "' . $tab . '",
                        },
                        480: {
                            slidesPerView: "' . $mobile . '",
                        }
                    }
                });
                if (' . $autoplay . ' === 0) {
                    oxiSwiperSlider.autoplay.stop();
                }
                if (' . $pause_on_hover . ' == true) {
                    oxi_swiper_slider.on("mouseenter", function() {
                        oxiSwiperSlider.autoplay.stop();
                    });
                    oxi_swiper_slider.on("mouseleave", function() {
                        oxiSwiperSlider.autoplay.start();
                    });
                };
        })(jQuery);';
        wp_add_inline_script($this->JSHANDLE, $jquery);
    }

}
