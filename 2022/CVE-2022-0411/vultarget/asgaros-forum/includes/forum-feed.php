<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumFeed {
    private $asgarosforum = null;

    public function __construct($object) {
        $this->asgarosforum = $object;

        add_action('asgarosforum_wp_head', array($this, 'add_feed_link'));
        add_action('asgarosforum_bottom_navigation', array($this, 'show_feed_navigation'), 20, 1);
        add_action('asgarosforum_prepare_topic', array($this, 'render_feed'));
        add_action('asgarosforum_prepare_forum', array($this, 'render_feed'));
    }

    public function add_feed_link() {
        if ($this->asgarosforum->options['enable_rss']) {
            switch($this->asgarosforum->current_view) {
                case 'topic':
                    $title = $this->asgarosforum->current_topic_name.' &#8211; '.$this->asgarosforum->options['forum_title'];
                    $link = $this->asgarosforum->rewrite->get_link('topic', $this->asgarosforum->current_topic, array('showfeed' => 'rss2'));
                    echo '<link rel="alternate" type="application/rss+xml" title="'.esc_attr($title).'" href="'.esc_url($link).'" />'.PHP_EOL;
                break;
                case 'forum':
                    $title = $this->asgarosforum->current_forum_name.' &#8211; '.$this->asgarosforum->options['forum_title'];
                    $link = $this->asgarosforum->rewrite->get_link('forum', $this->asgarosforum->current_forum, array('showfeed' => 'rss2'));
                    echo '<link rel="alternate" type="application/rss+xml" title="'.esc_attr($title).'" href="'.esc_url($link).'" />'.PHP_EOL;
                break;
            }
        }
    }

    public function show_feed_navigation($current_view) {
        if ($this->asgarosforum->options['enable_rss']) {
            switch($current_view) {
                case 'topic':
                    $link = $this->asgarosforum->rewrite->get_link('topic', $this->asgarosforum->current_topic, array('showfeed' => 'rss2'));
                    echo '<span class="fas fa-rss"></span>';
                    echo '<a href="'.esc_url($link).'" target="_blank">'.esc_html__('RSS Feed', 'asgaros-forum').'</a>';
                break;
                case 'forum':
                    $link = $this->asgarosforum->rewrite->get_link('forum', $this->asgarosforum->current_forum, array('showfeed' => 'rss2'));
                    echo '<span class="fas fa-rss"></span>';
                    echo '<a href="'.esc_url($link).'" target="_blank">'.esc_html__('RSS Feed', 'asgaros-forum').'</a>';
                break;
            }
        }
    }

    public function render_feed() {
        if ($this->asgarosforum->options['enable_rss'] && !empty($_GET['showfeed'])) {
            // Abort feed creation when an error occured.
            if ($this->asgarosforum->error !== false) {
                return;
            }

            header('Content-Type: '.feed_content_type('rss2').'; charset='.get_option('blog_charset'), true);

            echo '<?xml version="1.0" encoding="'.esc_attr(get_option('blog_charset')).'"?>'.PHP_EOL;
            echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;
            echo '<channel>'.PHP_EOL;

            if ($this->asgarosforum->current_view === 'forum') {
                echo '<title>'.esc_html(stripslashes($this->asgarosforum->current_forum_name)).'</title>'.PHP_EOL;
                echo '<link>'.esc_url($this->asgarosforum->rewrite->get_link('forum', absint($this->asgarosforum->current_forum))).'</link>'.PHP_EOL;
            } else if ($this->asgarosforum->current_view === 'topic') {
                echo '<title>'.esc_html(stripslashes($this->asgarosforum->current_topic_name)).'</title>'.PHP_EOL;
                echo '<link>'.esc_url($this->asgarosforum->rewrite->get_link('topic', absint($this->asgarosforum->current_topic))).'</link>'.PHP_EOL;
            }

            echo '<description>'.esc_html($this->asgarosforum->current_description).'</description>'.PHP_EOL;
            echo '<language>'.esc_html(get_bloginfo('language')).'</language>'.PHP_EOL;
            echo '<lastBuildDate>'.esc_html(mysql2date('D, d M Y H:i:s +0000', gmdate('Y-m-d H:i:s'), false)).'</lastBuildDate>'.PHP_EOL;
            echo '<generator>Asgaros Forum</generator>'.PHP_EOL;
            echo '<ttl>60</ttl>'.PHP_EOL;
            echo '<atom:link href="'.esc_url($this->asgarosforum->rewrite->get_link('current')).'" rel="self" type="application/rss+xml" />'.PHP_EOL;

            $feed_data = false;

            if ($this->asgarosforum->current_view === 'forum') {
                $query_post_content = "SELECT p.text FROM {$this->asgarosforum->tables->posts} AS p WHERE p.parent_id = t.id ORDER BY p.id ASC LIMIT 1";
                $query_post_date = "SELECT p.date FROM {$this->asgarosforum->tables->posts} AS p WHERE p.parent_id = t.id ORDER BY p.id ASC LIMIT 1";

                $feed_data = $this->asgarosforum->db->get_results("SELECT t.id, t.name, ({$query_post_content}) AS text, ({$query_post_date}) AS date, t.author_id FROM {$this->asgarosforum->tables->topics} AS t WHERE t.parent_id = {$this->asgarosforum->current_forum} AND t.approved = 1 ORDER BY t.id DESC LIMIT 0, 50;");
            } else if ($this->asgarosforum->current_view === 'topic') {
                $feed_data = $this->asgarosforum->db->get_results("SELECT p.id, p.parent_id, t.name, p.date, p.text, p.author_id FROM {$this->asgarosforum->tables->posts} AS p, {$this->asgarosforum->tables->topics} AS t WHERE p.parent_id = {$this->asgarosforum->current_topic} AND t.id = p.parent_id ORDER BY p.id DESC LIMIT 0, 50;");
            }

            if (!empty($feed_data)) {
                foreach ($feed_data as $element) {
                    echo '<item>'.PHP_EOL;
                        echo '<title>'.esc_html(stripslashes($element->name)).'</title>'.PHP_EOL;

                        if ($this->asgarosforum->current_view === 'forum') {
                            echo '<link>'.esc_url($this->asgarosforum->rewrite->get_link('topic', absint($element->id))).'</link>'.PHP_EOL;
                        } else if ($this->asgarosforum->current_view === 'topic') {
                            echo '<link>'.esc_url($this->asgarosforum->rewrite->get_post_link($element->id, absint($element->parent_id))).'</link>'.PHP_EOL;
                        }

                        echo '<pubDate>'.esc_html(mysql2date('D, d M Y H:i:s +0000', $element->date, false)).'</pubDate>'.PHP_EOL;
                        echo '<description><![CDATA['.esc_html(strip_tags($element->text)).']]></description>'.PHP_EOL;
                        echo '<dc:creator>'.esc_html($this->asgarosforum->get_plain_username($element->author_id)).'</dc:creator>'.PHP_EOL;

                        if ($this->asgarosforum->current_view === 'forum') {
                            echo '<guid isPermaLink="true">'.esc_url($this->asgarosforum->rewrite->get_link('topic', absint($element->id))).'</guid>'.PHP_EOL;
                        } else if ($this->asgarosforum->current_view === 'topic') {
                            echo '<guid isPermaLink="true">'.esc_url($this->asgarosforum->rewrite->get_post_link($element->id, $element->parent_id)).'</guid>'.PHP_EOL;
                        }
                    echo '</item>'.PHP_EOL;
                }
            }

            echo '</channel>'.PHP_EOL;
            echo '</rss>'.PHP_EOL;

            exit;
        }
    }
}
