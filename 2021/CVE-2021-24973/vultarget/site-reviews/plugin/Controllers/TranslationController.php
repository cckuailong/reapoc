<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Defaults\PostStatusLabelsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

class TranslationController
{
    /**
     * @var Translator
     */
    public $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array $messages
     * @return array
     * @filter bulk_post_updated_messages
     */
    public function filterBulkUpdateMessages($messages, array $counts)
    {
        $messages = Arr::consolidate($messages);
        $messages[glsr()->post_type] = [
            'updated' => _nx('%s review updated.', '%s reviews updated.', $counts['updated'], 'admin-text', 'site-reviews'),
            'locked' => _nx('%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'admin-text', 'site-reviews'),
            'deleted' => _nx('%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'admin-text', 'site-reviews'),
            'trashed' => _nx('%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'admin-text', 'site-reviews'),
            'untrashed' => _nx('%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'admin-text', 'site-reviews'),
        ];
        return $messages;
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter gettext_default
     */
    public function filterEnglishTranslation($translation, $text)
    {
        return $text;
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter gettext_{glsr()->id}
     */
    public function filterGettext($translation, $text)
    {
        return $this->translator->translate($translation, glsr()->id, [
            'single' => $text,
        ]);
    }

    /**
     * @param string $translation
     * @param string $text
     * @param string $context
     * @return string
     * @filter gettext_with_context_{glsr()->id}
     */
    public function filterGettextWithContext($translation, $text, $context)
    {
        if (Str::contains($context, Translation::CONTEXT_ADMIN_KEY)) {
            return $translation;
        }
        return $this->translator->translate($translation, glsr()->id, [
            'context' => $context,
            'single' => $text,
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @return string
     * @filter ngettext_{glsr()->id}
     */
    public function filterNgettext($translation, $single, $plural, $number)
    {
        return $this->translator->translate($translation, glsr()->id, [
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $context
     * @return string
     * @filter ngettext_with_context_{glsr()->id}
     */
    public function filterNgettextWithContext($translation, $single, $plural, $number, $context)
    {
        if (Str::contains($context, Translation::CONTEXT_ADMIN_KEY)) {
            return $translation;
        }
        return $this->translator->translate($translation, glsr()->id, [
            'context' => $context,
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param array $postStates
     * @param \WP_Post $post
     * @return array
     * @filter display_post_states
     */
    public function filterPostStates($postStates, $post)
    {
        $postStates = Arr::consolidate($postStates);
        if (glsr()->post_type == Arr::get($post, 'post_type') && array_key_exists('pending', $postStates)) {
            $postStates['pending'] = _x('Unapproved', 'admin-text', 'site-reviews');
        }
        return $postStates;
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter gettext_default
     */
    public function filterPostStatusLabels($translation, $text)
    {
        if ($this->canModifyTranslation()) {
            $replacements = $this->statusLabels();
            return array_key_exists($text, $replacements)
                ? $replacements[$text]
                : $translation;
        }
        return $translation;
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @return string
     * @filter ngettext_default
     */
    public function filterPostStatusText($translation, $single, $plural, $number)
    {
        if ($this->canModifyTranslation()) {
            $strings = [
                'Published' => _x('Approved', 'admin-text', 'site-reviews'),
                'Pending' => _x('Unapproved', 'admin-text', 'site-reviews'),
            ];
            foreach ($strings as $search => $replace) {
                if (!Str::contains($search, $single)) {
                    continue;
                }
                return $this->translator->getTranslation([
                    'number' => $number,
                    'plural' => str_replace($search, $replace, $plural),
                    'single' => str_replace($search, $replace, $single),
                ]);
            }
        }
        return $translation;
    }

    /**
     * @return void
     * @action admin_print_scripts-post.php
     */
    public function translatePostStatusLabels()
    {
        if (!$this->canModifyTranslation()) {
            return;
        }
        $pattern = '/^([^{]+)(.+)([^}]+)$/';
        $script = Arr::get(wp_scripts(), 'registered.post.extra.data');
        preg_match($pattern, $script, $matches);
        if (4 === count($matches) && $i10n = json_decode($matches[2], true)) {
            $i10n['privatelyPublished'] = _x('Privately Approved', 'admin-text', 'site-reviews');
            $i10n['publish'] = _x('Approve', 'admin-text', 'site-reviews');
            $i10n['published'] = _x('Approved', 'admin-text', 'site-reviews');
            $i10n['publishOn'] = _x('Approve on:', 'admin-text', 'site-reviews');
            $i10n['publishOnPast'] = _x('Approved on:', 'admin-text', 'site-reviews');
            $i10n['savePending'] = _x('Save as Unapproved', 'admin-text', 'site-reviews');
            $script = $matches[1].json_encode($i10n).$matches[3];
            Arr::set(wp_scripts(), 'registered.post.extra.data', $script);
        }
    }

    /**
     * @return bool
     */
    protected function canModifyTranslation()
    {
        $screen = glsr_current_screen();
        return glsr()->post_type == $screen->post_type
            && in_array($screen->base, ['edit', 'post']);
    }

    /**
     * Store the labels to avoid unnecessary loops.
     * @return array
     */
    protected function statusLabels()
    {
        static $labels;
        if (empty($labels)) {
            $labels = glsr(PostStatusLabelsDefaults::class)->defaults();
        }
        return $labels;
    }
}
