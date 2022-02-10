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

namespace PPVersionNotices\Template;

class TemplateLoader implements TemplateLoaderInterface
{
    /**
     * @var string
     */
    private $templatesPath;

    /**
     * TemplateLoader constructor.
     *
     * @param string $templatesPath
     */
    public function __construct($templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    /**
     * Load template for modules.
     *
     * @param       $moduleName
     * @param       $templateName
     * @param array $context
     * @param bool $return
     *
     * @return false|string
     * @throws TemplateNotFoundException
     */
    private function load($moduleName, $templateName, $context = [], $return = false)
    {
        $templatePath = $this->templatesPath . '/' . $moduleName . '/' . $templateName . '.php';

        if (!file_exists($templatePath)) {
            throw new TemplateNotFoundException('Template file not found: ' . $templatePath);
        }

        if ($return) {
            ob_start();
        }

        extract($context);

        require $templatePath;

        if ($return) {
            return ob_get_clean();
        }

        return false;
    }

    /**
     * Load template for modules.
     *
     * @param string $moduleName
     * @param string $templateName
     * @param array $context
     *
     * @throws TemplateNotFoundException
     */
    public function displayOutput($moduleName, $templateName, $context = [])
    {
        echo $this->load($moduleName, $templateName, $context, false);
    }

    /**
     * Load template for modules.
     *
     * @param string $moduleName
     * @param string $templateName
     * @param array $context
     *
     * @return false|string
     *
     * @throws TemplateNotFoundException
     */
    public function returnOutput($moduleName, $templateName, $context = [])
    {
        return $this->load($moduleName, $templateName, $context, true);
    }
}
