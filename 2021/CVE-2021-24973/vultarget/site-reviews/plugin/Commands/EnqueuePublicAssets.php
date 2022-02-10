<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Modules\Style;

class EnqueuePublicAssets implements Contract
{
    /**
     * @return void
     */
    public function handle()
    {
        $this->enqueueAssets();
        $this->enqueuePolyfillService();
        $this->enqueueRecaptchaScript();
    }

    /**
     * @return void
     */
    public function enqueueAssets()
    {
        if (glsr()->filterBool('assets/css', true)) {
            wp_enqueue_style(glsr()->id, $this->getStylesheet(), [], glsr()->version);
            wp_add_inline_style(glsr()->id, $this->inlineStyles());
        }
        if (glsr()->filterBool('assets/js', true)) {
            $dependencies = glsr()->filterBool('assets/polyfill', true)
                ? [glsr()->id.'/polyfill']
                : [];
            $dependencies = glsr()->filterArray('enqueue/public/dependencies', $dependencies);
            wp_enqueue_script(glsr()->id, $this->getScript(), $dependencies, glsr()->version, true);
            wp_add_inline_script(glsr()->id, $this->inlineScript(), 'before');
            wp_add_inline_script(glsr()->id, glsr()->filterString('enqueue/public/inline-script/after', ''));
        }
    }

    /**
     * @return void
     */
    public function enqueuePolyfillService()
    {
        if (!glsr()->filterBool('assets/polyfill', true)) {
            return;
        }
        wp_enqueue_script(glsr()->id.'/polyfill', add_query_arg([
            'features' => 'Array.prototype.find,Object.assign,CustomEvent,Element.prototype.closest,Element.prototype.dataset,Event,XMLHttpRequest,MutationObserver',
            'flags' => 'gated',
        ], 'https://polyfill.io/v3/polyfill.min.js?version=3.101.0'));
    }

    /**
     * @return void
     */
    public function enqueueRecaptchaScript()
    {
        // wpforms-recaptcha
        // google-recaptcha
        // nf-google-recaptcha
        if (!glsr(OptionManager::class)->isRecaptchaEnabled()) {
            return;
        }
        $language = glsr()->filterString('recaptcha/language', get_locale());
        wp_enqueue_script(glsr()->id.'/google-recaptcha', add_query_arg([
            'hl' => $language,
            'render' => 'explicit',
        ], 'https://www.google.com/recaptcha/api.js'));
    }

    /**
     * @return string
     */
    public function inlineScript()
    {
        $variables = [
            'action' => glsr()->prefix.'action',
            'ajaxpagination' => $this->getFixedSelectorsForPagination(),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nameprefix' => glsr()->id,
            'stars' => [
                'clearable' => false,
                'tooltip' => false,
            ],
            'urlparameter' => glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter'),
            'validationconfig' => array_merge(
                [
                    'field' => glsr(Style::class)->defaultClasses('field'),
                    'form' => glsr(Style::class)->defaultClasses('form'),
                ],
                glsr(Style::class)->validation
            ),
            'validationstrings' => glsr(ValidationStringsDefaults::class)->defaults(),
        ];
        $variables = glsr()->filterArray('enqueue/public/localize', $variables);
        return $this->buildInlineScript($variables);
    }

    /**
     * @return string|void
     */
    public function inlineStyles()
    {
        $inlineStylesheetPath = glsr()->path('assets/styles/inline-styles.css');
        if (!file_exists($inlineStylesheetPath)) {
            glsr_log()->error('Inline stylesheet is missing: '.$inlineStylesheetPath);
            return;
        }
        $inlineStylesheetValues = glsr()->config('inline-styles');
        $stylesheet = str_replace(
            array_keys($inlineStylesheetValues),
            array_values($inlineStylesheetValues),
            file_get_contents($inlineStylesheetPath)
        );
        return glsr()->filterString('enqueue/public/inline-styles', $stylesheet);
    }

    /**
     * @return string
     */
    protected function buildInlineScript(array $variables)
    {
        $script = 'window.hasOwnProperty("GLSR")||(window.GLSR={});';
        foreach ($variables as $key => $value) {
            $script .= sprintf('GLSR.%s=%s;', $key, json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $pattern = '/\"([a-zA-Z]+)\"(:[{\[\"])/'; // remove unnecessary quotes surrounding object keys
        $optimizedScript = preg_replace($pattern, '$1$2', $script);
        return glsr()->filterString('enqueue/public/inline-script', $optimizedScript, $script, $variables);
    }

    /**
     * @return array
     */
    protected function getFixedSelectorsForPagination()
    {
        $selectors = ['#wpadminbar', '.site-navigation-fixed'];
        return glsr()->filterArray('enqueue/public/localize/ajax-pagination', $selectors);
    }

    /**
     * @return string
     */
    protected function getScript()
    {
        return glsr()->url('assets/scripts/'.glsr()->id.'.js');
    }

    /**
     * @return string
     */
    protected function getStylesheet()
    {
        $style = glsr(Style::class)->style;
        return glsr()->url('assets/styles/'.$style.'.css');
    }
}
