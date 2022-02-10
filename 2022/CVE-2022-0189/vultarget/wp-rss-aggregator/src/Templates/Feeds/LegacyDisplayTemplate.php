<?php

namespace RebelCode\Wpra\Core\Templates\Feeds;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Output\TemplateInterface;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use InvalidArgumentException;

/**
 * A standard template wrapper for the legacy WP RSS Aggregator display template.
 *
 * @since 4.13
 */
class LegacyDisplayTemplate implements TemplateInterface
{
    /* @since 4.13 */
    use NormalizeArrayCapableTrait;

    /* @since 4.13 */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since 4.13 */
    use StringTranslatingTrait;

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function render($context = null)
    {
        try {
            $arrCtx = $this->_normalizeArray($context);
        } catch (InvalidArgumentException $exception) {
            $arrCtx = [];
        }

        wp_enqueue_style('wpra-legacy-styles', WPRSS_CSS . 'legacy-styles.css', [], WPRSS_VERSION);

        ob_start();

        wprss_display_feed_items($arrCtx);

        return ob_get_clean();
    }
}
