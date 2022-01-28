<?php

/**
 * Class FilemanagerViewwp_db
 */
class FilemanagerView {

  private $controller;
  private $model;

  /**
   * FilemanagerView constructor.
   * @param $controller
   * @param $model
   */
  public function __construct($controller, $model) {
    $this->controller = $controller;
    $this->model = $model;
  }

  /**
   * Display.
   */
  public function display( $params = array() ) {
	$dir = $params['dir'];
	$items = $params['items'];
	$path_components = $params['path_components'];
    $session = $params['session_data'];
    $sort_by = $session['sort_by'];
    $sort_order = $session['sort_order'];
    $clipboard_task = $session['clipboard_task'];
    $clipboard_files = $session['clipboard_files'];
    $clipboard_src = $session['clipboard_src'];
    $clipboard_dest = $session['clipboard_dest'];
    $items_view = $session['items_view'];
    $page_per = $params['page_per'];
    $ajax_pagination_url = $params["ajax_pagination_url"];
    $ajax_get_all_select_url = $params["ajax_get_all_select_url"];

	if (isset($_GET['filemanager_msg']) && WDWLibrary::get('filemanager_msg','','sanitize_text_field','GET') != '') {
      ?>
      <div id="file_manager_message" style="height:40px;">
        <div  style="background-color: #FFEBE8; border: 1px solid #CC0000; margin: 5px 15px 2px; padding: 5px 10px;">
          <strong style="font-size:14px"><?php echo stripslashes(WDWLibrary::get('filemanager_msg','','sanitize_text_field','GET')); ?></strong>
        </div>
      </div>
      <?php
      $_GET['filemanager_msg'] = '';
    }
    // Register and include styles and scripts.
    BWG()->register_admin_scripts();
    wp_print_scripts('jquery');
    wp_print_scripts('jquery-ui-widget');
    wp_print_scripts('wp-pointer');
    wp_print_styles('admin-bar');
    wp_print_styles('dashicons');
    wp_print_styles('wp-admin');
    wp_print_styles('buttons');
    wp_print_styles('wp-auth-check');
    wp_print_styles('wp-pointer');
    ?>
    <script src="<?php echo BWG()->plugin_url; ?>/filemanager/js/jq_uploader/jquery.iframe-transport.js?v=10.0.0"></script>
    <script src="<?php echo BWG()->plugin_url; ?>/filemanager/js/jq_uploader/jquery.fileupload.js?v=10.0.0"></script>
    <script>
	  var DS = "<?php echo addslashes('/'); ?>";
      var demo_message = "<?php echo addslashes(__('This option is disabled in demo.', BWG()->prefix)); ?>";
      var ajaxurl = "<?php echo wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' ); ?>";
      var ajax_pagination_url = "<?php echo $ajax_pagination_url; ?>";
	  var ajax_get_all_select_url = "<?php echo $ajax_get_all_select_url; ?>";
      var errorLoadingFile = "<?php echo __('File loading failed', BWG()->prefix); ?>";
      var warningRemoveItems = "<?php echo __('Are you sure you want to permanently remove selected items?', BWG()->prefix); ?>";
      var warningCancelUploads = "<?php echo __('This will cancel uploads. Continue?', BWG()->prefix); ?>";
      var messageEnterDirName = "<?php echo __('Enter directory name', BWG()->prefix); ?>";
      var messageEnterNewName = "<?php echo __('Enter new name', BWG()->prefix); ?>";
      var messageFilesUploadComplete = "<?php echo __('Processing uploaded files...', BWG()->prefix); ?>";
      var root = "<?php echo addslashes($this->controller->get_uploads_dir()); ?>";
      var dir = "<?php echo (isset($_REQUEST['dir']) ? WDWLibrary::get('dir','','sanitize_text_field','REQUEST') : ''); ?>";
      var dirUrl = "<?php echo $this->controller->get_uploads_url() . (isset($_REQUEST['dir']) ? WDWLibrary::get('dir','','sanitize_text_field','REQUEST') . '/' : ''); ?>";
      var callback = "<?php echo (isset($_REQUEST['callback']) ? WDWLibrary::get('callback','','sanitize_text_field','REQUEST') : ''); ?>";
      var sortBy = "<?php echo $sort_by; ?>";
      var sortOrder = "<?php echo $sort_order; ?>";
      var page_per = "<?php echo $page_per; ?>";
    </script>
    <script src="<?php echo BWG()->plugin_url; ?>/filemanager/js/default.js?ver=<?php echo BWG()->plugin_version; ?>"></script>
    <?php
    do_action( 'bwg_filemanager_scripts_after' );
    ?>
    <link href="<?php echo BWG()->plugin_url; ?>/filemanager/css/default.css?ver=<?php echo BWG()->plugin_version; ?>" type="text/css" rel="stylesheet">
	<link href="<?php echo BWG()->plugin_url; ?>/filemanager/css/default_view_<?php echo $items_view; ?>.css?ver=<?php echo BWG()->plugin_version; ?>" type="text/css" rel="stylesheet">
	<form id="adminForm" name="adminForm" action="" method="post" class="wp-core-ui">
      <?php wp_nonce_field( '', 'bwg_nonce' ); ?>
      <div id="wrapper">
		<div id="file_manager">
          <div class="ctrls_bar ctrls_bar_header">
            <div class="ctrls_left header_bar">
              <span class="dashicons dashicons-arrow-up-alt ctrl_bar_btn" onclick="onBtnUpClick(event, this);" title="<?php echo __('Up', BWG()->prefix); ?>"></span>
              <span class="dashicons dashicons-category ctrl_bar_btn" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnMakeDirClick(event, this)'); ?>" title="<?php echo __('Make a directory', BWG()->prefix); ?>"></span>
              <span class="dashicons dashicons-edit ctrl_bar_btn" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnRenameItemClick(event, this)'); ?>" title="<?php echo __('Rename item', BWG()->prefix); ?>"></span>
              <span class="ctrl_bar_divider">|</span>
              <span class="dashicons dashicons-admin-page ctrl_bar_btn"  onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnCopyClick(event, this)'); ?>" title="<?php echo __('Copy', BWG()->prefix); ?>"></span>
              <span class="dashicons dashicons-media-document ctrl_bar_btn" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnCutClick(event, this)'); ?>" title="<?php echo __('Cut', BWG()->prefix); ?>"></span>
              <span class="dashicons dashicons-editor-paste-text ctrl_bar_btn" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnPasteClick(event, this)'); ?>" title="<?php echo __('Paste', BWG()->prefix); ?>"></span>
              <span class="dashicons dashicons-trash ctrl_bar_btn" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnRemoveItemsClick(event, this)'); ?>" title="<?php echo __('Remove items', BWG()->prefix); ?>"></span>
              <span class="ctrl_bar_divider">|</span>
              <span class="dashicons dashicons-update ctrl_bar_btn" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnParsingItemsClick(event, this)'); ?>" title="<?php echo __('Refresh', BWG()->prefix); ?>"></span>
              <span class="ctrl_bar_divider">|</span>
            </div>
            <div class="ctrls_right">
              <span class="dashicons dashicons-grid-view ctrl_bar_btn" onclick="onBtnViewThumbsClick(event, this);" title="<?php echo __('View thumbs', BWG()->prefix); ?>"></span>
              <span class="dashicons dashicons-list-view ctrl_bar_btn" onclick="onBtnViewListClick(event, this);" title="<?php echo __('View list', BWG()->prefix); ?>"></span>
            </div>
            <div class="ctrls_left header_bar">
              <span id="upload_images_cont" class="ctrl_bar_btn">
                <a id="upload_images" class="button button-primary button-large" onclick="<?php echo (BWG()->is_demo ? 'alert(demo_message)' : 'onBtnShowUploaderClick(event, this)'); ?>"><?php echo __('Upload files', BWG()->prefix); ?></a>
              </span>
              <span id="search_by_name" class="ctrl_bar_btn">
                <input type="search" placeholder="Search" class="ctrl_bar_btn search_by_name">
              </span>
            </div>
          </div>
          <div id="path">
            <?php foreach ( $path_components as $key => $path_component ) { ?>
              <a class="path_component path_dir" onclick="onPathComponentClick(event, this, <?php echo $key; ?>);">
                <?php echo str_replace( BWG()->abspath, '', $path_component['name'] ); ?>
              </a>
              <a class="path_component path_separator"><?php echo '/'; ?></a>
            <?php } ?>
          </div>
          <div id="explorer">
            <div id="explorer_header_wrapper">
              <div id="explorer_header_container">
                <div id="explorer_header">
                  <span class="item_numbering"><?php echo $items_view == 'thumbs' ? __('Order by:', BWG()->prefix) : '#'; ?></span>
                  <span class="item_icon"></span>
                  <span class="item_name" title="<?php _e('Click to sort by name', BWG()->prefix); ?>">
                    <span class="clickable" onclick="onNameHeaderClick(event, this);">
                        <?php
                        echo '<span>'.__('Name', BWG()->prefix).'</span>';
                        if ($sort_by == 'name') {
                          if( $sort_order == 'asc' ){
                          ?>
                            <span class="dashicons dashicons-arrow-up"></span>
                          <?php } else { ?>
                            <span class="dashicons dashicons-arrow-down"></span>
                          <?php
                          }
                        }
                        ?>
                    </span>
                  </span>
                  <span class="item_size" title="<?php _e('Click to sort by size', BWG()->prefix); ?>">
                    <span class="clickable" onclick="onSizeHeaderClick(event, this);">
                      <?php
                      echo '<span>'.__('Size', BWG()->prefix).'</span>';
                      if ($sort_by == 'size') {
                        if( $sort_order == 'asc' ){
                        ?>
                          <span class="dashicons dashicons-arrow-up"></span>
                        <?php } else { ?>
                          <span class="dashicons dashicons-arrow-down"></span>
                        <?php
                        }
                      }
                      ?>
                    </span>
                  </span>
                  <span class="item_date_modified" title="<?php _e('Click to sort by date modified', BWG()->prefix); ?>">
                    <span class="clickable" onclick="onDateModifiedHeaderClick(event, this);">
                      <?php
                      echo '<span>'.__('Date modified', BWG()->prefix).'</span>';
                      if ($sort_by == 'date_modified') {
                        if( $sort_order == 'asc' ){
                        ?>
                          <span class="dashicons dashicons-arrow-up"></span>
                        <?php } else { ?>
                          <span class="dashicons dashicons-arrow-down"></span>
                        <?php
                        }
                      }
                      ?>
                    </span>
                  </span>
                  <span class="scrollbar_filler"></span>
                </div>
              </div>
            </div>
            <div id="explorer_body_wrapper">
              <div id="explorer_body_container">
                <div id="explorer_body" data-page_per=<?php echo $items['page_per']; ?> data-files_count="<?php echo $items["num_rows"]; ?>">
                  <?php
                  $i = 0;
                  foreach ( $items['files'] as $key => $file ) {
                    if ( BWG()->is_demo !== '1' || strpos( $file[ 'date_modified' ], '20 July 2014' ) === FALSE ) {
                      $file['index'] = ++$i;
                      echo $this->print_file_thumb($file);
                    }
                  }
                  ?>
                </div>
				<div class="fm-no-found-wrap">
					<h2><?php _e('No items found.', BWG()->prefix); ?></h2>
				</div>
              </div>
            </div>
          </div>
          <div class="ctrls_bar ctrls_bar_footer">
            <div class="ctrls_left">
              <a id="select_all_images" class="button button-primary button-large" onclick="onBtnSelectAllClick('<?php echo $dir; ?>');"><?php echo __('Select All', BWG()->prefix); ?></a>
            </div>
            <div class="ctrls_right">
              <span id="file_names_span">
                <span>
                </span>
              </span>
              <?php
              $add_image_btn = (isset($_REQUEST['callback']) && WDWLibrary::get('callback','','sanitize_text_field','REQUEST') == 'bwg_add_image') ? __('Add selected images to gallery', BWG()->prefix) : __('Add', BWG()->prefix);
              ?>
              <a id="add_selectid_img" title="<?php echo $add_image_btn; ?>" class="button button-primary button-large" onclick="window.parent.bwg_create_loading_block(); onBtnOpenClick(event, this);">
                <div id="bwg_img_add"><?php echo $add_image_btn; ?></div>
              </a>
              <a class="button button-secondary button-large" title="<?php _e('Cancel', BWG()->prefix); ?>" onclick="onBtnCancelClick(event, this);">
                <div id="bwg_img_cancel"><?php _e('Cancel', BWG()->prefix); ?></div>
              </a>
            </div>
          </div>
        </div>
        <div id="uploader">
          <div id="uploader_bg"></div>
          <div class="ctrls_bar ctrls_bar_header">
						<div class="ctrls_left">
							<span class="dashicons dashicons-arrow-left-alt ctrl_bar_btn" onclick="onBtnBackClick(event, this);" title="<?php echo __('Back', BWG()->prefix); ?>"></span>
						</div>
						<div class="ctrls_left upload_thumb">
              <div class="upload_thumb thumb_full_title"><?php _e("Thumbnail Max Dimensions:", BWG()->prefix); ?></div>
              <div class="upload_thumb thumb_title"><?php _e("Thumbnail:", BWG()->prefix); ?></div>
              <input type="text" class="upload_thumb_dim" name="upload_thumb_width" id="upload_thumb_width" value="<?php echo BWG()->options->upload_thumb_width; ?>" /> x
              <input type="text" class="upload_thumb_dim" name="upload_thumb_height" id="upload_thumb_height" value="<?php echo BWG()->options->upload_thumb_height; ?>" /> px
            </div>
            <div class="ctrls_right_img upload_thumb">
              <div class="upload_thumb thumb_full_title"><?php _e("Image Max Dimensions:", BWG()->prefix); ?></div>
              <div class="upload_thumb thumb_title"><?php _e("Image:", BWG()->prefix); ?></div>
              <input type="text" class="upload_thumb_dim" name="upload_img_width" id="upload_img_width" value="<?php echo BWG()->options->upload_img_width; ?>" /> x
              <input type="text" class="upload_thumb_dim" name="upload_img_height" id="upload_img_height" value="<?php echo BWG()->options->upload_img_height; ?>" /> px
            </div>
          </div>
          <label for="jQueryUploader">
            <div id="uploader_hitter">
              <div id="btnBrowseContainer">
				<div class="bwg-select-file-text"><?php _e('Drag files here', BWG()->prefix); ?><br><?php _e('or', BWG()->prefix); ?><br><span class="button"><?php _e('Select Files', BWG()->prefix); ?></span></div>
				<?php
				  $query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'bwg_UploadHandler', 'bwg_nonce' );
				  $query_url = add_query_arg(array('action' => 'bwg_UploadHandler', 'dir' => (isset($_REQUEST['dir']) ? WDWLibrary::get('dir','','sanitize_text_field','REQUEST') : '') . '/'), $query_url);
				?>
                <input id="jQueryUploader" type="file" name="files[]"
				   data-url="<?php echo $query_url; ?>"
				   multiple>
              </div>
             </div>
          </label>
          <div id="bwg-errors-wrap">
			<ul class="bwg-files-item"></ul>
		  </div>
          <div id="uploader_progress">
            <div id="uploader_progress_bar">
              <div></div>
            </div>
            <span id="uploader_progress_text" class="uploader_text">
              <?php echo __('No files to upload', BWG()->prefix); ?>
            </span>
          </div>
        </div>
      </div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="extensions" value="<?php echo (isset($_REQUEST['extensions']) ? WDWLibrary::get('extensions','','sanitize_text_field','REQUEST') : '*'); ?>" />
      <input type="hidden" name="callback" value="<?php echo (isset($_REQUEST['callback']) ? WDWLibrary::get('callback','','sanitize_text_field','REQUEST') : ''); ?>" />
      <input type="hidden" name="sort_by" value="<?php echo $sort_by; ?>" />
      <input type="hidden" name="sort_order" value="<?php echo $sort_order; ?>" />
      <input type="hidden" name="items_view" value="<?php echo $items_view; ?>" />
      <input type="hidden" name="dir" value="<?php echo (isset($_REQUEST['dir']) ? str_replace('\\', '', (WDWLibrary::get('dir','','sanitize_text_field','REQUEST'))) : ''); ?>" />
      <input type="hidden" name="file_names" value="" />
      <input type="hidden" name="file_namesML" value="" />
      <input type="hidden" name="file_new_name" value="" />
      <input type="hidden" name="new_dir_name" value="" />
      <input type="hidden" name="clipboard_task" value="<?php echo $clipboard_task; ?>" />
      <input type="hidden" name="clipboard_files" value="<?php echo $clipboard_files; ?>" />
      <input type="hidden" name="clipboard_src" value="<?php echo $clipboard_src; ?>" />
      <input type="hidden" name="clipboard_dest" value="<?php echo $clipboard_dest; ?>" />
    </form>
	<script>
		allowed_files = [];
		not_uploading_files = [];
		errorFiles = {};
		errorMessages = {};
		messages = {
			'uploaded' : '<?php _e('Uploaded', BWG()->prefix); ?>',
			'upload_failed' : '<?php _e('Upload failed', BWG()->prefix); ?>',
			'upload_problem': '<?php _e('There has been a problem while trying to upload the following images. Please try to upload them again.', BWG()->prefix); ?>',
			'allowed_upload_types' : '<?php _e('Allowed upload types JPG, JPEG, GIF, PNG, SVG.', BWG()->prefix); ?>'
		};
    last_uploaded = [];
		jQuery(function() {
			jQuery("#loading_div", window.parent.document).hide();
      if (localStorage.getItem("bwg_selected_images")) {
        var bwg_selected_images = localStorage.getItem("bwg_selected_images").split(",");
        filesSelected = bwg_selected_images;
        jQuery(".explorer_item").each(function () {
          if (bwg_selected_images.includes(jQuery(this).attr("name")) > 0) {
            jQuery(this).addClass("explorer_item_select");
          }
        });
        localStorage.removeItem("bwg_selected_images");
      }
		});
		jQuery("#jQueryUploader").fileupload({
		  dataType: "json",
		  dropZone: jQuery("#uploader_hitter"),
		  limitConcurrentUploads: 10, // upload step by step
		  acceptFileTypes: /(\.|\/)(jpe?g|gif|png|svg)$/i,
		  submit: function (e, data) {
      localStorage.removeItem( "bwg_selected_images" );
			isUploading = true;
			jQuery("#uploader_progress_text").removeClass("uploader_text");
			jQuery("#uploader_progress_bar").fadeIn();
		  },
		  progressall: function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			jQuery("#uploader_progress_text").text("Progress " + progress + "%");
			jQuery("#uploader_progress div div").css({width: progress + "%"});
			if ( data.loaded == data.total ) {
			  isUploading = false;
			  jQuery("#uploader_progress_bar").fadeOut(function () {
          jQuery("#uploader_progress_text").text(messageFilesUploadComplete);
          jQuery("#uploader_progress_text").addClass("uploader_text");
			  });
			}
		  },
		  stop: function (e, data) {
			jQuery("#bwg-errors-wrap .errors").remove();
			jQuery("#bwg-errors-wrap .bwg-files-item").html('');
			if ( errorMessages && Object.keys(errorMessages).length > 0 ) {
				var html = '';
				jQuery.each( errorMessages, function( index, message ) {
					html += '<div class="errors ' + index + '">';
						html += '<div class="error"><p>' + message + '</p></div>';
						if ( errorFiles[index]  && errorFiles[index].length > 0 ) {
							html += '<ul class="bwg-files-item">';
								jQuery.each( errorFiles[index], function( key, value ) {
									html += '<li class="uploaded_item_failed">' + value + ' (' + messages.upload_failed + ')</li>';
								});
							html += '</ul>';
							errorFiles[index] = {};
						}
					html += '</div>';
				});
				jQuery("#bwg-errors-wrap").prepend( html );
				errorMessages = {};
				allowed_files = [];
				not_uploading_files = [];
			}
			else {
				onBtnBackClick();
			}
		  },
		  done: function (e, data) {
			jQuery("#bwg-errors-wrap .errors").remove();
			var html = '';
			jQuery.each( data.result.files, function (index, file) {
				if ( file.error ) {
					allowed_files.push( file.name );
					errorFiles['allowed'] = allowed_files;
					errorMessages['allowed'] = messages.allowed_upload_types;
					html += '<li class="uploaded_item_failed">' + file.name + ' (' + messages.upload_failed + ')</li>';
				}
				else {
					html += '<li class="uploaded_item">' + file.name + ' (' + messages.uploaded + ')</li>';
          last_uploaded.push( file.name );
				}
				jQuery("#bwg-errors-wrap .bwg-files-item").prepend( html );
			});
      localStorage.setItem( "bwg_selected_images", last_uploaded );
		  },
		  fail: function (e, data) {
			  if ( data.textStatus == 'error' ) {
				var filename = data.files[0].name;
				var regex = /\.(jpe?g|png|gif|svg)$/i;
				if ( ! regex.test(filename) ) {
					allowed_files.push(filename);
					errorFiles['allowed'] = allowed_files;
					errorMessages['allowed'] = messages.allowed_upload_types;
					return;
				}
				not_uploading_files.push( filename );
				errorFiles['not_uploading'] = not_uploading_files;
				errorMessages['not_uploading'] = messages.upload_problem;
			  }
		  }
		});
	</script>
    <?php
    die();
  }

  /**
   * Print file thumb.
   *
   * @param array $file
   *
   * @return false|string
   */
	function print_file_thumb( $file = array() ) {
		ob_start();
		$file['name'] = esc_html( $file['name'] );
		$file['filename'] = esc_html( $file['filename'] );
		$file['alt' ] = esc_html( $file['alt'] );
		$file['thumb'] = esc_html( $file['thumb'] );
		$file['credit'] = esc_html( $file['credit'] );
		$file['aperture'] = esc_html( $file['aperture'] );
		$file['camera'] = esc_html( $file['camera'] );
		$file['caption'] = esc_html( $file['caption'] );
		$file['copyright'] = esc_html( $file['copyright'] );
		$file['tags'] = esc_html( $file['tags'] );
		?>
		<div class="explorer_item"
			name="<?php echo $file['name']; ?>"
			filename="<?php echo $file['filename']; ?>"
			alt="<?php echo $file['alt']; ?>"
			filethumb="<?php echo $file['thumb']; ?>"
			filesize="<?php echo $file['size']; ?>"
			filetype="<?php echo strtoupper( $file['type'] ); ?>"
			date_modified="<?php echo $file['date_modified']; ?>"
			fileresolution="<?php echo $file['resolution']; ?>"
			fileresolution_thumb="<?php echo $file['resolution_thumb']; ?>"
			fileCredit="<?php echo isset( $file['credit'] ) ? $file['credit'] : ''; ?>"
			fileAperture="<?php echo isset( $file['aperture'] ) ? $file['aperture'] : ''; ?>"
			fileCamera="<?php echo isset( $file['camera'] ) ? $file['camera'] : ''; ?>"
			fileCaption="<?php echo isset( $file['caption'] ) ? $file['caption'] : ''; ?>"
			fileIso="<?php echo isset( $file['iso'] ) ? $file['iso'] : ''; ?>"
			fileOrientation="<?php echo isset( $file['orientation'] ) ? $file['orientation'] : ''; ?>"
			fileCopyright="<?php echo isset( $file['copyright'] ) ? $file['copyright'] : ''; ?>"
			fileTags='<?php echo isset( $file['tags'] ) ? $file['tags'] : ''; ?>'
			onmouseover="onFileMOver(event, this);"
			onmouseout="onFileMOut(event, this);"
			onclick="onFileClick(event, this);"
			ondblclick="onFileDblClick(event, this);"
			<?php if ( $file[ 'is_dir' ] == true ) { ?>
				ontouchend="onFileDblClick(event, this);"
			<?php } ?>
			isDir="<?php echo ($file[ 'is_dir' ] == true) ? 'true' : 'false'; ?>">
				<span class="item_numbering"><?php echo $file['index']; ?></span>
				<span class="item_thumb"><img src="<?php echo $file[ 'thumb' ]; ?>" /></span>
				<span class="item_icon"><img src="<?php echo $file[ 'thumb' ]; ?>" /></span>
				<span class="item_name"><?php echo $file['name']; ?></span>
				<span class="item_size"><?php echo $file['size']; ?></span>
				<span class="item_date_modified"><?php echo $file['date_modified']; ?></span>
		</div>
		<?php
		return ob_get_clean();
	}
}