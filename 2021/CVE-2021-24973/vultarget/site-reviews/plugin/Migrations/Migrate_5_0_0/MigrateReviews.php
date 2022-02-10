<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_5_0_0;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Install;

class MigrateReviews
{
    public $db;
    public $limit;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->limit = 250;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->createDatabaseTable();
        $this->migrateRatings();
        $this->migrateAssignedTo();
        $this->migrateTerms();
        $this->migrateCustom();
    }

    /**
     * @return void
     */
    protected function createDatabaseTable()
    {
        glsr(Install::class)->run();
    }

    /**
     * @return array
     */
    protected function filterCustomValues(array $values)
    {
        $filtered = [];
        foreach ($values as $postId => $meta) {
            $custom = Arr::consolidate(maybe_unserialize(Arr::get($meta, '_custom')));
            if (!empty($custom)) {
                $custom = Arr::prefixKeys($custom, '_custom_');
                $filtered[$postId] = array_diff_key($custom, $meta);
            }
        }
        return array_filter($filtered);
    }

    /**
     * @return array
     */
    protected function groupCustomValues(array $values)
    {
        $grouped = [];
        foreach ($values as $result) {
            if (empty($grouped[$result->post_id])) {
                $grouped[$result->post_id] = [];
            }
            $grouped[$result->post_id][$result->meta_key] = $result->meta_value;
        }
        return $grouped;
    }

    /**
     * @return void
     */
    protected function migrateAssignedTo()
    {
        glsr(Database::class)->beginTransaction('assigned_posts');
        $offset = 0;
        $table = glsr(Query::class)->table('ratings');
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT r.ID AS rating_id, m.meta_value AS post_id, CAST(IF(p.post_status = 'publish', 1, 0) AS UNSIGNED) AS is_published
                FROM {$table} AS r
                INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
                INNER JOIN {$this->db->postmeta} AS m ON r.review_id = m.post_id
                WHERE m.meta_key = '_assigned_to' AND m.meta_value > 0 
                ORDER BY r.ID
                LIMIT %d, %d
            ", $offset, $this->limit), 'migrate-assigned-posts');
            $results = glsr(Database::class)->dbGetResults($sql, ARRAY_A);
            if (empty($results)) {
                break;
            }
            glsr(Database::class)->insertBulk('assigned_posts', $results, [
                'rating_id',
                'post_id',
                'is_published',
            ]);
            $offset += $this->limit;
        }
        glsr(Database::class)->finishTransaction('assigned_posts');
    }

    /**
     * @return void
     */
    protected function migrateCustom()
    {
        glsr(Database::class)->beginTransaction('postmeta');
        $offset = 0;
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT m1.post_id, m1.meta_key, m1.meta_value
                FROM {$this->db->postmeta} AS m1
                INNER JOIN {$this->db->posts} AS p ON m1.post_id = p.ID
                LEFT JOIN {$this->db->postmeta} AS m2 ON (m1.post_id = m2.post_id AND m1.meta_key = m2.meta_key AND m1.meta_id < m2.meta_id)
                WHERE m2.meta_id IS NULL
                AND p.post_type = '%s'
                AND m1.meta_value != '%s'
                AND m1.meta_key LIKE '_custom%%'
                ORDER BY m1.meta_id
                LIMIT %d, %d
            ", glsr()->post_type, serialize([]), $offset, $this->limit), 'migrate-custom');
            $results = glsr(Database::class)->dbGetResults($sql, OBJECT);
            if (empty($results)) {
                break;
            }
            if ($values = $this->prepareCustomValuesForInsert($results)) {
                glsr(Database::class)->insertBulk('postmeta', $values, [
                    'post_id',
                    'meta_key',
                    'meta_value',
                ]);
            }
            $offset += $this->limit;
        }
        glsr(Database::class)->finishTransaction('postmeta');
    }

    /**
     * @return void
     */
    protected function migrateRatings()
    {
        glsr(Database::class)->beginTransaction('ratings');
        $offset = 0;
        $table = glsr(Query::class)->table('ratings');
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT p.ID, m.meta_key AS mk, m.meta_value AS mv, CAST(IF(p.post_status = 'publish', 1, 0) AS UNSIGNED) AS is_approved
                FROM {$this->db->posts} AS p
                LEFT JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
                WHERE p.ID IN (
                    SELECT * FROM (
                        SELECT ID
                        FROM {$this->db->posts}
                        WHERE post_type = '%s'
                        ORDER BY ID
                        LIMIT %d, %d
                    ) AS post_ids
                )
                AND NOT EXISTS (
                    SELECT r.review_id
                    FROM {$table} AS r
                    WHERE r.review_id = p.ID
                )
            ", glsr()->post_type, $offset, $this->limit), 'migrate-ratings');
            $results = glsr(Database::class)->dbGetResults($sql, OBJECT);
            if (empty($results)) {
                break;
            }
            $values = $this->parseRatings($results);
            $fields = array_keys(glsr(RatingDefaults::class)->defaults());
            glsr(Database::class)->insertBulk('ratings', $values, $fields);
            $offset += $this->limit;
        }
        glsr(Database::class)->finishTransaction('ratings');
    }

    /**
     * @return void
     */
    protected function migrateTerms()
    {
        glsr(Database::class)->beginTransaction('assigned_terms');
        $offset = 0;
        $table = glsr(Query::class)->table('ratings');
        while (true) {
            $sql = glsr(Query::class)->sql($this->db->prepare("
                SELECT r.ID AS rating_id, tt.term_id AS term_id
                FROM {$table} AS r
                INNER JOIN {$this->db->term_relationships} AS tr ON r.review_id = tr.object_id
                INNER JOIN {$this->db->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                ORDER BY r.ID
                LIMIT %d, %d
            ", $offset, $this->limit), 'migrate-assigned-terms');
            $results = glsr(Database::class)->dbGetResults($sql, ARRAY_A);
            if (empty($results)) {
                break;
            }
            glsr(Database::class)->insertBulk('assigned_terms', $results, [
                'rating_id',
                'term_id',
            ]);
            $offset += $this->limit;
        }
        glsr(Database::class)->finishTransaction('assigned_terms');
    }

    /**
     * @return array
     */
    protected function parseRatings(array $results)
    {
        $values = [];
        foreach ($results as $result) {
            $value = maybe_unserialize($result->mv);
            if (is_array($value)) {
                continue;
            }
            if (!isset($values[$result->ID])) {
                $values[$result->ID] = ['is_approved' => (int) $result->is_approved];
            }
            $values[$result->ID][$result->mk] = $value;
        }
        $results = [];
        foreach ($values as $postId => $value) {
            $meta = Arr::unprefixKeys($value);
            $meta['name'] = Arr::get($meta, 'author');
            $meta['is_pinned'] = Arr::get($meta, 'pinned');
            $meta['review_id'] = $postId;
            $meta['type'] = Arr::get($meta, 'review_type', 'local');
            $meta = Arr::removeEmptyValues($meta);
            $meta = glsr(RatingDefaults::class)->restrict($meta);
            $results[] = $meta;
        }
        return $results;
    }

    /**
     * @return array
     */
    protected function prepareCustomValuesForInsert(array $results)
    {
        $filtered = $this->filterCustomValues(
            $this->groupCustomValues($results)
        );
        $values = [];
        foreach ($filtered as $postId => $meta) {
            foreach ($meta as $metaKey => $metaValue) {
                $values[] = [
                    'post_id' => $postId,
                    'meta_key' => $metaKey,
                    'meta_value' => $metaValue,
                ];
            }
        }
        return $values;
    }
}
