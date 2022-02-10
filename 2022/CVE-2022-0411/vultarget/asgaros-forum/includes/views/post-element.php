<?php

if (!defined('ABSPATH')) exit;

$counter++;

// Special CSS-class for first post-element in view.
$first_post_class = ($counter == 1) ? 'first-post' : '';

// Special CSS-class for highlighted posts.
$highlight_class = '';

if (!empty($_GET['highlight_post']) && $_GET['highlight_post'] == $post->id) {
    $highlight_class = 'highlight-post';
}

// Special CSS-class for online users.
$user_online_class = ($this->online->is_user_online($post->author_id)) ? 'user-online' : '';

$user_data = get_userdata($post->author_id);

echo '<div class="post-element '.esc_attr($highlight_class).' '.esc_attr($first_post_class).'" id="postid-'.esc_attr($post->id).'">';
    echo '<div class="post-author '.esc_attr($user_online_class).'">';
        // Show avatar if activated.
        if ($this->options['enable_avatars']) {
            $avatar_size = apply_filters('asgarosforum_filter_avatar_size', 120);
            echo get_avatar($post->author_id, $avatar_size, '', '', array('force_display' => true));
        }

        echo '<div class="post-author-block-name">';
            // Show username.
            $username = apply_filters('asgarosforum_filter_post_username', $this->getUsername($post->author_id), $post->author_id);
            echo '<span class="post-username">'.$username.'</span>';

            // Mentioning name.
            if ($user_data != false) {
                $this->mentioning->render_nice_name($post->author_id);
            }
        echo '</div>';

        if ($user_data != false) {
            echo '<div class="post-author-block-meta">';
                // Show reputation badges.
                $this->render_reputation_badges($post->author_posts);

                // Show author posts counter if activated.
                if ($this->options['show_author_posts_counter']) {
                    $author_posts_i18n = number_format_i18n($post->author_posts);
                    echo '<small class="post-counter">'.sprintf(_n('%s Post', '%s Posts', absint($post->author_posts), 'asgaros-forum'), esc_html($author_posts_i18n)).'</small>';
                }

                // Show marker for topic-author.
                if ($this->current_view != 'post' && $this->options['highlight_authors'] && ($counter > 1 || $this->current_page > 0) && $topicStarter != 0 && $topicStarter == $post->author_id) {
                    echo '<small class="topic-author">'.esc_html__('Topic Author', 'asgaros-forum').'</small>';
                }

                // Show marker for banned user.
                if ($this->permissions->isBanned($post->author_id)) {
                    echo '<small class="banned">'.esc_html__('Banned', 'asgaros-forum').'</small>';
                }
            echo '</div>';

            // Show groups of user.
            $usergroups = AsgarosForumUserGroups::getUserGroupsOfUser($post->author_id, 'all', true);

            if (!empty($usergroups)) {
                echo '<div class="post-author-block-group">';
                    foreach ($usergroups as $usergroup) {
                        echo AsgarosForumUserGroups::render_usergroup_tag($usergroup);
                    }
                echo '</div>';
            }
        }

        do_action('asgarosforum_after_post_author', $post->author_id, $post->author_posts);
    echo '</div>';

    echo '<div class="post-wrapper">';
        // Post header.
        echo '<div class="forum-post-header">';
            echo '<div class="forum-post-date">';
                // Show post counter.
                if ($this->current_view != 'post') {
                    echo '<a href="'.esc_url($this->rewrite->get_post_link($post->id, $this->current_topic, ($this->current_page + 1))).'">#'.absint(($this->options['posts_per_page'] * $this->current_page) + $counter).'</a> &middot; ';
                }

                echo esc_html($this->format_date($post->date));
            echo '</div>';

            if ($this->current_view != 'post') {
                echo $this->show_post_menu($post->id, $post->author_id, $counter, $post->date);
            }
        echo '</div>';

        // Post message.
        echo '<div class="post-message">';
            // Initial escaping.
            $allowed_html = wp_kses_allowed_html('post');
            $allowed_html['iframe'] = array('width' => array(), 'height' => array(), 'src' => array(), 'frameborder' => array(), 'allowfullscreen' => array());
            $post_content = wp_kses($post->text, $allowed_html);
            $post_content = stripslashes($post_content);

            echo '<div id="post-quote-container-'.esc_attr($post->id).'" style="display: none;"><blockquote><div class="quotetitle">'.esc_html__('Quote from', 'asgaros-forum').' '.$this->getUsername($post->author_id).' '.sprintf(__('on %s', 'asgaros-forum'), $this->format_date($post->date)).'</div>'.wpautop($post_content).'</blockquote><br></div>';

            // Automatically embed contents if enabled.
            if ($this->options['embed_content']) {
                global $wp_embed;
                $post_content = $wp_embed->autoembed($post_content);
            }

            // Wrap paragraphs.
            $post_content = wpautop($post_content);

            // Render shortcodes.
            $post_content = $this->shortcode->render_post_shortcodes($post_content);

            // Create nicename-links.
            $post_content = $this->mentioning->nice_name_to_link($post_content);

            // This function has to be called at last to ensure that we dont break links to mentioned users.
            $post_content = make_clickable($post_content);

            // Apply custom filters.
            $post_content = apply_filters('asgarosforum_filter_post_content', $post_content, $post->id);

            echo $post_content;
            echo $this->uploads->show_uploaded_files($post->id, $post->uploads);

            do_action('asgarosforum_after_post_message', $post->author_id, $post->id);
        echo '</div>';

        // Show post footer when the topic is approved.
        if ($this->approval->is_topic_approved($this->current_topic)) {
            echo '<div class="post-footer">';
                $this->reactions->render_reactions_area($post->id, $post->author_id);

                echo '<div class="post-meta">';
                    if ($this->options['show_edit_date'] && (strtotime($post->date_edit) > strtotime($post->date))) {
                        echo '<span class="post-edit-date">';

                        // Show who edited a post (when the information exist in the database).
                        if ($post->author_edit) {
                            echo sprintf(__('Last edited on %s by %s', 'asgaros-forum'), $this->format_date($post->date_edit), $this->getUsername($post->author_edit));
                        } else {
                            echo sprintf(__('Last edited on %s', 'asgaros-forum'), $this->format_date($post->date_edit));
                        }

                        echo '</span>';
                    }
                echo '</div>';
            echo '</div>';

            $this->reactions->render_reactions_summary_area($post->id);
        }

        // Show signature.
        if ($this->current_view != 'post') {
            $signature = $this->get_signature($post->author_id);

            if ($signature !== false) {
                echo '<div class="signature">'.$signature.'</div>';
            }
        }
        ?>
    </div>
</div>

<?php

do_action('asgarosforum_after_post');
?>
