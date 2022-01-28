<?php
class BWGControllerGalleryBox {
  public function execute() {
    $ajax_task = WDWLibrary::get('ajax_task');
    if (method_exists($this, $ajax_task)) {
	 $this->$ajax_task();
    }
    else {
      $this->display();
    }
  }

  public function display() {
    require_once BWG()->plugin_dir . "/frontend/models/BWGModelGalleryBox.php";
    $model = new BWGModelGalleryBox();

    require_once BWG()->plugin_dir . "/frontend/views/BWGViewGalleryBox.php";
    $view = new BWGViewGalleryBox($model);

    $view->display();
  }

  public function save_rate() {
    global $wpdb;
    $image_id = WDWLibrary::get('image_id', 0, 'intval','POST');
    $rate = WDWLibrary::get('rate');
    $ip = BWG()->options->save_ip ? $_SERVER['REMOTE_ADDR'] : '';
    if ( !$ip || !$wpdb->get_var($wpdb->prepare('SELECT `image_id` FROM `' . $wpdb->prefix . 'bwg_image_rate` WHERE `ip`="%s" AND `image_id`="%d"', $ip, $image_id)) ) {
      $wpdb->insert($wpdb->prefix . 'bwg_image_rate', array(
        'image_id' => $image_id,
        'rate' => $rate,
        'ip' => $ip,
        'date' => date('Y-m-d H:i:s'),
      ), array(
		  '%d',
		  '%f',
		  '%s',
		  '%s',
		));
    }
    $rates = $wpdb->get_row($wpdb->prepare('SELECT AVG(`rate`) as `average`, COUNT(`rate`) as `rate_count` FROM ' . $wpdb->prefix . 'bwg_image_rate WHERE image_id="%d"', $image_id));
    $wpdb->update($wpdb->prefix . 'bwg_image', array(
      'avg_rating' => $rates->average,
      'rate_count' => $rates->rate_count
    ), array( 'id' => $image_id ),
      array('%f','%d'),array('%d'));
    $this->display();
  }

  public function save_hit_count() {
    global $wpdb;
    $image_id = WDWLibrary::get('image_id', 0, 'intval');
    $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bwg_image SET hit_count = hit_count + 1 WHERE id="%d"', $image_id));
  }

  /**
   * Add comment.
   */
	public function add_comment() {
		global $wpdb;
		$error = false;
		$json =  array();
		$error_messages = array();
		// get post data.
		$image_id = WDWLibrary::get('comment_image_id', 0);
		$name = trim(WDWLibrary::get('comment_name', ''));
		$email = WDWLibrary::get('comment_email', '');
		$comment = trim(WDWLibrary::get('comment_text', ''));
		$moderation = trim(WDWLibrary::get('comment_moderation', 0));
		$privacy_policy = WDWLibrary::get('privacy_policy', '');
		$published = (current_user_can('manage_options') || !$moderation) ? 1 : 0;
		
		if ( empty($name) ) {
				$error = true;
				$error_messages['name'] = sprintf( __('The %s field is required.', BWG()->prefix), 'name' );
		}
		if ( WDWLibrary::get('popup_enable_email') ) {
			if ( empty($email) ) {
				$error = true;
				$error_messages['email'] = sprintf( __('The %s field is required.', BWG()->prefix), 'email' );
			}
			elseif ( !is_email($email) ) {
				$error = true;
				$error_messages['email'] = sprintf( __('The %s field must contain a valid email address.', BWG()->prefix), 'email' );
			}
		}
		if ( empty($comment) ) {
			$error = true;
			$error_messages['textarea'] = sprintf( __('The %s field is required.', BWG()->prefix), 'comment' );
		}
		if ( WDWLibrary::get('popup_enable_captcha') ) {
			 WDWLibrary::bwg_session_start();
			 $captcha = WDWLibrary::get('comment_captcha');
			 $session_captcha = (isset($_SESSION['bwg_captcha_code']) ? esc_html(stripslashes($_SESSION['bwg_captcha_code'])) : '');
			 if ( empty($captcha) ) {
				$error = true;
				$error_messages['captcha'] = sprintf( __('The %s field is required.', BWG()->prefix), 'captcha' );
			 }
			 elseif ( $captcha != $session_captcha ) {
				$error = true;
				$error_messages['captcha'] = __('Incorrect Security code.', BWG()->prefix);
			 }
		}
		if ( WDWLibrary::get_privacy_policy_url() ) {
			if ( empty($privacy_policy) ) {
				$error = true;
				$error_messages['privacy_policy'] = sprintf( __('The %s field is required.', BWG()->prefix), 'privacy policy' );
			}
		 }

		if ( $error === false ) {
			$added = $wpdb->insert( $wpdb->prefix . 'bwg_image_comment', array(
				'image_id' => $image_id,
				'name' => $name,
				'mail' => $email,
				'comment' => $comment,
				'url' => '',
				'date' => date('Y-m-d H:i:s'),
				'published' => $published,
			), array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			));
			if ( $added ) {
				$error_messages['success'] = 'ok';
				$wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `comment_count` = `comment_count` + 1 WHERE `id` = "%d"', $image_id));
				
				require_once BWG()->plugin_dir . "/frontend/models/BWGModelGalleryBox.php";
				$model = new BWGModelGalleryBox();

				require_once BWG()->plugin_dir . "/frontend/views/BWGViewGalleryBox.php";
				$view = new BWGViewGalleryBox($model);
				
				if ( $published ) {
					$comments = $model->get_comment_rows_data($image_id);
					if ( !empty($comments) ) {
						$html_comments_block = '';
						foreach ( $comments as $comment ) {
							$html_comments_block .= $view->html_comments_block($comment);
						}
					}
					$json['html_comments_block'] = $html_comments_block;
				}
			}
		}
		$json['error'] = $error;
		$json['published'] = $published;
		$json['error_messages'] = $error_messages;
		echo json_encode($json); exit;
	}

  /**
   *  Delete comment.
   */
	public function delete_comment() {
		global $wpdb;
		$error = false;
		$json = array();
		$id_image = WDWLibrary::get('id_image', 0, 'intval');
		$id_comment = WDWLibrary::get('id_comment', 0, 'intval');
		if ( $id_image && $id_comment ) {
			$delete = $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image_comment` WHERE `id` = "%d"', $id_comment));
			$update = $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `comment_count` = (CASE WHEN comment_count <= 0 THEN 0 ELSE `comment_count`-1 END) WHERE `id`="%d"', $id_image));
			if ( !$delete || !$update ) {
				$error = true;
			}
		}
		else {
			$error = true;
		}
		$json['error'] = $error;
		echo json_encode($json); exit;
	}
}