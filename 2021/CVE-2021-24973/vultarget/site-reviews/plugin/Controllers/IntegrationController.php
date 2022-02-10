<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Integrations\Elementor\ElementorFormWidget;
use GeminiLabs\SiteReviews\Integrations\Elementor\ElementorReviewsWidget;
use GeminiLabs\SiteReviews\Integrations\Elementor\ElementorSummaryWidget;

class IntegrationController extends Controller
{
    /**
     * Fix Star Rating control when review form is used inside an Elementor Pro Popup.
     * @param string $script
     * @return string
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterElementorPublicInlineScript($js)
    {
        if (defined('ELEMENTOR_VERSION')) {
            $js .= 'function glsr_init_elementor(){GLSR.Event.trigger("site-reviews/init")}"undefined"!==typeof jQuery&&(';
            if (defined('ELEMENTOR_PRO_VERSION') && 0 > version_compare('2.7.0', ELEMENTOR_PRO_VERSION)) {
                $js .= 'jQuery(document).on("elementor/popup/show",glsr_init_elementor),';
            }
            $js .= 'jQuery(window).on("elementor/frontend/init",function(){elementorFrontend.hooks.addAction("frontend/element_ready/site_reviews.default",glsr_init_elementor);elementorFrontend.hooks.addAction("frontend/element_ready/site_reviews_form.default",glsr_init_elementor)}));';
        }
        return $js;
    }

    /**
     * Fix Star Rating CSS class prefix in the Elementor editor.
     * @return array
     * @filter site-reviews/defaults/star-rating/defaults
     */
    public function filterElementorStarRatingDefaults(array $defaults)
    {
        if ('elementor' === filter_input(INPUT_GET, 'action')) {
            $defaults['prefix'] = 'glsr-';
        }
        return $defaults;
    }

    /**
     * @return void
     * @action elementor/init
     */
    public function registerElementorCategory()
    {
        \Elementor\Plugin::instance()->elements_manager->add_category(glsr()->id, [
            'title' => glsr()->name,
            'icon' => 'eicon-star-o', // default icon
        ]);
    }

    /**
     * @return void
     * @action elementor/widgets/widgets_registered
     */
    public function registerElementorWidgets()
    {
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
            new ElementorFormWidget()
        );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
            new ElementorReviewsWidget()
        );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
            new ElementorSummaryWidget()
        );
    }
}
