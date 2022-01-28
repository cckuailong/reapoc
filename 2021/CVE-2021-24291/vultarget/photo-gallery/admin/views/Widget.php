<?php

/**
 * Class WidgetView_bwg
 */
class WidgetView_bwg {
  /**
   * @param $args
   * @param $instance
   */
	function widget($args, $instance) {
		extract($args);
		$title = (!empty($instance['title']) ? $instance['title'] : "");
		$type  = (!empty($instance['type']) ? $instance['type'] : "gallery");
		$view_type = (!empty($instance['view_type']) && BWG()->is_pro ? $instance['view_type'] : "thumbnails");
		$gallery_id = (!empty($instance['gallery_id']) ? $instance['gallery_id'] : 0);
		$album_id = (!empty($instance['album_id']) ? $instance['album_id'] : 0);
		$theme_id = (!empty($instance['theme_id']) ? $instance['theme_id'] : 0);
		$show  = (!empty($instance['show']) ? $instance['show'] : "random");
		$sort_by = 'order';
		if ($show == 'random') {
			$sort_by = 'random';
		}
		$order_by = 'ASC';
		if ($show == 'last') {
			$order_by = 'DESC';
		}

		$count  = (!empty($instance['count']) ? $instance['count'] : BWG()->options->image_column_number);
		$width  = (!empty($instance['width']) ? $instance['width'] : BWG()->options->thumb_width);
		$height = (!empty($instance['height']) ? $instance['height'] : BWG()->options->thumb_height);

		// Before widget.
		echo $before_widget;
		// Title of widget.
		if ($title) {
		  echo $before_title . $title . $after_title;
		}
		// Widget output.
		$params = array (
		  'from' => 'widget',
		  'theme_id' => $theme_id,
		  'sort_by'  => $sort_by,
		  'order_by' => $order_by,
		  'image_enable_page' => 0
		);
		require_once(BWG()->plugin_dir . '/frontend/controllers/controller.php');
		$controller_class = 'BWGControllerSite';
		if ($type == 'gallery') {
			if ($view_type == 'thumbnails') {
				$gallery_type = 'thumbnails';
				$view = 'Thumbnails';
			}
			else if ($view_type == 'masonry') {
				$gallery_type = 'thumbnails_masonry';
				$view = 'Thumbnails_masonry';
			}

			$params['gallery_type']  = $gallery_type;
			$params['gallery_id'] 	 = $gallery_id;
			$params['thumb_width'] 	 = $width;
			$params['thumb_height']  = $height;
			$params['image_column_number'] = $count;
			$params['images_per_page'] = $count;
		}
		else {
			$view = 'Album_compact_preview';

			$params['gallery_type']  = 'album_compact_preview';
			$params['album_id'] = $album_id;
			$params['compuct_albums_per_page'] = $count;
			$params['compuct_album_thumb_width'] = $width;
			$params['compuct_album_thumb_height'] = $height;
			$params['compuct_album_image_thumb_width'] = $width;
			$params['compuct_album_image_thumb_height'] = $height;
			$params['all_album_sort_by']  = $sort_by;
			$params['all_album_order_by'] = $order_by;
			$params['compuct_album_enable_page'] = 0;
		}
		$controller = new $controller_class($view);
		$bwg = WDWLibrary::unique_number();
		$pairs = WDWLibrary::get_shortcode_option_params( $params );
		$controller->execute($pairs, 1, $bwg);
		// After widget.
		echo $after_widget;
	}

