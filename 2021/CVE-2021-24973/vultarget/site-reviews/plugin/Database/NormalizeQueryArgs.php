<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * @property int[] $assigned_posts;
 * @property int[] $assigned_terms;
 * @property int[] $assigned_users;
 * @property string|array $date;
 * @property string $email;
 * @property string $ip_address;
 * @property int $offset;
 * @property string $order;
 * @property string $orderby;
 * @property int $page;
 * @property string $pagination;
 * @property int $per_page;
 * @property int[] $post__in;
 * @property int[] $post__not_in;
 * @property int $rating;
 * @property string $rating_field;
 * @property string $status;
 * @property string $terms;
 * @property string $type;
 * @property int[] $user__in;
 * @property int[] $user__not_in;
 */
class NormalizeQueryArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->restrict($args);
        $args['assigned_posts'] = glsr(Multilingual::class)->getPostIds($args['assigned_posts']);
        $args['date'] = $this->normalizeDate($args['date']);
        $args['order'] = Str::restrictTo('ASC,DESC,', sanitize_key($args['order']), 'DESC'); // include an empty value
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['status'] = $this->normalizeStatus($args['status']);
        parent::__construct($args);
    }

    /**
     * @param string|array $value
     * @return array
     */
    protected function normalizeDate($value)
    {
        $date = array_fill_keys(['after', 'before', 'day', 'inclusive', 'month', 'year'], '');
        $timestamp = strtotime(Cast::toString($value));
        if (false !== $timestamp) {
            $date['year'] = date('Y', $timestamp);
            $date['month'] = date('n', $timestamp);
            $date['day'] = date('j', $timestamp);
            return $date;
        }
        $date['after'] = glsr(Sanitizer::class)->sanitizeDate(Arr::get($value, 'after'));
        $date['before'] = glsr(Sanitizer::class)->sanitizeDate(Arr::get($value, 'before'));
        if (!empty(array_filter($date))) {
            $date['inclusive'] = Cast::toBool(Arr::get($value, 'inclusive')) ? '=' : '';
        }
        return $date;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function normalizeOrderBy($value)
    {
        $value = strtolower($value);
        $orderBy = Str::restrictTo('author,comment_count,date,date_gmt,id,menu_order,none,random,rating', $value, 'date');
        if ('id' === $orderBy) {
            return 'p.ID';
        }
        if (in_array($orderBy, ['comment_count', 'menu_order'])) {
            return Str::prefix($orderBy, 'p.');
        }
        if (in_array($orderBy, ['author', 'date', 'date_gmt'])) {
            return Str::prefix($orderBy, 'p.post_');
        }
        if (in_array($orderBy, ['rating'])) {
            return Str::prefix($orderBy, 'r.');
        }
        return $orderBy;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function normalizeStatus($value)
    {
        $statuses = [
            'all' => '',
            'approved' => '1',
            'pending' => '0',
            'publish' => '1',
            'unapproved' => '0',
        ];
        $status = Str::restrictTo(array_keys($statuses), $value, 'approved', $strict = true);
        return $statuses[$status];
    }
}
