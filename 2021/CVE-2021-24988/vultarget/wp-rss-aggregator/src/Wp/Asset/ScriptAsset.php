<?php

namespace RebelCode\Wpra\Core\Wp\Asset;

/**
 * Class for enqueuing scripts.
 *
 * @since 4.14
 */
class ScriptAsset extends AbstractAsset implements ScriptInterface
{
    /**
     * Whether to enqueue the script before `</body>` instead of in the `<head>`.
     *
     * @since 4.14
     *
     * @var bool
     */
    protected $footer = false;

    /**
     * Function to execute after the script was enqueued.
     *
     * @since 4.14
     *
     * @var null|callable
     */
    protected $afterNq;

    /**
     * Localization data.
     *
     * @since 4.14
     *
     * @var array
     */
    protected $l10n;

    /**
     * @inheritdoc
     *
     * @since 4.14
     *
     * @param bool          $footer  Whether to enqueue the script before `</body>` instead of in the `<head>`.
     * @param callable|null $afterNq Function to execute after the script was enqueued.
     */
    public function __construct($handle, $src, $deps = [], $version = false, $footer = false, $afterNq = null)
    {
        parent::__construct($handle, $src, $deps, $version);

        $this->footer = $footer;
        $this->afterNq = $afterNq;
        $this->l10n = [];
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function localize($key, callable $callback)
    {
        $instance = clone $this;
        $instance->l10n[$key] = $callback;

        return $instance;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function register()
    {
        wp_register_script($this->handle, $this->src, $this->deps, $this->version, $this->footer);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.14
     */
    public function enqueue()
    {
        foreach ($this->l10n as $key => $data) {
            wp_localize_script($this->handle, $key, is_callable($data) ? call_user_func($data) : $data);
        }

        $this->register();
        wp_enqueue_script($this->handle);

        if (is_callable($this->afterNq)) {
            call_user_func($this->afterNq, [$this]);
        }
    }
}
