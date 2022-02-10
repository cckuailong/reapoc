<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface ShortcodeContract
{
    /**
     * @params string|array $atts
     * @return string
     */
    public function buildBlock($atts = []);

    /**
     * @param string|array $atts
     * @return string
     */
    public function buildShortcode($atts = []);
}
