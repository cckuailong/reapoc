<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface TemplateContract
{
    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function app();

    /**
     * @param string $templatePath
     * @return void|string
     */
    public function build($templatePath, array $data = []);

    /**
     * Interpolate context values into template placeholders.
     * @param string $template
     * @param string $templatePath
     * @return string
     */
    public function interpolate($template, $templatePath, array $data = []);

    /**
     * Interpolate context values into template placeholders.
     * @param string $text
     * @return string
     */
    public function interpolateContext($text, array $context = []);

    /**
     * @param string $templatePath
     * @return void|string
     */
    public function render($templatePath, array $data = []);
}
