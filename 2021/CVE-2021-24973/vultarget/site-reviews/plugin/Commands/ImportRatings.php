<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Migrate;

class ImportRatings implements Contract
{
    protected $limit;

    public function __construct()
    {
        $this->limit = 250;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->import();
        $this->cleanup();
    }

    /**
     * @return void
     */
    protected function cleanup()
    {
        glsr(Database::class)->deleteMeta(glsr()->export_key);
        glsr(Migrate::class)->reset();
    }

    /**
     * @return void
     */
    protected function import()
    {
        $page = 0;
        while (true) {
            $values = glsr(Query::class)->import([
                'page' => $page,
                'per_page' => $this->limit,
            ]);
            if (empty($values)) {
                break;
            }
            $this->importRatings($values);
            $this->importAssignedPosts($values);
            $this->importAssignedUsers($values);
            // It is unecessary to import term assignments as this is done in Migration
            $page++;
        }
    }

    /**
     * @return void
     */
    protected function importAssignedPosts(array $values)
    {
        if ($values = $this->prepareAssignedValues($values, 'post')) {
            glsr(Database::class)->insertBulk('assigned_posts', $values, [
                'rating_id',
                'post_id',
                'is_published',
            ]);
        }
    }

    /**
     * @return void
     */
    protected function importAssignedUsers(array $values)
    {
        if ($values = $this->prepareAssignedValues($values, 'user')) {
            glsr(Database::class)->insertBulk('assigned_users', $values, [
                'rating_id',
                'user_id',
            ]);
        }
    }

    /**
     * @return void
     */
    protected function importRatings(array $values)
    {
        array_walk($values, [$this, 'prepareRating']);
        $fields = array_keys(glsr(RatingDefaults::class)->unguardedDefaults());
        glsr(Database::class)->insertBulk('ratings', $values, $fields);
    }

    /**
     * @param string $key
     * @return array
     */
    protected function prepareAssignedValues(array $results, $key)
    {
        $assignedKey = $key.'_id';
        $values = [];
        foreach ($results as $result) {
            $meta = maybe_unserialize($result['meta_value']);
            if (!$assignedIds = Arr::uniqueInt(Arr::get($meta, $key.'_ids'))) {
                continue;
            }
            foreach ($assignedIds as $assignedId) {
                $value = [
                    'rating_id' => Arr::get($meta, 'ID'),
                    $assignedKey => $assignedId,
                ];
                if ('post' === $key) {
                    $value['is_published'] = Cast::toBool(Arr::get($meta, 'is_approved'));
                }
                $values[] = $value;
            }
        }
        return $values;
    }

    /**
     * @return void
     */
    protected function prepareRating(array &$result)
    {
        $values = maybe_unserialize($result['meta_value']);
        $values['review_id'] = $result['post_id'];
        $result = glsr(RatingDefaults::class)->unguardedRestrict($values);
    }
}
