<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumUploads {
	private $asgarosforum = null;
	private $upload_folder = 'asgarosforum';
	private $upload_path;
	private $upload_url;
	private $upload_allowed_filetypes;

	public function __construct($object) {
		$this->asgarosforum = $object;

		add_action('init', array($this, 'initialize'));
	}

	public function initialize() {
		$this->upload_folder = apply_filters('asgarosforum_filter_upload_folder', $this->upload_folder);
		$upload_dir = wp_upload_dir();
		$this->upload_path = $upload_dir['basedir'].'/'.$this->upload_folder.'/';
		$this->upload_url = $upload_dir['baseurl'].'/'.$this->upload_folder.'/';
		$this->upload_allowed_filetypes = explode(',', $this->asgarosforum->options['allowed_filetypes']);
	}

	public function delete_post_files($post_id) {
		$path = $this->upload_path.$post_id.'/';

        if (is_dir($path)) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                unlink($path.$file);
            }

            rmdir($path);
        }
	}

	// Check if its allowed to upload files with those extensions.
	public function check_uploads_extension() {
		if ($this->asgarosforum->options['allow_file_uploads'] && !empty($_FILES['forumfile'])) {
			if (!empty($_FILES['forumfile']['name'])) {
				$file_names = array_map('sanitize_file_name', $_FILES['forumfile']['name']);

				foreach ($file_names as $index => $tmpName) {
					if (empty($_FILES['forumfile']['error'][$index]) && !empty($file_names[$index])) {
						$file_extension = strtolower(pathinfo($file_names[$index], PATHINFO_EXTENSION));

						if (!in_array($file_extension, $this->upload_allowed_filetypes)) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	// Check if its allowed to upload files with those sizes.
	public function check_uploads_size() {
		if ($this->asgarosforum->options['allow_file_uploads'] && !empty($_FILES['forumfile'])) {
			if (!empty($_FILES['forumfile']['name'])) {
				$file_names = array_map('sanitize_file_name', $_FILES['forumfile']['name']);

				foreach ($file_names as $index => $tmpName) {
					if (!empty($_FILES['forumfile']['error'][$index]) && $_FILES['forumfile']['error'][$index] == 2) {
						return false;
					} else if (empty($_FILES['forumfile']['error'][$index]) && !empty($file_names[$index])) {
						$maximumFileSize = (1024 * (1024 * $this->asgarosforum->options['uploads_maximum_size']));

						if ($maximumFileSize != 0 && $_FILES['forumfile']['size'][$index] > $maximumFileSize) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	// Generates the list of new files to upload.
	public function get_upload_list() {
		$files = array();

		if ($this->asgarosforum->options['allow_file_uploads'] && !empty($_FILES['forumfile'])) {
			if (!empty($_FILES['forumfile']['name'])) {
				$file_names = array_map('sanitize_file_name', $_FILES['forumfile']['name']);

				foreach ($file_names as $index => $tmpName) {
					if (empty($_FILES['forumfile']['error'][$index]) && !empty($file_names[$index])) {
						$name = $file_names[$index];

						if (!empty($name)) {
							$files[$index] = $name;
						}
					}
				}
			}
        }

		return $files;
	}

	public function create_upload_folders($path) {
		if (!is_dir($this->upload_path)) {
			mkdir($this->upload_path);
		}

		if (!is_dir($path)) {
			mkdir($path);
		}
	}

	public function upload_files($post_id, $uploadList) {
		$path = $this->upload_path.$post_id.'/';
		$links = array();
		$files = $uploadList;

		// When there are files to upload, create the folders first.
        if (!empty($files)) {
            $this->create_upload_folders($path);
		}

		// Continue when the destination-folder exists.
		if (is_dir($path)) {
	        // Register existing files.
	        if (!empty($_POST['existingfile'])) {
				$existing_files = array_map('sanitize_file_name', $_POST['existingfile']);

	            foreach ($existing_files as $file) {
	                if (is_file($path.wp_basename($file))) {
	                    $links[] = $file;
	                }
	            }
	        }

	        // Remove deleted files.
	        if (!empty($_POST['deletefile'])) {
				$deleted_files = array_map('sanitize_file_name', $_POST['deletefile']);

	            foreach ($deleted_files as $file) {
	                if (is_file($path.wp_basename($file))) {
	                    unlink($path.wp_basename($file));
	                }
	            }
	        }

			// Upload new files.
	        if (!empty($files)) {
				$temporary_file_paths = array_map('sanitize_text_field', $_FILES['forumfile']['tmp_name']);

	            foreach($files as $index => $name) {
	                move_uploaded_file($temporary_file_paths[$index], $path.$name);
	                $links[] = $name;
	            }
	        }

			// Remove folder if it is empty.
	        if (count(array_diff(scandir($path), array('.', '..'))) == 0) {
	            rmdir($path);
	        }
		}

        return $links;
    }

	public function show_uploaded_files($post_id, $post_uploads) {
		$path = $this->upload_path.$post_id.'/';
        $url = $this->upload_url.$post_id.'/';
        $uploads = maybe_unserialize($post_uploads);
        $uploadedFiles = '';
		$output = '';

        if (!empty($uploads) && is_dir($path)) {
			// Generate special message instead of file-list when hiding uploads for guests.
			if (!is_user_logged_in() && $this->asgarosforum->options['hide_uploads_from_guests']) {
				$uploadedFiles .= '<li>'.__('You need to login to have access to uploads.', 'asgaros-forum').'</li>';
			} else {
				foreach ($uploads as $upload) {
	                if (is_file($path.wp_basename($upload))) {
						$file_extension = strtolower(pathinfo($path.wp_basename($upload), PATHINFO_EXTENSION));
						$imageThumbnail = ($this->asgarosforum->options['uploads_show_thumbnails'] && $file_extension !== 'pdf') ? wp_get_image_editor($path.wp_basename($upload)) : false;

						$uploadedFiles .= '<li class="uploaded-file">';

						if ($imageThumbnail && !is_wp_error($imageThumbnail)) {
							$uploadedFiles .= '<a href="'.$url.utf8_uri_encode($upload).'" target="_blank"><img class="resize" src="'.$url.utf8_uri_encode($upload).'" alt="'.$upload.'"></a>';
						} else {
							$uploadedFiles .= '<a href="'.$url.utf8_uri_encode($upload).'" target="_blank">'.$upload.'</a>';
						}

						$uploadedFiles .= '</li>';
	                }
	            }
			}

			if (!empty($uploadedFiles)) {
                $output .= '<strong class="uploaded-files-title">'.__('Uploaded files:', 'asgaros-forum').'</strong>';
                $output .= '<ul>'.$uploadedFiles.'</ul>';
			}
        }

		return $output;
    }

	public function show_editor_upload_form($postObject) {
		$uploadedFilesCounter = 0;

		// Show list of uploaded files first. Also shown when uploads are disabled to manage existing files if it was enabled before.
		if ($postObject && $this->asgarosforum->current_view === 'editpost') {
			$path = $this->upload_path.$postObject->id.'/';
	        $url = $this->upload_url.$postObject->id.'/';
	        $uploads = maybe_unserialize($postObject->uploads);
	        $uploadedFiles = '';

			if (!empty($uploads) && is_dir($path)) {
				foreach ($uploads as $upload) {
	                if (is_file($path.wp_basename($upload))) {
						$uploadedFilesCounter++;
	                    $uploadedFiles .= '<li>';
	                    $uploadedFiles .= '<a href="'.$url.utf8_uri_encode($upload).'" target="_blank">'.$upload.'</a> &middot; <a data-filename="'.$upload.'" class="delete">['.__('Delete', 'asgaros-forum').']</a>';
	                    $uploadedFiles .= '<input type="hidden" name="existingfile[]" value="'.$upload.'">';
	                    $uploadedFiles .= '</li>';
	                }
	            }

				if (!empty($uploadedFiles)) {
	                echo '<div class="editor-row">';
	                	echo '<span class="row-title">'.esc_html__('Uploaded files:', 'asgaros-forum').'</span>';
	                	echo '<div class="files-to-delete"></div>';
	                	echo '<ul class="uploaded-files">'.$uploadedFiles.'</ul>';
	                echo '</div>';
	            }
			}
		}

		// Show upload controls.
        if ($this->asgarosforum->options['allow_file_uploads']) {
			// Dont show upload controls under certain conditions.
			if (!is_user_logged_in() && $this->asgarosforum->options['upload_permission'] != 'everyone') {
				return;
			} else if (!$this->asgarosforum->permissions->isModerator('current') && $this->asgarosforum->options['upload_permission'] == 'moderator') {
				return;
			}

			echo '<div class="editor-row editor-row-uploads">';
				echo '<span class="row-title">'.esc_html__('Upload Files:', 'asgaros-forum').'</span>';

				// Set maximum file size.
				if ($this->asgarosforum->options['uploads_maximum_size'] != 0) {
					echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.(1024 * (1024 * absint($this->asgarosforum->options['uploads_maximum_size']))).'">';
				}

				$flag = 'style="display: none;"';

				if ($this->asgarosforum->options['uploads_maximum_number'] == 0 || $uploadedFilesCounter < $this->asgarosforum->options['uploads_maximum_number']) {
					$uploadedFilesCounter++;
					echo '<input type="file" name="forumfile[]"><br>';

					if ($this->asgarosforum->options['uploads_maximum_number'] == 0 || $uploadedFilesCounter < $this->asgarosforum->options['uploads_maximum_number']) {
						$flag = '';
					}
				}

				echo '<a id="add_file_link" data-maximum-number="'.esc_attr($this->asgarosforum->options['uploads_maximum_number']).'" '.$flag.'>'.esc_html__('Add another file ...', 'asgaros-forum').'</a>';

				$this->show_upload_restrictions();
			echo '</div>';
		}
	}

	public function show_upload_restrictions() {
		echo '<span class="upload-hints">';

		if ($this->asgarosforum->options['uploads_maximum_number'] != 0) {
			echo esc_html__('Maximum files:', 'asgaros-forum');
			echo '&nbsp;';
			echo number_format_i18n(esc_html(absint($this->asgarosforum->options['uploads_maximum_number'])));
			echo '&nbsp;&middot;&nbsp;';
		}

		if ($this->asgarosforum->options['uploads_maximum_size'] != 0) {
			echo esc_html__('Maximum file size:', 'asgaros-forum');
			echo '&nbsp;';
			echo number_format_i18n(esc_html(absint($this->asgarosforum->options['uploads_maximum_size']))).' MB';
			echo '&nbsp;&middot;&nbsp;';
		}

		echo esc_html__('Allowed file types:', 'asgaros-forum');
		echo '&nbsp;';
		echo esc_html($this->asgarosforum->options['allowed_filetypes']);

		echo '</span>';
	}
}
