<?php

namespace GeminiLabs\SiteReviews\Modules;

class Translator
{
    /**
     * @param string $original
     * @param string $domain
     * @return string
     */
    public function translate($original, $domain, array $args)
    {
        $domains = glsr()->filterArray('translator/domains', [glsr()->id]);
        if (!in_array($domain, $domains)) {
            return $original;
        }
        $args = $this->normalizeTranslationArgs($args);
        $strings = $this->getTranslationStrings($args['single'], $args['plural']);
        if (empty($strings)) {
            return $original;
        }
        $string = current($strings);
        return 'plural' == $string['type']
            ? $this->translatePlural($domain, $string, $args)
            : $this->translateSingle($domain, $string, $args);
    }

    /**
     * Used when search/replacing a default text-domain translation.
     * @return string
     */
    public function getTranslation(array $args)
    {
        $args = $this->normalizeTranslationArgs($args);
        return get_translations_for_domain(glsr()->id)->translate_plural($args['single'], $args['plural'], $args['number']);
    }

    /**
     * @param string $single
     * @param string $plural
     * @return array
     */
    protected function getTranslationStrings($single, $plural)
    {
        return array_filter(glsr(Translation::class)->translations(), function ($string) use ($single, $plural) {
            return $string['s1'] == html_entity_decode($single, ENT_COMPAT, 'UTF-8')
                && $string['p1'] == html_entity_decode($plural, ENT_COMPAT, 'UTF-8');
        });
    }

    /**
     * @return array
     */
    protected function normalizeTranslationArgs(array $args)
    {
        $defaults = [
            'context' => '',
            'number' => 1,
            'plural' => '',
            'single' => '',
        ];
        return shortcode_atts($defaults, $args);
    }

    /**
     * @param string $domain
     * @return string
     */
    protected function translatePlural($domain, array $string, array $args)
    {
        if (!empty($string['s2'])) {
            $args['single'] = $string['s2'];
        }
        if (!empty($string['p2'])) {
            $args['plural'] = $string['p2'];
        }
        return get_translations_for_domain($domain)->translate_plural(
            $args['single'],
            $args['plural'],
            $args['number'],
            $args['context']
        );
    }

    /**
     * @param string $domain
     * @return string
     */
    protected function translateSingle($domain, array $string, array $args)
    {
        if (!empty($string['s2'])) {
            $args['single'] = $string['s2'];
        }
        return get_translations_for_domain($domain)->translate(
            $args['single'],
            $args['context']
        );
    }
}
