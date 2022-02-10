<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class WelcomeController extends Controller
{
    protected $welcomePage;
    protected $welcomePath;

    public function __construct()
    {
        $this->welcomePage = glsr()->id.'-welcome';
        $this->welcomePath = 'index.php?page='.$this->welcomePage;
    }

    /**
     * @return array
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links)
    {
        $links['welcome'] = glsr(Builder::class)->a([
            'href' => admin_url($this->welcomePath),
            'text' => _x('About', 'admin-text', 'site-reviews'),
        ]);
        return $links;
    }

    /**
     * @return string
     * @filter admin_title
     */
    public function filterAdminTitle($title)
    {
        return 'dashboard_page_'.$this->welcomePage === glsr_current_screen()->id
            ? sprintf(_x('Welcome to %s &#8212; WordPress', 'admin-text', 'site-reviews'), glsr()->name)
            : $title;
    }

    /**
     * @param string $plugin
     * @param bool $isNetworkActivation
     * @return void
     * @action activated_plugin
     */
    public function redirectOnActivation($plugin, $isNetworkActivation)
    {
        if (!$isNetworkActivation
            && 'cli' !== php_sapi_name()
            && $plugin === plugin_basename(glsr()->file)) {
            wp_safe_redirect(admin_url($this->welcomePath));
            exit;
        }
    }

    /**
     * @return void
     * @action admin_menu
     */
    public function registerPage()
    {
        add_dashboard_page(
            sprintf(_x('Welcome to %s', 'admin-text', 'site-reviews'), glsr()->name),
            glsr()->name,
            glsr()->getPermission('welcome'),
            $this->welcomePage,
            [$this, 'renderPage']
        );
        remove_submenu_page('index.php', $this->welcomePage);
    }

    /**
     * @return void
     * @see $this->registerPage()
     * @callback add_dashboard_page
     */
    public function renderPage()
    {
        $tabs = glsr()->filterArray('addon/welcome/tabs', [
            'getting-started' => _x('Getting Started', 'admin-text', 'site-reviews'),
            'whatsnew' => _x('What\'s New', 'admin-text', 'site-reviews'),
            'upgrade-guide' => _x('Upgrade Guide', 'admin-text', 'site-reviews'),
            'support' => _x('Support', 'admin-text', 'site-reviews'),
        ]);
        glsr()->render('pages/welcome/index', [
            'data' => ['context' => []],
            'http_referer' => (string) wp_get_referer(),
            'tabs' => $tabs,
            'template' => glsr(Template::class),
        ]);
    }
}
