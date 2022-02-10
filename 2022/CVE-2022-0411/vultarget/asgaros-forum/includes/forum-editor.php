<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumEditor {
	private $asgarosforum = null;

	public function __construct($object) {
		$this->asgarosforum = $object;

        add_filter('mce_buttons', array($this, 'default_mce_buttons'), 1, 2);
		add_filter('mce_buttons', array($this, 'add_mce_buttons'), 9999, 2);
		add_filter('mce_buttons_2', array($this, 'remove_mce_buttons'), 1, 2);
		add_filter('mce_buttons_3', array($this, 'remove_mce_buttons'), 1, 2);
		add_filter('mce_buttons_4', array($this, 'remove_mce_buttons'), 1, 2);
        add_filter('disable_captions', array($this, 'disable_captions'));
		add_filter('tiny_mce_before_init', array($this, 'toggle_editor'));
	}

	// Set the default TinyMCE buttons.
	public function default_mce_buttons($buttons, $editor_id) {
		// Array of default editor buttons of WordPress which should not get added automatically to the forum.
		$default_buttons = array(
			'aligncenter',
			'alignleft',
			'alignright',
			'blockquote',
			'bold',
			'bullist',
			'charmap',
			'dfw',
			'forecolor',
			'formatselect',
			'fullscreen',
			'hr',
			'indent',
			'italic',
			'link',
			'numlist',
			'outdent',
			'pastetext',
			'redo',
			'removeformat',
			'spellchecker',
			'strikethrough',
			'underline',
			'undo',
			'unlink',
			'wp_add_media',
			'wp_adv',
			'wp_help',
			'wp_more'
		);

        if ($this->asgarosforum->executePlugin && $editor_id === 'message') {
			// Build array of available buttons.
			$forum_buttons = array(
				'bold',
				'italic',
				'underline',
				'strikethrough',
				'forecolor',
				'bullist',
				'numlist',
				'outdent',
				'indent',
				'alignleft',
				'aligncenter',
				'alignright',
				'pastetext',
				'removeformat',
				'undo',
				'redo',
				'blockquote',
				'link'
			);

			// Find non-default editor buttons.
			$unique_buttons = array_diff($buttons, $default_buttons);

			// Merge forum and non-default editor buttons.
			$buttons = array_merge($forum_buttons, $unique_buttons);

			// Apply filters.
			$buttons = apply_filters('asgarosforum_filter_editor_buttons', $buttons);
        }

		return $buttons;
    }

	// Add custom TinyMCE buttons.
	public function add_mce_buttons($buttons, $editor_id) {
		if ($this->asgarosforum->executePlugin && $editor_id === 'message') {
			$buttons[] = 'image';
		}

		return $buttons;
	}

	// Remove TinyMCE buttons.
	public function remove_mce_buttons($buttons, $editor_id) {
		if ($this->asgarosforum->executePlugin && $editor_id === 'message') {
			$buttons = array();
		}

		return $buttons;
	}

	public function disable_captions($args) {
        if ($this->asgarosforum->executePlugin) {
            return true;
        } else {
            return $args;
        }
    }

	public function toggle_editor($args) {
		if ($this->asgarosforum->executePlugin) {
			// Ensure that the editor is toggled.
			$args['wordpress_adv_hidden'] = false;
		}

		return $args;
	}

    // Check permissions before loading the editor.
    private function checkPermissions($editor_view) {
        switch ($editor_view) {
            case 'addtopic':
                // Error when the user is not logged-in and guest-posting is disabled.
                if (!is_user_logged_in() && !$this->asgarosforum->options['allow_guest_postings']) {
                    return false;
                    break;
                }

                // Error when the user is banned.
                if ($this->asgarosforum->permissions->isBanned('current')) {
                    return false;
                    break;
                }

                // Error when the forum is closed.
                if (!$this->asgarosforum->forumIsOpen()) {
                    return false;
                    break;
                }
                break;
            case 'addpost':
                // Error when user is not logged-in and guest-posting is disabled.
                if (!is_user_logged_in() && !$this->asgarosforum->options['allow_guest_postings']) {
                    return false;
                    break;
                }

                // Error when the user is banned.
                if ($this->asgarosforum->permissions->isBanned('current')) {
                    return false;
                    break;
                }

                // Error when the topic is closed and the user is not a moderator.
                if ($this->asgarosforum->is_topic_closed($this->asgarosforum->current_topic) && !$this->asgarosforum->permissions->isModerator('current')) {
                    return false;
                    break;
                }
                break;
            case 'editpost':
                // Error when user is not logged-in.
                if (!is_user_logged_in()) {
                    return false;
                    break;
                }

                // Error when the user cannot edit a post.
				$user_id = $this->asgarosforum->permissions->currentUserID;

                if (!$this->asgarosforum->permissions->can_edit_post($user_id, $this->asgarosforum->current_post)) {
                    return false;
                    break;
                }
                break;
        }

        return true;
    }

    public function showEditor($editor_view, $inOtherView = false) {
		if (!$this->checkPermissions($editor_view) && !$inOtherView) {
			$this->asgarosforum->render_notice(__('You are not allowed to do this.', 'asgaros-forum'));
        } else {
            $post = false;
            $subject = (isset($_POST['subject'])) ? sanitize_text_field($_POST['subject']) : '';
            $message = (isset($_POST['message'])) ? wp_kses_post($_POST['message']) : '';

            if ($editor_view === 'addpost') {
                if (!isset($_POST['message']) && isset($_GET['quote'])) {
					// We also select against the topic to ensure that we can only quote posts from the current topic.
                    $quoteData = $this->asgarosforum->db->get_row($this->asgarosforum->db->prepare("SELECT text, author_id, date FROM ".$this->asgarosforum->tables->posts." WHERE id = %d AND parent_id = %d;", absint($_GET['quote']), $this->asgarosforum->current_topic));

                    if ($quoteData) {
                        $message = '<blockquote><div class="quotetitle">'.__('Quote from', 'asgaros-forum').' '.$this->asgarosforum->getUsername($quoteData->author_id).' '.sprintf(__('on %s', 'asgaros-forum'), $this->asgarosforum->format_date($quoteData->date)).'</div>'.stripslashes($quoteData->text).'</blockquote><br>';
					}
                }
            } else if ($editor_view === 'editpost') {
                $post = $this->asgarosforum->db->get_row($this->asgarosforum->db->prepare("SELECT id, text, parent_id, author_id, uploads FROM ".$this->asgarosforum->tables->posts." WHERE id = %d;", $this->asgarosforum->current_post));

				if (!isset($_POST['message'])) {
                    $message = $post->text;
                }

                // TODO: Is first post query can get removed and get via the before query (get min(id)).
                if (!isset($_POST['subject']) && $this->asgarosforum->is_first_post($post->id)) {
                    $subject = $this->asgarosforum->current_topic_name;
                }
            }

			$editorTitle = '';
            if ($editor_view === 'addtopic') {
                $editorTitle = __('New Topic', 'asgaros-forum');
            } else if ($editor_view === 'addpost') {
                $editorTitle = __('Post Reply:', 'asgaros-forum').' '.esc_html(stripslashes($this->asgarosforum->current_topic_name));
            } else if ($editor_view === 'editpost') {
                $editorTitle = __('Edit Post', 'asgaros-forum');
            }

			$actionURL = '';
			if ($editor_view == 'addpost') {
				$actionURL = $this->asgarosforum->get_link('topic', $this->asgarosforum->current_topic);
			} else if ($editor_view == 'editpost') {
				$actionURL = $this->asgarosforum->get_link('editpost', $this->asgarosforum->current_post);
			} else if ($editor_view == 'addtopic') {
				$actionURL = $this->asgarosforum->get_link('forum', $this->asgarosforum->current_forum);
			}

			// We need the tabindex attribute in the form for scrolling.
			?>
            <form id="forum-editor-form" class="<?php echo esc_attr($editor_view); ?>-editor" tabindex="-1" name="addform" method="post" action="<?php echo esc_url($actionURL); ?>" enctype="multipart/form-data"<?php if ($inOtherView && !isset($_POST['subject']) && !isset($_POST['message'])) { echo ' style="display: none;"'; } ?>>
                <div class="title-element"><?php if ($inOtherView) { echo esc_html($editorTitle); } ?></div>
                <div class="editor-element">
                    <?php if ($editor_view === 'addtopic' || ($editor_view == 'editpost' && $this->asgarosforum->is_first_post($post->id))) { ?>
                        <div class="editor-row-subject">
                            <label for="subject"><?php esc_html_e('Subject:', 'asgaros-forum'); ?></label>
                            <span>
                                <input class="editor-subject-input" type="text" id="subject" maxlength="255" name="subject" value="<?php echo esc_html(stripslashes($subject)); ?>">
                            </span>
                        </div>
                    <?php
					}

					echo '<div class="editor-row no-padding">';
                        wp_editor(stripslashes($message), 'message', $this->asgarosforum->options_editor);
                    echo '</div>';

                    $this->asgarosforum->uploads->show_editor_upload_form($post);
                    $this->asgarosforum->notifications->show_editor_subscription_option();
                    do_action('asgarosforum_editor_custom_content_bottom', $editor_view);

                    echo '<div class="editor-row editor-row-submit">';
                        if ($editor_view === 'addtopic') {
                            echo '<input type="hidden" name="submit_action" value="add_topic">';
                            wp_nonce_field('asgaros_forum_add_topic');
                        } else if ($editor_view === 'addpost') {
                            echo '<input type="hidden" name="submit_action" value="add_post">';
                            wp_nonce_field('asgaros_forum_add_post');
                        } else if ($editor_view === 'editpost') {
                            echo '<input type="hidden" name="submit_action" value="edit_post">';
                            wp_nonce_field('asgaros_forum_edit_post');
                        }

						echo '<div class="left">';
						if ($inOtherView) {
							echo '<a href="'.esc_url($actionURL).'" class="button button-red cancel">'.esc_html__('Cancel', 'asgaros-forum').'</a>';
						} else {
							if ($editor_view === 'editpost') {
								$actionURL = $this->asgarosforum->get_link('topic', $this->asgarosforum->current_topic);
							}
							echo '<a href="'.esc_url($actionURL).'" class="button button-red">'.esc_html__('Cancel', 'asgaros-forum').'</a>';
						}
						echo '</div>';
	                    echo '<div class="right"><input class="button button-normal" type="submit" value="'.esc_attr__('Submit', 'asgaros-forum').'"></div>';
                    echo '</div>';
                echo '</div>';
            echo '</form>';
        }
    }
}
