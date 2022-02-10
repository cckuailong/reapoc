<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumActivity {
    private $asgarosforum = null;

    public function __construct($object) {
        $this->asgarosforum = $object;

        add_action('asgarosforum_breadcrumbs_activity', array($this, 'add_breadcrumbs'));
    }

    public function functionality_enabled() {
        return $this->asgarosforum->options['enable_activity'];
    }

    public function add_breadcrumbs() {
        $element_link = $this->asgarosforum->get_link('activity');
        $element_title = __('Activity', 'asgaros-forum');
        $this->asgarosforum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    public function show_activity() {
        $activity_days = (int) $this->asgarosforum->options['activity_days'];
        $activity_days_i18n = number_format_i18n($activity_days);

		$activity_days_singular = __('Activity in the last day.', 'asgaros-forum');
		$activity_days_plural = sprintf(__('Activity in the last %s days.', 'asgaros-forum'), $activity_days_i18n);
		$activity_days_string = ($activity_days === 1) ? $activity_days_singular : $activity_days_plural;

        echo '<div class="main-description">'.sprintf($activity_days_string, $activity_days_i18n).'</div>';

        $pagination_rendering = $this->asgarosforum->pagination->renderPagination('activity');
        $paginationRendering = ($pagination_rendering) ? '<div class="pages-and-menu">'.$pagination_rendering.'<div class="clear"></div></div>' : '';
        echo $paginationRendering;

        $data = $this->load_activity_data();

        if (!empty($data)) {
            $date_today = gmdate($this->asgarosforum->date_format);
            $date_yesterday = gmdate($this->asgarosforum->date_format, strtotime('-1 days'));
            $last_time = false;
            $first_group = true;

            foreach ($data as $activity) {
                $current_time = gmdate($this->asgarosforum->date_format, strtotime($activity->date));
                $human_time_diff = $this->asgarosforum->get_activity_timestamp($activity->date, 'relative');

                if ($current_time == $date_today) {
                    $current_time = __('Today', 'asgaros-forum');
                } else if ($current_time == $date_yesterday) {
                    $current_time = __('Yesterday', 'asgaros-forum');
                } else {
                    $current_time = $human_time_diff;
                }

                if ($last_time != $current_time) {
                    $last_time = $current_time;

                    if ($first_group) {
                        $first_group = false;
                    } else {
                        echo '</div>';
                    }

                    echo '<div class="title-element">'.esc_html($current_time).'</div>';
                    echo '<div class="content-container">';
                }

                $name_author = $this->asgarosforum->getUsername($activity->author_id);
                $name_topic = esc_html(stripslashes($activity->name));
                $read_status = $this->asgarosforum->unread->get_status_post($activity->id, $activity->author_id, $activity->date, $activity->parent_id);

                if ($this->asgarosforum->is_first_post($activity->id, $activity->parent_id)) {
                    $link = $this->asgarosforum->get_link('topic', $activity->parent_id);
                    $link_html = '<a href="'.$link.'">'.$name_topic.'</a>';
                    echo '<div class="content-element activity-element">';
                    echo '<span class="activity-icon fas fa-comments '.esc_attr($read_status).'"></span>';
                    echo sprintf(__('New topic %s created by %s.', 'asgaros-forum'), $link_html, $name_author).' <i class="activity-time">'.esc_html($this->asgarosforum->get_activity_timestamp($activity->date)).'</i>';
                    echo '</div>';
                } else {
                    $link = $this->asgarosforum->rewrite->get_post_link($activity->id, $activity->parent_id);
                    $link_html = '<a href="'.$link.'">'.$name_topic.'</a>';
                    echo '<div class="content-element activity-element">';
                    echo '<span class="activity-icon fas fa-comment '.esc_attr($read_status).'"></span>';
                    echo sprintf(__('%s replied in %s.', 'asgaros-forum'), $name_author, $link_html).' <i class="activity-time">'.esc_html($this->asgarosforum->get_activity_timestamp($activity->date)).'</i>';
                    echo '</div>';
                }
            }

            echo '</div>';
        } else {
            echo '<div class="title-element"></div>';
            echo '<div class="content-container">';
            $this->asgarosforum->render_notice(__('No activity yet!', 'asgaros-forum'));
            echo '</div>';
        }

        echo $paginationRendering;
    }

    public function load_activity_data($count_all = false) {
        $ids_categories = $this->asgarosforum->content->get_categories_ids();

        if (empty($ids_categories)) {
            return false;
        } else {
            $ids_categories = implode(',', $ids_categories);

            // Calculate activity end-time.
            $time_current = time();
            $time_end = $time_current - ((int) $this->asgarosforum->options['activity_days'] * 24 * 60 * 60);
            $time_end = gmdate('Y-m-d H:i:s', $time_end);

            if ($count_all) {
                return $this->asgarosforum->db->get_var("SELECT COUNT(*) FROM {$this->asgarosforum->tables->posts} p, {$this->asgarosforum->tables->topics} t, (SELECT id FROM {$this->asgarosforum->tables->forums} WHERE parent_id IN ({$ids_categories})) f WHERE p.parent_id = t.id AND t.parent_id = f.id AND t.approved = 1 AND p.date > '{$time_end}';");
            } else {
                $start = $this->asgarosforum->current_page * $this->asgarosforum->options['activities_per_page'];
                $end = $this->asgarosforum->options['activities_per_page'];

                return $this->asgarosforum->db->get_results("SELECT p.id, p.parent_id, p.date, p.author_id, t.name FROM {$this->asgarosforum->tables->posts} p, {$this->asgarosforum->tables->topics} t, (SELECT id FROM {$this->asgarosforum->tables->forums} WHERE parent_id IN ({$ids_categories})) f WHERE p.parent_id = t.id AND t.parent_id = f.id AND t.approved = 1 AND p.date > '{$time_end}' ORDER BY p.id DESC LIMIT {$start}, {$end};");
            }
        }
    }

    public function show_activity_link() {
        if ($this->functionality_enabled()) {
            $activity_link = $this->asgarosforum->get_link('activity');

            return array(
                'menu_class'        => 'activity-link',
                'menu_link_text'    => esc_html__('Activity', 'asgaros-forum'),
                'menu_url'          => $activity_link,
                'menu_login_status' => 0,
                'menu_new_tab'      => false
            );
        }
    }
}
