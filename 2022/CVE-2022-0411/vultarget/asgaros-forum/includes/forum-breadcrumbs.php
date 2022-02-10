<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumBreadCrumbs {
    private $asgarosforum = null;
    public $breadcrumbs_level = 4;
    public $breadcrumbs_links = array();
    private $breadcrumb_position = 0;

    public function __construct($object) {
        $this->asgarosforum = $object;
    }

    public function add_breadcrumb($link, $title) {
        $this->breadcrumbs_links[] = array(
            'link'      => $link,
            'title'     => $title
        );
    }

    public function show_breadcrumbs() {
        // Ensure that this feature is not disabled.
        if (!$this->asgarosforum->options['enable_breadcrumbs']) {
            return;
        }

        // Ensure that no error is thrown.
        if ($this->asgarosforum->error !== false) {
            return;
        }

        if ($this->breadcrumbs_level >= 4) {
            $element_link = $this->asgarosforum->get_link('home');
            $element_title = $this->asgarosforum->options['forum_title'];
            $this->add_breadcrumb($element_link, $element_title);
        }

        // Define category prefix.
        $category_prefix = '';

        if ($this->asgarosforum->options['breadcrumbs_show_category']) {
            if ($this->breadcrumbs_level >= 4 && $this->asgarosforum->current_category) {
                $category_name = $this->asgarosforum->get_category_name($this->asgarosforum->current_category);

                if ($category_name) {
                    $category_prefix = $category_name.': ';
                }
            }
        }

        // Define forum breadcrumbs.
        if ($this->breadcrumbs_level >= 3 && $this->asgarosforum->parent_forum && $this->asgarosforum->parent_forum > 0) {
            $element_link = $this->asgarosforum->get_link('forum', $this->asgarosforum->parent_forum);
            $element_title = $category_prefix.esc_html(stripslashes($this->asgarosforum->parent_forum_name));
            $this->add_breadcrumb($element_link, $element_title);
            $category_prefix = '';
        }

        if ($this->breadcrumbs_level >= 2 && $this->asgarosforum->current_forum) {
            $element_link = $this->asgarosforum->get_link('forum', $this->asgarosforum->current_forum);
            $element_title = $category_prefix.esc_html(stripslashes($this->asgarosforum->current_forum_name));
            $this->add_breadcrumb($element_link, $element_title);
        }

        if ($this->breadcrumbs_level >= 1 && $this->asgarosforum->current_topic) {
            $name = stripslashes($this->asgarosforum->current_topic_name);
            $element_link = $this->asgarosforum->get_link('topic', $this->asgarosforum->current_topic);
            $element_title = esc_html($this->asgarosforum->cut_string($name));
            $this->add_breadcrumb($element_link, $element_title);
        }

        if ($this->asgarosforum->current_view === 'addpost') {
            $element_link = $this->asgarosforum->get_link('current');
            $element_title = __('Post Reply', 'asgaros-forum');
            $this->add_breadcrumb($element_link, $element_title);
        } else if ($this->asgarosforum->current_view === 'editpost') {
            $element_link = $this->asgarosforum->get_link('current');
            $element_title = __('Edit Post', 'asgaros-forum');
            $this->add_breadcrumb($element_link, $element_title);
        } else if ($this->asgarosforum->current_view === 'addtopic') {
            $element_link = $this->asgarosforum->get_link('current');
            $element_title = __('New Topic', 'asgaros-forum');
            $this->add_breadcrumb($element_link, $element_title);
        } else if ($this->asgarosforum->current_view === 'movetopic') {
            $element_link = $this->asgarosforum->get_link('current');
            $element_title = __('Move Topic', 'asgaros-forum');
            $this->add_breadcrumb($element_link, $element_title);
        }

        do_action('asgarosforum_breadcrumbs_'.$this->asgarosforum->current_view);

        // Render breadcrumbs links.
        echo '<div id="forum-breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';
            echo '<span class="screen-reader-text">'.esc_html__('Forum breadcrumbs - You are here:', 'asgaros-forum').'</span>';
            echo '<span class="breadcrumb-icon fas fa-home"></span>';

            foreach ($this->breadcrumbs_links as $element) {
                $this->render_breadcrumb($element);
            }
        echo '</div>';
    }

    public function render_breadcrumb($element) {
        $this->breadcrumb_position++;

        echo '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a itemprop="item" href="'.esc_url($element['link']).'" title="'.esc_attr($element['title']).'">';
                echo '<span itemprop="name">'.esc_html($element['title']).'</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="'.esc_attr($this->breadcrumb_position).'">';
        echo '</span>';

        echo '<span class="breadcrumb-icon fas fa-chevron-right separator"></span>';
    }
}
