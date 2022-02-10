<?php
/**
 * Copyright (c) 2020 PublishPress
 *
 * GNU General Public License, Free Software Foundation <https://www.gnu.org/licenses/gpl-3.0.html>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     PPVersionNotices
 * @category    Core
 * @author      PublishPress
 * @copyright   Copyright (c) 2020 PublishPress. All rights reserved.
 **/

namespace PPVersionNotices\Module\TopNotice;

use PPVersionNotices\Module\AdInterface;
use PPVersionNotices\Template\TemplateInvalidArgumentsException;
use PPVersionNotices\Template\TemplateLoaderInterface;

/**
 * Class Module
 *
 * @package PPVersionNotices
 */
class Module implements AdInterface
{
    const SETTINGS_FILTER = 'pp_version_notice_top_notice_settings';

    const DISPLAY_ACTION = 'pp_version_notice_display_top_notice';

    /**
     * @var TemplateLoaderInterface
     */
    private $templateLoader;

    /**
     * @var array
     */
    private $exceptions = [];

    /**
     * @var array
     */
    private $settings = [];

    public function __construct(TemplateLoaderInterface $templateLoader)
    {
        $this->templateLoader = $templateLoader;
    }

    public function init()
    {
        add_action(self::DISPLAY_ACTION, [$this, 'display'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueStyle']);
        add_action('in_admin_header', [$this, 'displayTopNotice']);
        add_action('admin_init', [$this, 'collectTheSettings'], 5);
    }

    public function collectTheSettings()
    {
        $this->settings = apply_filters(self::SETTINGS_FILTER, []);
    }

    /**
     * @param string $message
     * @param string $linkURL
     */
    public function display($message = '', $linkURL = '')
    {
        try {
            if (empty($message) || empty($linkURL)) {
                throw new TemplateInvalidArgumentsException();
            }

            $context = [
                'message' => $message,
                'linkURL' => $linkURL
            ];

            $this->templateLoader->displayOutput('top-notice', 'notice', $context);
        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $this->exceptions[] = $e->getMessage();

                add_action('admin_notices', [$this, 'showNoticeWithException']);
            }
        }
    }

    /**
     * @return array|false
     */
    private function isValidScreen()
    {
        $screen = get_current_screen();

        if (!empty($screen)) {
            foreach ($this->settings as $pluginName => $setting) {
                if (!is_array($setting) || !isset($setting['screens'])) {
                    continue;
                }

                foreach ($setting['screens'] as $screenParams) {
                    if ($screenParams === true) {
                        return $setting;
                    }

                    $validVars = 0;
                    foreach ($screenParams as $var => $value) {
                        if (isset($screen->$var) && $screen->$var === $value) {
                            $validVars++;
                        }
                    }

                    if ($validVars === count($screenParams)) {
                        return $setting;
                    }
                }
            }
        }

        return false;
    }

    public function adminEnqueueStyle()
    {
        if ($this->isValidScreen()) {
            wp_enqueue_style(
                'pp-version-notice-bold-purple-style',
                PP_VERSION_NOTICES_BASE_URL . '/assets/css/top-notice-bold-purple.css',
                false,
                PP_VERSION_NOTICES_VERSION
            );
        }
    }

    public function displayTopNotice()
    {
        if ($settings = $this->isValidScreen()) {
            do_action(self::DISPLAY_ACTION, $settings['message'], $settings['link']);
        }
    }

    public function showNoticeWithException()
    {
        $class   = 'notice notice-error';
        $message = implode("<br>", $this->exceptions);

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
}
