<?php

if (!defined('ABSPATH')) exit;

?>
<div class="wrap" id="af-options">
    <?php
    $title = __('Settings', 'asgaros-forum');
    $titleUpdated = __('Settings updated.', 'asgaros-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <form method="post">
        <?php

        // Render nonce-field.
        wp_nonce_field('asgaros_forum_save_options');

        // Get selected tab.
        $selected_tab = 'general';

		// Parse selected tab in URL.
        if (!empty($_POST['selected_tab']) && isset($this->option_views[$_POST['selected_tab']])) {
            $selected_tab = sanitize_key($_POST['selected_tab']);
        }

        // Generate hidden input for selected tab.
        echo '<input type="hidden" name="selected_tab" value="'.esc_attr($selected_tab).'">';

        ?>

        <div id="settings-wrapper">
            <ul id="settings-tabs">
                <?php
                foreach ($this->option_views as $key => $value) {
                    $active_css = ($selected_tab == $key) ? 'class="active-tab"' : '';

                    echo '<li data-slug="'.esc_attr($key).'" '.$active_css.'>';
                    echo '<a href="#asgaros-panel">';
                    echo '<i class="'.esc_attr($value['icon']).'"></i>';
                    echo '<span>'.esc_html($value['label']).'</span>';
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>

            <div id="tab-content">
                <?php $display = ($selected_tab == 'general') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-general" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('general'); ?>
                    <table>
                        <tr>
                            <th><label for="forum_title"><?php esc_html_e('Forum title:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="forum_title" id="forum_title" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['forum_title'])); ?>"></td>
                        </tr>

                        <tr>
                            <th>
                                <label for="forum_description"><?php esc_html_e('Forum description:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('The description is used for meta tags.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input class="regular-text" type="text" name="forum_description" id="forum_description" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['forum_description'])); ?>"></td>
                        </tr>

                        <tr>
                            <th><label for="location"><?php esc_html_e('Forum location:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <?php
                                // Set a post_status argument because of a core bug. See: https://core.trac.wordpress.org/ticket/8592
                                wp_dropdown_pages(array('selected' => esc_attr($this->asgarosforum->options['location']), 'name' => 'location', 'id' => 'location', 'post_status' => array('publish', 'pending', 'draft', 'private')));
                                echo '<span class="description">'.esc_html__('Page which contains the [forum]-shortcode.', 'asgaros-forum').'</span>';
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="posts_per_page"><?php esc_html_e('Replies to show per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="posts_per_page" id="posts_per_page" value="<?php echo absint($this->asgarosforum->options['posts_per_page']); ?>" size="3" min="1"></td>
                        </tr>

                        <tr>
                            <th><label for="topics_per_page"><?php esc_html_e('Topics to show per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="topics_per_page" id="topics_per_page" value="<?php echo absint($this->asgarosforum->options['topics_per_page']); ?>" size="3" min="1"></td>
                        </tr>

                        <tr>
                            <th><label for="create_blog_topics"><?php esc_html_e('Create topics for new blog posts in the following forum:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <?php
                                echo '<select name="create_blog_topics_id" id="create_blog_topics">';

                                echo '<option value="0"'.(0 == $this->asgarosforum->options['create_blog_topics_id'] ? ' selected="selected"' : '').'>'.esc_html__('Dont create topics', 'asgaros-forum').'</option>';

                                $categories = $this->asgarosforum->content->get_categories(false);

                                if ($categories) {
                                    foreach ($categories as $category) {
                                        $forums = $this->asgarosforum->get_forums($category->term_id, 0);

                                        if ($forums) {
                                            foreach ($forums as $forum) {
                                                echo '<option value="'.esc_attr($forum->id).'"'.($forum->id == $this->asgarosforum->options['create_blog_topics_id'] ? ' selected="selected"' : '').'>'.esc_html($forum->name).'</option>';

                                                if ($forum->count_subforums > 0) {
                                                    $subforums = $this->asgarosforum->get_forums($category->term_id, $forum->id);

                                                    foreach ($subforums as $subforum) {
                                                        echo '<option value="'.esc_attr($subforum->id).'"'.($subforum->id == $this->asgarosforum->options['create_blog_topics_id'] ? ' selected="selected"' : '').'>--- '.esc_html($subforum->name).'</option>';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                echo '</select>';
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="allow_shortcodes"><?php esc_html_e('Allow shortcodes in posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_shortcodes" id="allow_shortcodes" <?php checked(!empty($this->asgarosforum->options['allow_shortcodes'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="embed_content"><?php esc_html_e('Automatically embed content in posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="embed_content" id="embed_content" <?php checked(!empty($this->asgarosforum->options['embed_content'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="highlight_admin"><?php esc_html_e('Highlight administrator/moderator names', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="highlight_admin" id="highlight_admin" <?php checked(!empty($this->asgarosforum->options['highlight_admin'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="highlight_authors"><?php esc_html_e('Highlight topic authors', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="highlight_authors" id="highlight_authors" <?php checked(!empty($this->asgarosforum->options['highlight_authors'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_author_posts_counter"><?php esc_html_e('Show author posts counter', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_author_posts_counter" id="show_author_posts_counter" <?php checked(!empty($this->asgarosforum->options['show_author_posts_counter'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_description_in_forum"><?php esc_html_e('Show description in forum', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_description_in_forum" id="show_description_in_forum" <?php checked(!empty($this->asgarosforum->options['show_description_in_forum'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="require_login"><?php esc_html_e('Hide forum from logged-out users', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="require_login" id="require_login" <?php checked(!empty($this->asgarosforum->options['require_login'])); ?>></td>
                        </tr>

                        <tr>
                            <th>
                                <label for="require_login_posts"><?php esc_html_e('Hide posts from logged-out users', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('Guests can see topics but need to log in to access the posts they contain.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <input type="checkbox" name="require_login_posts" id="require_login_posts" <?php checked(!empty($this->asgarosforum->options['require_login_posts'])); ?>>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="show_login_button"><?php esc_html_e('Show login button', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_login_button" id="show_login_button" <?php checked(!empty($this->asgarosforum->options['show_login_button'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_logout_button"><?php esc_html_e('Show logout button', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_logout_button" id="show_logout_button" <?php checked(!empty($this->asgarosforum->options['show_logout_button'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_register_button"><?php esc_html_e('Show register button', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_register_button" id="show_register_button" <?php checked(!empty($this->asgarosforum->options['show_register_button'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_edit_date"><?php esc_html_e('Show edit date', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_edit_date" id="show_edit_date" <?php checked(!empty($this->asgarosforum->options['show_edit_date'])); ?>></td>
                        </tr>

                        <tr>
                            <th>
                                <label for="approval_for"><?php esc_html_e('Approval needed for new topics from:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('This setting only affects forums that require approval for new topics.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="approval_for" id="approval_for">';
                                    <option value="guests" <?php if ($this->asgarosforum->options['approval_for'] == 'guests') { echo 'selected="selected"'; } ?>><?php esc_html_e('Guests', 'asgaros-forum'); ?></option>
                                    <option value="normal" <?php if ($this->asgarosforum->options['approval_for'] == 'normal') { echo 'selected="selected"'; } ?>><?php esc_html_e('Guests & Normal Users', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="subforums_location"><?php esc_html_e('Location of subforums:', 'asgaros-forum'); ?></label>
                            </th>
                            <td>
                                <select name="subforums_location" id="subforums_location">';
                                    <option value="above" <?php if ($this->asgarosforum->options['subforums_location'] == 'above') { echo 'selected="selected"'; } ?>><?php esc_html_e('Above Topics', 'asgaros-forum'); ?></option>
                                    <option value="below" <?php if ($this->asgarosforum->options['subforums_location'] == 'below') { echo 'selected="selected"'; } ?>><?php esc_html_e('Below Topics', 'asgaros-forum'); ?></option>
                                    <option value="both" <?php if ($this->asgarosforum->options['subforums_location'] == 'both') { echo 'selected="selected"'; } ?>><?php esc_html_e('Above & Below Topics', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="activity_timestamp_format"><?php esc_html_e('Format for activity-timestamps:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('Defines if activity-timestamps are shown in its relative or actual format.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="activity_timestamp_format" id="activity_timestamp_format">';
                                    <option value="relative" <?php if ($this->asgarosforum->options['activity_timestamp_format'] == 'relative') { echo 'selected="selected"'; } ?>><?php esc_html_e('Relative', 'asgaros-forum'); ?></option>
                                    <option value="actual" <?php if ($this->asgarosforum->options['activity_timestamp_format'] == 'actual') { echo 'selected="selected"'; } ?>><?php esc_html_e('Actual', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'features') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-features" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('features'); ?>
                    <table>
                        <tr>
                            <th><label for="enable_avatars"><?php esc_html_e('Enable Avatars', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_avatars" id="enable_avatars" <?php checked(!empty($this->asgarosforum->options['enable_avatars'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="enable_reactions"><?php esc_html_e('Enable reactions', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_reactions" id="enable_reactions" <?php checked(!empty($this->asgarosforum->options['enable_reactions'])); ?>></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="reactions_show_names"><?php esc_html_e('Show usernames in reactions', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('If enabled, the names of users who have reacted to a post are shown.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="reactions_show_names" id="reactions_show_names" <?php checked(!empty($this->asgarosforum->options['reactions_show_names'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="enable_search"><?php esc_html_e('Enable search functionality', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_search" id="enable_search" <?php checked(!empty($this->asgarosforum->options['enable_search'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="enable_rss"><?php esc_html_e('Enable RSS Feeds', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_rss" id="enable_rss" <?php checked(!empty($this->asgarosforum->options['enable_rss'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="count_topic_views"><?php esc_html_e('Count topic views', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="count_topic_views" id="count_topic_views" <?php checked(!empty($this->asgarosforum->options['count_topic_views'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="show_who_is_online"><?php esc_html_e('Show who is online', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_who_is_online" id="show_who_is_online" <?php checked(!empty($this->asgarosforum->options['show_who_is_online'])); ?>></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="show_last_seen"><?php esc_html_e('Show "Last seen"', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('Shows when a user got last seen inside of his profile and in the members list. This information is only gathered and shown when the "Who is Online" functionality is enabled.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="show_last_seen" id="show_last_seen" <?php checked(!empty($this->asgarosforum->options['show_last_seen'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="show_newest_member"><?php esc_html_e('Show newest member', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_newest_member" id="show_newest_member" <?php checked(!empty($this->asgarosforum->options['show_newest_member'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="show_statistics"><?php esc_html_e('Show statistics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_statistics" id="show_statistics" <?php checked(!empty($this->asgarosforum->options['show_statistics'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="allow_guest_postings"><?php esc_html_e('Allow guest postings', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_guest_postings" id="allow_guest_postings" <?php checked(!empty($this->asgarosforum->options['allow_guest_postings'])); ?>></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="load_fontawesome"><?php esc_html_e('Load Font Awesome v5 icon library', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('You can disable loading the built-in Font Awesome v5 icon library to reduce traffic if your theme or another plugin already loads this library.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="load_fontawesome" id="load_fontawesome" <?php checked(!empty($this->asgarosforum->options['load_fontawesome'])); ?>></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="load_fontawesome_compat_v4"><?php esc_html_e('Load Font Awesome v4 compatibility library', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('The Font Awesome v4 compatibility library is required, if your theme or another plugin uses the Font Awesome v4 icon library. If the Font Awesome v4 icon library is not used on your website, you can disable this option.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="load_fontawesome_compat_v4" id="load_fontawesome_compat_v4" <?php checked(!empty($this->asgarosforum->options['load_fontawesome_compat_v4'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'urls') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-urls" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('urls'); ?>
                    <?php
                    $seo_option = checked(!empty($this->asgarosforum->options['enable_seo_urls']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_seo_urls"><?php esc_html_e('Enable SEO-friendly URLs', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_seo_urls" id="enable_seo_urls" class="show_hide_initiator" data-hide-class="seo-option" <?php checked(!empty($this->asgarosforum->options['enable_seo_urls'])); ?>></td>
                        </tr>
                        <tr class="seo-option" <?php if (!$seo_option) { echo 'style="display: none;"'; } ?>>
                            <th>
                                <label for="seo_url_mode_content"><?php esc_html_e('URL mode for forums & topics:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('Define if the slug or the ID should be used in URLs for forums and topics. This setting is useful if you encounter problems when your slugs include special characters.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="seo_url_mode_content" id="seo_url_mode_content">';
                                    <option value="slug" <?php if ($this->asgarosforum->options['seo_url_mode_content'] == 'slug') { echo 'selected="selected"'; } ?>><?php esc_html_e('Slug', 'asgaros-forum'); ?></option>
                                    <option value="id" <?php if ($this->asgarosforum->options['seo_url_mode_content'] == 'id') { echo 'selected="selected"'; } ?>><?php esc_html_e('ID', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="seo-option" <?php if (!$seo_option) { echo 'style="display: none;"'; } ?>>
                            <th>
                                <label for="seo_url_mode_profile"><?php esc_html_e('URL mode for profiles:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('Define if the slug or the ID should be used in URLs for profiles. This setting is useful if you want to hide the unique nicename of users from the public.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="seo_url_mode_profile" id="seo_url_mode_profile">';
                                    <option value="slug" <?php if ($this->asgarosforum->options['seo_url_mode_profile'] == 'slug') { echo 'selected="selected"'; } ?>><?php esc_html_e('Slug', 'asgaros-forum'); ?></option>
                                    <option value="id" <?php if ($this->asgarosforum->options['seo_url_mode_profile'] == 'id') { echo 'selected="selected"'; } ?>><?php esc_html_e('ID', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom_url_login"><?php esc_html_e('Custom Login URL', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('You can use this option if you are using a custom login page.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input class="regular-text" type="text" name="custom_url_login" id="custom_url_login" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['custom_url_login'])); ?>"></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom_url_register"><?php esc_html_e('Custom Register URL', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('You can use this option if you are using a custom register page.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input class="regular-text" type="text" name="custom_url_register" id="custom_url_register" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['custom_url_register'])); ?>"></td>
                        </tr>
                        <tr>
                            <th>
                                <label><?php esc_html_e('URL-slugs for views:', 'asgaros-forum'); ?></label>
                            </th>
                            <td>
                                <table>
                                    <tr>
                                        <th><label for="view_name_activity">activity:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_activity" id="view_name_activity" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_activity'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_subscriptions">subscriptions:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_subscriptions" id="view_name_subscriptions" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_subscriptions'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_search">search:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_search" id="view_name_search" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_search'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_forum">forum:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_forum" id="view_name_forum" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_forum'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_topic">topic:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_topic" id="view_name_topic" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_topic'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_addtopic">addtopic:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_addtopic" id="view_name_addtopic" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_addtopic'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_movetopic">movetopic:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_movetopic" id="view_name_movetopic" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_movetopic'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_addpost">addpost:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_addpost" id="view_name_addpost" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_addpost'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_editpost">editpost:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_editpost" id="view_name_editpost" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_editpost'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_markallread">markallread:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_markallread" id="view_name_markallread" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_markallread'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_members">members:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_members" id="view_name_members" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_members'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_profile">profile:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_profile" id="view_name_profile" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_profile'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_history">history:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_history" id="view_name_history" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_history'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_unread">unread:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_unread" id="view_name_unread" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_unread'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_unapproved">unapproved:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_unapproved" id="view_name_unapproved" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_unapproved'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_reports">reports:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_reports" id="view_name_reports" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['view_name_reports'])); ?>"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="title_separator"><?php esc_html_e('Title Separator', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('Allows you to define a custom title-separator for the forum. This setting is useful when different title-separators are shown in parts of the title - which is a common problem when using other SEO plugins.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input class="small-text" type="text" name="title_separator" id="title_separator" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['title_separator'])); ?>"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'permissions') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-permissions" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('permissions'); ?>
                    <table>
                        <tr>
                            <th><label for="enable_edit_post"><?php esc_html_e('Users can edit their own posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_edit_post" id="enable_edit_post" class="show_hide_initiator" data-hide-class="edit-post-option" <?php checked(!empty($this->asgarosforum->options['enable_edit_post'])); ?>></td>
                        </tr>

                        <?php
                        $edit_post_option = checked(!empty($this->asgarosforum->options['enable_edit_post']), true, false);
                        ?>

                        <tr class="edit-post-option" <?php if (!$edit_post_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="time_limit_edit_posts"><?php esc_html_e('Time limitation for editing posts (in minutes):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="time_limit_edit_posts" id="time_limit_edit_posts" value="<?php echo absint($this->asgarosforum->options['time_limit_edit_posts']); ?>" size="3" min="0">
                                <span class="description"><?php esc_html_e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="enable_delete_post"><?php esc_html_e('Users can delete their own posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_delete_post" id="enable_delete_post" class="show_hide_initiator" data-hide-class="delete-post-option" <?php checked(!empty($this->asgarosforum->options['enable_delete_post'])); ?>></td>
                        </tr>

                        <?php
                        $delete_post_option = checked(!empty($this->asgarosforum->options['enable_delete_post']), true, false);
                        ?>

                        <tr class="delete-post-option" <?php if (!$delete_post_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="time_limit_delete_posts"><?php esc_html_e('Time limitation for deleting posts (in minutes):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="time_limit_delete_posts" id="time_limit_delete_posts" value="<?php echo absint($this->asgarosforum->options['time_limit_delete_posts']); ?>" size="3" min="0">
                                <span class="description"><?php esc_html_e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="enable_delete_topic"><?php esc_html_e('Users can delete their own topics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_delete_topic" id="enable_delete_topic" class="show_hide_initiator" data-hide-class="delete-topic-option" <?php checked(!empty($this->asgarosforum->options['enable_delete_topic'])); ?>></td>
                        </tr>

                        <?php
                        $delete_topic_option = checked(!empty($this->asgarosforum->options['enable_delete_topic']), true, false);
                        ?>

                        <tr class="delete-topic-option" <?php if (!$delete_topic_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="time_limit_delete_topics"><?php esc_html_e('Time limitation for deleting topics (in minutes):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="time_limit_delete_topics" id="time_limit_delete_topics" value="<?php echo absint($this->asgarosforum->options['time_limit_delete_topics']); ?>" size="3" min="0">
                                <span class="description"><?php esc_html_e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="enable_open_topic"><?php esc_html_e('Users can open their own topics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_open_topic" id="enable_open_topic" <?php checked(!empty($this->asgarosforum->options['enable_open_topic'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="enable_close_topic"><?php esc_html_e('Users can close their own topics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_close_topic" id="enable_close_topic" <?php checked(!empty($this->asgarosforum->options['enable_close_topic'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'breadcrumbs') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-breadcrumbs" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('breadcrumbs'); ?>
                    <?php
                    $breadcrumbs_option = checked(!empty($this->asgarosforum->options['enable_breadcrumbs']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_breadcrumbs"><?php esc_html_e('Enable breadcrumbs', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_breadcrumbs" id="enable_breadcrumbs" class="show_hide_initiator" data-hide-class="breadcrumbs-option" <?php checked(!empty($this->asgarosforum->options['enable_breadcrumbs'])); ?>></td>
                        </tr>
                        <tr class="breadcrumbs-option" <?php if (!$breadcrumbs_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="breadcrumbs_show_category"><?php esc_html_e('Show category name in breadcrumbs', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="breadcrumbs_show_category" id="breadcrumbs_show_category" <?php checked(!empty($this->asgarosforum->options['breadcrumbs_show_category'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'notifications') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-notifications" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('notifications'); ?>
                    <table>
                        <tr>
                            <th><label for="notification_sender_name"><?php esc_html_e('Sender name:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="notification_sender_name" id="notification_sender_name" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['notification_sender_name'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="notification_sender_mail"><?php esc_html_e('Sender mail:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="notification_sender_mail" id="notification_sender_mail" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['notification_sender_mail'])); ?>"></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="receivers_admin_notifications"><?php esc_html_e('Receivers of administrative notifications:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('A comma-separated list of mail-addresses which can receive administrative notifications (new reports, unapproved topics, and more).', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input class="regular-text" type="text" name="receivers_admin_notifications" id="receivers_admin_notifications" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['receivers_admin_notifications'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="allow_subscriptions">
                                <?php esc_html_e('Enable subscriptions', 'asgaros-forum'); ?></label>
                                <span class="description"><?php esc_html_e('The subscription-functionality is only available for logged-in users.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="allow_subscriptions" id="allow_subscriptions" <?php checked(!empty($this->asgarosforum->options['allow_subscriptions'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="admin_subscriptions"><?php esc_html_e('Notify receivers of administrative notifications about new topics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="admin_subscriptions" id="admin_subscriptions" <?php checked(!empty($this->asgarosforum->options['admin_subscriptions'])); ?>></td>
                        </tr>
                        <!-- New Post Notifications -->
                        <tr>
                            <th><label for="mail_template_new_post_subject"><?php esc_html_e('New post notification subject:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="mail_template_new_post_subject" id="mail_template_new_post_subject" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['mail_template_new_post_subject'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="mail_template_new_post_message"><?php esc_html_e('New post notification message:', 'asgaros-forum'); ?></label></th>
                            <td><textarea class="large-text" rows="8" cols="80" type="text" name="mail_template_new_post_message" id="mail_template_new_post_message"><?php echo esc_attr(stripslashes($this->asgarosforum->options['mail_template_new_post_message'])); ?></textarea></td>
                        </tr>
                        <!-- New Topic Notifications -->
                        <tr>
                            <th><label for="mail_template_new_topic_subject"><?php esc_html_e('New topic notification subject:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="mail_template_new_topic_subject" id="mail_template_new_topic_subject" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['mail_template_new_topic_subject'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="mail_template_new_topic_message"><?php esc_html_e('New topic notification message:', 'asgaros-forum'); ?></label></th>
                            <td><textarea class="large-text" rows="8" cols="80" type="text" name="mail_template_new_topic_message" id="mail_template_new_topic_message"><?php echo esc_attr(stripslashes($this->asgarosforum->options['mail_template_new_topic_message'])); ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'mentioning') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-mentioning" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('mentioning'); ?>
                    <?php
                    $mentioning_option = checked(!empty($this->asgarosforum->options['enable_mentioning']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_mentioning"><?php esc_html_e('Enable Mentioning', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_mentioning" id="enable_mentioning" class="show_hide_initiator" data-hide-class="mentioning-option" <?php checked(!empty($this->asgarosforum->options['enable_mentioning'])); ?>></td>
                        </tr>
                        <tr class="mentioning-option" <?php if (!$mentioning_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="enable_mentioning_suggestions"><?php esc_html_e('Enable Suggestions', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_mentioning_suggestions" id="enable_mentioning_suggestions" <?php checked(!empty($this->asgarosforum->options['enable_mentioning_suggestions'])); ?>></td>
                        </tr>
                        <tr class="mentioning-option" <?php if (!$mentioning_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="mail_template_mentioned_subject"><?php esc_html_e('Mentioning notification subject:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="mail_template_mentioned_subject" id="mail_template_mentioned_subject" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['mail_template_mentioned_subject'])); ?>"></td>
                        </tr>
                        <tr class="mentioning-option" <?php if (!$mentioning_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="mail_template_mentioned_message"><?php esc_html_e('Mentioning notification message:', 'asgaros-forum'); ?></label></th>
                            <td><textarea class="large-text" rows="8" cols="80" type="text" name="mail_template_mentioned_message" id="mail_template_mentioned_message"><?php echo esc_html(stripslashes($this->asgarosforum->options['mail_template_mentioned_message'])); ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'memberslist') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-memberslist" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('memberslist'); ?>
                    <?php
                    $membersListOption = checked(!empty($this->asgarosforum->options['enable_memberslist']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_memberslist"><?php esc_html_e('Enable members list', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_memberslist" id="enable_memberslist" class="show_hide_initiator" data-hide-class="memberslist-option" <?php checked(!empty($this->asgarosforum->options['enable_memberslist'])); ?>></td>
                        </tr>
                        <tr class="memberslist-option" <?php if (!$membersListOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="memberslist_loggedin_only"><?php esc_html_e('Show members list to logged-in users only', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="memberslist_loggedin_only" id="memberslist_loggedin_only" <?php checked(!empty($this->asgarosforum->options['memberslist_loggedin_only'])); ?>></td>
                        </tr>
                        <tr class="memberslist-option" <?php if (!$membersListOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="memberslist_filter_siteadmins"><?php esc_html_e('Hide site-admins in memberslist', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="memberslist_filter_siteadmins" id="memberslist_filter_siteadmins" <?php checked(!empty($this->asgarosforum->options['memberslist_filter_siteadmins'])); ?>></td>
                        </tr>
                        <tr class="memberslist-option" <?php if (!$membersListOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="members_per_page"><?php esc_html_e('Members per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="members_per_page" id="members_per_page" value="<?php echo absint($this->asgarosforum->options['members_per_page']); ?>" size="3" min="1"></td>
                        </tr>
                        <tr class="memberslist-option" <?php if (!$membersListOption) { echo 'style="display: none;"'; } ?>>
                            <th><?php esc_html_e('Available filters', 'asgaros-forum'); ?></th>
                            <td>
                                <table>
                                    <tr>
                                        <th><label for="memberslist_filter_normal"><?php esc_html_e('Users', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="checkbox" name="memberslist_filter_normal" id="memberslist_filter_normal" <?php checked(!empty($this->asgarosforum->options['memberslist_filter_normal'])); ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label for="memberslist_filter_moderator"><?php esc_html_e('Moderators', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="checkbox" name="memberslist_filter_moderator" id="memberslist_filter_moderator" <?php checked(!empty($this->asgarosforum->options['memberslist_filter_moderator'])); ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label for="memberslist_filter_administrator"><?php esc_html_e('Administrators', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="checkbox" name="memberslist_filter_administrator" id="memberslist_filter_administrator" <?php checked(!empty($this->asgarosforum->options['memberslist_filter_administrator'])); ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label for="memberslist_filter_banned"><?php esc_html_e('Banned', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="checkbox" name="memberslist_filter_banned" id="memberslist_filter_banned" <?php checked(!empty($this->asgarosforum->options['memberslist_filter_banned'])); ?>></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'profiles') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-profiles" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('profiles'); ?>
                    <?php
                    $profileOption = checked(!empty($this->asgarosforum->options['enable_profiles']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_profiles"><?php esc_html_e('Enable profiles', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_profiles" id="enable_profiles" class="show_hide_initiator" data-hide-class="profile-option" <?php checked(!empty($this->asgarosforum->options['enable_profiles'])); ?>></td>
                        </tr>
                        <tr class="profile-option" <?php if (!$profileOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="hide_profiles_from_guests"><?php esc_html_e('Show profiles to logged-in users only', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="hide_profiles_from_guests" id="hide_profiles_from_guests" <?php checked(!empty($this->asgarosforum->options['hide_profiles_from_guests'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'uploads') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-uploads" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('uploads'); ?>
                    <?php
                    $uploadsOption = checked(!empty($this->asgarosforum->options['allow_file_uploads']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="allow_file_uploads"><?php esc_html_e('Allow uploads', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_file_uploads" id="allow_file_uploads" class="show_hide_initiator" data-hide-class="uploads-option" <?php echo $uploadsOption; ?>></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="uploads_show_thumbnails"><?php esc_html_e('Show thumbnails', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="uploads_show_thumbnails" id="uploads_show_thumbnails" <?php checked(!empty($this->asgarosforum->options['uploads_show_thumbnails'])); ?>></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="hide_uploads_from_guests"><?php esc_html_e('Show uploaded files to logged-in users only', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="hide_uploads_from_guests" id="hide_uploads_from_guests" <?php checked(!empty($this->asgarosforum->options['hide_uploads_from_guests'])); ?>></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="upload_permission"><?php esc_html_e('Who can upload files:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <select name="upload_permission" id="upload_permission">';
                                    <option value="everyone" <?php if ($this->asgarosforum->options['upload_permission'] == 'everyone') { echo 'selected="selected"'; } ?>><?php esc_html_e('Everyone', 'asgaros-forum'); ?></option>
                                    <option value="loggedin" <?php if ($this->asgarosforum->options['upload_permission'] == 'loggedin') { echo 'selected="selected"'; } ?>><?php esc_html_e('Logged in users only', 'asgaros-forum'); ?></option>
                                    <option value="moderator" <?php if ($this->asgarosforum->options['upload_permission'] == 'moderator') { echo 'selected="selected"'; } ?>><?php esc_html_e('Moderators only', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="allowed_filetypes"><?php esc_html_e('Allowed filetypes:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="allowed_filetypes" id="allowed_filetypes" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['allowed_filetypes'])); ?>"></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="uploads_maximum_number"><?php esc_html_e('Maximum files per post:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="uploads_maximum_number" id="uploads_maximum_number" value="<?php echo absint($this->asgarosforum->options['uploads_maximum_number']); ?>" size="3" min="0">
                                <span class="description"><?php esc_html_e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="uploads_maximum_size"><?php esc_html_e('Maximum file size (in megabyte):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="uploads_maximum_size" id="uploads_maximum_size" value="<?php echo absint($this->asgarosforum->options['uploads_maximum_size']); ?>" size="3" min="0">
                                <span class="description"><?php esc_html_e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'reports') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-reports" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('reports'); ?>
                    <?php
                    $reportsOption = checked(!empty($this->asgarosforum->options['reports_enabled']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="reports_enabled"><?php esc_html_e('Enable reports', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="reports_enabled" id="reports_enabled" class="show_hide_initiator" data-hide-class="reports-option" <?php checked(!empty($this->asgarosforum->options['reports_enabled'])); ?>></td>
                        </tr>
                        <tr class="reports-option" <?php if (!$reportsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="reports_notifications"><?php esc_html_e('Notify receivers of administrative notifications about new reports', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="reports_notifications" id="reports_notifications" <?php checked(!empty($this->asgarosforum->options['reports_notifications'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'signatures') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-signatures" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('signatures'); ?>
                    <?php
                    $signaturesOption = checked(!empty($this->asgarosforum->options['allow_signatures']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="allow_signatures"><?php esc_html_e('Enable signatures', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_signatures" id="allow_signatures" class="show_hide_initiator" data-hide-class="signatures-option" <?php checked(!empty($this->asgarosforum->options['allow_signatures'])); ?>></td>
                        </tr>
                        <tr class="signatures-option" <?php if (!$signaturesOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="signatures_permission"><?php esc_html_e('Who can use signatures:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <select name="signatures_permission" id="signatures_permission">';
                                    <option value="loggedin" <?php if ($this->asgarosforum->options['signatures_permission'] == 'loggedin') { echo 'selected="selected"'; } ?>><?php esc_html_e('Logged in users & Moderators', 'asgaros-forum'); ?></option>
                                    <option value="moderator" <?php if ($this->asgarosforum->options['signatures_permission'] == 'moderator') { echo 'selected="selected"'; } ?>><?php esc_html_e('Moderators only', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="signatures-option" <?php if (!$signaturesOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="signatures_html_allowed"><?php esc_html_e('Allow HTML tags in signatures', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="signatures_html_allowed" id="signatures_html_allowed" <?php checked(!empty($this->asgarosforum->options['signatures_html_allowed'])); ?>></td>
                        </tr>
                        <tr class="signatures-option" <?php if (!$signaturesOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="signatures_html_tags"><?php esc_html_e('Allowed HTML tags:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="signatures_html_tags" id="signatures_html_tags" value="<?php echo esc_attr(stripslashes($this->asgarosforum->options['signatures_html_tags'])); ?>"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'activity') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-activity" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('activity'); ?>
                    <?php
                    $activityOption = checked(!empty($this->asgarosforum->options['enable_activity']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_activity"><?php esc_html_e('Enable Activity Feed', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_activity" id="enable_activity" class="show_hide_initiator" data-hide-class="activity-option" <?php checked(!empty($this->asgarosforum->options['enable_activity'])); ?>></td>
                        </tr>
                        <tr class="activity-option" <?php if (!$activityOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="activity_days"><?php esc_html_e('Days of activity to show:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="activity_days" id="activity_days" value="<?php echo absint($this->asgarosforum->options['activity_days']); ?>" size="3" min="1"></td>
                        </tr>
                        <tr class="activity-option" <?php if (!$activityOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="activities_per_page"><?php esc_html_e('Activities per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="activities_per_page" id="activities_per_page" value="<?php echo absint($this->asgarosforum->options['activities_per_page']); ?>" size="3" min="1"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'polls') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-polls" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('polls'); ?>
                    <?php
                    $polls_option = checked(!empty($this->asgarosforum->options['enable_polls']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_polls"><?php esc_html_e('Enable Polls', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_polls" id="enable_polls" class="show_hide_initiator" data-hide-class="polls-option" <?php checked(!empty($this->asgarosforum->options['enable_polls'])); ?>></td>
                        </tr>

                        <tr class="polls-option" <?php if (!$polls_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="polls_results_visible"><?php esc_html_e('Results visible without vote', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="polls_results_visible" id="polls_results_visible" <?php checked(!empty($this->asgarosforum->options['polls_results_visible'])); ?>></td>
                        </tr>

                        <tr class="polls-option" <?php if (!$polls_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="polls_permission"><?php esc_html_e('Who can create polls:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <select name="polls_permission" id="polls_permission">';
                                    <option value="loggedin" <?php if ($this->asgarosforum->options['polls_permission'] == 'loggedin') { echo 'selected="selected"'; } ?>><?php esc_html_e('Logged in users only', 'asgaros-forum'); ?></option>
                                    <option value="moderator" <?php if ($this->asgarosforum->options['polls_permission'] == 'moderator') { echo 'selected="selected"'; } ?>><?php esc_html_e('Moderators only', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'spoilers') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-spoilers" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('spoilers'); ?>
                    <?php
                    $spoilers_option = checked(!empty($this->asgarosforum->options['enable_spoilers']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_spoilers"><?php esc_html_e('Enable spoilers', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_spoilers" id="enable_spoilers" class="show_hide_initiator" data-hide-class="spoilers-option" <?php checked(!empty($this->asgarosforum->options['enable_spoilers'])); ?>></td>
                        </tr>
                        <tr class="spoilers-option" <?php if (!$spoilers_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="hide_spoilers_from_guests"><?php esc_html_e('Hide spoilers from logged-out users', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="hide_spoilers_from_guests" id="hide_spoilers_from_guests" <?php checked(!empty($this->asgarosforum->options['hide_spoilers_from_guests'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'reputation') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-reputation" style="display: <?php echo esc_attr($display); ?>;">
                    <?php $this->render_options_header('reputation'); ?>
                    <?php
                    $reputation_option = checked(!empty($this->asgarosforum->options['enable_reputation']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_reputation"><?php esc_html_e('Enable Reputation', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_reputation" id="enable_reputation" class="show_hide_initiator" data-hide-class="reputation-option" <?php checked(!empty($this->asgarosforum->options['enable_reputation'])); ?>></td>
                        </tr>
                        <tr class="reputation-option" <?php if (!$reputation_option) { echo 'style="display: none;"'; } ?>>
                            <th>
                                <label><?php esc_html_e('Minimum amount of posts:', 'asgaros-forum'); ?></label>
                            </th>
                            <td>
                                <table>
                                    <tr>
                                        <th><label for="reputation_level_1_posts"><?php esc_html_e('Level 1', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="number" name="reputation_level_1_posts" id="reputation_level_1_posts" value="<?php echo absint($this->asgarosforum->options['reputation_level_1_posts']); ?>" size="3" min="1"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="reputation_level_2_posts"><?php esc_html_e('Level 2', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="number" name="reputation_level_2_posts" id="reputation_level_2_posts" value="<?php echo absint($this->asgarosforum->options['reputation_level_2_posts']); ?>" size="3" min="1"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="reputation_level_3_posts"><?php esc_html_e('Level 3', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="number" name="reputation_level_3_posts" id="reputation_level_3_posts" value="<?php echo absint($this->asgarosforum->options['reputation_level_3_posts']); ?>" size="3" min="1"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="reputation_level_4_posts"><?php esc_html_e('Level 4', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="number" name="reputation_level_4_posts" id="reputation_level_4_posts" value="<?php echo absint($this->asgarosforum->options['reputation_level_4_posts']); ?>" size="3" min="1"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="reputation_level_5_posts"><?php esc_html_e('Level 5', 'asgaros-forum'); ?>:</label></th>
                                        <td><input type="number" name="reputation_level_5_posts" id="reputation_level_5_posts" value="<?php echo absint($this->asgarosforum->options['reputation_level_5_posts']); ?>" size="3" min="1"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <input type="submit" name="af_options_submit" class="button button-primary" value="<?php esc_attr_e('Save Settings', 'asgaros-forum'); ?>">
            </div>

            <div class="clear"></div>
        </div>
    </form>
</div>
