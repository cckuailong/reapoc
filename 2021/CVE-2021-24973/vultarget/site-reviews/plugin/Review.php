<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\ReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;

/**
 * @property bool $approved  This property is mapped to $is_approved
 * @property array $assigned_posts
 * @property array $assigned_terms
 * @property array $assigned_users
 * @property string $author
 * @property int $author_id
 * @property string $avatar
 * @property string $content
 * @property Arguments $custom
 * @property string $date
 * @property string $date_gmt
 * @property string $name  This property is mapped to $author
 * @property string $email
 * @property bool $has_revisions  This property is mapped to $is_modified
 * @property int $ID
 * @property string $ip_address
 * @property bool $is_approved
 * @property bool $is_modified
 * @property bool $is_pinned
 * @property bool $modified  This property is mapped to $is_modified
 * @property bool $pinned  This property is mapped to $is_pinned
 * @property int $rating
 * @property int $rating_id
 * @property string $response
 * @property string $status
 * @property bool $terms
 * @property string $title
 * @property string $type
 * @property string $url
 * @property int $user_id  This property is mapped to $author_id
 */
class Review extends Arguments
{
    /**
     * @var Arguments
     */
    protected $_meta;

    /**
     * @var \WP_Post
     */
    protected $_post;

    /**
     * @var bool
     */
    protected $has_checked_revisions;

    /**
     * @var int
     */
    protected $id;

    /**
     * @param array|object $values
     * @param bool $init
     */
    public function __construct($values, $init = true)
    {
        $values = glsr()->args($values);
        $this->id = Cast::toInt($values->review_id);
        $args = glsr(ReviewDefaults::class)->restrict($values->toArray());
        $args['ID'] = $this->id;
        parent::__construct($args);
        if ($init) {
            $this->set('avatar', glsr(Avatar::class)->url($this));
            $this->set('custom', $this->custom());
            $this->set('response', $this->meta()->_response);
        }
    }

    /**
     * @return mixed
     */
    public function __call($method, $args)
    {
        array_unshift($args, $this);
        $result = apply_filters_ref_array(glsr()->id.'/review/call/'.$method, $args);
        if (!is_a($result, get_class($this))) {
            return $result;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->build();
    }

    /**
     * @return array
     */
    public function assignedPosts()
    {
        if (empty($this->assigned_posts)) {
            return $this->assigned_posts;
        }
        return get_posts([
            'post__in' => $this->assigned_posts,
            'post_type' => 'any',
            'posts_per_page' => -1,
        ]);
    }

    /**
     * @return array
     */
    public function assignedTerms()
    {
        if (empty($this->assigned_terms)) {
            return $this->assigned_terms;
        }
        $terms = get_terms(glsr()->taxonomy, ['include' => $this->assigned_terms]);
        if (is_wp_error($terms)) {
            return $this->assigned_terms;
        }
        return $terms;
    }

    /**
     * @return array
     */
    public function assignedUsers()
    {
        if (empty($this->assigned_users)) {
            return $this->assigned_users;
        }
        return get_users([
            'fields' => ['display_name', 'ID', 'user_email', 'user_nicename', 'user_url'],
            'include' => $this->assigned_users,
        ]);
    }

    /**
     * @return string
     */
    public function author()
    {
        return Text::name($this->get('author'));
    }

    /**
     * @param int $size
     * @return string
     */
    public function avatar($size = 0)
    {
        return glsr(Avatar::class)->img($this, $size);
    }

    /**
     * @return ReviewHtml
     */
    public function build(array $args = [])
    {
        return new ReviewHtml($this, $args);
    }

    /**
     * @return Arguments
     */
    public function custom()
    {
        $custom = array_filter($this->meta()->toArray(), function ($key) {
            return Str::startsWith('_custom', $key);
        }, ARRAY_FILTER_USE_KEY);
        $custom = Arr::unprefixKeys($custom, '_custom_');
        $custom = Arr::unprefixKeys($custom, '_');
        $custom = glsr(CustomFieldsDefaults::class)->merge($custom);
        return glsr()->args($custom);
    }

    /**
     * @return string
     */
    public function date($format = 'F j, Y')
    {
        $value = $this->get('date_gmt');
        if (!empty(func_get_args())) {
            return date_i18n($format, strtotime($value));
        }
        $dateFormat = glsr_get_option('reviews.date.format', 'default');
        if ('relative' == $dateFormat) {
            return glsr(Date::class)->relative($value, 'past');
        }
        $format = 'custom' == $dateFormat
            ? glsr_get_option('reviews.date.custom', 'M j, Y')
            : glsr(OptionManager::class)->getWP('date_format', 'F j, Y');
        return date_i18n($format, strtotime($value));
    }

    /**
     * @param int|\WP_Post $post
     * @return bool
     */
    public static function isEditable($post)
    {
        $postId = Helper::getPostId($post);
        return static::isReview($postId)
            && in_array(glsr(Query::class)->review($postId)->type, ['', 'local']);
    }

    /**
     * @param \WP_Post|int|false $post
     * @return bool
     */
    public static function isReview($post)
    {
        return glsr()->post_type === get_post_type($post);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->id) && !empty($this->get('rating_id'));
    }

