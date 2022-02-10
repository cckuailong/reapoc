<?php

namespace GeminiLabs\SiteReviews\Modules;

use Exception;
use GeminiLabs\Sepia\PoParser\Parser;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Translation
{
    const CONTEXT_ADMIN_KEY = 'admin-text';
    const SEARCH_THRESHOLD = 3;

    /**
     * @var array|null
     */
    protected $entries;

    /**
     * @var array
     */
    protected $results;

    /**
     * Returns all saved custom translations with translation context.
     * @return array
     */
    public function all()
    {
        $translations = $this->translations();
        $entries = $this->filter($translations, $this->entries())->results();
        array_walk($translations, function (&$entry) use ($entries) {
            $entry['desc'] = array_key_exists($entry['id'], $entries)
                ? $this->getEntryString($entries[$entry['id']], 'msgctxt')
                : '';
        });
        return $translations;
    }

    /**
     * @return array
     */
    public function entries()
    {
        if (!isset($this->entries)) {
            $potFile = glsr()->path(glsr()->languages.'/'.glsr()->id.'.pot');
            $entries = $this->extractEntriesFromPotFile($potFile);
            $entries = glsr()->filterArray('translation/entries', $entries);
            $this->entries = $entries;
        }
        return $this->entries;
    }

    /**
     * @param array|null $entriesToExclude
     * @param array|null $entries
     * @return static
     */
    public function exclude($entriesToExclude = null, $entries = null)
    {
        return $this->filter($entriesToExclude, $entries, false);
    }

    /**
     * @param string $potFile
     * @return array
     */
    public function extractEntriesFromPotFile($potFile, array $entries = [])
    {
        try {
            $potEntries = $this->normalize(Parser::parseFile($potFile)->getEntries());
            foreach ($potEntries as $key => $entry) {
                if (Str::contains(Arr::get($entry, 'msgctxt'), static::CONTEXT_ADMIN_KEY)) {
                    continue;
                }
                $entries[html_entity_decode($key, ENT_COMPAT, 'UTF-8')] = $entry;
            }
        } catch (Exception $e) {
            glsr_log()->error($e->getMessage());
        }
        return $entries;
    }

    /**
     * @param array|null $filterWith
     * @param array|null $entries
     * @param bool $intersect
     * @return static
     */
    public function filter($filterWith = null, $entries = null, $intersect = true)
    {
        if (!is_array($entries)) {
            $entries = $this->results;
        }
        if (!is_array($filterWith)) {
            $filterWith = $this->translations();
        }
        $keys = array_flip(wp_list_pluck($filterWith, 'id'));
        $this->results = $intersect
            ? array_intersect_key($entries, $keys)
            : array_diff_key($entries, $keys);
        return $this;
    }

    /**
     * @param string $template
     * @return string
     */
    public function render($template, array $entry)
    {
        $data = array_combine(
            array_map(function ($key) { return 'data.'.$key; }, array_keys($entry)),
            $entry
        );
        $data['data.class'] = $data['data.error'] = '';
        if (false === Arr::searchByKey($entry['s1'], $this->entries(), 'msgid')) { // @todo handle htmlentities i.e. &rarr;
            $data['data.class'] = 'is-invalid';
            $data['data.error'] = _x('This custom translation is no longer valid as the original text has been changed or removed.', 'admin-text', 'site-reviews');
        }
        return glsr(Template::class)->build('partials/translations/'.$template, [
            'context' => array_map('esc_html', $data),
        ]);
    }

    /**
     * Returns a rendered string of all saved custom translations with translation context.
     * @return string
     */
    public function renderAll()
    {
        $rendered = '';
        foreach ($this->all() as $index => $entry) {
            $entry['index'] = $index;
            $entry['prefix'] = OptionManager::databaseKey();
            $rendered .= $this->render($entry['type'], $entry);
        }
        return $rendered;
    }

    /**
     * @param bool $resetAfterRender
     * @return string
     */
    public function renderResults($resetAfterRender = true)
    {
        $rendered = '';
        foreach ($this->results as $id => $entry) {
            $data = [
                'desc' => $this->getEntryString($entry, 'msgctxt'),
                'id' => $id,
                'p1' => $this->getEntryString($entry, 'msgid_plural'),
                's1' => $this->getEntryString($entry, 'msgid'),
            ];
            $text = !empty($data['p1'])
                ? sprintf('%s | %s', $data['s1'], $data['p1'])
                : $data['s1'];
            $rendered .= $this->render('result', [
                'entry' => json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'text' => wp_strip_all_tags($text),
            ]);
        }
        if ($resetAfterRender) {
            $this->reset();
        }
        return $rendered;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->results = [];
    }

    /**
     * @return array
     */
    public function results()
    {
        $results = $this->results;
        $this->reset();
        return $results;
    }

    /**
     * @param string $needle
     * @return static
     */
    public function search($needle = '')
    {
        $this->reset();
        $needle = trim(strtolower($needle));
        foreach ($this->entries() as $key => $entry) {
            $single = strtolower($this->getEntryString($entry, 'msgid'));
            $plural = strtolower($this->getEntryString($entry, 'msgid_plural'));
            if (strlen($needle) < static::SEARCH_THRESHOLD) {
                if (in_array($needle, [$single, $plural])) {
                    $this->results[$key] = $entry;
                }
            } elseif (Str::contains($needle, sprintf('%s %s', $single, $plural))) {
                $this->results[$key] = $entry;
            }
        }
        return $this;
    }

    /**
     * Store the translations to avoid unnecessary loops.
     * @return array
     */
    public function translations()
    {
        static $translations;
        if (empty($translations)) {
            $settings = glsr(OptionManager::class)->get('settings');
            $translations = isset($settings['strings'])
                ? $this->normalizeSettings((array) $settings['strings'])
                : [];
        }
        return $translations;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getEntryString(array $entry, $key)
    {
        return isset($entry[$key])
            ? implode('', (array) $entry[$key])
            : '';
    }

    /**
     * @return array
     */
    protected function normalize(array $entries)
    {
        $keys = [
            'msgctxt', 'msgid', 'msgid_plural', 'msgstr', 'msgstr[0]', 'msgstr[1]',
        ];
        array_walk($entries, function (&$entry) use ($keys) {
            foreach ($keys as $key) {
                $entry = $this->normalizeEntryString($entry, $key);
            }
        });
        return $entries;
    }

    /**
     * @param string $key
     * @return array
     */
    protected function normalizeEntryString(array $entry, $key)
    {
        if (isset($entry[$key])) {
            $entry[$key] = $this->getEntryString($entry, $key);
        }
        return $entry;
    }

    /**
     * @return array
     */
    protected function normalizeSettings(array $strings)
    {
        $defaultString = array_fill_keys(['id', 's1', 's2', 'p1', 'p2'], '');
        $strings = array_filter($strings, 'is_array');
        foreach ($strings as &$string) {
            $string['type'] = isset($string['p1']) ? 'plural' : 'single';
            $string = wp_parse_args($string, $defaultString);
        }
        return array_filter($strings, function ($string) {
            return !empty($string['id']);
        });
    }
}
