<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumPagination {
    private $asgarosforum = null;

    public function __construct($object) {
        $this->asgarosforum = $object;
    }

    public function renderTopicOverviewPagination($topic_id, $topic_counter) {
        $num_pages = ceil($topic_counter / $this->asgarosforum->options['posts_per_page']);

        // Only show pagination when there is more than one page.
        if ($num_pages > 1) {
            echo '&nbsp;&middot;&nbsp;<div class="pages">';

            if ($num_pages <= 5) {
                for ($i = 1; $i <= $num_pages; $i++) {
                    $this->render_page_link('topic', $topic_id, $i);
                }
            } else {
                for ($i = 1; $i <= 3; $i++) {
                    $this->render_page_link('topic', $topic_id, $i);
                }

                $link = $this->asgarosforum->get_link('topic', $topic_id, array('part' => $num_pages));
                echo '<a href="'.esc_url($link).'">'.esc_html(_x('Last', 'Last topic', 'asgaros-forum')).'&nbsp;&raquo;</a>';
            }

            echo '</div>';
        }
    }

    public function render_page_link($location, $id, $page) {
        $link = $this->asgarosforum->get_link($location, $id, array('part' => $page));

        echo '<a href="'.esc_url($link).'">'.esc_html(number_format_i18n($page)).'</a>';
    }

    public function renderPagination($location, $sourceID = false, $element_counter = false) {
        $current_page = $this->asgarosforum->current_page;
        $num_pages = 0;
        $select_url = $this->asgarosforum->get_link('current', false, false, '', false);

        if ($location == $this->asgarosforum->tables->posts) {
            $count = $this->asgarosforum->db->get_var($this->asgarosforum->db->prepare("SELECT COUNT(*) FROM {$location} WHERE parent_id = %d;", $sourceID));
            $num_pages = ceil($count / $this->asgarosforum->options['posts_per_page']);
        } else if ($location == $this->asgarosforum->tables->topics) {
            $count = $this->asgarosforum->db->get_var($this->asgarosforum->db->prepare("SELECT COUNT(*) FROM {$location} WHERE parent_id = %d AND approved = 1 AND sticky = 0;", $sourceID));
            $num_pages = ceil($count / $this->asgarosforum->options['topics_per_page']);
        } else if ($location === 'search') {
            $categories = $this->asgarosforum->content->get_categories();
            $categoriesFilter = array();

            foreach ($categories as $category) {
                $categoriesFilter[] = $category->term_id;
            }

            $where = 'AND f.parent_id IN ('.implode(',', $categoriesFilter).')';
            $shortcodeSearchFilter = $this->asgarosforum->shortcode->shortcodeSearchFilter;

            $query_match_name = "SELECT id AS topic_id FROM {$this->asgarosforum->tables->topics} WHERE MATCH (name) AGAINST ('{$this->asgarosforum->search->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $query_match_text = "SELECT parent_id AS topic_id FROM {$this->asgarosforum->tables->posts} WHERE MATCH (text) AGAINST ('{$this->asgarosforum->search->search_keywords_for_query}*' IN BOOLEAN MODE)";
            $count = $this->asgarosforum->db->get_var("SELECT COUNT(*) FROM (({$query_match_name}) UNION ({$query_match_text})) AS su, {$this->asgarosforum->tables->topics} AS t, {$this->asgarosforum->tables->forums} AS f WHERE su.topic_id = t.id AND t.parent_id = f.id AND t.approved = 1 {$where} {$shortcodeSearchFilter};");
            $count = (int) $count;
            $num_pages = ceil($count / $this->asgarosforum->options['topics_per_page']);
        } else if ($location === 'members') {
            $count = count($this->asgarosforum->memberslist->memberslist);
            $num_pages = ceil($count / $this->asgarosforum->options['members_per_page']);
        } else if ($location === 'activity') {
            $count = $this->asgarosforum->activity->load_activity_data(true);
            $num_pages = ceil($count / $this->asgarosforum->options['activities_per_page']);
        } else if ($location === 'history') {
            $user_id = $this->asgarosforum->current_element;
            $count = $this->asgarosforum->profile->count_post_history_by_user($user_id);
            $num_pages = ceil($count / 50);
        } else if ($location === 'unread') {
            $num_pages = ceil($element_counter / 50);
        } else if ($location === 'unapproved') {
            $num_pages = ceil($element_counter / 50);
        }

        // Only show pagination when there is more than one page.
        if ($num_pages > 1) {
            $out = '<div class="pages">';

            if ($num_pages <= 5) {
                for ($i = 1; $i <= $num_pages; $i++) {
                    if ($i == ($current_page + 1)) {
                        $out .= '<strong>'.number_format_i18n($i).'</strong>';
                    } else {
                        $link = add_query_arg('part', $i, $select_url);
                        $out .= '<a href="'.$link.'">'.number_format_i18n($i).'</a>';
                    }
                }
            } else {
                if ($current_page >= 3) {
                    $link = remove_query_arg('part', $select_url);
                    $out .= '<a href="'.$link.'">&laquo;&nbsp;'.__('First', 'asgaros-forum').'</a>';
                }

                for ($i = 2; $i > 0; $i--) {
                    if ((($current_page + 1) - $i) > 0) {
                        $link = add_query_arg('part', (($current_page + 1) - $i), $select_url);
                        $out .= '<a href="'.$link.'">'.number_format_i18n(($current_page + 1) - $i).'</a>';
                    }
                }

                $out .= '<strong>'.number_format_i18n($current_page + 1).'</strong>';

                for ($i = 1; $i <= 2; $i++) {
                    if ((($current_page + 1) + $i) <= $num_pages) {
                        $link = add_query_arg('part', (($current_page + 1) + $i), $select_url);
                        $out .= '<a href="'.$link.'">'.number_format_i18n(($current_page + 1) + $i).'</a>';
                    }
                }

                if ($num_pages - $current_page >= 4) {
                    $link = add_query_arg('part', $num_pages, $select_url);
                    $out .= '<a href="'.$link.'">'._x('Last', 'Last Page', 'asgaros-forum').'&nbsp;&raquo;</a>';
                }
            }

            $out .= '</div>';
            return $out;
        } else {
            return false;
        }
    }
}