  /**
   * Widget Control Panel.
   *
   * @param $params
   * @param $instance
   */
	function form($params, $instance) {
		extract($params);
		$defaults = array(
			'title' =>  __('Photo Gallery', BWG()->prefix),
			'type' => 'gallery',
			'view_type' => 'thumbnails',
			'gallery_id' => 0,
			'album_id' => 0,
			'show' => 'random',
			'count' => 4,
			'width' => 100,
			'height' => 100,
			'theme_id' => 0,
		);		
		$instance = wp_parse_args( (array) $instance, $defaults );
    if (!isset($instance['view_type'])) {
      $instance['view_type'] = "thumbnails";
    }
    ?>    
		<p>
		  <label for="<?php echo $id_title; ?>"><?php _e('Title:', BWG()->prefix); ?></label>
		  <input class="widefat" id="<?php echo $id_title; ?>" name="<?php echo $name_title; ?>'" type="text" value="<?php echo htmlspecialchars( $instance['title'] ); ?>"/>
		</p>
		<p>
		  <label for="<?php echo $id_show; ?>"><?php _e('Type:', BWG()->prefix); ?></label><br>
		  <input type="radio" name="<?php echo $name_type; ?>" id="<?php echo $id_type . "_1"; ?>" value="gallery" class="sel_gallery" onclick="bwg_change_type(event, this)" <?php if ($instance['type'] == "gallery") echo 'checked="checked"'; ?> /><label for="<?php echo $id_type . "_1"; ?>"><?php _e('Gallery', BWG()->prefix); ?></label><br>
		  <input type="radio" name="<?php echo $name_type; ?>" id="<?php echo $id_type . "_2"; ?>" value="album" class="sel_album" onclick="bwg_change_type(event, this)" <?php if ($instance['type'] == "album") echo 'checked="checked"'; ?> /><label for="<?php echo $id_type . "_2"; ?>"><?php _e('Gallery groups', BWG()->prefix); ?></label>
		</p>	
		<p id="p_galleries" style="display:<?php echo ($instance['type'] == "gallery") ? "" : "none" ?>;">
		  <label for="<?php echo $id_gallery_id; ?>"><?php _e('Galleries:', BWG()->prefix); ?></label><br>
		  <select name="<?php echo $name_gallery_id; ?>" id="<?php echo $id_gallery_id; ?>" class="widefat">
			<option value="0"><?php _e('All images', BWG()->prefix); ?></option>
			<?php
			foreach ($gallery_rows as $gallery_row) {
			  ?>
			  <option value="<?php echo $gallery_row->id; ?>" <?php echo (($instance['gallery_id'] == $gallery_row->id) ? 'selected="selected"' : ''); ?>><?php echo $gallery_row->name; ?></option>
			  <?php
			}
			?>
		  </select>
		</p>
		<p id="view_type_container" style="display: <?php echo $instance['type'] != 'album' ? 'block' : 'none'; ?>;">
		  <label for="<?php echo $id_view_type; ?>"><?php _e('Gallery Type:', BWG()->prefix); ?></label><br>
		  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="<?php echo $name_view_type; ?>" id="<?php echo $id_view_type . "_1"; ?>" value="thumbnails" class="sel_thumbnail_gallery"  <?php if (isset($instance['view_type']) && $instance['view_type'] == "thumbnails") echo 'checked="checked"';  ?> /><label for="<?php echo $id_view_type . "_1"; ?>"><?php _e('Thumbnail', BWG()->prefix); ?></label><br>
		  <input <?php echo BWG()->is_pro ? '' : 'disabled="disabled"'; ?> type="radio" name="<?php echo $name_view_type; ?>" id="<?php echo $id_view_type . "_2"; ?>" value="masonry" class="sel_masonry_gallery"  <?php if (isset($instance['view_type']) && $instance['view_type'] == "masonry") echo 'checked="checked"'; ?> /><label for="<?php echo $id_view_type . "_2"; ?>"><?php _e('Masonry', BWG()->prefix); ?></label>
      <?php if ( !BWG()->is_pro ) { ?>
      <p class="description" style="display: <?php echo $instance['type'] != 'album' ? 'block' : 'none'; ?>; background-color: #e0e0e0; border: 1px solid #c3c3c3; border-radius: 2px; color: #666666; padding: 2px;"><?php echo BWG()->free_msg; ?></p>
      <?php } ?>
		</p>
		<p id="p_albums" style="display:<?php echo ($instance['type'] == "album") ? "" : "none" ?>;">
		  <label for="<?php echo $id_album_id; ?>"><?php _e('Gallery Groups:', BWG()->prefix); ?></label><br>
		  <select name="<?php echo $name_album_id; ?>" id="<?php echo $id_album_id; ?>" class="widefat">
			<option value="0"><?php _e('All Galleries', BWG()->prefix); ?></option>
			<?php
			foreach ($album_rows as $album_row) {
			  ?>
			  <option value="<?php echo $album_row->id; ?>" <?php echo (($instance['album_id'] == $album_row->id) ? 'selected="selected"' : ''); ?>><?php echo $album_row->name; ?></option>
			  <?php
			}
			?>
		  </select>
		</p>    
		<p>
		<label for="<?php echo $id_show; ?>"><?php _e('Sort:', BWG()->prefix); ?></label><br>
		  <input type="radio" name="<?php echo $name_show; ?>" id="<?php echo $id_show . "_1"; ?>" value="random" <?php if ($instance['show'] == "random") echo 'checked="checked"'; ?> onclick='jQuery(this).nextAll(".bwg_hidden").first().attr("value", "random");' /><label for="<?php echo $id_show . "_1"; ?>"><?php _e('Random', BWG()->prefix); ?></label><br>
		  <input type="radio" name="<?php echo $name_show; ?>" id="<?php echo $id_show . "_2"; ?>" value="first" <?php if ($instance['show'] == "first") echo 'checked="checked"'; ?> onclick='jQuery(this).nextAll(".bwg_hidden").first().attr("value", "first");' /><label for="<?php echo $id_show . "_2"; ?>"><?php _e('First', BWG()->prefix); ?></label><br>
		  <input type="radio" name="<?php echo $name_show; ?>" id="<?php echo $id_show . "_3"; ?>" value="last" <?php if ($instance['show'] == "last") echo 'checked="checked"'; ?> onclick='jQuery(this).nextAll(".bwg_hidden").first().attr("value", "last");' /><label for="<?php echo $id_show . "_3"; ?>"><?php _e('Last', BWG()->prefix); ?></label>
		  <input type="hidden" name="<?php echo $name_show; ?>" id="<?php echo $id_show; ?>" value="<?php echo $instance['show']; ?>" class="bwg_hidden" />
		</p>
		<p>
		  <label for="<?php echo $id_count; ?>"><?php _e('Count:', BWG()->prefix); ?></label><br>
		  <input class="widefat" style="width:25%;" id="<?php echo $id_count; ?>" name="<?php echo $name_count; ?>'" type="text" value="<?php echo $instance['count']; ?>"/>
		</p>
		<p>
		  <label for="<?php echo $id_width; ?>"><?php _e('Dimensions:', BWG()->prefix); ?></label><br>
		  <input class="widefat" style="width:25%;" id="<?php echo $id_width; ?>" name="<?php echo $name_width; ?>'" type="text" value="<?php echo $instance['width']; ?>"/> x 
		  <input class="widefat" style="width:25%;" id="<?php echo $id_height; ?>" name="<?php echo $name_height; ?>'" type="text" value="<?php echo $instance['height']; ?>"/> px
		</p>
		<p>
		  <label for="<?php echo $id_theme_id; ?>"><?php _e('Themes:', BWG()->prefix); ?></label><br>
		  <select name="<?php echo $name_theme_id; ?>" id="<?php echo $id_theme_id; ?>" class="widefat">
			<?php
			foreach ($theme_rows as $theme_row) {
			  ?>
			  <option value="<?php echo $theme_row->id; ?>" <?php echo (($instance['theme_id'] == $theme_row->id || $theme_row->default_theme == 1) ? 'selected="selected"' : ''); ?>><?php echo $theme_row->name; ?></option>
			  <?php
			}
			?>
		  </select>
		</p>
		<script>
		  function bwg_change_type(event, obj) {
			var div = jQuery(obj).closest("div");
			if (jQuery(jQuery(div).find(".sel_gallery")[0]).prop("checked")) {
			  jQuery(jQuery(div).find("#p_galleries")).css("display", "");
			  jQuery(jQuery(div).find("#p_albums")).css("display", "none");
			  jQuery(obj).nextAll(".bwg_hidden").first().attr("value", "gallery");
			  jQuery(jQuery(div).find("#view_type_container")).css("display", "block");
			  jQuery(jQuery(div).find("#view_type_container")).next("p.description").css("display", "block");
			}
			else {
			  jQuery(jQuery(div).find("#p_galleries")).css("display", "none");
			  jQuery(jQuery(div).find("#p_albums")).css("display", "");
			  jQuery(obj).nextAll(".bwg_hidden").first().attr("value", "album");
			  jQuery(jQuery(div).find("#view_type_container")).css("display", "none");
			  jQuery(jQuery(div).find("#view_type_container")).next("p.description").css("display", "none");
			}
		  }
		</script>
    <?php
	}
}
