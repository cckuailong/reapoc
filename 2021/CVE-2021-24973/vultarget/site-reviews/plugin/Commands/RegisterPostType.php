<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Defaults\PostTypeColumnDefaults;
use GeminiLabs\SiteReviews\Defaults\PostTypeDefaults;
use GeminiLabs\SiteReviews\Defaults\PostTypeLabelDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class RegisterPostType implements Contract
{
    public $args;
    public $columns;

    public function __construct(array $input = [])
    {
        $input = wp_parse_args($input, [
            'labels' => glsr(PostTypeLabelDefaults::class)->defaults(),
        ]);
        $this->args = glsr(PostTypeDefaults::class)->merge($input);
        $this->columns = glsr(PostTypeColumnDefaults::class)->defaults();
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!in_array(glsr()->post_type, get_post_types(['_builtin' => true]))) {
            register_post_type(glsr()->post_type, $this->args);
            $this->setColumns();
        }
    }

    /**
     * @return void
     */
    protected function setColumns()
    {
        if (array_key_exists('category', $this->columns)) {
            $keys = array_keys($this->columns);
            $keys[array_search('category', $keys)] = 'taxonomy-'.glsr()->taxonomy;
            $this->columns = array_combine($keys, $this->columns);
        }
        if (array_key_exists('is_pinned', $this->columns)) {
            $this->columns['is_pinned'] = glsr(Builder::class)->span('<span>'.$this->columns['is_pinned'].'</span>',
                ['class' => 'pinned-icon']
            );
        }
        if (count(glsr()->retrieveAs('array', 'review_types')) < 2) {
            unset($this->columns['type']);
        }
        $columns = wp_parse_args(glsr()->retrieve('columns', []), [
            glsr()->post_type => $this->columns,
        ]);
        glsr()->store('columns', $columns);
    }
}
