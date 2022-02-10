<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode as Shortcode;

class SiteReviewsBlock extends Block
{
    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'assigned_to' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_posts' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'assigned_users' => [
                'default' => '',
                'type' => 'string',
            ],
            'category' => [
                'default' => '',
                'type' => 'string',
            ],
            'className' => [
                'default' => '',
                'type' => 'string',
            ],
            'display' => [
                'default' => 5,
                'type' => 'number',
            ],
            'hide' => [
                'default' => '',
                'type' => 'string',
            ],
            'id' => [
                'default' => '',
                'type' => 'string',
            ],
            'pagination' => [
                'default' => '',
                'type' => 'string',
            ],
            'post_id' => [
                'default' => '',
                'type' => 'string',
            ],
            'rating' => [
                'default' => 0,
                'type' => 'number',
            ],
            'schema' => [
                'default' => false,
                'type' => 'boolean',
            ],
            'terms' => [
                'default' => '',
                'type' => 'string',
            ],
            'type' => [
                'default' => 'local',
                'type' => 'string',
            ],
            'user' => [
                'default' => '',
                'type' => 'string',
            ],
        ];
    }

    /**
     * @return string
     */
    public function render(array $attributes)
    {
        $attributes['class'] = $attributes['className'];
        $shortcode = glsr(Shortcode::class);
        if ('edit' == filter_input(INPUT_GET, 'context')) {
            $attributes = $this->normalize($attributes);
            $this->filterReviewLinks();
            $this->filterShowMoreLinks('content');
            $this->filterShowMoreLinks('response');
            if (!$this->hasVisibleFields($shortcode, $attributes)) {
                $attributes['pagination'] = false;
                $this->filterInterpolation();
            }
        }
        return $shortcode->buildBlock($attributes);
    }

    /**
     * @return void
     */
    protected function filterInterpolation()
    {
        add_filter('site-reviews/interpolate/reviews', function ($context) {
            $context['class'] = 'glsr-block-disabled';
            $context['reviews'] = _x('You have hidden all of the fields for this block.', 'admin-text', 'site-reviews');
            return $context;
        });
    }

    /**
     * @return void
     */
    protected function filterReviewLinks()
    {
        add_filter('site-reviews/rendered/template/reviews', function ($template) {
            $template = str_replace('<a', '<a tabindex="-1"', $template);
            $template = str_replace('page-numbers', 'page-numbers components-button is-secondary', $template);
            return $template;
        });
    }

    /**
     * @param string $field
     * @return void
     */
    protected function filterShowMoreLinks($field)
    {
        add_filter('site-reviews/review/wrap/'.$field, function ($value) {
            $value = preg_replace(
                '/(.*)(<span class="glsr-hidden)(.*)(<\/span>)(.*)/us',
                '$1... <a href="#" class="glsr-read-more" tabindex="-1">'.__('Show more', 'site-reviews').'</a>$5',
                $value
            );
            return $value;
        });
    }
}
