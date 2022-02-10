<?php

namespace RebelCode\Wpra\Core\Handlers\FeedTemplates;

/**
 * The handler that detects a preview template request and redirects to the template's front-facing page.
 *
 * @since 4.13
 */
class PreviewTemplateRedirectHandler
{
    /**
     * The name of the GET parameter to detect.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $getParam;

    /**
     * The name of the nonce to that allows template content to be shown on the public-facing side.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $nonce;

    /**
     * The feed templates CPT capability type.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $capability;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string $getArg     The name of the GET parameter to detect.
     * @param string $nonce      The name of the nonce to that allows template content to be shown on the public-facing
     *                           side of the site.
     * @param string $capability The feed templates CPT capability type.
     */
    public function __construct($getArg, $nonce, $capability)
    {
        $this->getParam = $getArg;
        $this->nonce = $nonce;
        $this->capability = $capability;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        if (!is_admin() || wp_doing_ajax() || wp_doing_cron() || is_feed()) {
            return;
        }

        $previewId = filter_input(INPUT_GET, $this->getParam, FILTER_SANITIZE_STRING);
        if (empty($previewId)) {
            return;
        }

        $capability = sprintf('read_%s', $this->capability);

        if (!current_user_can($capability, $previewId)) {
            wp_die(__('You do not have sufficient privileges!', 'wprss'));
            exit;
        }

        $permalink = get_permalink($previewId);
        if ($permalink === false) {
            wp_die(__('Invalid template ID', 'wprss'));
            exit;
        }

        // Get the template options, if present. They will in the form of a Base64 string of their JSON serialization
        $options = filter_input(INPUT_GET, 'wpra_template_options', FILTER_SANITIZE_STRING);
        $options = empty($options) ? '' : $options;

        $urlQuery = parse_url($permalink, PHP_URL_QUERY);
        $separator = (empty($urlQuery)) ? '?' : "&";
        $nonce = wp_create_nonce($this->nonce);
        $getParams = [
            '_wpnonce' => $nonce,
            'options' => $options,
        ];

        $fullUrl = $permalink . $separator . http_build_query($getParams);

        wp_safe_redirect($fullUrl);
        die;
    }
}
