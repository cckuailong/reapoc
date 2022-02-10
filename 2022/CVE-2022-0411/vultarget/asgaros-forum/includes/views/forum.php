<?php

if (!defined('ABSPATH')) exit;

// Get topics.
$topics = $this->content->get_topics($this->current_forum);
$topics_sticky = $this->content->get_sticky_topics($this->current_forum);

// Set counter.
$counter_normal = count($topics);
$counter_total = count($topics_sticky) + $counter_normal;

// Load editor.
$this->editor->showEditor('addtopic', true);

// Show pagination and menu.
echo '<div class="pages-and-menu">';
    $paginationRendering = ($counter_normal > 0) ? $this->pagination->renderPagination($this->tables->topics, $this->current_forum) : '';

    echo $paginationRendering;
    echo $this->showForumMenu();
    echo '<div class="clear"></div>';
echo '</div>';

// Render subforums.
if ($this->options['subforums_location'] == 'above' || $this->options['subforums_location'] == 'both') {
    $this->render_subforums($this->current_category, $this->current_forum);
}

if ($counter_total > 0) {
    echo '<div class="title-element" id="title-element-forum-'.esc_attr($this->current_forum).'">';
        echo esc_html__('Topics', 'asgaros-forum');
        echo '<span class="last-post-headline">'.esc_html__('Last post', 'asgaros-forum').'</span>';
    echo '</div>';

    echo '<div class="content-container" id="content-container-forum-'.esc_attr($this->current_forum).'">';
        // Show sticky topics.
        if ($topics_sticky && !$this->current_page) {
            foreach ($topics_sticky as $topic) {
                $this->render_topic_element($topic, 'topic-sticky');
            }
        }

        foreach ($topics as $topic) {
            $this->render_topic_element($topic);
        }
    echo '</div>';

    echo '<div class="pages-and-menu">';
        echo $paginationRendering;
        echo $this->showForumMenu();
        echo '<div class="clear"></div>';
    echo '</div>';
} else {
    echo '<div class="title-element" id="title-element-forum-'.esc_attr($this->current_forum).'"></div>';

    echo '<div class="content-container" id="content-container-forum-'.esc_attr($this->current_forum).'">';
        $this->render_notice(__('There are no topics yet!', 'asgaros-forum'));
    echo '</div>';
}

// Render subforums.
if ($this->options['subforums_location'] == 'below' || $this->options['subforums_location'] == 'both') {
    $this->render_subforums($this->current_category, $this->current_forum);
}
