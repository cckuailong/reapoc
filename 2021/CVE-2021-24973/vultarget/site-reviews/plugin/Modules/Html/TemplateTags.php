<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\TemplateTagsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class TemplateTags
{
    /**
     * @return string
     */
    public function description(array $args = [])
    {
        $tags = $this->filteredTags($args);
        array_walk($tags, function (&$description, $tag) {
            $description = sprintf('<code>{%s}</code> %s', $tag, $description);
        });
        return implode('<br>', $tags);
    }

    /**
     * @return string
     */
    public function tagList(array $args = [])
    {
        $tags = array_keys($this->filteredTags($args));
        array_walk($tags, function (&$tag) {
            $tag = sprintf('<li><code>{%s}</code></li>', $tag);
        });
        return sprintf('<ul>%s</ul>', implode('', $tags));
    }

    /**
     * @return array
     */
    public function tags(Review $review, array $args = [])
    {
        $tags = $this->filteredTags($args);
        array_walk($tags, function (&$content, $tag) use ($review) {
            $content = ''; // remove the tag description first!
            $method = Helper::buildMethodName($tag.'_tag');
            if (method_exists($this, $method)) {
                $content = call_user_func([$this, $method], $review);
            }
            $content = glsr()->filterString('notification/tag/'.$tag, $content, $review);
        });
        return $tags;
    }

    /**
     * @return string
     */
    protected function adminEmailTag()
    {
        return get_bloginfo('admin_email');
    }

    /**
     * @return array
     */
    protected function filteredTags(array $args)
    {
        $exclude = Arr::consolidate(Arr::get($args, 'exclude'));
        $include = Arr::consolidate(Arr::get($args, 'include'));
        $tags = glsr(TemplateTagsDefaults::class)->defaults();
        if (!empty($exclude)) {
            $tags = array_diff_key($tags, array_flip($exclude));
        }
        if (!empty($include)) {
            $tags = array_intersect_key($tags, array_flip($include));
        }
        return $tags;
    }

    /**
     * @return string
     */
    protected function reviewAssignedPostsTag(Review $review)
    {
        $posts = $review->assignedPosts();
        $postTitles = array_filter(wp_list_pluck($posts, 'post_title'));
        return Str::naturalJoin($postTitles);
    }

    /**
     * @return string
     */
    protected function reviewAssignedUsersTag(Review $review)
    {
        $users = $review->assignedUsers();
        $userNames = array_filter(wp_list_pluck($users, 'display_name'));
        return Str::naturalJoin($userNames);
    }

    /**
     * @return string
     */
    protected function reviewAuthorTag(Review $review)
    {
        return $review->author ?: __('Anonymous', 'site-reviews');
    }

    /**
     * @return string
     */
    protected function reviewCategoriesTag(Review $review)
    {
        $terms = $review->assignedTerms();
        $termNames = array_filter(wp_list_pluck($terms, 'name'));
        return Str::naturalJoin($termNames);
    }

    /**
     * @return string
     */
    protected function reviewContentTag(Review $review)
    {
        return $review->content;
    }

    /**
     * @return string
     */
    protected function reviewEmailTag(Review $review)
    {
        return $review->email;
    }

    /**
     * @return string
     */
    protected function reviewIpTag(Review $review)
    {
        return $review->ip_address;
    }

    /**
     * @return string
     */
    protected function reviewLinkTag(Review $review)
    {
        return glsr(Builder::class)->a([
            'href' => admin_url('post.php?post='.$review->ID.'&action=edit'),
            'text' => _x('View the review in WordPress &rarr;', 'admin-text', 'site-reviews'),
        ]);
    }

    /**
     * @return string
     */
    protected function reviewRatingTag(Review $review)
    {
        return Cast::toString($review->rating);
    }

    /**
     * @return string
     */
    protected function reviewTitleTag(Review $review)
    {
        return $review->title;
    }

    /**
     * @return string
     */
    protected function siteTitleTag()
    {
        return wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    }

    /**
     * @return string
     */
    protected function siteUrlTag()
    {
        return get_bloginfo('url');
    }
}
