<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Page;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Welcome
 *
 * @author $biplob018
 */
class Welcome {

    use \OXI_IMAGE_HOVER_PLUGINS\Helper\CSS_JS_Loader;

    public function __construct() {
        $this->header();
        $this->Public_Render();
    }

    public function header() {
        $this->admin_css();
    }

    public function Public_Render() {
        apply_filters('oxi-image-hover-plugin/admin_menu', TRUE);
        ?>
        <div class="oxi-addons-wrapper">
            <div class="oxi-addons-import-layouts">
                <div class="about-wrap text-center">
                    <h1>Welcome to Image Hover Ultimate</h1>
                    <div class="about-text">
                        Thank you for Installing Image Hover Ultimate, The most friendly Image Hover extension or all in one Package Image effects for any Wordpress Sites. Here's how to get started.
                    </div>
                </div>
                <div class="feature-section">
                    <div class="about-container">
                        <div class="about-addons-videos"><iframe src="https://www.youtube.com/embed/SGHeoNPogbE" frameborder="0" allowfullscreen="" class="about-video"></iframe></div>
                    </div>
                </div>
            </div>
            <div class="oxi-addons-docs-column-wrapper">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="oxi-docs-admin-wrapper">

                            <div class="oxi-docs-admin-block">
                                <div class="oxi-docs-admin-header">
                                    <div class="oxi-docs-admin-header-icon">
                                        <span class="dashicons dashicons-format-aside"></span>
                                    </div>
                                    <h4 class="oxi-docs-admin-header-title">Documentation</h4>
                                </div>
                                <div class="oxi-docs-admin-block-content">
                                    <p>Get started by spending some time with the documentation to get familiar with Image Hover Effects. Build awesome Image Hover Effects for you or your clients with ease.</p>
                                    <a href="https://www.oxilabdemos.com/image-hover/docs/" class="oxi-docs-button" target="_blank">Documentation</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="oxi-docs-admin-wrapper">
                            <div class="oxi-docs-admin-block">
                                <div class="oxi-docs-admin-header">
                                    <div class="oxi-docs-admin-header-icon">
                                        <span class="dashicons dashicons-format-aside"></span>
                                    </div>
                                    <h4 class="oxi-docs-admin-header-title">Contribute to Image Hover</h4>
                                </div>
                                <div class="oxi-docs-admin-block-content">
                                    <p>You can contribute to make Image Hover better reporting bugs &amp; creating issues. Our Development team always try to make more powerfull Plugins day by day with solved Issues</p>
                                    <a href="https://wordpress.org/support/plugin/image-hover-effects-ultimate/" class="oxi-docs-button" target="_blank">Report a bug</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="oxi-docs-admin-wrapper">
                            <div class="oxi-docs-admin-block">
                                <div class="oxi-docs-admin-header">
                                    <div class="oxi-docs-admin-header-icon">
                                        <span class="dashicons dashicons-format-aside"></span>
                                    </div>
                                    <h4 class="oxi-docs-admin-header-title">Video Tutorials </h4>
                                </div>
                                <div class="oxi-docs-admin-block-content">
                                    <p>Unable to use Image Hover Ultimate? Don't worry you can check your web tutorials to make easier to use :) </p>
                                    <a href="https://www.youtube.com/playlist?list=PLUIlGSU2bl8hv8JNPhyvYR_O5Krj-ZYCw" class="oxi-docs-button" target="_blank">Video Tutorials</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="oxi-docs-admin-wrapper">
                            <div class="oxi-docs-admin-block">
                                <div class="oxi-docs-admin-header">
                                    <div class="oxi-docs-admin-header-icon">
                                        <span class="dashicons dashicons-format-aside"></span>
                                    </div>
                                    <h4 class="oxi-docs-admin-header-title">Page Builder Integrations </h4>
                                </div>
                                <div class="oxi-docs-admin-block-content">
                                    <p>Image Hover Ultimate fully compatible  with popular Page Builder like
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/elementor-page-builder/">Elementor</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/beaver-builder-integration/">Beaver Builder</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/wpbakery-page-builder/">WPBakery</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/visual-composer/"> Visual Composer</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/divi-builder/"> Divi Builder</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/brizy/"> Brizy</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/siteorigin-page-builder/"> SiteOrigin</a>,
                                        <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/themify-builder/"> Themefy Builder</a>.
                                        As shortcode based Plugins you can use any wedpress sites and works properly.</p>
                                    <a href="https://www.oxilabdemos.com/image-hover/docs/integrate-with-page-builder/" class="oxi-docs-button" target="_blank">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php
    }

}
