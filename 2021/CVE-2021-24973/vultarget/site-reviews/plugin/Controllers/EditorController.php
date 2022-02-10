<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueType;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\UpdatedMessageDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class EditorController extends Controller
{
    /**
     * @param array $settings
     * @return array
     * @filter wp_editor_settings
     */
    public function filterEditorSettings($settings)
    {
        if ($this->isReviewEditor()) {
            $settings = [
                'media_buttons' => false,
                'quicktags' => false,
                'textarea_rows' => 12,
                'tinymce' => false,
            ];
        }
        return Arr::consolidate($settings);
    }

    /**
     * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script.
     * @param string $html
     * @return string
     * @filter the_editor
     */
    public function filterEditorTextarea($html)
    {
        if ($this->isReviewEditor()) {
            $html = str_replace('<textarea', '<div id="ed_toolbar"></div><textarea', $html);
        }
        return $html;
    }

    /**
     * @param bool $protected
     * @param string $metaKey
     * @param string $metaType
     * @return bool
     * @filter is_protected_meta
     */
    public function filterIsProtectedMeta($protected, $metaKey, $metaType)
    {
        if ('post' == $metaType && Str::startsWith('_custom_,_'.glsr()->prefix, $metaKey)) {
            if ('delete-meta' === filter_input(INPUT_POST, 'action')) {
                return false; // allow delete but not update
            }
            if (glsr()->post_type === get_post_type()) {
                return false; // display the field in the Custom Fields metabox
            }
        }
        return $protected;
    }

    /**
     * @param array $messages
     * @return array
     * @filter post_updated_messages
     */
    public function filterUpdateMessages($messages)
    {
        $post = get_post();
        if (!$post instanceof WP_Post) {
            return $messages;
        }
        $strings = glsr(UpdatedMessageDefaults::class)->defaults();
        $restored = filter_input(INPUT_GET, 'revision');
        if ($revisionTitle = wp_post_revision_title(intval($restored), false)) {
            $restored = sprintf($strings['restored'], $revisionTitle);
        }
        $scheduled_date = date_i18n('M j, Y @ H:i', strtotime($post->post_date));
        $messages = Arr::consolidate($messages);
        $messages[glsr()->post_type] = [
             1 => $strings['updated'],
             4 => $strings['updated'],
             5 => $restored,
             6 => $strings['published'],
             7 => $strings['saved'],
             8 => $strings['submitted'],
             9 => sprintf($strings['scheduled'], '<strong>'.$scheduled_date.'</strong>'),
            10 => $strings['draft_updated'],
            50 => $strings['approved'],
            51 => $strings['unapproved'],
            52 => $strings['reverted'],
        ];
        return $messages;
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/mce-shortcode
     */
    public function mceShortcodeAjax(Request $request)
    {
        $shortcode = $request->shortcode;
        $response = false;
        if ($data = glsr()->retrieve('mce.'.$shortcode, false)) {
            if (!empty($data['errors'])) {
                $data['btn_okay'] = [esc_attr_x('Okay', 'admin-text', 'site-reviews')];
            }
            $response = [
                'body' => $data['fields'],
                'close' => $data['btn_close'],
                'ok' => $data['btn_okay'],
                'shortcode' => $shortcode,
                'title' => $data['title'],
            ];
        }
        wp_send_json_success($response);
    }

    /**
     * @param WP_Post $post
     * @return void
     * @action edit_form_top
     */
    public function renderReviewNotice($post)
    {
        if (!$this->isReviewEditor()) {
            return;
        }
        if (Review::isReview($post) && !Review::isEditable($post)) {
            glsr(Notice::class)->addWarning(sprintf(
                _x('Publicly responding to third-party %s reviews is disabled.', 'admin-text', 'site-reviews'),
                glsr(ColumnValueType::class)->handle(glsr(Query::class)->review($post->ID))
            ));
            glsr(Template::class)->render('partials/editor/notice', [
                'context' => [
                    'notices' => glsr(Notice::class)->get(),
                ],
            ]);
        }
    }

    /**
     * @return bool
     */
    protected function isReviewEditor()
    {
        $screen = glsr_current_screen();
        return ('post' == $screen->base)
            && glsr()->post_type == $screen->id
            && glsr()->post_type == $screen->post_type;
    }

    /**
     * @param int $postId
     * @param int $messageIndex
     * @return void
     */
    protected function redirect($postId, $messageIndex)
    {
        $referer = wp_get_referer();
        $hasReferer = !$referer
            || Str::contains('post.php', $referer)
            || Str::contains('post-new.php', $referer);
        $redirectUri = $hasReferer
            ? remove_query_arg(['deleted', 'ids', 'trashed', 'untrashed'], $referer)
            : get_edit_post_link($postId);
        wp_safe_redirect(add_query_arg(['message' => $messageIndex], $redirectUri));
        exit;
    }
}
