<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;

class CountManager
{
    const META_AVERAGE = '_glsr_average';
    const META_RANKING = '_glsr_ranking';
    const META_REVIEWS = '_glsr_reviews';

    /**
     * @param int $postId
     * @return void
     */
    public function posts($postId)
    {
        $counts = glsr_get_ratings(['assigned_posts' => $postId]);
        update_post_meta($postId, static::META_AVERAGE, $counts->average);
        update_post_meta($postId, static::META_RANKING, $counts->ranking);
        update_post_meta($postId, static::META_REVIEWS, $counts->reviews);
    }

    /**
     * @return void
     */
    public function recalculate()
    {
        $this->recalculateFor('post');
        $this->recalculateFor('term');
        $this->recalculateFor('user');
    }

    /**
     * @param string $type
     * @return void
     */
    public function recalculateFor($type)
    {
        $metaKeys = [static::META_AVERAGE, static::META_RANKING, static::META_REVIEWS];
        $metaTable = $this->metaTable($type);
        glsr(Database::class)->deleteMeta($metaKeys, $metaTable);
        if ($values = $this->ratingValuesForInsert($type)) {
            glsr(Database::class)->insertBulk($metaTable, $values, [
                $this->metaId($type),
                'meta_key',
                'meta_value',
            ]);
        }
    }

    /**
     * @param int $termId
     * @return void
     */
    public function terms($termId)
    {
        $counts = glsr_get_ratings(['assigned_terms' => $termId]);
        update_term_meta($termId, static::META_AVERAGE, $counts->average);
        update_term_meta($termId, static::META_RANKING, $counts->ranking);
        update_term_meta($termId, static::META_REVIEWS, $counts->reviews);
    }

    /**
     * @param int $userId
     * @return void
     */
    public function users($userId)
    {
        $counts = glsr_get_ratings(['assigned_users' => $userId]);
        update_user_meta($userId, static::META_AVERAGE, $counts->average);
        update_user_meta($userId, static::META_RANKING, $counts->ranking);
        update_user_meta($userId, static::META_REVIEWS, $counts->reviews);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function metaId($type)
    {
        return $this->metaType($type).'_id';
    }

    /**
     * @param string $type
     * @return string
     */
    protected function metaTable($type)
    {
        return $this->metaType($type).'meta';
    }

    /**
     * @param string $type
     * @return string
     */
    protected function metaType($type)
    {
        return Str::restrictTo(['post', 'term', 'user'], Cast::toString($type), 'post');
    }

    /**
     * @param string $metaType
     * @return array
     */
    protected function ratingValuesForInsert($metaType)
    {
        $metaId = $this->metaId($metaType);
        $ratings = glsr(RatingManager::class)->ratingsGroupedBy($metaType);
        $values = [];
        foreach ($ratings as $id => $counts) {
            $values[] = [
                $metaId => $id,
                'meta_key' => static::META_AVERAGE,
                'meta_value' => glsr(Rating::class)->average($counts),
            ];
            $values[] = [
                $metaId => $id,
                'meta_key' => static::META_RANKING,
                'meta_value' => glsr(Rating::class)->ranking($counts),
            ];
            $values[] = [
                $metaId => $id,
                'meta_key' => static::META_REVIEWS,
                'meta_value' => array_sum($counts),
            ];
        }
        return $values;
    }
}
