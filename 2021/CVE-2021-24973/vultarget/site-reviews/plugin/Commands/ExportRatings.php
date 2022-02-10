<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Str;

class ExportRatings implements Contract
{
    protected $limit;

    public function __construct(Arguments $args)
    {
        $this->limit = 250;
        if (glsr()->post_type === $args->content) {
            add_filter('wxr_export_skip_postmeta', [$this, 'filterExportSkipPostMeta'], 10, 2);
        }
    }

    /**
     * @param bool $skip
     * @param string $metaKey
     * @return bool
     * @filter wxr_export_skip_postmeta
     */
    public function filterExportSkipPostMeta($skip, $metaKey)
    {
        return !Str::startsWith('_custom,_'.glsr()->prefix, $metaKey);
    }

    /**
     * @return void
     */
    public function handle()
    {
        glsr(Database::class)->deleteMeta(glsr()->export_key);
        $this->export();
        $this->cleanup();
    }

    /**
     * @return void
     */
    protected function cleanup()
    {
        $tenMinutes = 10 * MINUTE_IN_SECONDS;
        wp_schedule_single_event(time() + $tenMinutes, glsr()->id.'/export/cleanup');
    }

    /**
     * @return void
     */
    protected function export()
    {
        $page = 1;
        while (true) {
            $values = glsr(Query::class)->export([
                'page' => $page,
                'per_page' => $this->limit,
            ]);
            if (empty($values)) {
                break;
            }
            array_walk($values, [$this, 'prepareMeta']);
            glsr(Database::class)->insertBulk('postmeta', $values, [
                'post_id',
                'meta_key',
                'meta_value',
            ]);
            $page++;
        }
    }

    /**
     * @return void
     */
    protected function prepareMeta(array &$result)
    {
        $postId = $result['review_id'];
        unset($result['review_id']);
        $result = [
            'post_id' => $postId,
            'meta_key' => glsr()->export_key,
            'meta_value' => maybe_serialize($result),
        ];
    }
}
