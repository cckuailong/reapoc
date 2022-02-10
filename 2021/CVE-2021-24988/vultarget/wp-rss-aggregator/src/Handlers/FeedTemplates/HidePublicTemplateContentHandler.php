<?php

namespace RebelCode\Wpra\Core\Handlers\FeedTemplates;

/**
 * The handler that hides template content from the public-facing side unless a nonce is given.
 *
 * @since 4.13
 */
class HidePublicTemplateContentHandler
{
    /**
     * The name of the templates CPT.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $cpt;

    /**
     * The name of the nonce to use for allowing template content to be shown.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $nonce;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string $cpt   The name of the templates CPT.
     * @param string $nonce The name of the nonce to use for allowing template content to be shown.
     */
    public function __construct($cpt, $nonce)
    {
        $this->cpt = $cpt;
        $this->nonce = $nonce;
    }

    public function __invoke()
    {
        global $post;

        if (is_admin() || is_feed() || wp_doing_cron() || wp_doing_ajax()) {
            return;
        }

        if (!is_object($post) || $post->post_type !== $this->cpt) {
            return;
        }

        check_admin_referer($this->nonce);
    }
}
