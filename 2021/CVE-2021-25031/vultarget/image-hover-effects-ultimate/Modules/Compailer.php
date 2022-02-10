<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Compailer
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;
use OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic\Post_Query as Post_Query;
use OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic\Layouts_Query as Layouts_Query;

class Compailer extends Public_Render {

    public function public_jquery() {
        if (is_array($this->style) && array_key_exists('image_hover_dynamic_content', $this->style) && $this->style['image_hover_dynamic_content'] == 'yes') :
            $this->dynamicPost = true;
        endif;
        if (is_array($this->style) && array_key_exists('image_hover_dynamic_load', $this->style) && $this->style['image_hover_dynamic_load'] == 'yes') :
            $this->dynamicLoad = true;
        endif;
        if (is_array($this->style) && array_key_exists('image_hover_dynamic_carousel', $this->style) && $this->style['image_hover_dynamic_carousel'] == 'yes') :
            $this->dynamicCarousel = true;
        endif;

        if ($this->dynamicLoad):
            wp_enqueue_script('oxi_image_dynamic_loader', OXI_IMAGE_HOVER_URL . '/Modules/Dynamic/Files/dynamic-loader.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
            $this->JSHANDLE = 'oxi_image_dynamic_loader';
            wp_localize_script('oxi_image_dynamic_loader', 'oxi_image_dynamic_loader', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('image_hover_ultimate')));

        elseif ($this->dynamicCarousel):
            wp_enqueue_script('oxi-image-carousel-slick.min', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/slick.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
            $this->JSHANDLE = 'oxi-image-carousel-slick.min';
        endif;
    }

    public function public_css() {

        if ($this->dynamicLoad):
            wp_enqueue_style('oxi-image-dynamic-loader', OXI_IMAGE_HOVER_URL . '/Modules/Dynamic/Files/dynamic-loader.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        elseif ($this->dynamicCarousel):
            wp_enqueue_style('oxi-image-hover-carousel-slick', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/slick.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
            wp_enqueue_style('oxi-image-hover-style-1', OXI_IMAGE_HOVER_URL . '/Modules/Carousel/Files/style-1.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        endif;
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

    public function render() {

        echo '<div class="oxi-addons-container noLightbox ' . $this->WRAPPER . ' ' . $this->WRAPPER . '" id="' . $this->WRAPPER . '">
                 <div class="oxi-addons-row">';

        $this->default_render($this->style, $this->child, $this->admin);
        echo '   </div>
             </div>';
    }

    public function default_render($style, $child, $admin) {
        if ($this->dynamicPost == true):
            $args = [
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'post_type' => $style['image_hover_dynamic_content_type'],
                'orderby' => $style['image_hover_dynamic_content_orderby'],
                'order' => $style['image_hover_dynamic_content_ordertype'],
                'posts_per_page' => $style['image_hover_dynamic_load_per_page'],
                'offset' => $style['image_hover_dynamic_content_offset'],
                'tax_query' => [],
            ];
            if (!empty($style['image_hover_dynamic_content_author'])):
                $args['author__in'] = $style['image_hover_dynamic_content_author'];
            endif;

            $type = $style['image_hover_dynamic_content_type'];

            if (!empty($style[$type . '_exclude'])) {
                $args['post__not_in'] = $style[$type . '_exclude'];
            }
            if (!empty($style[$type . '_include'])) {
                $args['post__in'] = $style[$type . '_include'];
            }
            if ($type != 'page') :
                if (!empty($style[$type . '_category'])) :
                    $args['tax_query'][] = [
                        'taxonomy' => $type == 'post' ? 'category' : $type . '_category',
                        'field' => 'term_id',
                        'terms' => $style[$type . '_category'],
                    ];
                endif;
                if (!empty($style[$type . '_tag'])) :
                    $args['tax_query'][] = [
                        'taxonomy' => $type . '_tag',
                        'field' => 'term_id',
                        'terms' => $style[$type . '_tag'],
                    ];
                endif;
                if (!empty($args['tax_query'])) :
                    $args['tax_query']['relation'] = 'OR';
                endif;
            endif;
            $settings = [
                'display_post_id' => $this->oxiid,
                'display_post_thumb_sizes' => $style['image_hover_dynamic_content_thumb_sizes'],
                'display_post_excerpt' => (int) $style['image_hover_dynamic_post_excerpt'] ? $style['image_hover_dynamic_post_excerpt'] : 15,
            ];
            ob_start();
            new Post_Query('post_query', $this->dbdata, $args, $settings);
            $oh = ob_get_clean();
            echo str_replace('Image Hover Empty Data', '', $oh);
            if ($this->dynamicLoad):
                if ($style['image_hover_dynamic_load_type'] == 'button'):
                    echo '  <div class="oxi-image-hover-load-more-button-wrap oxi-bt-col-sm-12">
                            <button class="oxi-image-load-more-button" data-class="OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic\Post_Query" data-function="__rest_api_post" data-args=\'' . json_encode($args) . '\' data-settings=\'' . json_encode($settings) . '\' data-page="1">
                                    <div class="oxi-image-hover-loader button__loader"></div>
                                    <span>' . $style['image_hover_dynamic_load_button_text'] . '</span>
                            </button>
                        </div>';
                else:
                    echo '<div class="oxi-image-hover-dynamic-load-infinite" id="oxi-image-hover-dynamic-load-infinite-' . $this->dbdata['id'] . '" data-class="OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic\Post_Query" data-function="__rest_api_post" data-args=\'' . json_encode($args) . '\' data-settings=\'' . json_encode($settings) . '\' data-page="1">
                          </div>';
                endif;
            endif;
        else:
            $args = [
                'posts_per_page' => isset($style['image_hover_dynamic_load_per_page']) ? $style['image_hover_dynamic_load_per_page'] : 10,
                'offset' => 0,
            ];
            $settings = [
                'display_post_id' => $this->oxiid,
            ];

            ob_start();
            new Layouts_Query('layouts_query', $this->dbdata, $args, $settings);
            $oh = ob_get_clean();
            echo str_replace('Image Hover Empty Data', '', $oh);

            if ($this->dynamicLoad):
                if ($style['image_hover_dynamic_load_type'] == 'button'):
                    echo '  <div class="oxi-image-hover-load-more-button-wrap oxi-bt-col-sm-12">
                            <button class="oxi-image-load-more-button" data-class="OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic\Layouts_Query" data-function="__rest_api_post" data-args=\'' . json_encode($args) . '\' data-settings=\'' . json_encode($settings) . '\' data-page="1">
                                    <div class="oxi-image-hover-loader button__loader"></div>
                                    <span>' . $style['image_hover_dynamic_load_button_text'] . '</span>
                            </button>
                        </div>';
                else:
                    echo '<div class="oxi-image-hover-dynamic-load-infinite" id="oxi-image-hover-dynamic-load-infinite-' . $this->dbdata['id'] . '" data-class="OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic\Layouts_Query" data-function="__rest_api_post" data-args=\'' . json_encode($args) . '\' data-settings=\'' . json_encode($settings) . '\' data-page="1">
                      </div>';
                endif;
            endif;
        endif;

        if ($this->dynamicCarousel == true):
            $lap = $this->public_column_render($style['oxi-image-hover-col-lap']);
            $tab = $this->public_column_render($style['oxi-image-hover-col-tab']);
            $mobile = $this->public_column_render($style['oxi-image-hover-col-mob']);

            $lap_item = $style['carousel_item_slide-lap-size'];
            $tab_item = $style['carousel_item_slide-tab-size'];
            $mobile_item = $style['carousel_item_slide-mob-size'];

            $prev = $this->font_awesome_render($style['carousel_left_arrow']);
            $next = $this->font_awesome_render($style['carousel_right_arrow']);

            $autoplay = ($style['carousel_autoplay'] == 'yes') ? 'true' : 'false';
            $autoplayspeed = $style['carousel_autoplay_speed'];
            $speed = $style['carousel_speed'];
            $pause_on_hover = ($style['carousel_pause_on_hover'] == 'yes') ? 'true' : 'false';
            $infinite = ($style['carousel_infinite'] == 'yes') ? 'true' : 'false';
            $adaptiveheight = ($style['carousel_adaptive_height'] == 'yes') ? 'true' : 'false';
            $center_mode = ($style['carousel_center_mode'] == 'yes') ? 'true' : 'false';

            $arrows = ($style['carousel_show_arrows'] == 'yes') ? 'true' : 'false';
            $dots = ($style['carousel_show_dots'] == 'yes') ? 'true' : 'false';

            $jquery = '(function ($) {
            $(".' . $this->WRAPPER . ' .oxi-addons-row").slick({
                fade: false,
                autoplay: ' . $autoplay . ',
                autoplaySpeed: ' . $autoplayspeed . ',
                speed: ' . $speed . ',
                infinite: ' . $infinite . ',
                pauseOnHover: ' . $pause_on_hover . ',
                adaptiveHeight: ' . $adaptiveheight . ',
                arrows: ' . $arrows . ',
                prevArrow: \'<div class="oxi_carousel_arrows oxi_carousel_prev">' . $prev . '</div>\',
                nextArrow: \'<div class="oxi_carousel_arrows oxi_carousel_next">' . $next . '</div>\',
                dots: ' . $dots . ',
                dotsClass: "oxi_carousel_dots",
                slidesToShow: ' . $lap . ',
                slidesToScroll:  ' . $lap_item . ',
                centerMode: ' . $center_mode . ',
                rtl: false,
                responsive: [
                    {
                        breakpoint: 991,
                        settings: {
                        slidesToShow:  ' . $tab . ',
                        slidesToScroll:  ' . $tab_item . '
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                        slidesToShow:  ' . $mobile . ',
                        slidesToScroll:  ' . $mobile_item . '
                        }
                    }
                ]
            });
        })(jQuery);';
            wp_add_inline_script($this->JSHANDLE, $jquery);
        endif;
    }

}