    /**
     * @return Arguments
     */
    public function meta()
    {
        if (!$this->_meta instanceof Arguments) {
            $meta = Arr::consolidate(get_post_meta($this->id));
            $meta = array_map(function ($item) {
                return array_shift($item);
            }, array_filter($meta));
            $meta = array_filter($meta, 'strlen');
            $meta = array_map('maybe_unserialize', $meta);
            $this->_meta = glsr()->args($meta);
        }
        return $this->_meta;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return parent::offsetExists($key) || !is_null($this->custom()->$key);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        $alternateKeys = [
            'approved' => 'is_approved',
            'has_revisions' => 'is_modified',
            'modified' => 'is_modified',
            'name' => 'author',
            'pinned' => 'is_pinned',
            'user_id' => 'author_id',
        ];
        if (array_key_exists($key, $alternateKeys)) {
            return $this->offsetGet($alternateKeys[$key]);
        }
        if ('is_modified' === $key) {
            return $this->hasRevisions();
        }
        if (is_null($value = parent::offsetGet($key))) {
            return $this->custom()->$key;
        }
        return $value;
    }

    /**
     * @param mixed $key
     * @return void
     */
    public function offsetSet($key, $value)
    {
        // This class is read-only, except for custom fields
        if ('custom' === $key) {
            $value = Arr::consolidate($value);
            $value = Arr::prefixKeys($value, '_custom_');
            $meta = wp_parse_args($this->_meta->toArray(), $value);
            $this->_meta = glsr()->args($meta);
            parent::offsetSet($key, $this->custom());
        }
    }

    /**
     * @param mixed $key
     * @return void
     */
    public function offsetUnset($key)
    {
        // This class is read-only
    }

    /**
     * @return \WP_Post|null
     */
    public function post()
    {
        if (!$this->_post instanceof \WP_Post) {
            $this->_post = get_post($this->id);
        }
        return $this->_post;
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }

    /**
     * @return string
     */
    public function rating()
    {
        return glsr_star_rating($this->get('rating'));
    }

    /**
     * @return string
     */
    public function type()
    {
        $type = $this->get('type');
        $reviewTypes = glsr()->retrieveAs('array', 'review_types');
        return Arr::get($reviewTypes, $type, _x('Unknown', 'admin-text', 'site-reviews'));
    }

    /**
     * @return bool
     */
    protected function hasRevisions()
    {
        if (!$this->has_checked_revisions) {
            $modified = glsr(Query::class)->hasRevisions($this->ID);
            $this->set('is_modified', $modified);
            $this->has_checked_revisions = true;
        }
        return $this->get('is_modified');
    }
}
