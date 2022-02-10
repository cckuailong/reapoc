<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Avatars\InitialsAvatar;
use GeminiLabs\SiteReviews\Modules\Avatars\PixelAvatar;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class Avatar
{
    const FALLBACK_SIZE = 40;
    const GRAVATAR_URL = 'https://secure.gravatar.com/avatar';

    /**
     * @var string
     */
    public $type;

    public function __construct()
    {
        $this->type = glsr_get_option('reviews.avatars_fallback', 'mystery', 'string');
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @return string
     */
    public function fallbackDefault($review)
    {
        if ('pixels' === $this->type) {
            return $this->generatePixels($review);
        }
        if ('initials' === $this->type) {
            if (!empty($review->author)) {
                return $this->generateInitials($review);
            }
            $this->type = 'mystery'; // can't create initials without a name
        }
        return $this->type;
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @param int $size
     * @return string
     */
    public function fallbackUrl($review, $size = 0)
    {
        $fallbackUrl = $this->fallbackDefault($review);
        if ($fallbackUrl === $this->type) {
            $fallbackUrl = add_query_arg('d', $this->type, static::GRAVATAR_URL);
            $fallbackUrl = add_query_arg('s', $this->size($size), $fallbackUrl);
        }
        return glsr()->filterString('avatar/fallback', $fallbackUrl, $size, $review);
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @param int $size
     * @return string
     */
    public function generate($review, $size = 0)
    {
        $default = $this->fallbackDefault($review);
        if ($default !== $this->type) {
            $default = '404';
        }
        $size = $this->size($size);
        $avatarUrl = get_avatar_url($this->userField($review), [
            'default' => $default,
            'size' => $size,
        ]);
        if (!$this->isUrl($avatarUrl)) {
            return $this->fallbackUrl($review, $size);
        }
        if (404 === Helper::remoteStatusCheck($avatarUrl)) {
            // @todo generate the images with javascript on canvas to avoid this status check
            return $this->fallbackUrl($review, $size);
        }
        return $avatarUrl;
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @return string
     */
    public function generateInitials($review)
    {
        return glsr(InitialsAvatar::class)->create($review->author);
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @return string
     */
    public function generatePixels($review)
    {
        return glsr(PixelAvatar::class)->create($this->userField($review));
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @param int $size
     * @return string
     */
    public function img($review, $size = 0)
    {
        $size = $this->size($size);
        $attributes = [
            'alt' => sprintf(__('Avatar for %s', 'site-reviews'), $review->author()),
            'height' => $size, // @2x
            'loading' => 'lazy',
            'src' => $this->url($review, $size), // @2x
            'style' => sprintf('width:%1$spx; height:%1$spx;', $size / 2), // @1x
            'width' => $size, // @2x
        ];
        if (glsr()->isAdmin()) {
            $attributes['data-fallback'] = $this->fallbackUrl($review, $size);
        }
        $attributes = glsr()->filterArray('avatar/attributes', $attributes, $review);
        return glsr(Builder::class)->img($attributes);
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @param int $size
     * @return string
     */
    public function url($review, $size = 0)
    {
        if ($this->isUrl($review->avatar)) {
            return $review->avatar;
        }
        return $this->fallbackUrl($review, $size);
    }

    /**
     * @param mixed $url
     * @return bool
     */
    protected function isUrl($url)
    {
        return !empty(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED));
    }

    /**
     * @param int $size
     * @return int
     */
    protected function size($size = 0)
    {
        $size = Cast::toInt($size);
        if ($size < 1) {
            $size = glsr_get_option('reviews.avatars_size', static::FALLBACK_SIZE, 'int');
            $size = Helper::ifEmpty($size, static::FALLBACK_SIZE, $strict = true);
        }
        return $size * 2; // @2x
    }

    /**
     * @param string $contents
     * @param string $name
     * @return string
     */
    protected function svg($contents, $name)
    {
        $uploadsDir = wp_upload_dir();
        $baseDir = trailingslashit($uploadsDir['basedir']);
        $baseUrl = trailingslashit($uploadsDir['baseurl']);
        $pathDir = trailingslashit(glsr()->id).trailingslashit('avatars');
        $filename = sprintf('%s.svg', $name);
        $filepath = $baseDir.$pathDir.$filename;
        if (!file_exists($filepath)) {
            wp_mkdir_p($baseDir.$pathDir);
            $fp = @fopen($filepath, 'wb');
            if (false === $fp) {
                return '';
            }
            mbstring_binary_safe_encoding();
            $dataLength = strlen($contents);
            $bytesWritten = fwrite($fp, $contents);
            reset_mbstring_encoding();
            fclose($fp);
            if ($dataLength !== $bytesWritten) {
                return '';
            }
            chmod($filepath, (fileperms(ABSPATH.'index.php') & 0777 | 0644));
        }
        return set_url_scheme($baseUrl.$pathDir.$filename);
    }

    /**
     * @param string $initials
     * @return string
     */
    protected function svgContent($initials)
    {
        $colors = [
            ['background' => '#e3effb', 'color' => '#134d92'], // blue
            ['background' => '#e1f0ee', 'color' => '#125960'], // green
            ['background' => '#ffeff7', 'color' => '#ba3a80'], // pink
            ['background' => '#fcece3', 'color' => '#a14326'], // red
            ['background' => '#faf7d9', 'color' => '#da9640'], // yellow
        ];
        $colors = glsr()->filterArray('avatar/colors', $colors);
        shuffle($colors);
        $color = Cast::toArray(Arr::get($colors, 0));
        $data = wp_parse_args($color, [
            'background' => '#dcdce6',
            'color' => '#6f6f87',
            'text' => $initials,
        ]);
        return trim(glsr()->build('avatar', $data));
    }

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     * @return int|string
     */
    protected function userField($review)
    {
        if ($review->author_id) {
            $value = get_the_author_meta('user_email', $review->author_id);
        }
        if (empty($value)) {
            $value = $review->email;
        }
        return glsr()->filterString('avatar/id_or_email', $value, $review->toArray());
    }
}
