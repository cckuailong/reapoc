<?php
/**
 * Class EditimageView_bwg
 */
class EditimageView_bwg {

  public function display() {
	wp_print_scripts('jquery');
    $popup_width = WDWLibrary::get('width', 650, 'intval');
    $image_width = $popup_width - 40;
    $popup_height = WDWLibrary::get('height', 500, 'intval');
    $image_height = $popup_height - 40;

    $instagram_post_width  = WDWLibrary::get('instagram_post_width', $image_width, 'intval');
    $instagram_post_height = WDWLibrary::get('instagram_post_height', $image_height, 'intval');
    $modified_date = WDWLibrary::get('modified_date', '');
    $FeedbackSocialProofHeight = 176;
    if ( $instagram_post_width ) {
      if ( $image_height / ($instagram_post_height + $FeedbackSocialProofHeight) < $image_width / $instagram_post_width ) {
        $instagram_post_width = ($image_height - $FeedbackSocialProofHeight) * $instagram_post_width / $instagram_post_height + 16;
        $instagram_post_height = $image_height;
      }
      else {
        $instagram_post_height = ($image_width - 16) * $instagram_post_height / $instagram_post_width + 16;
        $instagram_post_width = $image_width;
      }
    }
    $image_id =  WDWLibrary::get('image_id', '0');
    $image_url =  WDWLibrary::get('image_url', '');
    $facebook_post = WDWLibrary::get('FACEBOOK_POST', '0');
    $fb_post_url =  WDWLibrary::get('fb_post_url', '');
    $app_id = BWG()->options->facebook_app_id;
	?>
	<div id="loading_div"></div>
    <div id="wd-content" style="width:100%; height:100%;">
      <div id="bwg_container_for_media_1" style="width:100%; height:100%; margin:0 auto; text-align:center; vertical-align:middle;">
        <?php if ( !$facebook_post ) { ?>
			<img id="image_display" src="<?php echo BWG()->upload_url . WDWLibrary::image_url_version($image_url, $modified_date); ?>" style="max-width:100%; max-height:100%; position: relative; transform: translateY(-50%); top: 50%;" />
        <?php }
		else { ?>
          <div id="fb-root"></div>
          <script>
            (function (d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) {
                return;
              }
              js = d.createElement(s);
              js.id = id;
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&version=v2.3&appId=<?php echo $app_id; ?>";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
          </script>
          <div class="fb-post" data-width="300" data-href="https://www.facebook.com/{user_name_or_id}/<?php echo $fb_post_url; ?>"></div>
        <?php } ?>
      </div>
    </div>
    <script language="javascript" type="text/javascript" src="<?php echo BWG()->plugin_url . '/js/bwg_embed.js?ver=' . BWG()->plugin_version; ?>"></script>
    <script>
      var file_type = window.parent.document.getElementById("input_filetype_<?php echo $image_id; ?>").value;
      var file_url = window.parent.document.getElementById("image_url_<?php echo $image_id; ?>").value;
      var is_embed = file_type.indexOf("EMBED_") > -1 ? true : false;
      //for facebook
      var is_facebook_post = file_type.indexOf("_FACEBOOK_POST") > -1 ? true : false;
      var is_instagram_post = file_type.indexOf("INSTAGRAM_POST") > -1 ? true : false;
      if (is_embed) {
        var embed_id = window.parent.document.getElementById("input_filename_<?php echo $image_id; ?>").value;
        if (!is_facebook_post) {
          window.document.getElementById("image_display").setAttribute('style', 'display: none;');
          if (!is_instagram_post) {
            window.document.getElementById("bwg_container_for_media_1").innerHTML = spider_display_embed(file_type, file_url, embed_id, {
              class: "embed_display",
              frameborder: "0",
              allowfullscreen: "allowfullscreen",
              style: "width:100%; height:100%; vertical-align:middle; text-align: center; margin: 0 auto;"
            });
          }
          else {
            window.document.getElementById("bwg_container_for_media_1").innerHTML = spider_display_embed(file_type, file_url, embed_id, {
              class: "embed_display",
              width: "<?php echo $instagram_post_width; ?>",
              height: "<?php echo $instagram_post_height; ?>",
              frameborder: "0",
              allowfullscreen: "allowfullscreen",
              style: "width:<?php echo $instagram_post_width; ?>px; height:<?php echo $instagram_post_height; ?>px; vertical-align:middle; text-align: center; margin: 0 auto;"
            });
          }
        }
      }
      jQuery(window).on('load',function(){
      jQuery('#loading_div', window.parent.document).hide();
	  });
    </script>
    <?php
    die();
  }

  public function thumb_display() {
    $popup_width = WDWLibrary::get('width', 1000, 'intval') - 30;
    $image_width = $popup_width - 40;
    $popup_height = WDWLibrary::get('width', 600, 'intval') - 50;
    $image_height = $popup_height - 40;
    $image_id = WDWLibrary::get('image_id', 0, 'intval');
    $modified_date = WDWLibrary::get('modified_date', '');
    ?>
    <div style="display:table; width:100%; height:<?php echo $popup_height; ?>px;">
      <div style="display:table-cell; text-align:center; vertical-align:middle;">
        <img id="thumb_view" src="" style="max-width:<?php echo $image_width; ?>px; max-height:<?php echo $image_height; ?>px;" />
      </div>
    </div>
    <script>
      var image_url = "<?php echo BWG()->upload_url; ?>" + window.parent.document.getElementById("thumb_url_<?php echo $image_id; ?>").value;
      window.document.getElementById("thumb_view").src = image_url + "<?php echo $modified_date ? '?bwg=' . $modified_date : ''; ?>";
    </script>
    <?php
    die();
  }

  public function crop($image_data = array()) {
    $thumb_width = BWG()->options->upload_thumb_width;
    $thumb_height = BWG()->options->upload_thumb_height;
    $popup_width = ((int) WDWLibrary::get('width', 1000)) - 50;
    $image_width = $popup_width - $thumb_width - 70;
    $popup_height = ((int) WDWLibrary::get('height', 600)) - 75;
    $image_height = $popup_height - 70;
    $image_id = WDWLibrary::get('image_id','0');
    $edit_type =  WDWLibrary::get('edit_type','');
    $task = WDWLibrary::get('task');
	  $aspect_ratio = WDWLibrary::get('aspect_ratio', 0);
    $x = (int) WDWLibrary::get('x', 0);
    $y = (int) WDWLibrary::get('y', 0);
    $w = (int) WDWLibrary::get('w', 0);
    $h = (int) WDWLibrary::get('h', 0);
    $modified_date = time();
    if ( WDWLibrary::get('image_url') ) {
      $image_data = new stdClass();
      $image_data->image_url = WDWLibrary::get('image_url', '');
      $image_data->thumb_url = WDWLibrary::get('thumb_url', '');
        if( WDWLibrary::get('data-image-url', '') != '' ) {
            $image_data->image_url = WDWLibrary::get('data-image-url', '');
            $image_data->thumb_url = WDWLibrary::get('data-thumb-url', '');
        }
      $filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
      $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
      $form_action = add_query_arg(array(
                                     'action' => 'editimage_' . BWG()->prefix,
                                     'type' => 'crop',
                                     'image_id' => $image_id,
                                     'image_url' => $image_data->image_url,
                                     'thumb_url' => $image_data->thumb_url,
                                     'bwg_width' => '1000',
                                     'bwg_height' => '600',
                                     'TB_iframe' => '1',
                                   ), admin_url('admin-ajax.php'));
    }
    else {
      $image_data->image_url = stripslashes($image_data->image_url);
      $filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
      $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
      $form_action = add_query_arg(array(
                                     'action' => 'editimage_' . BWG()->prefix,
                                     'type' => 'crop',
                                     'image_id' => $image_id,
                                     'bwg_width' => '1000',
                                     'bwg_height' => '600',
                                     'TB_iframe' => '1',
                                   ), admin_url('admin-ajax.php'));
    }
    $image_data->image_url = WDWLibrary::image_url_version($image_data->image_url, $modified_date);
    @ini_set('memory_limit', '-1');
    $exp_filename = explode("?", $filename);
    list( $width_orig, $height_orig, $type_orig ) = getimagesize($exp_filename[0]);
    if ( $task == 'crop' ) {
	  if( ! $aspect_ratio ) {
      $scale = min( $w / $width_orig, $h / $height_orig );
      $thumb_width = $w * $scale;
      $thumb_height = $h * $scale;
	  }
      if ( $type_orig == 2 ) {
        $img_r = imagecreatefromjpeg($exp_filename[0]);
        $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $thumb_width, $thumb_height, $w, $h);
        imagejpeg($dst_r, $thumb_filename, BWG()->options->jpeg_quality);
        imagedestroy($img_r);
        imagedestroy($dst_r);
      }
      elseif ( $type_orig == 3 ) {
        $img_r = imagecreatefrompng($exp_filename[0]);
        $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
        imageColorAllocateAlpha($dst_r, 0, 0, 0, 127);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $thumb_width, $thumb_height, $w, $h);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagepng($dst_r, $thumb_filename, BWG()->options->png_quality);
        imagedestroy($img_r);
        imagedestroy($dst_r);
      }
      elseif ( $type_orig == 1 ) {
        $img_r = imagecreatefromgif($exp_filename[0]);
        $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
        imageColorAllocateAlpha($dst_r, 0, 0, 0, 127);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $thumb_width, $thumb_height, $w, $h);
        imagealphablending($dst_r, FALSE);
        imagesavealpha($dst_r, TRUE);
        imagegif($dst_r, $thumb_filename);
        imagedestroy($img_r);
        imagedestroy($dst_r);
      }
      else {
        ?>
        <div class="message"><strong><?php echo __("You can't crop this type of image.", BWG()->prefix); ?></strong></div>
        <?php
      }
      $where = ' `id` = ' . $image_id;
      $resolution_thumb = intval($thumb_width)."x".intval($thumb_height);
      WDWLibrary::update_thumb_dimansions($resolution_thumb, $where);
      $updated_image = WDWLibrary::update_image_modified_date( $where );
      $image_data->image_url = WDWLibrary::image_url_version($image_data->image_url, $updated_image['modified_date']);
    }
    @ini_restore('memory_limit');
	 // Register and include styles and scripts.
    BWG()->register_admin_scripts();
    wp_print_styles(BWG()->prefix . '_tables');
    wp_print_scripts(BWG()->prefix . '_admin');
    wp_print_scripts('jquery');
    wp_print_scripts('jcrop');
    wp_print_styles('jcrop');
    ?>
    <style>
		body {
			height: <?php echo $popup_height; ?>px;
		}
		#crop_image {
			margin-top: 2px;
		}
		.spider_crop {
			float: right;
      margin-right: 10px!important;
		}
		.thumb_preview_td {
			height: 20px;
			background-color: #F5F5F5;
			border-radius: 3px;
			border: 1px solid #CCCCCC;
			font-family: sans-serif;
			font-size: 12px;
		}
		.message {
			min-height: 37px;
			padding: 0px 0px 2px 0px;
		}
		.message_block {
			padding: 8px 5px;
			width: 100%;
			display: block;
			text-align: center;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			background: linear-gradient(to top, #ECECEC, #F9F9F9) repeat scroll 0 0 #F1F1F1;
			background-color: #F5F5F5;
			border: 1px solid #CCCCCC;
			border-radius: 3px 3px 3px 3px;
			box-sizing: border-box;
			font-family: sans-serif;
			font-size: 12px;
			color: #333333;
		}
		.crop_and_preview {
			margin:5px 0;
			width: 100%;
		}

    #croped_image_cont {
      background-color: #F5F5F5;
      border-radius: 3px;
      border: 1px solid #CCCCCC;
      margin-bottom: 5px;
    }

    #success_msg {
      display: block;
      margin-bottom: 5px;
    }

		.jcrop-holder {
			margin: 0 auto;
		}
    </style>
	<div style="padding:0 5px;">
		<div class="message<?php echo ( $task == 'crop' )  ? ' croped' : '' ?>">
      <span id="select_msg" class="notice notice-warning"><p><?php _e('Select the area for the thumbnail.', BWG()->prefix); ?></p></span>
    </div>
		<form method="post" id="crop_image" action="<?php echo $form_action; ?>" class="wd-form wp-core-ui">
			<div class="thumb_preview_td" style="padding: 5px;">
				<input type="checkbox" id="chb" name="aspect_ratio" value="1" onclick="spider_crop_ratio()" checked="checked">
				<label for="chb"><?php _e('Keep aspect ratio', BWG()->prefix); ?></label>
			</div>
		  <?php wp_nonce_field('editimage_' . BWG()->prefix, 'bwg_nonce'); ?>
		  <div style="max-height:<?php echo $image_height-200; ?>px; margin: 0 auto;">
		   <table class="crop_and_preview" cellpadding="0" cellspacing="0">
			  <tr>
				<td class="thumb_preview_td" style="vertical-align: middle; max-width: <?php echo ($popup_width - $thumb_width) - 40; ?>px; height:409px;" max-width: <?php echo ($popup_height - $thumb_height) - 75; ?>px;">
				  <img id="image_view" data-mod-date = "<?php echo $updated_image['modified_date'] ?>" src="<?php echo BWG()->upload_url . $image_data->image_url; ?>" data-image-url="<?php echo $image_data->image_url ?>" data-thumb-url="<?php echo $image_data->thumb_url ?>" style="max-width:800px; max-height: 400px; visibility: hidden" />
				</td>
			  </tr>
			</table>
			<button type="button" class="button button-primary button-large button-hero spider_crop" style="margin-top: 10px" onclick="spider_crop(); return false;"><?php _e('Crop', BWG()->prefix); ?></button>
		  </div>
		  <input type="hidden" name="edit_type" id="edit_type" />
		  <input id="x" type="hidden" name="x" value="" />
		  <input id="y" type="hidden" name="y" value="" />
		  <input id="w" type="hidden" name="w" value="" />
		  <input id="h" type="hidden" name="h" value="" />
      <input id="res_thumb_crop" type="hidden" name="res_thumb_crop" value="" />
		</form>

    <div id="croped_preview"  class="bwg-hidden wp-core-ui">
      <span id="success_msg" class="notice notice-success"><p><?php _e('The thumbnail was successfully cropped.', BWG()->prefix); ?></p></span>
      <div id="croped_image_cont" style="height: 445px; display: grid;">
        <img id='croped_image_thumb'>
      </div>
      <button type="button" class="button button-secondary button-large spider_crop button-hero" onclick="bwg_reset_crop(); return false;"><?php _e('Edit', BWG()->prefix); ?></button>
    </div>
	</div>
	<script language="javascript">
	  jQuery(window).on('load',function(){
        spider_crop_fix("<?php echo $thumb_width * 300 / $thumb_height; ?>", "<?php echo 300; ?>");
      });
      function spider_crop_ratio() {
        spider_crop_fix("<?php echo BWG()->options->upload_thumb_width; ?>", "<?php echo BWG()->options->upload_thumb_height; ?>");
        if ( document.getElementById("chb").checked == false ) {
          spider_crop_fix();
        }
      }

      /* Edit button action after reset */
      function bwg_reset_crop() {
        jQuery("#croped_preview").hide();
        jQuery("#crop_image").show();
        jQuery('.message').show();
        jQuery("td.thumb_preview_td").css("height","455x");
      }

      function spider_crop() {
        var url = jQuery("#crop_image").attr("action");
        var data_image_url = jQuery("#image_view").attr("data-image-url");
        var data_thumb_url = jQuery("#image_view").attr("data-thumb-url");
        if(!jQuery("#w").val().length) {
          return;
        }
        var post_data = {
          'task': 'crop',
          'x' : jQuery("#x").val(),
          'y' : jQuery("#y").val(),
          'w' : jQuery("#w").val(),
          'h' : jQuery("#h").val(),
          'data-image-url' :  data_image_url,
          'data-thumb-url' :  data_thumb_url,
        };

        jQuery.ajax({
          data: post_data,
          method: "POST",
          url: url,
        })
        .complete(function( data ) {
          var params;
          var mod_date = jQuery("#image_view").attr("data-mod-date");
          if( mod_date == '' ){
            params = '?bwg='+Math.random();
          } else {
            params = '';
          }
          var image_src = window.parent.jQuery("#image_thumb_<?php echo $image_id; ?>").attr("src");
          window.parent.jQuery("#image_thumb_<?php echo $image_id; ?>").attr("src", image_src + params);
          var croped_image_src = window.parent.jQuery("#image_thumb_<?php echo $image_id; ?>").attr("src");

          /* Hide Form content of Frame */
          jQuery("#crop_image").hide();

          jQuery("#croped_image_thumb").attr('src',croped_image_src);
          jQuery("#croped_preview").show();

          jQuery("#croped_image_thumb").css({
            'max-width':'800px',
            'max-height':'455px',
            'margin': 'auto',
            'display': 'block',
          });
          //this will save thumbnail cropped size
          var res = jQuery("#res_thumb_crop").val();
          window.parent.jQuery("#input_resolution_thumb_<?php echo $image_id; ?>").val(res);

          jQuery('.message').hide();
        });
      }

      function spider_crop_fix(wi, he) {
        var ratio = parseInt('<?php echo $width_orig; ?>') / jQuery('#image_view').width();
        var thumb_width = parseInt(wi);
        var thumb_height = parseInt(he);
        if (<?php echo $w; ?> == 0) {
          jQuery('#image_view').Jcrop({
            onSelect: spider_update_coords,
            bgOpacity: .7,
            aspectRatio: thumb_width / thumb_height
          });
        }
        else {
          jQuery('#image_view').Jcrop({
            onSelect: spider_update_coords,
            bgOpacity: .7,
            setSelect: [ <?php echo $x; ?> / ratio, <?php echo $y; ?> / ratio, <?php echo $x + $w; ?> / ratio, <?php echo $y + $h; ?> / ratio],
            aspectRatio: thumb_width / thumb_height
          });
        }
      }

      function spider_update_coords(c) {
        var ratio = parseInt('<?php echo $width_orig; ?>') / jQuery('#image_view').width();
        jQuery('#x').val(c.x * ratio);
        jQuery('#y').val(c.y * ratio);
        jQuery('#w').val(c.w * ratio);
        jQuery('#h').val(c.h * ratio);
        jQuery('#res_thumb_crop').val(c.w+'x'+c.h);
        jQuery('.message').css('visibility', 'hidden');
        if ( jQuery('.message').hasClass('croped') ) {
          /* TODO. remove TB_window block.
          window.parent.tb_remove(); */
          jQuery('.message').css({ 'visibility':'unset' });
        }
      }
    </script>
    <?php
    die();
  }

  public function recover_image( $id, $thumb_width, $thumb_height ) {
    global $wpdb;
    $image_data = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $id));
    if ( !$image_data ) {
      $image_data = new stdClass();
      $image_data->image_url = WDWLibrary::get('image_url', '', 'esc_url_raw');
      $image_data->thumb_url = WDWLibrary::get('thumb_url', '', 'esc_url_raw');
    }
    $filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
    $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
    $original_filename = str_replace('/thumb/', '/.original/', $thumb_filename);
    if ( WDWLibrary::repair_image_original($original_filename) ) {
      WDWLibrary::resize_image( $original_filename, $filename, BWG()->options->upload_img_width, BWG()->options->upload_img_height );
      WDWLibrary::resize_image( $original_filename, $thumb_filename, BWG()->options->upload_thumb_width, BWG()->options->upload_thumb_height );
    }
  }

  public function rotate($image_data = array()) {
    $popup_width = WDWLibrary::get('width', 650, 'intval') - 30;
    $image_width = $popup_width - 40;
    $popup_height = WDWLibrary::get('height', 500, 'intval') - 55;
    $image_height = $popup_height - 70;
    $image_id = WDWLibrary::get('image_id', 0, 'intval');
    $edit_type = WDWLibrary::get('edit_type');
    $brightness_val = WDWLibrary::get('brightness_val', 0, 'intval');
    $contrast_val = WDWLibrary::get('contrast_val', 0, 'intval');
    $image_data = new stdClass();
    $modified_date = time();
    if ( WDWLibrary::get('image_url') ) {
      $image_data->image_url = WDWLibrary::get('image_url', '', 'esc_url_raw');
      $image_data->thumb_url = WDWLibrary::get('thumb_url', '', 'esc_url_raw');
      $filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
      $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
      $form_action = add_query_arg(array(
                                     'action' => 'editimage_' . BWG()->prefix,
                                     'type' => 'rotate',
                                     'image_id' => $image_id,
                                     'image_url' => $image_data->image_url,
                                     'thumb_url' => $image_data->thumb_url,
                                     'bwg_width' => '650',
                                     'bwg_height' => '500',
                                     'TB_iframe' => '1',
                                   ), admin_url('admin-ajax.php'));
    }
    else {
      $image_data->image_url = stripcslashes($image_data->image_url);
      $filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
      $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
      $form_action = add_query_arg(array(
                                     'action' => 'editimage_' . BWG()->prefix,
                                     'type' => 'rotate',
                                     'image_id' => $image_id,
                                     'bwg_width' => '650',
                                     'bwg_height' => '500',
                                     'TB_iframe' => '1',
                                   ), admin_url('admin-ajax.php'));
    }
    $image_data->image_url = WDWLibrary::image_url_version($image_data->image_url, $modified_date);
    @ini_set('memory_limit', '-1');
    list($width_rotate, $height_rotate, $type_rotate) = getimagesize($filename);
    if ( $edit_type == '270' || $edit_type == '90' ) {
      if ( $type_rotate == 2 ) {
        $source = imagecreatefromjpeg($filename);
        $thumb_source = imagecreatefromjpeg($thumb_filename);
        $rotate = imagerotate($source, $edit_type, 0);
        $thumb_rotate = imagerotate($thumb_source, $edit_type, 0);
        imagejpeg($thumb_rotate, $thumb_filename, BWG()->options->jpeg_quality);
        imagejpeg($rotate, $filename, BWG()->options->jpeg_quality);
        imagedestroy($source);
        imagedestroy($rotate);
        imagedestroy($thumb_source);
        imagedestroy($thumb_rotate);
      }
      elseif ( $type_rotate == 3 ) {
        $source = imagecreatefrompng($filename);
        $thumb_source = imagecreatefrompng($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        $rotate = imagerotate($source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
        $thumb_rotate = imagerotate($thumb_source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
        imagealphablending($rotate, FALSE);
        imagealphablending($thumb_rotate, FALSE);
        imagesavealpha($rotate, TRUE);
        imagesavealpha($thumb_rotate, TRUE);
        imagepng($rotate, $filename, BWG()->options->png_quality);
        imagepng($thumb_rotate, $thumb_filename, BWG()->options->png_quality);
        imagedestroy($source);
        imagedestroy($rotate);
        imagedestroy($thumb_source);
        imagedestroy($thumb_rotate);
      }
      elseif ( $type_rotate == 1 ) {
        $source = imagecreatefromgif($filename);
        $thumb_source = imagecreatefromgif($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        $rotate = imagerotate($source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
        $thumb_rotate = imagerotate($thumb_source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
        imagealphablending($rotate, FALSE);
        imagealphablending($thumb_rotate, FALSE);
        imagesavealpha($rotate, TRUE);
        imagesavealpha($thumb_rotate, TRUE);
        imagegif($rotate, $filename);
        imagegif($thumb_rotate, $thumb_filename);
        imagedestroy($source);
        imagedestroy($rotate);
        imagedestroy($thumb_source);
        imagedestroy($thumb_rotate);
      }
    }
    elseif ( $edit_type == 'vertical' || $edit_type == 'horizontal' || $edit_type == 'both' ) {
      function bwg_image_flip( $imgsrc, $mode ) {
        $width = imagesx($imgsrc);
        $height = imagesy($imgsrc);
        $src_x = 0;
        $src_y = 0;
        $src_width = $width;
        $src_height = $height;
        switch ( $mode ) {
          case 'vertical':
            $src_y = $height - 1;
            $src_height = -$height;
            break;
          case 'horizontal':
            $src_x = $width - 1;
            $src_width = -$width;
            break;
          case 'both':
            $src_x = $width - 1;
            $src_y = $height - 1;
            $src_width = -$width;
            $src_height = -$height;
            break;
          default:
            return $imgsrc;
        }
        $trans_colour = imageColorAllocateAlpha($imgsrc, 0, 0, 0, 127);
        $imgdest = imagecreatetruecolor($width, $height);
        imagefill($imgdest, 0, 0, $trans_colour);
        if ( imagecopyresampled($imgdest, $imgsrc, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height) ) {
          return $imgdest;
        }
        return $imgsrc;
      }

      if ( $type_rotate == 2 ) {
        $source = imagecreatefromjpeg($filename);
        $flip = bwg_image_flip($source, $edit_type);
        imagejpeg($flip, $filename, BWG()->options->jpeg_quality);
        $thumb_source = imagecreatefromjpeg($thumb_filename);
        $thumb_flip = bwg_image_flip($thumb_source, $edit_type);
        imagejpeg($thumb_flip, $thumb_filename, BWG()->options->jpeg_quality);
        imagedestroy($source);
        imagedestroy($flip);
        imagedestroy($thumb_source);
        imagedestroy($thumb_flip);
      }
      elseif ( $type_rotate == 3 ) {
        $source = imagecreatefrompng($filename);
        $thumb_source = imagecreatefrompng($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        $flip = bwg_image_flip($source, $edit_type);
        $thumb_flip = bwg_image_flip($thumb_source, $edit_type);
        imagealphablending($flip, FALSE);
        imagealphablending($thumb_flip, FALSE);
        imagesavealpha($flip, TRUE);
        imagesavealpha($thumb_flip, TRUE);
        imagepng($flip, $filename, BWG()->options->png_quality);
        imagepng($thumb_flip, $thumb_filename, BWG()->options->png_quality);
        imagedestroy($source);
        imagedestroy($flip);
        imagedestroy($thumb_source);
        imagedestroy($thumb_flip);
      }
      elseif ( $type_rotate == 1 ) {
        $source = imagecreatefromgif($filename);
        $thumb_source = imagecreatefromgif($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        $flip = bwg_image_flip($source, $edit_type);
        $thumb_flip = bwg_image_flip($thumb_source, $edit_type);
        imagealphablending($flip, FALSE);
        imagealphablending($thumb_flip, FALSE);
        imagesavealpha($flip, TRUE);
        imagesavealpha($thumb_flip, TRUE);
        imagegif($flip, $filename);
        imagegif($thumb_flip, $thumb_filename);
        imagedestroy($source);
        imagedestroy($flip);
        imagedestroy($thumb_source);
        imagedestroy($thumb_flip);
      }
    }
    elseif ( $edit_type == 'brightness' || $edit_type == 'contrast' || $edit_type == 'grayscale' || $edit_type == 'negative' || $edit_type == 'remove' || $edit_type == 'emboss' || $edit_type == 'smooth' ) {
      switch ( $edit_type ) {
        case 'brightness' :
          $img_filter_type = IMG_FILTER_BRIGHTNESS;
          $ratio = $brightness_val;
          break;
        case 'contrast' :
          $img_filter_type = IMG_FILTER_CONTRAST;
          $ratio = $contrast_val;
          break;
        case 'grayscale' :
          $img_filter_type = IMG_FILTER_GRAYSCALE;
          $ratio = '';
          break;
        case 'negative' :
          $img_filter_type = IMG_FILTER_NEGATE;
          $ratio = '';
          break;
        case 'remove' :
          $img_filter_type = IMG_FILTER_MEAN_REMOVAL;
          $ratio = '';
          break;
        case 'emboss' :
          $img_filter_type = IMG_FILTER_EMBOSS;
          $ratio = '';
          break;
        case 'smooth' :
          $img_filter_type = IMG_FILTER_SMOOTH;
          $ratio = 30;
          break;
        default:
          return;
      }
      $img_type = $type_rotate;
      if ( $img_type == 2 ) {
        $source = imagecreatefromjpeg($filename);
        $thumb_source = imagecreatefromjpeg($thumb_filename);
        imagefilter($source, $img_filter_type, $ratio);
        imagefilter($thumb_source, $img_filter_type, $ratio);
        imagejpeg($source, $filename, BWG()->options->jpeg_quality);
        imagejpeg($thumb_source, $thumb_filename, BWG()->options->jpeg_quality);
        imagedestroy($source);
        imagedestroy($thumb_source);
      }
      elseif ( $img_type == 3 ) {
        $source = imagecreatefrompng($filename);
        $thumb_source = imagecreatefrompng($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        imagefilter($source, $img_filter_type, $ratio);
        imagefilter($thumb_source, $img_filter_type, $ratio);
        imagepng($source, $filename, BWG()->options->png_quality);
        imagepng($thumb_source, $thumb_filename, BWG()->options->png_quality);
        imagedestroy($source);
        imagedestroy($thumb_source);
      }
      elseif ( $img_type == 1 ) {
        $source = imagecreatefromgif($filename);
        $thumb_source = imagecreatefromgif($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        imagefilter($source, $img_filter_type, $ratio);
        imagefilter($thumb_source, $img_filter_type, $ratio);
        imagegif($source, $filename);
        imagegif($thumb_source, $thumb_filename);
        imagedestroy($source);
        imagedestroy($thumb_source);
      }
    }
    elseif ( $edit_type == 'sepia' || $edit_type == 'dark_slate_grey' || $edit_type == 'saturate' ) {
      switch ( $edit_type ) {
        case 'sepia' :
          $img_filter_type = IMG_FILTER_COLORIZE;
          $red = 112;
          $green = 66;
          $blue = 20;
          break;
        case 'dark_slate_grey' :
          $img_filter_type = IMG_FILTER_COLORIZE;
          $red = 47;
          $green = 79;
          $blue = 79;
          break;
        case 'saturate' :
          $img_filter_type = IMG_FILTER_COLORIZE;
          $red = 236;
          $green = 40;
          $blue = 41;
          break;
        default:
          return;
      }
      $img_type = $type_rotate;
      if ( $img_type == 2 ) {
        $source = imagecreatefromjpeg($filename);
        $thumb_source = imagecreatefromjpeg($thumb_filename);
        imagefilter($source, $img_filter_type, $red, $green, $blue);
        imagefilter($thumb_source, $img_filter_type, $red, $green, $blue);
        imagejpeg($source, $filename, BWG()->options->jpeg_quality);
        imagejpeg($thumb_source, $thumb_filename, BWG()->options->jpeg_quality);
        imagedestroy($source);
        imagedestroy($thumb_source);
      }
      elseif ( $img_type == 3 ) {
        $source = imagecreatefrompng($filename);
        $thumb_source = imagecreatefrompng($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        imagefilter($source, $img_filter_type, $red, $green, $blue);
        imagefilter($thumb_source, $img_filter_type, $red, $green, $blue);
        imagepng($source, $filename, BWG()->options->png_quality);
        imagepng($thumb_source, $thumb_filename, BWG()->options->png_quality);
        imagedestroy($source);
        imagedestroy($thumb_source);
      }
      elseif ( $img_type == 1 ) {
        $source = imagecreatefromgif($filename);
        $thumb_source = imagecreatefromgif($thumb_filename);
        imagealphablending($source, FALSE);
        imagealphablending($thumb_source, FALSE);
        imagesavealpha($source, TRUE);
        imagesavealpha($thumb_source, TRUE);
        imagefilter($source, $img_filter_type, $red, $green, $blue);
        imagefilter($thumb_source, $img_filter_type, $red, $green, $blue);
        imagegif($source, $filename);
        imagegif($thumb_source, $thumb_filename);
        imagedestroy($source);
        imagedestroy($thumb_source);
      }
    }
    elseif ( $edit_type == 'recover' ) {
      global $wpdb;
      $id = WDWLibrary::get('image_id', 0, 'intval');
      $thumb_width = BWG()->options->thumb_width;
      $thumb_height = BWG()->options->thumb_height;
      $this->recover_image($id, $thumb_width, $thumb_height);
    }
    @ini_restore('memory_limit');
    if ( !empty($edit_type) ) {
      $resolution_thumb = WDWLibrary::get_thumb_size( $image_data->thumb_url );
      if ( $resolution_thumb != '' ) {
        WDWLibrary::update_thumb_dimansions($resolution_thumb, "id = $image_id");
      }

      $where = ' `id` = ' . $image_id;
      $updated_image = WDWLibrary::update_image_modified_date( $where );
      $image_data->image_url = WDWLibrary::image_url_version($image_data->image_url, $updated_image['modified_date']);
      $image_data->thumb_url = WDWLibrary::image_url_version($image_data->thumb_url, $updated_image['modified_date']);
    }
    wp_print_scripts('jquery');
    wp_print_scripts('jquery-ui-widget');
    wp_print_scripts('jquery-ui-slider');
    ?>
    <link type="text/css" rel="stylesheet" id="bwg_tables-css" href="<?php echo BWG()->front_url . '/css/bwg_edit_image.css'; ?>" media="all">
    <form method="post" id="bwg_rotate_image" action="<?php echo $form_action; ?>">
      <?php wp_nonce_field('editimage_' . BWG()->prefix, 'bwg_nonce'); ?>
      <div class="main_cont" style="height: <?php echo $popup_height; ?>px;">
        <div class="cont_for_effect">
          <div class="effect_cont">
            <img class="effect" onclick="spider_rotate('grayscale', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/grayscale.png'; ?>" />
            <p class="effect_title"><?php echo __('Grayscale', BWG()->prefix); ?></p>
          </div>
          <div class="effect_cont">
            <img class="effect" onclick="spider_rotate('negative', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/negative.png'; ?>" />
            <p class="effect_title"><?php echo __('Negative', BWG()->prefix); ?></p>
          </div>
          <div class="effect_cont">
            <img class="effect" onclick="spider_rotate('remove', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/remove.png'; ?>" />
            <p class="effect_title"><?php echo __('Removal', BWG()->prefix); ?></p>
          </div>
          <div class="effect_cont">
            <img class="effect" onclick="spider_rotate('sepia', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/sepia.png'; ?>" />
            <p class="effect_title"><?php echo __('Sepia', BWG()->prefix); ?></p>
          </div>
          <div class="effect_cont">
            <img class="effect" onclick="spider_rotate('dark_slate_grey', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/dark_slate_grey.png'; ?>" />
            <p class="effect_title"><?php echo __('Slate', BWG()->prefix); ?></p>
          </div>
          <div class="effect_cont">
            <img class="effect" onclick="spider_rotate('saturate', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/saturate.png'; ?>" />
            <p class="effect_title"><?php echo __('Saturate', BWG()->prefix); ?></p>
          </div>
        </div>
        <div class="reset_cont">
          <a class="reset_img" onclick="if (confirm('<?php echo addslashes(__('Do you want to reset the image?', BWG()->prefix)); ?>')){spider_rotate('recover', 'bwg_rotate_image');
            }else {return false;
            } "><?php echo __('Reset image', BWG()->prefix); ?></a>
        </div>
        <div class="flip_cont">
          <img title="Flip Both" class="effect" onclick="spider_rotate('both', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/flip_both.png'; ?>" />
          <img title="Flip Vertical" class="effect" onclick="spider_rotate('vertical', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/flip_vertical.png'; ?>" />
          <img title="Flip Horizontal" class="effect" onclick="spider_rotate('horizontal', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/flip_horizontal.png'; ?>" />
          <img title="Rotate Left" class="effect" onclick="spider_rotate('90', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/rotate_left.png'; ?>" />
          <img title="Rotate Right" class="effect" onclick="spider_rotate('270', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/rotate_right.png'; ?>" />
        </div>
        <div class="img_cont" style="height:<?php echo $popup_height - 40; ?>px;">
          <div class="img_main_cont">
            <div class="last_cont">
              <img class="bwg_preview_image" src="<?php echo BWG()->upload_url . $image_data->image_url; ?>" style="max-width: <?php echo $image_width; ?>px; max-height: <?php echo $image_height; ?>px;" />
            </div>
          </div>
          <div class="cont_bright_cont">
            <div class="cont_bright_cont_main">
              <div class="last_cont">
                <div class="bwg_opt_cont">
                  <img title="Options" src="<?php echo BWG()->plugin_url . '/images/effects/option.png'; ?>" />
                </div>
                <div id="brightness_contrast">
                  <div class="brightness_part">
                    <div class="brightness_part_1">
                      <div class="brightness_butt">
                        <div class="contForBrightness">
                          <div class="brightness_title"><?php echo __('Brightness', BWG()->prefix); ?></div>
                          <img title="Press for brightness" class="brightnessEffect" onclick="spider_rotate('brightness', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/brightness.png'; ?>" />
                          <div class="tooltip_for_press"><?php echo __('Press for result', BWG()->prefix); ?></div>
                        </div>
                      </div>
                      <div class="cont_for_val">
                        <div id="sliderForBrightness">
                          <div class="brightness_val">
                            <div class="brightness_value">0</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="contrast_part">
                    <div class="contrast_part_1">
                      <div class="contrast_part_slider">
                        <div id="sliderForcontrast">
                          <div class="contrast_val">
                            <div class="contrast_val_cont">0</div>
                          </div>
                        </div>
                      </div>
                      <div class="contrast_butt">
                        <div class="contForContrast">
                          <div class="contrast_title"><?php echo __('Contrast', BWG()->prefix); ?></div>
                          <img title="Press for Contrast" class="contrastEffect" onclick="spider_rotate('contrast', 'bwg_rotate_image')" src="<?php echo BWG()->plugin_url . '/images/effects/contrast.png'; ?>" />
                          <div class="tooltip_for_press_contrast"><?php echo __('Press for result', BWG()->prefix); ?></div>
                        </div>
                      </div>
                    </div>
                  </div>
				        </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" name="edit_type" id="edit_type" />
      <input type="hidden" name="image_id" id="image_id" value="<?php echo $image_id; ?>" />
      <input type="hidden" name="brightness_val" id="brightness_val" value="<?php echo $brightness_val; ?>" />
      <input type="hidden" name="contrast_val" id="contrast_val" value="<?php echo $contrast_val; ?>" />
    </form>
    <script>
      jQuery(function () {
        jQuery("#sliderForBrightness").slider({
          range: "min",
          value: 0,
          min: -255,
          max: 255,
          step: 1,
          slide: function (event, ui) {
            jQuery('#brightness_val').val(ui.value);
            jQuery('.brightness_value').html(ui.value);
            var x = parseInt(ui.value);
            x = x + 255;
            var in_percents = (x / 510) * 100;
            var in_percents_for_arrow = in_percents - 12;
            jQuery('.brightness_val').css('left', in_percents_for_arrow + '%');
            jQuery('.tooltip_for_press').fadeIn("slow");
          }
        });
        jQuery("#sliderForcontrast").slider({
          range: "min",
          value: 0,
          min: -100,
          max: 100,
          step: 1,
          slide: function (event, ui) {
            jQuery('#contrast_val').val(ui.value);
            jQuery('.contrast_val_cont').html(ui.value);
            var x = parseInt(ui.value);
            x = x + 100;
            var in_percents = (x / 200) * 100;
            var in_percents_for_arrow = in_percents - 12;
            jQuery('.contrast_val').css('left', in_percents_for_arrow + '%');
            jQuery('.tooltip_for_press_contrast').fadeIn("slow");
          }
        });
      });

      function spider_rotate(type, form_id) {
        document.getElementById("edit_type").value = type;
        document.getElementById(form_id).submit();
      }
      if (window.parent.document.getElementById("image_thumb_pr_<?php echo $image_id; ?>") != null) {
        var image_src = window.parent.document.getElementById("image_thumb_pr_<?php echo $image_id; ?>").src;
        window.parent.document.getElementById("image_thumb_pr_<?php echo $image_id; ?>").src = image_src + "<?php echo isset($updated_image['modified_date']) && $updated_image['modified_date'] ? '?bwg=' . $updated_image['modified_date'] : ''; ?>";
      }
      else {
        var image_src = window.parent.document.getElementById("image_thumb_<?php echo $image_id; ?>").src;
        window.parent.document.getElementById("image_thumb_<?php echo $image_id; ?>").src = image_src + "<?php echo isset($updated_image['modified_date']) && $updated_image['modified_date'] ? '?bwg=' . $updated_image['modified_date'] : ''; ?>";
      }

      jQuery(function() {
        jQuery(".bwg_opt_cont").click(function () {
          if (jQuery('#brightness_contrast').height() == 0) {
            jQuery('#brightness_contrast').animate({
                height: 40
              },
              'linear',
              function () {
                jQuery('#sliderForBrightness').css('opacity', 1);
                jQuery('#sliderForBrightness').css('display', 'inline-block');
                jQuery('#sliderForcontrast').css('opacity', 1);
                jQuery('#sliderForcontrast').css('display', 'inline-block');
                jQuery('.contForBrightness').css('display', 'inline-block');
                jQuery('.contForContrast').css('display', 'inline-block');
              });
          }
          else {
            jQuery('#brightness_contrast').animate({
                height: 0
              },
              'linear',
              function () {
                jQuery('#sliderForBrightness').css('opacity', 0);
                jQuery('#sliderForBrightness').css('display', 'none');
                jQuery('#sliderForcontrast').css('opacity', 0);
                jQuery('#sliderForcontrast').css('display', 'none');
                jQuery('.contForBrightness').css('display', 'none');
                jQuery('.contForContrast').css('display', 'none');
              }
            );
          }
        });
        jQuery('body').click(function () {
          jQuery('.tooltip_for_press').fadeOut("slow");
        });
        jQuery('body').click(function () {
          jQuery('.tooltip_for_press_contrast').fadeOut("slow");
        });
      });
    </script>
    <?php
    die();
  }
}
