<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumSearch {
    private $asgarosforum = null;
    public $search_keywords_for_query = '';
    public $search_keywords_for_output = '';

    public function __construct($object) {
		$this->asgarosforum = $object;

        add_action('init', array($this, 'initialize'));
        add_action('asgarosforum_breadcrumbs_search', array($this, 'add_breadcrumbs'));
    }

    public function initialize() {
        if (!empty($_GET['keywords'])) {
            $keywords = sanitize_text_field($_GET['keywords']);
            $this->search_keywords_for_query = esc_sql($keywords);
            $this->search_keywords_for_output = stripslashes(esc_html($keywords));
        }
    }

    public function add_breadcrumbs() {
        $element_link = $this->asgarosforum->get_link('current');
        $element_title = __('Search', 'asgaros-forum');
        $this->asgarosforum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    public function show_search_input() {
        if ($this->asgarosforum->options['enable_search']) {
            echo '<div id="forum-search">';
            echo '<span class="search-icon fas fa-search"></span>';

            echo '<form method="get" action="'.esc_url($this->asgarosforum->get_link('search')).'">';

            // Workaround for broken search when using plain permalink structure.
            if (!$this->asgarosforum->rewrite->use_permalinks) {
                echo '<input name="view" type="hidden" value="search">';
            }

            // Workaround for broken search in posts when using plain permalink structure.
            if (!empty($_GET['p'])) {
                $value = sanitize_key($_GET['p']);
                echo '<input name="p" type="hidden" value="'.esc_attr($value).'">';
            }

            // Workaround for broken search in pages when using plain permalink structure.
            if (!empty($_GET['page_id'])) {
                $value = sanitize_key($_GET['page_id']);
                echo '<input name="page_id" type="hidden" value="'.esc_attr($value).'">';
            }

            echo '<input name="keywords" type="search" placeholder="'.esc_html__('Search ...', 'asgaros-forum').'" value="'.esc_attr($this->search_keywords_for_output).'">';
            echo '</form>';
            echo '</div>';
        }
    }

    public function show_search_results() {
        $results = $this->get_search_results();

        $paginationRendering = ($results) ? '<div class="pages-and-menu">'.$this->asgarosforum->pagination->renderPagination('search').'<div class="clear"></div></div>' : '';
        echo $paginationRendering;

        echo '<div class="title-element">';
            echo esc_html__('Search results:', 'asgaros-forum').' '.esc_html($this->search_keywords_for_output);
            echo '<span class="last-post-headline">'.esc_html__('Last post', 'asgaros-forum').'</span>';
        echo '</div>';
        echo '<div class="content-container">';

        if ($results) {
            foreach ($results as $topic) {
                $this->asgarosforum->render_topic_element($topic, 'topic-normal', true);
            }
        } else {
            $notice = __('No results found for:', 'asgaros-forum').'&nbsp;<b>'.$this->search_keywords_for_output.'</b>';
            $this->asgarosforum->render_notice($notice);
        }

        echo '</div>';

        echo $paginationRendering;
    }

    public function get_search_results() {
        if (!empty($this->search_keywords_for_query)) {
            $categories = $this->asgarosforum->content->get_categories();
            $categoriesFilter = array();

            foreach ($categories as $category) {
                $categoriesFilter[] = $category->term_id;
            }

            // Do not execute a search-query when no categories are accessible.
            if (empty($categoriesFilter)) {
                return false;
            }

            $where = 'AND f.parent_id IN ('.implode(',', $categoriesFilter).')';

            $start = $this->asgarosforum->current_page * $this->asgarosforum->options['topics_per_page'];
            $end = $this->asgarosforum->options['topics_per_page'];
            $limit = $this->asgarosforum->db->prepare("LIMIT %d, %d", $start, $end);

            $shortcodeSearchFilter = $this->asgarosforum->shortcode->shortcodeSearchFilter;

            $match_name = "MATCH (name) AGAINST ('{$this->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $match_text = "MATCH (text) AGAINST ('{$this->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $query_answers = "SELECT (COUNT(*) - 1) FROM {$this->asgarosforum->tables->posts} WHERE parent_id = t.id";
            $query_match_name = "SELECT id AS topic_id, {$match_name} AS score_name, 0 AS score_text FROM {$this->asgarosforum->tables->topics} WHERE {$match_name} GROUP BY topic_id";
            $query_match_text = "SELECT parent_id AS topic_id, 0 AS score_name, {$match_text} AS score_text FROM {$this->asgarosforum->tables->posts} WHERE {$match_text} GROUP BY topic_id";

            $query = "SELECT t.*, f.id AS forum_id, f.name AS forum_name, ({$query_answers}) AS answers, su.topic_id, SUM(su.score_name + su.score_text) AS score FROM ({$query_match_name} UNION {$query_match_text}) AS su, {$this->asgarosforum->tables->topics} AS t, {$this->asgarosforum->tables->forums} AS f WHERE su.topic_id = t.id AND t.parent_id = f.id AND t.approved = 1 {$where} {$shortcodeSearchFilter} GROUP BY su.topic_id ORDER BY score DESC, su.topic_id DESC {$limit}";

            $results = $this->asgarosforum->db->get_results($query);

            if (!empty($results)) {
                return $results;
            }
        }

        return false;
    }
}
