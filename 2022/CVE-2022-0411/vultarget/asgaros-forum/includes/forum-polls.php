<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumPolls {
    private $asgarosforum = null;

    public function __construct($object) {
        $this->asgarosforum = $object;

        add_action('asgarosforum_editor_custom_content_bottom', array($this, 'editor_poll_form'), 10, 1);
        add_action('asgarosforum_after_add_topic_submit', array($this, 'editor_poll_process'), 10, 6);
        add_action('asgarosforum_after_edit_post_submit', array($this, 'editor_poll_process'), 10, 6);
        add_action('asgarosforum_prepare_topic', array($this, 'save_vote'));
        add_action('asgarosforum_after_delete_topic', array($this, 'delete_poll'), 10, 1);
    }

    public function editor_poll_form($editor_view) {
        // Cancel if poll-functionality is disabled.
        if (!$this->asgarosforum->options['enable_polls']) {
            return;
        }

        // Cancel if user is not logged-in.
        if (!is_user_logged_in()) {
            return;
        }

        // Cancel if user has no permissions to create a poll.
        if ($this->asgarosforum->options['polls_permission'] == 'moderator' && !$this->asgarosforum->permissions->isModerator('current')) {
            return;
        }

        // Set IDs.
        $post_id = $this->asgarosforum->current_post;
        $topic_id = $this->asgarosforum->current_topic;

        if ($editor_view === 'addtopic') {
            $this->poll_form_add();
        }

        if ($editor_view === 'editpost') {
            // Cancel if this is not the first post.
            if (!$this->asgarosforum->is_first_post($post_id)) {
                return;
            }

            // Try to get a poll.
            $poll = $this->get_poll($topic_id);

            if ($poll === false) {
                $this->poll_form_add();
            } else {
                $this->poll_form_edit($poll);
            }
        }
    }

    public function poll_form_add() {
        echo '<div class="editor-row">';
            echo '<span class="row-title add-poll">';
                echo '<span class="editor-row-title-icon fas fa-poll-h"></span>';
                echo esc_html__('Add Poll', 'asgaros-forum');
            echo '</span>';

            echo '<div id="poll-form" style="display: none;">';
                echo '<div id="poll-question">';
                    echo '<input class="editor-subject-input" type="text" maxlength="255" name="poll-title" placeholder="'.esc_html__('Enter your question here', 'asgaros-forum').'" value="">';
                echo '</div>';

                echo '<div id="poll-options">';
                    $this->render_empty_poll_option_container();
                    $this->render_empty_poll_option_container();
                    $this->render_empty_poll_option_container();

                    echo '<a class="poll-option-add">'.esc_html__('Add another answer', 'asgaros-forum').'</a>';

                    // Hidden container for new poll-options.
                    echo '<div id="poll-option-template" style="display: none;">';
                        $this->render_empty_poll_option_container();
                    echo '</div>';

                    echo '<div id="poll-warning">';
                        echo esc_html__('After creating a poll it will be no longer possible to add or remove answers. This ensures the integrity of existing votes. However, it will be still possible to make text-changes to existing answers and to poll-settings. If major changes for a poll are required, you must delete the existing poll and create a new one for this topic.', 'asgaros-forum');
                    echo '</div>';
                echo '</div>';

                echo '<div id="poll-settings">';
                    echo '<label class="checkbox-label">';
                        echo '<input type="checkbox" name="poll-multiple"><span>'.esc_html__('Allow multiple answers', 'asgaros-forum').'</span>';
                    echo '</label>';
                    echo '<span class="remove-poll">'.esc_html__('Remove Poll', 'asgaros-forum').'</span>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    public function render_empty_poll_option_container() {
        echo '<div class="poll-option-container">';
            echo '<div class="poll-option-input">';
                echo '<input class="editor-subject-input" type="text" maxlength="255" name="poll-option[]" placeholder="'.esc_attr__('An answer ...', 'asgaros-forum').'" value="">';
            echo '</div>';

            echo '<div class="poll-option-delete">';
                echo '<span class="fas fa-trash-alt"></span>';
            echo '</div>';
        echo '</div>';
    }

    public function poll_form_edit($poll) {
        echo '<div class="editor-row">';
            echo '<span class="row-title add-poll">';
                echo '<span class="editor-row-title-icon fas fa-poll-h"></span>';
                echo esc_html__('Add Poll', 'asgaros-forum');
            echo '</span>';

            echo '<div id="poll-form" style="display: block;">';
                echo '<div id="poll-question">';
                    echo '<input class="editor-subject-input" type="text" maxlength="255" name="poll-title" placeholder="'.esc_attr__('Enter your question here', 'asgaros-forum').'" value="'.esc_html(stripslashes($poll->title)).'">';
                echo '</div>';

                echo '<div id="poll-options">';
                    foreach ($poll->options as $option) {
                        echo '<div class="poll-option-container">';
                            echo '<div class="poll-option-input">';
                                echo '<input class="editor-subject-input" type="text" maxlength="255" name="poll-option['.esc_attr($option->id).']" placeholder="'.esc_attr__('An answer ...', 'asgaros-forum').'" value="'.esc_html(stripslashes($option->title)).'">';
                            echo '</div>';
                        echo '</div>';
                    }

                    echo '<div id="poll-warning">';
                        echo esc_html__('To ensure the integrity of existing votes it is not possible to add or remove answers. However, it is still possible to make text-changes to existing answers and to poll-settings. If major changes for a poll are required, you must delete the existing poll and create a new one for this topic.', 'asgaros-forum');
                    echo '</div>';
                echo '</div>';

                echo '<div id="poll-settings">';
                    echo '<label class="checkbox-label">';
                        echo '<input type="checkbox" name="poll-multiple" '.checked($poll->multiple, 1, false).'><span>'.esc_html__('Allow multiple answers', 'asgaros-forum').'</span>';
                    echo '</label>';
                    echo '<span class="remove-poll">'.esc_html__('Remove Poll', 'asgaros-forum').'</span>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    public function add_poll($topic_id, $title, $options, $multiple) {
        // Insert poll.
        $this->asgarosforum->db->insert(
            $this->asgarosforum->tables->polls,
            array('id' => $topic_id, 'title' => $title, 'multiple' => $multiple),
            array('%d', '%s', '%d')
        );

        // Insert poll options.
        foreach ($options as $option) {
            $this->asgarosforum->db->insert(
                $this->asgarosforum->tables->polls_options,
                array('poll_id' => $topic_id, 'title' => $option),
                array('%d', '%s')
            );
        }
    }

    public function update_poll($poll_id, $title, $options, $multiple) {
        // Update poll.
        $this->asgarosforum->db->update(
            $this->asgarosforum->tables->polls,
            array('title' => $title, 'multiple' => $multiple),
            array('id' => $poll_id),
            array('%s', '%d'),
            array('%d')
        );

        // Update options.
        foreach ($options as $key => $value) {
            $this->asgarosforum->db->update(
                $this->asgarosforum->tables->polls_options,
                array('title' => $value),
                array('id' => $key, 'poll_id' => $poll_id),
                array('%s'),
                array('%d', '%d')
            );
        }
    }

    public function delete_poll($poll_id) {
        $poll = $this->get_poll($poll_id);

        // Cancel if this topic has no poll.
        if ($poll === false) {
            return;
        }

        // Delete all existing poll-data.
        $this->asgarosforum->db->delete($this->asgarosforum->tables->polls, array('id' => $poll_id), array('%d'));
        $this->asgarosforum->db->delete($this->asgarosforum->tables->polls_options, array('poll_id' => $poll_id), array('%d'));
        $this->asgarosforum->db->delete($this->asgarosforum->tables->polls_votes, array('poll_id' => $poll_id), array('%d'));
    }

    public function editor_poll_process($post_id, $topic_id, $topic_subject, $topic_content, $topic_link, $author_id) {
        // Cancel if poll-functionality is disabled.
        if (!$this->asgarosforum->options['enable_polls']) {
            return;
        }

        // Cancel if this is not the first post.
        if (!$this->asgarosforum->is_first_post($post_id)) {
            return;
        }

        // Check if topic has a poll.
        $has_poll = $this->has_poll($topic_id);

        // Prepare variables.
        $poll_valid = true;
        $poll_title = '';
        $poll_options = array();
        $poll_multiple = 0;

        // Try to set poll-title.
        if (!empty($_POST['poll-title'])) {
            // Trim poll-title and remove tags.
            $poll_title = sanitize_text_field($_POST['poll-title']);
        }

        // Validate poll-title.
        if (empty($poll_title)) {
            $poll_valid = false;
        }

        // Try to set poll-options.
        if (!empty($_POST['poll-option'])) {
			$available_poll_options = array_map('sanitize_text_field', $_POST['poll-option']);

            // Assign not-empty poll-options to array.
            foreach ($available_poll_options as $key => $value) {
                $poll_option = trim(strip_tags($value));

                if (!empty($poll_option)) {
                    $poll_options[$key] = $poll_option;
                }
            }
        }

        // Validate poll-options.
        if (empty($poll_options)) {
            $poll_valid = false;
        }

        // Set multiple-option.
        if (isset($_POST['poll-multiple'])) {
            $poll_multiple = 1;
        }

        // If topic has no poll and a valid poll is given: Add poll.
        if ($has_poll === false && $poll_valid === true) {
            // Add poll.
            $this->add_poll($topic_id, $poll_title, $poll_options, $poll_multiple);

            // Terminate function.
            return;
        }

        // If topic has a poll and a valid poll is given: Update poll.
        if ($has_poll === true && $poll_valid === true) {
            // Update poll.
            $this->update_poll($topic_id, $poll_title, $poll_options, $poll_multiple);

            // Terminate function.
            return;
        }

        // If topic has a poll and no valid poll is given: Delete poll.
        if ($has_poll === true && $poll_valid === false) {
            // Delete poll.
            $this->delete_poll($topic_id);

            // Terminate function.
            return;
        }
    }

    public function save_vote() {
        // Cancel if poll-functionality is disabled.
        if (!$this->asgarosforum->options['enable_polls']) {
            return;
        }

        // Ensure that a vote happened.
        if (empty($_POST['poll_action']) || $_POST['poll_action'] !== 'vote') {
            return;
        }

        // Ensure that there is a poll.
        $poll = $this->get_poll($this->asgarosforum->current_topic);

        if ($poll === false) {
            return;
        }

        // Ensure that the user can vote.
        $user_id = get_current_user_id();

        if (!$this->can_vote($user_id, $poll->id)) {
            return;
        }

        // Ensure that an option got selected.
        $votes = (!empty($_POST['poll-option'])) ? array_map('sanitize_key', $_POST['poll-option']) : false;

        if ($votes === false) {
            return;
        }

        // Ensure that amount of votes represents poll-settings.
        if ($poll->multiple == 0 && count($votes) > 1) {
            return;
        }

        // Ensure that voted options belongs to the poll.
        foreach ($votes as $vote) {
            if (!isset($poll->options[$vote])) {
                return;
            }
        }

        // Save votes in database.
        foreach ($votes as $vote) {
            $this->asgarosforum->db->insert(
                $this->asgarosforum->tables->polls_votes,
                array('poll_id' => $poll->id, 'option_id' => $vote, 'user_id' => $user_id),
                array('%d', '%d', '%d')
            );
        }
    }

    // Checks if a given user voted for a specific poll.
    public function has_voted($user_id, $poll_id) {
        $has_voted = $this->asgarosforum->db->get_var("SELECT COUNT(*) FROM {$this->asgarosforum->tables->polls_votes} WHERE poll_id = {$poll_id} AND user_id = {$user_id};");

        if ($has_voted > 0) {
            return true;
        }

        return false;
    }

    // Checks if an user can vote.
    public function can_vote($user_id, $poll_id) {
        // Logged-out users cant vote.
        if ($user_id === 0) {
            return false;
        }

        // Ensure that user has not already voted.
        if ($this->has_voted($user_id, $poll_id)) {
            return false;
        }

        return true;
    }

    public function render_poll($topic_id) {
        // Cancel if poll-functionality is disabled.
        if (!$this->asgarosforum->options['enable_polls']) {
            return;
        }

        // Get poll.
        $poll = $this->get_poll($topic_id);

        // Cancel if there is no poll.
        if ($poll === false) {
            return;
        }

        // Get id of the current user.
        $user_id = get_current_user_id();

        // Check if the current user can vote.
        $can_vote = $this->can_vote($user_id, $poll->id);

        // Check if results are visible.
        $can_see_results = $this->asgarosforum->options['polls_results_visible'];

        if ($this->has_voted($user_id, $poll->id)) {
            $can_see_results = true;
        }

        echo '<div id="poll-panel">';
        echo '<form method="post" action="'.esc_url($this->asgarosforum->get_link('topic', $topic_id)).'">';
            echo '<div id="poll-headline">';
                echo '<span class="fas fa-poll-h"></span>';
                echo esc_html(stripslashes($poll->title));
            echo '</div>';

            // Show poll-results.
            echo '<div id="poll-results">';
                foreach ($poll->options as $key => $option) {
                    $percentage = 0;
                    $percentage_css = 0;

                    // Only calculate percentage-values when there are votes.
                    if ($poll->total_votes > 0) {
                        $percentage = ($option->votes / $poll->total_votes) * 100;
                        $percentage_css = number_format($percentage, 2);
                    }

                    echo '<div class="poll-result-row">';
                        echo '<div class="poll-result-name">';

                            // Render additional voting-elements based on can-vote status.
                            if ($can_vote) {
                                echo '<label class="checkbox-label">';

                                if ($poll->multiple == 1) {
                                    echo '<input type="checkbox" name="poll-option[]" value="'.esc_attr($option->id).'">';
                                } else {
                                    echo '<input type="radio" name="poll-option[]" value="'.esc_attr($option->id).'">';
                                }

                                echo '<span>'.esc_html(stripslashes($option->title)).'</span>';
                                echo '</label>';
                            } else {
                                echo esc_html(stripslashes($option->title));
                            }

                            if ($can_see_results) {
                                echo '<small class="poll-result-numbers">';
                                    echo sprintf(_n('%s Vote', '%s Votes', absint($option->votes), 'asgaros-forum'), esc_html(number_format_i18n($option->votes)));
                                    echo '&nbsp;&middot;&nbsp;';
                                    echo esc_html(number_format_i18n($percentage, 2)).'%';
                                echo '</small>';
                            }
                        echo '</div>';

                        if ($can_see_results) {
                            echo '<div class="poll-result-bar">';
                                echo '<div class="poll-result-filling" style="width: '.esc_attr($percentage_css).'%; background-color: '.esc_html($this->get_bar_color()).';"></div>';
                            echo '</div>';
                        }
                    echo '</div>';
                }

                if ($can_see_results) {
                    echo '<div class="poll-result-total">';
                        echo sprintf(_n('%s Participant', '%s Participants', absint($poll->total_participants), 'asgaros-forum'), esc_html(number_format_i18n($poll->total_participants)));
                    echo '</div>';
                }

                // Show vote-panel when user can vote.
                if ($can_vote) {
                    echo '<div class="actions">';
                        echo '<input type="hidden" name="poll_action" value="vote">';
                        echo '<input class="button button-normal" type="submit" value="'.esc_attr__('Vote', 'asgaros-forum').'">';
                    echo '</div>';
                }
            echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    // Checks if a given topic has a poll.
    public function has_poll($topic_id) {
        // Cancel if topic_id is not set.
        if (!$topic_id) {
            return false;
        }

        // Try to get the poll for the given topic first.
        $poll = $this->asgarosforum->db->get_row("SELECT * FROM {$this->asgarosforum->tables->polls} WHERE id = {$topic_id};");

        // Cancel if there is no poll for the given topic.
        if (!$poll) {
            return false;
        }

        // Otherwise return true.
        return true;
    }

    public function get_poll($topic_id) {
        // Try to get the poll for the given topic first.
        $poll = $this->asgarosforum->db->get_row("SELECT * FROM {$this->asgarosforum->tables->polls} WHERE id = {$topic_id};");

        // Cancel if there is no poll for the given topic.
        if (!$poll) {
            return false;
        }

        // Get options and votes for the poll.
        $poll->options = $this->asgarosforum->db->get_results("SELECT po.id, po.title, (SELECT COUNT(*) FROM {$this->asgarosforum->tables->polls_votes} AS pv WHERE pv.option_id = po.id) AS votes FROM {$this->asgarosforum->tables->polls_options} AS po WHERE po.poll_id = {$poll->id} ORDER BY po.id ASC;", 'OBJECT_K');

        // Get total votes.
        $poll->total_votes = $this->asgarosforum->db->get_var("SELECT COUNT(*) FROM {$this->asgarosforum->tables->polls_votes} WHERE poll_id = {$poll->id};");

        // Get total participants.
        $participants = $this->asgarosforum->db->get_results("SELECT user_id, COUNT(*) FROM {$this->asgarosforum->tables->polls_votes} WHERE poll_id = {$poll->id} GROUP BY user_id;");
        $poll->total_participants = count($participants);

        return $poll;
    }

    private $get_bar_color_counter = 0;
    public function get_bar_color() {
        $this->get_bar_color_counter++;

        $colors = array();
        $colors[] = '#4661EE';
        $colors[] = '#EC5657';
        $colors[] = '#1BCDD1';
        $colors[] = '#8FAABB';
        $colors[] = '#B08BEB';
        $colors[] = '#3EA0DD';
        $colors[] = '#F5A52A';
        $colors[] = '#23BFAA';
        $colors[] = '#FAA586';
        $colors[] = '#EB8CC6';

        return $colors[$this->get_bar_color_counter % 10];
    }
}
