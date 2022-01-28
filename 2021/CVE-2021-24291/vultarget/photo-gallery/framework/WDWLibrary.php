<?php

class WDWLibrary {

  public static $shortcode_ids = array();

  public static $thumb_dimansions;

  /**
   * Get request value.
   *
   * @param string $key
   * @param string $default_value
   * @param string $callback
   *
   * @return string|array
   */
  public static function get($key, $default_value = '', $callback = 'sanitize_text_field', $type = 'DEFAULT') {
    switch ($type) {
      case 'REQUEST' :
        if (isset($_REQUEST[$key])) {
          $value = $_REQUEST[$key];
        }
        break;
      case 'DEFAULT' :
      case 'POST' :
        if (isset($_POST[$key])) {
          $value = $_POST[$key];
        }
        if ( 'POST' === $type ) break;
      case 'GET' :
        if (isset($_GET[$key])) {
          $value = $_GET[$key];
        }
        break;
    }
    if ( !isset($value) ) {
      if( $default_value === NULL ) {
        return NULL;
      } else {
        $value = $default_value;
      }
    }

    if( is_bool($value) ) {
      return $value;
    }

    if (is_array($value)) {
      // $callback should be third parameter of the validate_data function, so there is need to add unused second parameter to validate_data function.
      array_walk_recursive($value, array('self', 'validate_data'), $callback);
    }
    else {
      self::validate_data($value, 0, $callback);
    }

    return $value;
  }

  /**
   * Validate data.
   *
   * @param $value
   * @param $key
   * @param $callback
   */
  private static function validate_data(&$value, $key, $callback) {
    $value = stripslashes($value);
    if ( $callback && function_exists($callback) ) {
      $value = $callback($value);
    }
  }

  /**
   * Generate message container  by message id or directly by message.
   *
   * @param int $message_id
   * @param string $message If message_id is 0
   * @param string $type
   *
   * @return mixed|string|void
   */
  public static function message_id($message_id, $message = '', $type = 'updated') {
    if ($message_id) {
      switch($message_id) {
        case 1: {
          $message = __('Item successfully saved.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 2: {
          $message = __('Failed.', BWG()->prefix);
          $type = 'error';
          break;

        }
        case 3: {
          $message = __('Item successfully deleted.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 4: {
          $message = __("You can't delete default theme.", BWG()->prefix);
          $type = 'error';
          break;

        }
        case 5: {
          $message = __('Items Successfully Deleted.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 6: {
          $message = __('You must set watermark type from Options page.', BWG()->prefix);
          $type = 'wd_error';
          break;

        }
        case 7: {
          $message = __('The item is successfully set as default.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 8: {
          $message = __('Options successfully saved.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 9: {
          $message = __('Item successfully published.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 10: {
          $message = __('Item successfully unpublished.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 11: {
          $message = __('Item successfully duplicated.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 12: {
          // ToDO: delete
          $message = __('Items Succesfully Unpublished.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 13: {
          $message = __('Ordering Succesfully Saved.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 14: {
          $message = __('A term with the name provided already exists.', BWG()->prefix);
          $type = 'error';
          break;

        }
        case 15: {
          $message = __('Name field is required.', BWG()->prefix);
          $type = 'error';
          break;

        }
        case 16: {
          $message = __('The slug must be unique.', BWG()->prefix);
          $type = 'error';
          break;

        }
        case 17: {
          $message = __('Changes must be saved.', BWG()->prefix);
          $type = 'error';
          break;

        }
         case 18: {
          $message = __('Theme successfully copied.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        case 19: {
          $message = __('Failed.', BWG()->prefix);
          $type = 'error';
          break;
        }
        case 20: {
          $message = __('Items were reset successfully.', BWG()->prefix);
          $type = 'updated';
          break;
        }
        case 21: {
          $message = __('Watermark successfully set.', BWG()->prefix);
          $type = 'updated';
          break;
        }
        case 22: {
          $message = __('Items successfully rotated.', BWG()->prefix);
          $type = 'updated';
          break;
        }
        case 23: {
          $message = __('Items successfully recreated.', BWG()->prefix);
          $type = 'updated';
          break;
        }
        case 24: {
          $message = __('Items successfully resized.', BWG()->prefix);
          $type = 'updated';
          break;
        }
        case 25: {
          $message = __('Items successfully edited.', BWG()->prefix);
          $type = 'updated';
          break;
        }
		    case 26: {
          $message = __('Watermark could not be set. The image URL is incorrect.', BWG()->prefix);
          $type = 'error';
          break;
        }
        case 27: {
          $message = __('http:// wrapper is disabled in the server configuration by allow_url_fopen=0.', BWG()->prefix);
          $type = 'error';
          break;
        }
        case 28: {
          $message = __('All items are successfully duplicated.', BWG()->prefix);
          $type = 'updated';
          break;

        }
        default: {
          $message = '';
          break;
        }
      }
    }
    if ($message) {
      ob_start();
      ?><div class="<?php echo $type; ?> inline">
      <p>
        <strong><?php echo $message; ?></strong>
      </p>
      </div><?php
      $message = ob_get_clean();
    }

    return $message;
  }

  public static function message($message, $type) {
    return '<div style="width:100%"><div class="' . $type . '"><p><strong>' . $message . '</strong></p></div></div>';
  }

  /**
   * Ordering.
   *
   * @param        $id
   * @param        $orderby
   * @param        $order
   * @param        $text
   * @param        $page_url
   * @param string $additional_class
   *
   * @return string
   */
  public static function ordering($id, $orderby, $order, $text, $page_url, $additional_class = '') {
    $class = array(
      ($orderby == $id ? 'sorted': 'sortable'),
      $order,
      $additional_class,
      'col_' . $id,
    );
    $order = (($orderby == $id) && ($order == 'asc')) ? 'desc' : 'asc';
    ob_start();
    ?>
    <th id="order-<?php echo $id; ?>" class="<?php echo implode(' ', $class); ?>">
      <a href="<?php echo add_query_arg( array('orderby' => $id, 'order' => $order), $page_url ); ?>"
         title="<?php _e('Click to sort by this item', BWG()->prefix); ?>">
        <span><?php echo $text; ?></span><span class="sorting-indicator"></span>
      </a>
    </th>
    <?php
    return ob_get_clean();
  }

  /**
   * Possible choices to order images in admin page.
   *
   * @return array
   */
  public static function admin_images_ordering_choices() {
    return array(
      'order_asc' => 'Default sorting',
      'filename_asc' => 'File name (Asc)',
      'filename_desc' => 'File name (Desc)',
      'alt_asc' => 'Alt/Title (Asc)',
      'alt_desc' => 'Alt/Title (Desc)',
      'description_asc' => 'Description (Asc)',
      'description_desc' => 'Description (Desc)',
    );
  }

  /**
   * Redirect.
   *
   * @param $url
   */
  public static function redirect($url) {
    $url = html_entity_decode(wp_nonce_url($url, BWG()->nonce, BWG()->nonce));
    ?>
    <script>
      window.location = "<?php echo $url; ?>";
    </script>
    <?php
    exit();
  }

  public static function search($search_by, $search_value, $form_id, $position_search) {
    if($position_search != ''){
      $position_search = 'alignleft';
      $margin_right = 0;
    }
    else {
      $position_search = 'alignright';
      $margin_right = 5;
    }
    ?>
    <div class="<?php echo  $position_search; ?>  actions" style="clear:both;">
      <script>
        function spider_search() {
          document.getElementById("page_number").value = "1";
          document.getElementById("search_or_not").value = "search";
          document.getElementById("<?php echo $form_id; ?>").submit();
        }
        function spider_reset() {
          if (document.getElementById("search_value")) {
            document.getElementById("search_value").value = "";
          }
          if (document.getElementById("search_select_value")) {
            document.getElementById("search_select_value").value = 0;
          }
          document.getElementById("<?php echo $form_id; ?>").submit();
        }
        function check_search_key(e, that) {
          if ( e.key == 'Enter') { /*Enter keycode*/
            spider_search();
            return false;
          }
          return true;
        }
      </script>
      <div class="alignleft actions">
        <label for="search_value" style="font-size:14px; width:50px; display:inline-block;"><?php echo $search_by; ?>:</label>
        <input type="text" id="search_value" name="search_value" class="spider_search_value" onkeypress="return check_search_key(event, this);" value="<?php echo esc_html($search_value); ?>" style="width: 150px;margin-right:<?php echo $margin_right; ?>px; padding-top:10px; <?php echo (get_bloginfo('version') > '3.7') ? ' height: 33px;' : ''; ?>" />
      </div>
      <div class="alignleft actions">
        <input type="button" value="" title="<?php _e('Search',BWG()->prefix); ?>" onclick="spider_search()" class="wd-search-btn action">
        <input type="button" value="" title="<?php _e('Reset',BWG()->prefix); ?>" onclick="spider_reset()" class="wd-reset-btn action">
      </div>
    </div>
    <?php
  }

  public static function search_select($search_by, $search_select_id = 'search_select_value', $search_select_value, $playlists, $form_id) {
    ?>
      <script>
        function spider_search_select() {
          document.getElementById("page_number").value = "1";
          document.getElementById("search_or_not").value = "search";
          document.getElementById("<?php echo $form_id; ?>").submit();
        }
      </script>
      <div class="alignleft actions" >
        <select id="<?php echo $search_select_id; ?>" name="<?php echo $search_select_id; ?>" class="select_icon" onchange="spider_search_select();" style="width:150px; margin-left:5px;">        
        <?php
          foreach ($playlists as $id => $playlist) {
            ?>
            <option value="<?php echo $id; ?>" <?php echo (($search_select_value == $id) ? 'selected="selected"' : ''); ?>><?php echo $playlist; ?></option>
            <?php
          }
        ?>
        </select>
      </div>
    
    <?php
  }
  
  public static function html_page_nav($count_items, $pager, $page_number, $form_id, $items_per_page = 20) {
    $limit = $items_per_page;
    if ($count_items) {
      if ($count_items % $limit) {
        $items_county = ($count_items - $count_items % $limit) / $limit + 1;
      }
      else {
        $items_county = ($count_items - $count_items % $limit) / $limit;
      }
    }
    else {
      $items_county = 1;
    }
    if ($count_items > $items_per_page) {
      $margin_top = 0;
    }
    else {
      $margin_top = 12;
    }
    if (!$pager) {
    ?>
    <script type="text/javascript">
      var items_county = <?php echo $items_county; ?>;
      function spider_page(x, y) {       
        switch (y) {
          case 1:
            if (x >= items_county) {
              document.getElementById('page_number').value = items_county;
            }
            else {
              document.getElementById('page_number').value = x + 1;
            }
            break;
          case 2:
            document.getElementById('page_number').value = items_county;
            break;
          case -1:
            if (x == 1) {
              document.getElementById('page_number').value = 1;
            }
            else {
              document.getElementById('page_number').value = x - 1;
            }
            break;
          case -2:
            document.getElementById('page_number').value = 1;
            break;
          default:
            document.getElementById('page_number').value = 1;
        }
        document.getElementById('<?php echo $form_id; ?>').submit();
      }
      function check_enter_key(e, that) {
        if ( e.key == 'Enter' ) { /*Enter keycode*/
          if (jQuery(that).val() >= items_county) {
           document.getElementById('page_number').value = items_county;
          }
          else {
           document.getElementById('page_number').value = jQuery(that).val();
          }
          document.getElementById('<?php echo $form_id; ?>').submit();
        }
        return true;
      }
    </script>
    <?php } ?>
    <div class="alignright tablenav-pages" >
      <span class="displaying-num">
        <?php
        if ($count_items != 0) {
          echo $count_items; ?> <?php echo __('item', BWG()->prefix); ?><?php echo (($count_items == 1) ? '' : 's');
        }
        ?>
      </span>
      <?php
      if ($count_items > $items_per_page) {
        $first_page = "first-page";
        $prev_page = "prev-page";
        $next_page = "next-page";
        $last_page = "last-page";
        if ($page_number == 1) {
          $first_page = "first-page disabled";
          $prev_page = "prev-page disabled";
          $next_page = "next-page";
          $last_page = "last-page";
        }
        if ($page_number >= $items_county) {
          $first_page = "first-page ";
          $prev_page = "prev-page";
          $next_page = "next-page disabled";
          $last_page = "last-page disabled";
        }
      ?>
      <span class="pagination-links">
        <a class="<?php echo $first_page; ?>" title="Go to the first page" href="javascript:spider_page(<?php echo $page_number; ?>,-2);">«</a>
        <a class="<?php echo $prev_page; ?>" title="Go to the previous page" href="javascript:spider_page(<?php echo $page_number; ?>,-1);">‹</a>
        <span class="paging-input">
          <span class="total-pages">
          <input class="current_page" id="current_page" name="current_page" value="<?php echo $page_number; ?>" onkeypress="return check_enter_key(event, this)" title="Go to the page" type="text" size="1" />
        </span> <?php echo __('of', BWG()->prefix); ?>
        <span class="total-pages">
            <?php echo $items_county; ?>
          </span>
        </span>
        <a class="<?php echo $next_page ?>" title="Go to the next page" href="javascript:spider_page(<?php echo $page_number; ?>,1);">›</a>
        <a class="<?php echo $last_page ?>" title="Go to the last page" href="javascript:spider_page(<?php echo $page_number; ?>,2);">»</a>
        <?php
      }
      ?>
      </span>
    </div>
    <?php if (!$pager) { ?>
    <input type="hidden" id="page_number"  name="page_number" value="<?php echo self::get('page_number', 1, 'intval'); ?>" />
    <input type="hidden" id="search_or_not" name="search_or_not" value="<?php echo self::get('search_or_not'); ?>"/>
    <?php
    }
  }
  public static function ajax_search($search_by, $search_value, $form_id) {
    ?>
    <div class="alignright actions" style="clear:both;">
      <script>
        function spider_search() {
          document.getElementById("page_number").value = "1";
          /*document.getElementById("search_or_not").value = "search";*/
          spider_ajax_save('<?php echo $form_id; ?>');
        }
        function spider_reset() {
          if (document.getElementById("search_value")) {
            document.getElementById("search_value").value = "";
          }
          spider_ajax_save('<?php echo $form_id; ?>');
        }        
        function check_search_key(e, that) {
          if ( e.key == 'Enter' ) { /*Enter keycode*/
            spider_search();
            return false;
          }
          return true;
        }
      </script>
      <div class="alignleft actions">
        <label for="search_value" style="font-size:14px; width:60px; display:inline-block;"><?php echo $search_by; ?>:</label>
        <input type="text" id="search_value" name="search_value" class="spider_search_value" onkeypress="return check_search_key(event, this);" value="<?php echo esc_html($search_value); ?>" style="width: 150px;margin-right:5px;<?php echo (get_bloginfo('version') > '3.7') ? ' height: 33px;' : ''; ?>" />
      </div>
      <div class="alignleft actions">
        <input type="button" value="" title="<?php echo __('Search',BWG()->prefix); ?>" onclick="spider_search()" class="wd-search-btn action">
        <input type="button" value="" title="<?php echo __('Reset',BWG()->prefix); ?>" onclick="spider_reset()" class="wd-reset-btn action">
      </div>
    </div>
    <?php
  }

  public static function ajax_html_page_nav($count_items, $page_number, $form_id, $items_per_page = 20, $pager = 0) {
    $limit = $items_per_page;
    if ($count_items) {
      if ($count_items % $limit) {
        $items_county = ($count_items - $count_items % $limit) / $limit + 1;
      }
      else {
        $items_county = ($count_items - $count_items % $limit) / $limit;
      }
    }
    else {
      $items_county = 1;
    }
    if (!$pager) {
    ?>
    <script type="text/javascript">
      var items_county = <?php echo $items_county; ?>;
      function spider_page(x, y) {
        switch (y) {
          case 1:
            if (x >= items_county) {
              document.getElementById('page_number').value = items_county;
            }
            else {
              document.getElementById('page_number').value = x + 1;
            }
            break;
          case 2:
            document.getElementById('page_number').value = items_county;
            break;
          case -1:
            if (x == 1) {
              document.getElementById('page_number').value = 1;
            }
            else {
              document.getElementById('page_number').value = x - 1;
            }
            break;
          case -2:
            document.getElementById('page_number').value = 1;
            break;
          default:
            document.getElementById('page_number').value = 1;
        }
        spider_ajax_save('<?php echo $form_id; ?>');
      }
      function check_enter_key(e, that) {
        if ( e.key == 'Enter' ) { /*Enter keycode*/
          if (jQuery(that).val() >= items_county) {
           document.getElementById('page_number').value = items_county;
          }
          else {
           document.getElementById('page_number').value = jQuery(that).val();
          }
          spider_ajax_save('<?php echo $form_id; ?>');
          return false;
        }
       return true;		 
      }
    </script>
    <?php } ?>
    <div id="tablenav-pages" class="alignright tablenav-pages">
      <span class="displaying-num">
        <?php
        if ($count_items != 0) {
          echo $count_items; ?> <?php echo __('item', BWG()->prefix); ?><?php echo (($count_items == 1) ? '' : 's');
        }
        ?>
      </span>
      <?php
      if ($count_items > $limit) {
        $first_page = "first-page";
        $prev_page = "prev-page";
        $next_page = "next-page";
        $last_page = "last-page";
        if ($page_number == 1) {
          $first_page = "first-page disabled";
          $prev_page = "prev-page disabled";
          $next_page = "next-page";
          $last_page = "last-page";
        }
        if ($page_number >= $items_county) {
          $first_page = "first-page ";
          $prev_page = "prev-page";
          $next_page = "next-page disabled";
          $last_page = "last-page disabled";
        }
      ?>
      <span class="pagination-links">
        <a class="bwg-a <?php echo $first_page; ?>" title="Go to the first page" onclick="spider_page(<?php echo $page_number; ?>,-2)">«</a>
        <a class="bwg-a <?php echo $prev_page; ?>" title="Go to the previous page" onclick="spider_page(<?php echo $page_number; ?>,-1)">‹</a>
        <span class="paging-input">
          <span class="total-pages">
          <input class="current_page" id="current_page" name="current_page" value="<?php echo $page_number; ?>" onkeypress="return check_enter_key(event, this)" title="Go to the page" type="text" size="1" />
        </span> <?php echo __('of', BWG()->prefix); ?>
        <span class="total-pages">
            <?php echo $items_county; ?>
          </span>
        </span>
        <a class="bwg-a <?php echo $next_page ?>" title="Go to the next page" onclick="spider_page(<?php echo $page_number; ?>,1)">›</a>
        <a class="bwg-a <?php echo $last_page ?>" title="Go to the last page" onclick="spider_page(<?php echo $page_number; ?>,2)">»</a>
        <?php
      }
      ?>
      </span>
    </div>
    <?php if (!$pager) { ?>
    <input type="hidden" id="page_number" name="page_number" value="<?php echo self::get('page_number', 1, 'intval'); ?>" />
    <input type="hidden" id="search_or_not" name="search_or_not" value="<?php echo self::get('search_or_not'); ?>"/>
    <?php
    }
  }

  public static function spider_hex2rgb($colour) {
    if ($colour[0] == '#') {
      $colour = substr( $colour, 1 );
    }
    if (strlen($colour) == 6) {
      list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    }
    else if (strlen($colour) == 3) {
      list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
    }
    else {
      return FALSE;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    return array('red' => $r, 'green' => $g, 'blue' => $b);
  }

  public static function spider_redirect($url) {
    ?>
    <script>
      window.location = "<?php echo $url; ?>";
    </script>
    <?php
    exit();
  }

 /**
  *  If string argument passed, put it into delimiters for AJAX response to separate from other data.
  */

  public static function delimit_wd_output($data) {
    if(is_string ( $data )){
      return "WD_delimiter_start". $data . "WD_delimiter_end";
    }
    else{
      return $data;
    }
  }

  public static function verify_nonce($page){

    $nonce_verified = false;
    if ( isset( $_GET['bwg_nonce'] ) && wp_verify_nonce( $_GET['bwg_nonce'], $page )) {
      $nonce_verified = true;
    }
    elseif ( isset( $_POST['bwg_nonce'] ) && wp_verify_nonce( $_POST['bwg_nonce'], $page )) {
      $nonce_verified = true;
    }
    elseif ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], $page )) {
      $nonce_verified = true;
    }
    elseif ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $page )) {
      $nonce_verified = true;
    }
    return $nonce_verified;
  }

  public static function spider_replace4byte($string) {
    return preg_replace('%(?:
          \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )%xs', '', $string);    
  }

  public static function get_share_page() {
    $share_page = get_posts(array('post_type' => 'bwg_share'));
    if ($share_page) {
      return get_permalink(current($share_page));
    }
    else {
      $bwg_post_args = array(
        'post_title' => 'Image',
        'post_status' => 'publish',
        'post_type' => 'bwg_share'
      );
      $share_page = wp_insert_post($bwg_post_args);
      return get_permalink($share_page);
    }
  }

  public static function esc_script($method = '', $index = '', $default = '', $type = 'string') {
    if ($method == 'post') {
      $escaped_value = ((isset($_POST[$index]) && preg_match("/^[A-Za-z0-9_]+$/", $_POST[$index])) ? esc_js($_POST[$index]) : $default);
    }
    elseif ($method == 'get') {
      $escaped_value = ((isset($_GET[$index]) && preg_match("/^[A-Za-z0-9_]+$/", $_GET[$index])) ? esc_js($_GET[$index]) : $default);
    }
    else {
      $escaped_value = (preg_match("/^[a-zA-Z0-9]", $index) ? esc_js($index) : $default);
    }
    if ($type == 'int') {
      $escaped_value = (int) $escaped_value;
    }
    return $escaped_value;
  }

  public static function get_google_fonts() {
    $google_fonts = array('ABeeZee' => 'ABeeZee', 'Abel' => 'Abel', 'Abril Fatface' => 'Abril Fatface', 'Aclonica' => 'Aclonica', 'Acme' => 'Acme', 'Actor' => 'Actor', 'Adamina' => 'Adamina', 'Advent Pro' => 'Advent Pro', 'Aguafina Script' => 'Aguafina Script', 'Akronim' => 'Akronim', 'Aladin' => 'Aladin', 'Aldrich' => 'Aldrich', 'Alef' => 'Alef', 'Alegreya' => 'Alegreya', 'Alegreya SC' => 'Alegreya SC', 'Alegreya Sans' => 'Alegreya Sans', 'Alex Brush' => 'Alex Brush', 'Alfa Slab One' => 'Alfa Slab One', 'Alice' => 'Alice', 'Alike' => 'Alike', 'Alike Angular' => 'Alike Angular', 'Allan' => 'Allan', 'Allerta' => 'Allerta', 'Allerta Stencil' => 'Allerta Stencil', 'Allura' => 'Allura', 'Almendra' => 'Almendra', 'Almendra display' => 'Almendra Display', 'Almendra sc' => 'Almendra SC', 'Amarante' => 'Amarante', 'Amaranth' => 'Amaranth', 'Amatic sc' => 'Amatic SC', 'Amethysta' => 'Amethysta', 'Amiri' => 'Amiri', 'Amita' => 'Amita', 'Anaheim' => 'Anaheim', 'Andada' => 'Andada', 'Andika' => 'Andika', 'Angkor' => 'Angkor', 'Annie Use Your Telescope' => 'Annie Use Your Telescope', 'Anonymous Pro' => 'Anonymous Pro', 'Antic' => 'Antic', 'Antic Didone' => 'Antic Didone', 'Antic Slab' => 'Antic Slab', 'Anton' => 'Anton', 'Arapey' => 'Arapey', 'Arbutus' => 'Arbutus', 'Arbutus slab' => 'Arbutus Slab', 'Architects daughter' => 'Architects Daughter', 'Archivo black' => 'Archivo Black', 'Archivo narrow' => 'Archivo Narrow', 'Arimo' => 'Arimo', 'Arizonia' => 'Arizonia', 'Armata' => 'Armata', 'Artifika' => 'Artifika', 'Arvo' => 'Arvo', 'Arya' => 'Arya', 'Asap' => 'Asap', 'Asar' => 'Asar', 'Asset' => 'Asset', 'Astloch' => 'Astloch', 'Asul' => 'Asul', 'Atomic age' => 'Atomic Age', 'Aubrey' => 'Aubrey', 'Audiowide' => 'Audiowide', 'Autour one' => 'Autour One', 'Average' => 'Average', 'Average Sans' => 'Average Sans', 'Averia Gruesa Libre' => 'Averia Gruesa Libre', 'Averia Libre' => 'Averia Libre', 'Averia Sans Libre' => 'Averia Sans Libre', 'Averia Serif Libre' => 'Averia Serif Libre', 'Bad Script' => 'Bad Script', 'Balthazar' => 'Balthazar', 'Bangers' => 'Bangers', 'Basic' => 'Basic', 'Battambang' => 'Battambang', 'Baumans' => 'Baumans', 'Bayon' => 'Bayon', 'Belgrano' => 'Belgrano', 'BenchNine' => 'BenchNine', 'Bentham' => 'Bentham', 'Berkshire Swash' => 'Berkshire Swash', 'Bevan' => 'Bevan', 'Bigelow Rules' => 'Bigelow Rules', 'Bigshot One' => 'Bigshot One', 'Bilbo' => 'Bilbo', 'Bilbo Swash Caps' => 'Bilbo Swash Caps', 'Biryani' => 'Biryani', 'Bitter' => 'Bitter', 'Black Ops One' => 'Black Ops One', 'Bokor' => 'Bokor', 'Bonbon' => 'Bonbon', 'Boogaloo' => 'Boogaloo', 'Bowlby One' => 'Bowlby One', 'bowlby One SC' => 'Bowlby One SC', 'Brawler' => 'Brawler', 'Bree Serif' => 'Bree Serif', 'Bubblegum Sans' => 'Bubblegum Sans', 'Bubbler One' => 'Bubbler One', 'Buda' => 'Buda', 'Buda Light 300' => 'Buda Light 300', 'Buenard' => 'Buenard', 'Butcherman' => 'Butcherman', 'Butterfly Kids' => 'Butterfly Kids', 'Cabin' => 'Cabin', 'Cabin Condensed' => 'Cabin Condensed', 'Cabin Sketch' => 'Cabin Sketch', 'Caesar Dressing' => 'Caesar Dressing', 'Cagliostro' => 'Cagliostro', 'Calligraffitti' => 'Calligraffitti', 'Cambay' => 'Cambay', 'Cambo' => 'Cambo', 'Candal' => 'Candal', 'Cantarell' => 'Cantarell', 'Cantata One' => 'Cantata One', 'Cantora One' => 'Cantora One', 'Capriola' => 'Capriola', 'Cardo' => 'Cardo', 'Carme' => 'Carme', 'Carrois Gothic' => 'Carrois Gothic', 'Carrois Gothic SC' => 'Carrois Gothic SC', 'Carter One' => 'Carter One', 'Caudex' => 'Caudex', 'Caveat Brush' => 'Caveat Brush', 'Cedarville cursive' => 'Cedarville Cursive', 'Ceviche One' => 'Ceviche One', 'Changa One' => 'Changa One', 'Chango' => 'Chango','Charmonman' => 'Charmonman', 'Chau philomene One' => 'Chau Philomene One', 'Chela One' => 'Chela One', 'Chelsea Market' => 'Chelsea Market', 'Chenla' => 'Chenla', 'Cherry Cream Soda' => 'Cherry Cream Soda', 'Chewy' => 'Chewy', 'Chicle' => 'Chicle', 'Chivo' => 'Chivo', 'Chonburi' => 'Chonburi', 'Cinzel' => 'Cinzel', 'Cinzel Decorative' => 'Cinzel Decorative', 'Clicker Script' => 'Clicker Script', 'Coda' => 'Coda', 'Coda Caption' => 'Coda Caption', 'Codystar' => 'Codystar', 'Combo' => 'Combo', 'Comfortaa' => 'Comfortaa', 'Coming soon' => 'Coming Soon', 'Concert One' => 'Concert One', 'Condiment' => 'Condiment', 'Content' => 'Content', 'Contrail One' => 'Contrail One', 'Convergence' => 'Convergence', 'Cookie' => 'Cookie', 'Copse' => 'Copse', 'Corben' => 'Corben', 'Courgette' => 'Courgette', 'Cousine' => 'Cousine', 'Coustard' => 'Coustard', 'Covered By Your Grace' => 'Covered By Your Grace', 'Crafty Girls' => 'Crafty Girls', 'Creepster' => 'Creepster', 'Crete Round' => 'Crete Round', 'Crimson Text' => 'Crimson Text', 'Croissant One' => 'Croissant One', 'Crushed' => 'Crushed', 'Cuprum' => 'Cuprum', 'Cutive' => 'Cutive', 'Cutive Mono' => 'Cutive Mono', 'Damion' => 'Damion', 'Dancing Script' => 'Dancing Script', 'Dangrek' => 'Dangrek', 'Dawning of a New Day' => 'Dawning of a New Day', 'Days One' => 'Days One', 'Dekko' => 'Dekko', 'Delius' => 'Delius', 'Delius Swash Caps' => 'Delius Swash Caps', 'Delius Unicase' => 'Delius Unicase', 'Della Respira' => 'Della Respira', 'Denk One' => 'Denk One', 'Devonshire' => 'Devonshire', 'Dhurjati' => 'Dhurjati', 'Didact Gothic' => 'Didact Gothic', 'Diplomata' => 'Diplomata', 'Diplomata SC' => 'Diplomata SC', 'Domine' => 'Domine', 'Donegal One' => 'Donegal One', 'Doppio One' => 'Doppio One', 'Dorsa' => 'Dorsa', 'Dosis' => 'Dosis', 'Dr Sugiyama' => 'Dr Sugiyama', 'Droid Sans' => 'Droid Sans', 'Droid Sans Mono' => 'Droid Sans Mono', 'Droid Serif' => 'Droid Serif', 'Duru Sans' => 'Duru Sans', 'Dynalight' => 'Dynalight', 'Eb Garamond' => 'EB Garamond', 'Eagle Lake' => 'Eagle Lake', 'Eater' => 'Eater', 'Economica' => 'Economica', 'Eczar' => 'Eczar', 'Ek Mukta' => 'Ek Mukta', 'Electrolize' => 'Electrolize', 'Elsie' => 'Elsie', 'Elsie Swash Caps' => 'Elsie Swash Caps', 'Emblema One' => 'Emblema One', 'Emilys Candy' => 'Emilys Candy', 'Engagement' => 'Engagement', 'Englebert' => 'Englebert', 'Enriqueta' => 'Enriqueta', 'Erica One' => 'Erica One', 'Esteban' => 'Esteban', 'Euphoria Script' => 'Euphoria Script', 'Ewert' => 'Ewert', 'Exo' => 'Exo', 'Exo 2' => 'Exo 2', 'Expletus Sans' => 'Expletus Sans', 'Fanwood Text' => 'Fanwood Text', 'Fascinate' => 'Fascinate', 'Fascinate Inline' => 'Fascinate Inline', 'Faster One' => 'Faster One', 'Fasthand' => 'Fasthand', 'Fauna One' => 'Fauna One', 'Federant' => 'Federant', 'Federo' => 'Federo', 'Felipa' => 'Felipa', 'Fenix' => 'Fenix', 'Finger Paint' => 'Finger Paint', 'Fira Mono' => 'Fira Mono', 'Fjalla One' => 'Fjalla One', 'Fjord One' => 'Fjord One', 'Flamenco' => 'Flamenco', 'Flavors' => 'Flavors', 'Fondamento' => 'Fondamento', 'Fontdiner swanky' => 'Fontdiner Swanky', 'Forum' => 'Forum', 'Francois One' => 'Francois One', 'Freckle Face' => 'Freckle Face', 'Fredericka the Great' => 'Fredericka the Great', 'Fredoka One' => 'Fredoka One', 'Freehand' => 'Freehand', 'Fresca' => 'Fresca', 'Frijole' => 'Frijole', 'Fruktur' => 'Fruktur', 'Fugaz One' => 'Fugaz One', 'GFS Didot' => 'GFS Didot', 'GFS Neohellenic' => 'GFS Neohellenic', 'Gabriela' => 'Gabriela', 'Gafata' => 'Gafata', 'Galdeano' => 'Galdeano', 'Galindo' => 'Galindo', 'Gentium Basic' => 'Gentium Basic', 'Gentium Book Basic' => 'Gentium Book Basic', 'Geo' => 'Geo', 'Geostar' => 'Geostar', 'Geostar Fill' => 'Geostar Fill', 'Germania One' => 'Germania One', 'Gidugu' => 'Gidugu', 'Gilda Display' => 'Gilda Display', 'Give You Glory' => 'Give You Glory', 'Glass Antiqua' => 'Glass Antiqua', 'Glegoo' => 'Glegoo', 'Gloria Hallelujah' => 'Gloria Hallelujah', 'Goblin One' => 'Goblin One', 'Gochi Hand' => 'Gochi Hand', 'Gorditas' => 'Gorditas', 'Goudy Bookletter 1911' => 'Goudy Bookletter 1911', 'Graduate' => 'Graduate', 'Grand Hotel' => 'Grand Hotel', 'Gravitas One' => 'Gravitas One', 'Great Vibes' => 'Great Vibes', 'Griffy' => 'Griffy', 'Gruppo' => 'Gruppo', 'Gudea' => 'Gudea', 'Gurajada' => 'Gurajada', 'Habibi' => 'Habibi', 'Halant' => 'Halant', 'Hammersmith One' => 'Hammersmith One', 'Hanalei' => 'Hanalei', 'Hanalei Fill' => 'Hanalei Fill', 'Handlee' => 'Handlee', 'Hanuman' => 'Hanuman', 'Happy Monkey' => 'Happy Monkey', 'Headland One' => 'Headland One', 'Henny Penny' => 'Henny Penny', 'Herr Von Muellerhoff' => 'Herr Von Muellerhoff', 'Hind' => 'Hind', 'Holtwood One  SC' => 'Holtwood One SC', 'Homemade Apple' => 'Homemade Apple', 'Homenaje' => 'Homenaje', 'IM Fell DW Pica' => 'IM Fell DW Pica', 'IM Fell DW Pica SC' => 'IM Fell DW Pica SC', 'IM Fell Double Pica' => 'IM Fell Double Pica', 'IM Fell Double Pica SC' => 'IM Fell Double Pica SC', 'IM Fell English' => 'IM Fell English', 'IM Fell English SC' => 'IM Fell English SC', 'IM Fell French Canon' => 'IM Fell French Canon', 'IM Fell French Canon SC' => 'IM Fell French Canon SC', 'IM Fell Great Primer' => 'IM Fell Great Primer', 'IM Fell Great Primer SC' => 'IM Fell Great Primer SC', 'Iceberg' => 'Iceberg', 'Iceland' => 'Iceland', 'Imprima' => 'Imprima', 'Inconsolata' => 'Inconsolata', 'Inder' => 'Inder', 'Indie Flower' => 'Indie Flower', 'Inika' => 'Inika', 'Inknut Antiqua' => 'Inknut Antiqua', 'Irish Grover' => 'Irish Grover', 'Istok Web' => 'Istok Web', 'Italiana' => 'Italiana', 'Italianno' => 'Italianno', 'Itim' => 'Itim', 'Jacques Francois' => 'Jacques Francois', 'Jacques Francois Shadow' => 'Jacques Francois Shadow', 'Jaldi' => 'Jaldi', 'Jim Nightshade' => 'Jim Nightshade', 'Jockey One' => 'Jockey One', 'Jolly Lodger' => 'Jolly Lodger', 'Josefin Sans' => 'Josefin Sans', 'Josefin Slab' => 'Josefin Slab', 'Joti One' => 'Joti One', 'Judson' => 'Judson', 'Julee' => 'Julee', 'Julius Sans One' => 'Julius Sans One', 'Junge' => 'Junge', 'Jura' => 'Jura', 'Just Another Hand' => 'Just Another Hand', 'Just Me Again Down Here' => 'Just Me Again Down Here', 'Kadwa' => 'Kadwa', 'Kameron' => 'Kameron', 'Kanit' => 'Kanit', 'Karla' => 'Karla', 'Kaushan Script' => 'Kaushan Script', 'Kavoon' => 'Kavoon', 'Keania One' => 'Keania One', 'kelly Slab' => 'Kelly Slab', 'Kenia' => 'Kenia', 'Khand' => 'Khand', 'Khmer' => 'Khmer', 'Khula' => 'Khula', 'Kite One' => 'Kite One', 'Knewave' => 'Knewave', 'Kotta One' => 'Kotta One', 'Koulen' => 'Koulen', 'Kranky' => 'Kranky', 'Kreon' => 'Kreon', 'Kristi' => 'Kristi', 'Krona One' => 'Krona One', 'Kurale' => 'Kurale', 'La Belle Aurore' => 'La Belle Aurore', 'Laila' => 'Laila', 'Lakki Reddy' => 'Lakki Reddy', 'Lancelot' => 'Lancelot', 'Lateef' => 'Lateef', 'Lato' => 'Lato', 'League Script' => 'League Script', 'Leckerli One' => 'Leckerli One', 'Ledger' => 'Ledger', 'Lekton' => 'Lekton', 'Lemon' => 'Lemon', 'Libre Baskerville' => 'Libre Baskerville', 'Life Savers' => 'Life Savers', 'Lilita One' => 'Lilita One', 'Lily Script One' => 'Lily Script One', 'Limelight' => 'Limelight', 'Linden Hill' => 'Linden Hill', 'Lobster' => 'Lobster', 'Lobster Two' => 'Lobster Two', 'Londrina Outline' => 'Londrina Outline', 'Londrina Shadow' => 'Londrina Shadow', 'Londrina Sketch' => 'Londrina Sketch', 'Londrina Solid' => 'Londrina Solid', 'Lora' => 'Lora', 'Love Ya Like A Sister' => 'Love Ya Like A Sister', 'Loved by the King' => 'Loved by the King', 'Lovers Quarrel' => 'Lovers Quarrel', 'Luckiest Guy' => 'Luckiest Guy', 'Lusitana' => 'Lusitana', 'Lustria' => 'Lustria', 'Macondo' => 'Macondo', 'Macondo Swash Caps' => 'Macondo Swash Caps', 'Magra' => 'Magra', 'Maiden Orange' => 'Maiden Orange', 'Mako' => 'Mako', 'Mandali' => 'Mandali', 'Marcellus' => 'Marcellus', 'Marcellus SC' => 'Marcellus SC', 'Marck Script' => 'Marck Script', 'Margarine' => 'Margarine', 'Marko One' => 'Marko One', 'Marmelad' => 'Marmelad', 'Martel' => 'Martel', 'Martel Sans' => 'Martel Sans', 'Marvel' => 'Marvel', 'Mate' => 'Mate', 'Mate SC' => 'Mate SC', 'Maven Pro' => 'Maven Pro', 'McLaren' => 'McLaren', 'Meddon' => 'Meddon', 'MedievalSharp' => 'MedievalSharp', 'Medula One' => 'Medula One', 'Megrim' => 'Megrim', 'Meie Script' => 'Meie Script', 'Merienda' => 'Merienda', 'Merienda One' => 'Merienda One', 'Merriweather' => 'Merriweather', 'Merriweather Sans' => 'Merriweather Sans', 'Metal' => 'Metal', 'Metal mania' => 'Metal Mania', 'Metamorphous' => 'Metamorphous', 'Metrophobic' => 'Metrophobic', 'Michroma' => 'Michroma', 'Milonga' => 'Milonga', 'Miltonian' => 'Miltonian', 'Miltonian Tattoo' => 'Miltonian Tattoo', 'Miniver' => 'Miniver', 'Miss Fajardose' => 'Miss Fajardose', 'Modak' => 'Modak', 'Modern Antiqua' => 'Modern Antiqua', 'Molengo' => 'Molengo', 'Molle' => 'Molle:400i', 'Monda' => 'Monda', 'Monofett' => 'Monofett', 'Monoton' => 'Monoton', 'Monsieur La Doulaise' => 'Monsieur La Doulaise', 'Montaga' => 'Montaga', 'Montez' => 'Montez', 'Montserrat' => 'Montserrat', 'Montserrat Alternates' => 'Montserrat Alternates', 'Montserrat Subrayada' => 'Montserrat Subrayada', 'Moul' => 'Moul', 'Moulpali' => 'Moulpali', 'Mountains of Christmas' => 'Mountains of Christmas', 'Mouse Memoirs' => 'Mouse Memoirs', 'Mr Bedfort' => 'Mr Bedfort', 'Mr Dafoe' => 'Mr Dafoe', 'Mr De Haviland' => 'Mr De Haviland', 'Mrs Saint Delafield' => 'Mrs Saint Delafield', 'Mrs Sheppards' => 'Mrs Sheppards', 'Muli' => 'Muli', 'Mystery Quest' => 'Mystery Quest', 'NTR' => 'NTR', 'Neucha' => 'Neucha', 'Neuton' => 'Neuton', 'New Rocker' => 'New Rocker', 'News Cycle' => 'News Cycle', 'Niconne' => 'Niconne', 'Nixie One' => 'Nixie One', 'Nobile' => 'Nobile', 'Nokora' => 'Nokora', 'Norican' => 'Norican', 'Nosifer' => 'Nosifer', 'Nothing You Could Do' => 'Nothing You Could Do', 'Noticia Text' => 'Noticia Text', 'Noto Sans' => 'Noto Sans', 'Noto Serif' => 'Noto Serif', 'Nova Cut' => 'Nova Cut', 'Nova Flat' => 'Nova Flat', 'Nova Mono' => 'Nova Mono', 'Nova Oval' => 'Nova Oval', 'Nova Round' => 'Nova Round', 'Nova Script' => 'Nova Script', 'Nova Slim' => 'Nova Slim', 'Nova Square' => 'Nova Square', 'Numans' => 'Numans', 'Nunito' => 'Nunito', 'Odor Mean Chey' => 'Odor Mean Chey', 'Offside' => 'Offside', 'Old standard tt' => 'Old Standard TT', 'Oldenburg' => 'Oldenburg', 'Oleo Script' => 'Oleo Script', 'Oleo Script Swash Caps' => 'Oleo Script Swash Caps', 'Open Sans' => 'Open Sans', 'Open Sans Condensed' => 'Open Sans Condensed:300', 'Oranienbaum' => 'Oranienbaum', 'Orbitron' => 'Orbitron', 'Oregano' => 'Oregano', 'Orienta' => 'Orienta', 'Original Surfer' => 'Original Surfer', 'Oswald' => 'Oswald', 'Over the Rainbow' => 'Over the Rainbow', 'Overlock' => 'Overlock', 'Overlock SC' => 'Overlock SC', 'Ovo' => 'Ovo', 'Oxygen' => 'Oxygen', 'Oxygen Mono' => 'Oxygen Mono', 'PT Mono' => 'PT Mono', 'PT Sans' => 'PT Sans', 'PT Sans Caption' => 'PT Sans Caption', 'PT Sans Narrow' => 'PT Sans Narrow', 'PT Serif' => 'PT Serif', 'PT Serif Caption' => 'PT Serif Caption', 'Pacifico' => 'Pacifico', 'Palanquin' => 'Palanquin', 'Palanquin Dark' => 'Palanquin Dark', 'Paprika' => 'Paprika', 'Parisienne' => 'Parisienne', 'Passero One' => 'Passero One', 'Passion One' => 'Passion One', 'Pathway Gothic One' => 'Pathway Gothic One', 'Patrick Hand' => 'Patrick Hand', 'Patrick Hand SC' => 'Patrick Hand SC', 'Patua One' => 'Patua One', 'Paytone One' => 'Paytone One', 'Peddana' => 'Peddana', 'Peralta' => 'Peralta', 'Permanent Marker' => 'Permanent Marker', 'Petit Formal Script' => 'Petit Formal Script', 'Petrona' => 'Petrona', 'Philosopher' => 'Philosopher', 'Piedra' => 'Piedra', 'Pinyon Script' => 'Pinyon Script', 'Pirata One' => 'Pirata One', 'Plaster' => 'Plaster', 'Play' => 'Play', 'Playball' => 'Playball', 'Playfair Display' => 'Playfair Display', 'Playfair Display SC' => 'Playfair Display SC', 'Podkova' => 'Podkova', 'Poiret One' => 'Poiret One', 'Poller One' => 'Poller One', 'Poly' => 'Poly', 'Pompiere' => 'Pompiere', 'Pontano Sans' => 'Pontano Sans', 'Poppins' => 'Poppins', 'Port Lligat Sans' => 'Port Lligat Sans', 'Port Lligat Slab' => 'Port Lligat Slab', 'Pragati Narrow' => 'Pragati Narrow', 'Prata' => 'Prata', 'Preahvihear' => 'Preahvihear', 'Press start 2P' => 'Press Start 2P', 'Princess Sofia' => 'Princess Sofia', 'Prociono' => 'Prociono', 'Prosto One' => 'Prosto One', 'Puritan' => 'Puritan', 'Purple Purse' => 'Purple Purse', 'Quando' => 'Quando', 'Quantico' => 'Quantico', 'Quattrocento' => 'Quattrocento', 'Quattrocento Sans' => 'Quattrocento Sans', 'Questrial' => 'Questrial', 'Quicksand' => 'Quicksand', 'Quintessential' => 'Quintessential', 'Qwigley' => 'Qwigley', 'Racing sans One' => 'Racing Sans One', 'Radley' => 'Radley', 'Rajdhani' => 'Rajdhani', 'Raleway' => 'Raleway', 'Raleway Dots' => 'Raleway Dots', 'Ramabhadra' => 'Ramabhadra', 'Ramaraja' => 'Ramaraja', 'Rambla' => 'Rambla', 'Rammetto One' => 'Rammetto One', 'Ranchers' => 'Ranchers', 'Rancho' => 'Rancho', 'Ranga' => 'Ranga', 'Rationale' => 'Rationale', 'Ravi Prakash' => 'Ravi Prakash', 'Redressed' => 'Redressed', 'Reenie Beanie' => 'Reenie Beanie', 'Revalia' => 'Revalia', 'Rhodium Libre' => 'Rhodium Libre', 'Ribeye' => 'Ribeye', 'Ribeye Marrow' => 'Ribeye Marrow', 'Righteous' => 'Righteous', 'Risque' => 'Risque', 'Roboto' => 'Roboto', 'Roboto Condensed' => 'Roboto Condensed', 'Roboto Mono' => 'Roboto Mono', 'Roboto Slab' => 'Roboto Slab', 'Rochester' => 'Rochester', 'Rock Salt' => 'Rock Salt', 'Rokkitt' => 'Rokkitt', 'Romanesco' => 'Romanesco', 'Ropa Sans' => 'Ropa Sans', 'Rosario' => 'Rosario', 'Rosarivo' => 'Rosarivo', 'Rouge Script' => 'Rouge Script', 'Rozha One' => 'Rozha One', 'Rubik' => 'Rubik', 'Rubik Mono One' => 'Rubik Mono One', 'Rubik One' => 'Rubik One', 'Ruda' => 'Ruda', 'Rufina' => 'Rufina', 'Ruge Boogie' => 'Ruge Boogie', 'Ruluko' => 'Ruluko', 'Rum Raisin' => 'Rum Raisin', 'Ruslan Display' => 'Ruslan Display', 'Russo One' => 'Russo One', 'Ruthie' => 'Ruthie', 'Rye' => 'Rye', 'Sacramento' => 'Sacramento', 'Sahitya' => 'Sahitya', 'Sail' => 'Sail', 'Salsa' => 'Salsa', 'Sanchez' => 'Sanchez', 'Sancreek' => 'Sancreek', 'Sansita One' => 'Sansita One', 'Sarina' => 'Sarina', 'Sarpanch' => 'Sarpanch', 'Satisfy' => 'Satisfy', 'Scada' => 'Scada', 'Schoolbell' => 'Schoolbell', 'Seaweed Script' => 'Seaweed Script', 'Sevillana' => 'Sevillana', 'Seymour One' => 'Seymour One', 'Shadows Into Light' => 'Shadows Into Light', 'Shadows Into Light Two' => 'Shadows Into Light Two', 'Shanti' => 'Shanti', 'Share' => 'Share', 'Share Tech' => 'Share Tech', 'Share Tech Mono' => 'Share Tech Mono', 'Shojumaru' => 'Shojumaru', 'Short Stack' => 'Short Stack', 'Siemreap' => 'Siemreap', 'Sigmar One' => 'Sigmar One', 'Signika' => 'Signika', 'Signika Negative' => 'Signika Negative', 'Simonetta' => 'Simonetta', 'Sintony' => 'Sintony', 'Sirin Stencil' => 'Sirin Stencil', 'Six Caps' => 'Six Caps', 'Skranji' => 'Skranji', 'Slabo 13px' => 'Slabo 13px', 'Slackey' => 'Slackey', 'Smokum' => 'Smokum', 'Smythe' => 'Smythe', 'Sniglet' => 'Sniglet', 'Snippet' => 'Snippet', 'Snowburst One' => 'Snowburst One', 'Sofadi One' => 'Sofadi One', 'Sofia' => 'Sofia', 'Sonsie One' => 'Sonsie One', 'Sorts Mill Goudy' => 'Sorts Mill Goudy', 'Source Code Pro' => 'Source Code Pro', 'Source Sans Pro' => 'Source Sans Pro', 'Source Serif Pro' => 'Source Serif Pro', 'Special Elite' => 'Special Elite', 'Spicy Rice' => 'Spicy Rice', 'Spinnaker' => 'Spinnaker', 'Spirax' => 'Spirax', 'Squada One' => 'Squada One', 'Sree Krushnadevaraya' => 'Sree Krushnadevaraya', 'Stalemate' => 'Stalemate', 'Stalinist One' => 'Stalinist One', 'Stardos Stencil' => 'Stardos Stencil', 'Stint Ultra Condensed' => 'Stint Ultra Condensed', 'Stint Ultra Expanded' => 'Stint Ultra Expanded', 'Stoke' => 'Stoke', 'Strait' => 'Strait', 'Sue Ellen Francisco' => 'Sue Ellen Francisco', 'Sumana' => 'Sumana', 'Sunshiney' => 'Sunshiney', 'Supermercado One' => 'Supermercado One', 'Sura' => 'Sura', 'Suranna' => 'Suranna', 'Suravaram' => 'Suravaram', 'Suwannaphum' => 'Suwannaphum', 'Swanky and Moo Moo' => 'Swanky and Moo Moo', 'Syncopate' => 'Syncopate', 'Tangerine' => 'Tangerine', 'Taprom' => 'Taprom', 'Tauri' => 'Tauri', 'Teko' => 'Teko', 'Telex' => 'Telex', 'Tenali Ramakrishna' => 'Tenali Ramakrishna', 'Tenor Sans' => 'Tenor Sans', 'Text Me One' => 'Text Me One', 'The Girl Next Door' => 'The Girl Next Door', 'Tienne' => 'Tienne', 'Tillana' => 'Tillana', 'Timmana' => 'Timmana', 'Tinos' => 'Tinos', 'Titan One' => 'Titan One', 'Titillium Web' => 'Titillium Web', 'Trade Winds' => 'Trade Winds', 'Trocchi' => 'Trocchi', 'Trochut' => 'Trochut', 'Trykker' => 'Trykker', 'Tulpen One' => 'Tulpen One', 'Ubuntu' => 'Ubuntu', 'Ubuntu Condensed' => 'Ubuntu Condensed', 'Ubuntu Mono' => 'Ubuntu Mono', 'Ultra' => 'Ultra', 'Uncial Antiqua' => 'Uncial Antiqua', 'Underdog' => 'Underdog', 'Unica One' => 'Unica One', 'UnifrakturCook' => 'UnifrakturCook:700', 'UnifrakturMaguntia' => 'UnifrakturMaguntia', 'Unkempt' => 'Unkempt', 'Unlock' => 'Unlock', 'Unna' => 'Unna', 'VT323' => 'VT323', 'Vampiro One' => 'Vampiro One', 'Varela' => 'Varela', 'Varela Round' => 'Varela Round', 'Vast Shadow' => 'Vast Shadow', 'Vibur' => 'Vibur', 'Vidaloka' => 'Vidaloka', 'Viga' => 'Viga', 'Voces' => 'Voces', 'Volkhov' => 'Volkhov', 'Vollkorn' => 'Vollkorn', 'Voltaire' => 'Voltaire', 'Waiting for the sunrise' => 'Waiting for the Sunrise', 'Wallpoet' => 'Wallpoet', 'Walter Turncoat' => 'Walter Turncoat', 'Warnes' => 'Warnes', 'Wellfleet' => 'Wellfleet', 'Wendy One' => 'Wendy One', 'Wire One' => 'Wire One', 'Work Sans' => 'Work Sans', 'Yanone Kaffeesatz' => 'Yanone Kaffeesatz', 'Yantramanav' => 'Yantramanav', 'Yellowtail' => 'Yellowtail', 'Yeseva One' => 'Yeseva One', 'Yesteryear' => 'Yesteryear', 'Zeyada' => 'Zeyada');
    return $google_fonts;
  }

  /**
   * Get value of option using key
   *
   * @param        $string
   * @param        $option
   *
   * @return string
   */
  public static function get_option_value_from_string( $string, $option ) {
    $len_start = strpos($string, $option);
    if( !$len_start ) {
      return;
    }
    $len_current = strpos(substr($string, $len_start), '"');
    $len_end =  strpos(substr(substr($string, $len_start), $len_current + 1), '"');
    $option_value = str_replace('"', '', substr(substr($string, $len_start), $len_current, $len_end + 1));
    return $option_value;
  }

  /**
   * Get options of gallery type from whole options string.
   *
   * @param        $gallery_type
   * @param        $option_key
   *
   * @return bool
   */
  public static function get_option_by_gallery_type( $gallery_type, $option_key ) {
    switch ($gallery_type) {
      case "thumbnails":
        if(strpos($option_key, 'thumb_') === 0) {
          return true;
        }
        break;
      case "thumbnails_masonry":
        if(strpos($option_key, 'masonry_') === 0) {
          return true;
        }
        break;
      case "thumbnails_mosaic":
        if(strpos($option_key, 'mosaic_') === 0) {
          return true;
        }
        break;
      case "slideshow":
        if(strpos($option_key, 'slideshow_') === 0) {
          return true;
        }
        break;
      case "image_browser":
        if(strpos($option_key, 'image_browser_') === 0) {
          return true;
        }
        break;
      case "blog_style":
        if(strpos($option_key, 'blog_style_') === 0) {
          return true;
        }
        break;
      case "carousel":
        if(strpos($option_key, 'carousel_') === 0) {
          return true;
        }
        break;
      case "album_compact_preview":
        if(strpos($option_key, 'album_compact_') === 0) {
          return true;
        }
        break;
      case "album_masonry_preview":
        if(strpos($option_key, 'album_masonry_') === 0) {
          return true;
        }
        break;
      case "album_extended_preview":
        if(strpos($option_key, 'album_extended_') === 0) {
          return true;
        }
        break;
      default:
        return false;
    }
    return false;
  }

  /**
   * Get google fonts used in themes and options.
   *
   * @return string
   */
  public static function get_all_used_google_fonts() {
    global $wpdb;

    $url = '';
    $google_array = array();
    $google_fonts = self::get_google_fonts();
    $theme = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_theme', 'OBJECT_K');

    if ( $theme ) {
      foreach ( $theme as $row ) {
        if ( isset($row->options) ) {
          $options = json_decode($row->options);
          foreach ( $options as $option ) {
            $is_google_fonts = in_array((string) $option, $google_fonts) ? TRUE : FALSE;
            if ( TRUE == $is_google_fonts ) {
              $google_array[$option] = $option;
            }
          }
        }
      }
    }

    if ( TRUE == in_array(BWG()->options->watermark_font, $google_fonts) ) {
      $google_array[BWG()->options->watermark_font] = BWG()->options->watermark_font;
    }

    if ( !empty($google_array) ) {
      $query = implode("|", str_replace(' ', '+', $google_array));

      $url = 'https://fonts.googleapis.com/css?family=' . $query;
      $url .= '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic';
    }

    return $url;
  }

    /**
     * Get all google fonts used in themes and options.
     *
     * @return string
     */
    public static function get_all_google_fonts() {
      global $wpdb;

      $url = '';
        $google_fonts = self::get_google_fonts();
      if ( !empty( $google_fonts )){
          $query = implode("|", str_replace(' ', '+', $google_fonts));

          $url = 'https://fonts.googleapis.com/css?family=' . $query;
          $url .= '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic';
      }

      return $url;
    }

  public static function get_used_google_fonts($theme = null, $shortcode = null) {
    global $wpdb;

    $url = '';
    $google_array = array();
    $google_fonts = self::get_google_fonts();
    if (null === $theme) {
      $theme = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_theme', 'OBJECT_K');
    }
    else {
      $theme = array($theme);
    }
    if (null === $shortcode) {
      $shortcode_google_fonts = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_shortcode');
    }
    else {
      $shortcode_google_fonts = array($shortcode);
    }
    if ($shortcode_google_fonts) {
      foreach($shortcode_google_fonts as $shortcode_google_font){
        $shortcode_font_string = $shortcode_google_font->tagtext;
        $shortcode_fonts = self::get_option_value_from_string( $shortcode_font_string, 'watermark_font="' );
        if (true == in_array($shortcode_fonts, $google_fonts)) {
          $google_array[$shortcode_fonts] = $shortcode_fonts;
        }

        $showthumbs_name = self::get_option_value_from_string( $shortcode_font_string, 'showthumbs_name="' );
        $show_gallery_description = self::get_option_value_from_string( $shortcode_font_string, 'show_gallery_description="' );
        $image_title = self::get_option_value_from_string( $shortcode_font_string, 'image_title="' );
        $theme_id = self::get_option_value_from_string( $shortcode_font_string, 'theme_id="' );
        $gallery_type = self::get_option_value_from_string( $shortcode_font_string, 'gallery_type="' );
        // Check if show Gallery title, description, image title options are true
        if( $showthumbs_name == 1 || $show_gallery_description == 1 || ($image_title != 'none' && $image_title != '') || !isset($showthumbs_name)) {
          if ( !empty($theme[$theme_id]) ) {
            $row = $theme[$theme_id];
            if (isset($row->options)) {
              $options = json_decode($row->options);
              foreach ($options as $key=>$option) {
                $is_google_fonts = (in_array((string)$option, $google_fonts)) ? true : false;
                if (true == $is_google_fonts && self::get_option_by_gallery_type( $gallery_type, $key )) {
                  $google_array[$option] = $option;
                }
              }
            }
          }

          if (true == in_array(BWG()->options->watermark_font, $google_fonts)) {
            $google_array[BWG()->options->watermark_font] = BWG()->options->watermark_font;
          }
        }

        if ( !empty($google_array) ) {
          $query = implode("|", str_replace(' ', '+', $google_array));
          $url = 'https://fonts.googleapis.com/css?family=' . $query;
          $url .= '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic';
          $shortcode_id = $shortcode_google_font->id;
          wp_register_style('bwg_googlefonts'. $shortcode_id, $url, null, null);
          $google_array = array();
        }
      }
    }

    // Register style for widget
    if ( $theme ) {
      foreach ( $theme as $row ) {
        if ( isset($row->options) ) {
          $options = json_decode($row->options);
          foreach ( $options as $option ) {
            $is_google_fonts = (in_array((string) $option, $google_fonts)) ? TRUE : FALSE;
            if ( TRUE == $is_google_fonts ) {
              $google_array[$option] = $option;
            }
          }
        }
      }
    }

    if ( TRUE == in_array(BWG()->options->watermark_font, $google_fonts) ) {
      $google_array[BWG()->options->watermark_font] = BWG()->options->watermark_font;
    }

    if ( !empty($google_array) ) {
      $query = implode("|", str_replace(' ', '+', $google_array));

      $url = 'https://fonts.googleapis.com/css?family=' . $query;
      $url .= '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic';
    }

	  return $url;
  }

  public static function get_default_theme_id() {
    global $wpdb;
    $id = $wpdb->get_var('SELECT id FROM ' . $wpdb->prefix . 'bwg_theme WHERE default_theme = 1');
    return $id;
  }

  /**
   * Create custom post.
   *
   * @param  array $params
   *
   * @return object  $post
   */
  public static function bwg_create_custom_post( $params ) {
    $title  = $params['title'];
  	$slug   = $params['slug'];
    $type   = $params['type'];
    $post_type = BWG()->prefix . '_' . $type['post_type'];

    // Get post by slug.
    $slugcheck = !empty($params['old_slug']) ? $params['old_slug'] : $slug;
    $post = get_page_by_path($slugcheck, OBJECT, $post_type);
    if (!$post) {
      // Insert shortcode data.
      $shortecode_id = self::create_shortcode($params);
      self::$shortcode_ids[] = $shortecode_id;
      $custom_post_shortecode = '[Best_Wordpress_Gallery id="' . $shortecode_id . '" gal_title="' . $title . '"]';
      $post = array(
        'post_name' => $slug,
        'post_title' => $title,
        'post_content' => $custom_post_shortecode,
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'post_author' => 1,
        'post_type' => $post_type,
      );
      $post_id = wp_insert_post($post);
      if ( $post_id !== 0 || !is_wp_error($post_id) ) {
        $post = get_post($post_id);
        if ( !is_null($post) && $post->post_name != $slug ) {
          global $wpdb;
          $wpdb->update(
            $wpdb->prefix . 'bwg_gallery',
            array('slug' => $post->post_name),
            array('id' => $params['id']),
            array('%s'),
            array('%d')
          );
        }
      }
    }
    else {
      $post->post_name = $slug;
      $post->post_title = $title;
      wp_update_post($post);
    }

    $post = get_page_by_path($slug, OBJECT, $post_type);

    return $post;
  }
  
   /**
   * Remove custom post.
   *
   * @param  array 	$params
   *
   * @return object $post
   */
  public static function bwg_remove_custom_post( $params ) {
    $slug = $params['slug'];
    $post_type = $params['post_type'];
    $post = get_page_by_path($slug, OBJECT, $post_type);
    $delete = new stdClass();
    if (!empty($post)) {
      $delete = wp_delete_post($post->ID, TRUE);
    }
    return $delete;
  }

  /**
   * Create shortcode.
   *
   * @param  array $params
   *
   * @return int    $shortcode_id
   */
  private static function create_shortcode( $params ) {
    global $wpdb;
    $id = $params['id'];
    $type = $params['type'];
    $theme_id = self::get_default_theme_id();
    $shortcode_id = 0;
    if (!empty($type['post_type'])) {
      $shortcode = ' use_option_defaults="1" type="' . $type['post_type'] . '" theme_id="' . $theme_id . '" ';
      switch ($type['post_type']) {
        case 'gallery':
          $shortcode .= 'gallery_id="' . $id . '" tag="0" gallery_type="thumbnails"';
          break;
        case 'album':
          $shortcode .= 'album_id="' . $id . '" tag="0" gallery_type="album_compact_preview"';
          break;
        case 'tag':
          $shortcode .= 'tag="' . $id . '" gallery_id="0" gallery_type="thumbnails"';
          break;
        default:
          break;
      }
      $shortcode_id = self::get_shortcode_max_id();
      $wpdb->insert($wpdb->prefix . 'bwg_shortcode',
        array('id' => $shortcode_id, 'tagtext' => $shortcode),
        array('%d', '%s')
      );
    }

    return $shortcode_id;
  }

  /**
   * Create custom posts before update.
   *
   * @return bool
   */
    public static function before_update_create_custom_posts() {
		global $wpdb;
		$query = new WP_Query( array('post_type' => array( 'bwg_gallery', 'bwg_tag', 'bwg_album')) );
		if( !empty($query->posts) ){
			foreach( $query->posts as $post ) {
				$delete = wp_delete_post($post->ID, TRUE);
			}
		}

		$sql_query = '(SELECT `a`.`id` AS `id`, `a`.`name` AS `title`, `a`.`slug` AS `slug`, CONCAT("album") as `type`  FROM `'. $wpdb->prefix .'bwg_album` `a`)
						UNION
					  (SELECT `g`.`id` AS `id`, `g`.`name` AS `title`,`g`.`slug` AS `slug`, CONCAT("gallery") as `type` FROM `'. $wpdb->prefix .'bwg_gallery` `g`)
						UNION
					  (SELECT `t`.`term_id` AS `id`, `t`.`name` AS `title`,`t`.`slug` AS `slug`, CONCAT("tag") as `type` FROM '. $wpdb->prefix .'terms as `t` 
						LEFT JOIN '. $wpdb->prefix .'term_taxonomy as `tt`
							ON `t`.`term_id` = `tt`.`term_id` WHERE `tt`.`taxonomy`="bwg_tag");';

		$results = $wpdb->get_results( $sql_query, OBJECT );
		if( !empty($results) ) {
			foreach($results as $row){
				$custom_params = array(
						'id' => $row->id,
						'title' => $row->title,
						'slug'  => $row->slug,
						'type' => array(
						  'post_type' => $row->type,
						  'mode' => '',
						),
					);
				self::bwg_create_custom_post( $custom_params );
			}
		}
    }

  /**
   * Get custom post.
   *
   * @param  array $params
   *
   * @return string  $permalink
   */
  public static function get_custom_post_permalink( $params ) {
    $slug = $params['slug'];
    $post_type = $params['post_type'];
    $post_type = BWG()->prefix . '_' . $post_type;
    // Get post by slug.
    $post = get_page_by_path($slug, OBJECT, $post_type);
    if ( $post ) {
      return get_permalink($post->ID);
    }
    return '';
  }

  /**
   * Get shortcode id from custom post.
   *
   * @param $params
   *
   * @return int
   */
  public static function get_shortcode_id( $params ) {
    $shortcode_id = 0;

    // Get post by slug.
    $post = get_page_by_path($params['slug'], OBJECT, BWG()->prefix . '_' . $params['post_type']);
    if ( $post ) {
      if ( isset($post->post_content) ) {
        $exploded_shortcode = explode('[Best_Wordpress_Gallery id="', $post->post_content);
        if ( isset($exploded_shortcode[1]) ) {
          $shortcode_id = (int) substr($exploded_shortcode[1], 0, strpos($exploded_shortcode[1], '"'));
        }
      }
    }

    return $shortcode_id;
  }

  /**
   * Get shortcode max id.
   *
   * @return int $id
   */
  public static function get_shortcode_max_id() {
    global $wpdb;
    $id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "bwg_shortcode");

    return $id + 1;
  }

  /**
   * @param        $gallery_id
   * @param        $bwg
   * @param        $type
   * @param        $tag_input_name
   * @param        $tag
   * @param        $images_per_page
   * @param        $load_more_image_count
   * @param        $sort_by
   * @param string $sort_direction
   *
   * @return array
   */
  public static function get_image_rows_data( $gallery_id, $bwg, $type, $tag_input_name, $tag, $images_per_page, $load_more_image_count, $sort_by, $sort_direction = 'ASC' ) {
    if ( $images_per_page < 0 ) {
      $images_per_page = 0;
    }
    if ( $load_more_image_count < 0 ) {
      $load_more_image_count = 0;
    }
    $gallery_id = (int) $gallery_id;
    $tag = (int) $tag;
    global $wpdb;
    $bwg_search = trim(self::get('bwg_search_' . $bwg));
    $prepareArgs = array();

    if ( BWG()->options->front_ajax == "1" ) {
      $sort_by = trim( WDWLibrary::get('sort_by_' . $bwg, $sort_by ) );
      $filter_teg = trim( WDWLibrary::get('filter_tag_' . $bwg) );

      if ( !empty($filter_teg) ) {
        $filter_teg_arr = explode(',', trim($filter_teg));
        $_REQUEST[$tag_input_name] = $filter_teg_arr;
      }
    }

    $where = '';
    if ( $bwg_search !== '' ) {
      $bwg_search_keys = explode(' ', $bwg_search);
      $alt_search = '(';
      $description_search = '(';
      foreach( $bwg_search_keys as $search_key) {
        $alt_search .= '`image`.`alt` LIKE %s AND ';
        $description_search .= '`image`.`description` LIKE %s AND ';
        $prepareArgs[] = "%" . trim($search_key) . "%";
        $prepareArgs[] = "%" . trim($search_key) . "%";
      }
      $alt_search = rtrim($alt_search, 'AND ');
      $alt_search .= ')';
      $description_search = rtrim($description_search, 'AND ');
      $description_search .= ')';
      $where = 'AND (' . $alt_search . ' OR ' . $description_search . ')';
    }

    if ( $sort_by == 'size' || $sort_by == 'resolution' ) {
      $sort_by = ' CAST(image.' . $sort_by . ' AS SIGNED) ';
    }
    elseif ( $sort_by == 'random' || $sort_by == 'RAND()' ) {
      $sort_by = 'RAND()';
    }
    elseif ( ($sort_by != 'alt') && ($sort_by != 'date') && ($sort_by != 'filetype') && ($sort_by != 'RAND()') && ($sort_by != 'filename') ) {
      $sort_by = 'image.`order`';
    }
    else {
      $sort_by = 'image.' . $sort_by;
    }

    $items_in_page = $images_per_page;
    $limit = 0;
    $page_number = self::get('page_number_' . $bwg, 0, 'intval');

    if ( !empty($page_number) ) {
      if ( $page_number > 1 ) {
        $items_in_page = $load_more_image_count;
      }
      $limit = ( ($page_number - 2) * $items_in_page ) + $images_per_page;
      $bwg_random_seed = self::get('bwg_random_seed_' . $bwg);
    }
    else {
      $bwg_random_seed = rand();
      $GLOBALS['bwg_random_seed_' . $bwg] = $bwg_random_seed;
    }

    if($gallery_id) {
      $where .= ' AND image.gallery_id = %d ';
      $prepareArgs[] = $gallery_id;
    }
    if($tag) {
      $where .= ' AND tag.tag_id = %d ';
      $prepareArgs[] = $tag;
    }

    $limit_str = '';
    if ( $images_per_page ) {
      $limit_str .= 'LIMIT %d, %d';
      $prepareArgs[] = $limit;
      $prepareArgs[] = $items_in_page;
    }

    $join = $tag ? 'LEFT JOIN ' . $wpdb->prefix . 'bwg_image_tag as tag ON image.id=tag.image_id' : '';

    $filter_tags_name = self::get($tag_input_name, '', 'sanitize_text_field', 'REQUEST');
    if ( $filter_tags_name ) {
      if ( !BWG()->options->tags_filter_and_or ) {
        // To find images which have at least one from tags filtered by.
        $compare_sign = "|";
      }
      else {
        // To find images which have all tags filtered by.
        // For this case there is need to sort tags by ascending to compare with comma.
        sort($filter_tags_name);
        $compare_sign = ",";
      }
      $join .= ' LEFT JOIN (SELECT GROUP_CONCAT(tag_id order by tag_id SEPARATOR ",") AS tags_combined, image_id FROM  ' . $wpdb->prefix . 'bwg_image_tag' . ($gallery_id ?  $wpdb->prepare(' WHERE gallery_id=%d', $gallery_id) : '') . ' GROUP BY image_id) AS tags ON image.id=tags.image_id';
      $where .= ' AND CONCAT(",", tags.tags_combined, ",") REGEXP ",(' . implode( $compare_sign, $filter_tags_name ) . ')," ';
    }

    $join .= ' LEFT JOIN '. $wpdb->prefix .'bwg_gallery as gallery ON gallery.id = image.gallery_id';
    $where .= ' AND gallery.published = 1 ';

    if ( !empty($prepareArgs) ) {
      $rows = $wpdb->get_results($wpdb->prepare('SELECT image.* FROM ' . $wpdb->prefix . 'bwg_image as image ' . $join . ' WHERE image.published=1 ' . $where . ' ORDER BY ' . str_replace('RAND()', 'RAND(' . $bwg_random_seed . ')', $sort_by) . ' ' . $sort_direction . ', image.id asc ' . $limit_str, $prepareArgs));
    }
    else {
      $rows = $wpdb->get_results('SELECT image.* FROM ' . $wpdb->prefix . 'bwg_image as image ' . $join . ' WHERE image.published=1 ' . $where . ' ORDER BY ' . str_replace('RAND()', 'RAND(' . $bwg_random_seed . ')', $sort_by) . ' ' . $sort_direction . ', image.id asc ' . $limit_str);
    }
    if ( $images_per_page ) {
      array_splice($prepareArgs, -2);
    }
    if ( !empty($prepareArgs) ) {
      $total = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'bwg_image as image ' . $join . ' WHERE image.published=1 ' . $where, $prepareArgs));
    }
    else {
      $total = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'bwg_image as image ' . $join . ' WHERE image.published=1 ' . $where);
    }

    $page_nav['total'] = $total;
    $page_nav['limit'] = 1;
    if ( $page_number ) {
      $page_nav['limit'] = (int) $page_number;
    }
    $images = array();
    $thumb_urls = array();
    if ( !empty($rows) ) {
      foreach ( $rows as $row ) {
        $row->pure_image_url = $row->image_url;
        $row->pure_thumb_url = $row->thumb_url;
        $row->alt = esc_html($row->alt);
        if ( strpos($row->filetype, 'EMBED') === FALSE ) {
            $row->image_url_raw = $row->image_url;
            $row->image_url = self::image_url_version($row->image_url, $row->modified_date);
            $row->thumb_url = self::image_url_version($row->thumb_url, $row->modified_date);
            $thumb_urls[] = BWG()->upload_url . $row->thumb_url;
        } else {
            // To disable Jetpack Photon module.
            $thumb_urls[] = $row->thumb_url;
        }
        $images[] = $row;
      }
    }

    return array( 'images' => $images, 'page_nav' => $page_nav, 'thumb_urls' => $thumb_urls );
  }

  /**
   * Image set watermark.
   *
   * @param $gallery_id
   * @param int $image_id
   * @param string $limit
   * @return int
   */
  public static function bwg_image_set_watermark( $gallery_id, $image_id = 0, $limit = '' ) {
    global $wpdb;
    $message_id = 21;
    $options = new WD_BWG_Options();
    if ( $options->built_in_watermark_type != 'none' ) {
      $prepareArgs = array();
      if ( $gallery_id ) {
          $where = ' `gallery_id`=%d';
          $prepareArgs[] = $gallery_id;
          if ( $image_id ) {
            $where .= ' AND `id`=%d';
            $prepareArgs[] = $image_id;
          }
      } else {
          $where = 1;
      }
      //$where = (($gallery_id) ? ' `gallery_id`=' . $gallery_id . ($image_id ? ' AND `id`=' . $image_id : '') : 1);
      $search = WDWLibrary::get( 's', '' );
      if ( $search ) {
        $where .= ' AND `filename` LIKE "%s"';
        $prepareArgs[] = "%" . $search . "%";
      }
      $limitstart = '';
      if ( !$limit ) {
        $limitstart = ' LIMIT 50 OFFSET %d';
        $prepareArgs[] = $limit;
      }
      $images = $wpdb->get_results( $wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where . $limitstart, $prepareArgs) );

      if ( !empty( $images ) ) {
        switch ( $options->built_in_watermark_type ) {
          case 'text':
            foreach ( $images as $image ) {
              if ( preg_match( '/EMBED/', $image->filetype ) == 1 ) {
                continue;
              }
              self::set_text_watermark( BWG()->upload_dir . $image->image_url, BWG()->upload_dir . $image->image_url, html_entity_decode( $options->built_in_watermark_text ), $options->built_in_watermark_font, $options->built_in_watermark_font_size, '#' . $options->built_in_watermark_color, $options->built_in_watermark_opacity, $options->built_in_watermark_position );
            }
            break;
          case 'image':
            $watermark_path = str_replace( BWG()->upload_url, BWG()->upload_dir, $options->built_in_watermark_url );
            foreach ( $images as $image ) {
              if ( preg_match( '/EMBED/', $image->filetype ) == 1 ) {
                continue;
              }
              self::set_image_watermark( BWG()->upload_dir . $image->image_url, BWG()->upload_dir . $image->image_url, $watermark_path, $options->built_in_watermark_size, $options->built_in_watermark_size, $options->built_in_watermark_position );
            }
            break;
        }
        self::update_image_modified_date( $where );
      }
    }
    else {
      $message_id = 6;
    }
    return $message_id;
  }

  public static function set_text_watermark($original_filename, $dest_filename, $watermark_text, $watermark_font, $watermark_font_size, $watermark_color, $watermark_transparency, $watermark_position) {
    $original_filename = htmlspecialchars_decode($original_filename, ENT_COMPAT | ENT_QUOTES);
    $dest_filename = htmlspecialchars_decode($dest_filename, ENT_COMPAT | ENT_QUOTES);

    $watermark_transparency = 127 - ($watermark_transparency * 1.27);
    list($width, $height, $type) = getimagesize($original_filename);
    $watermark_image = imagecreatetruecolor($width, $height);

    $watermark_color = self::bwg_hex2rgb($watermark_color);
    $watermark_color = imagecolorallocatealpha($watermark_image, $watermark_color[0], $watermark_color[1], $watermark_color[2], $watermark_transparency);
    $watermark_font = BWG()->plugin_dir . '/fonts/' . $watermark_font;
    $watermark_font_size = (($height > $width ? $width : $height) * $watermark_font_size / 500);
    $watermark_position = explode('-', $watermark_position);
    $watermark_sizes = self::bwg_imagettfbboxdimensions($watermark_font_size, 0, $watermark_font, $watermark_text);

    $top = $height - 5;
    $left = $width - $watermark_sizes['width'] - 5;
    switch ($watermark_position[0]) {
      case 'top':
        $top = $watermark_sizes['height'] + 5;
        break;
      case 'middle':
        $top = ($height + $watermark_sizes['height']) / 2;
        break;
    }
    switch ($watermark_position[1]) {
      case 'left':
        $left = 5;
        break;
      case 'center':
        $left = ($width - $watermark_sizes['width']) / 2;
        break;
    }
    @ini_set('memory_limit', '-1');
    if ($type == 2) {
      $image = imagecreatefromjpeg($original_filename);
      imagettftext($image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imagejpeg ($image, $dest_filename, BWG()->options->jpeg_quality);
      imagedestroy($image);
    }
    elseif ($type == 3) {
      $image = imagecreatefrompng($original_filename);
      imagettftext($image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imageColorAllocateAlpha($image, 0, 0, 0, 127);
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagepng($image, $dest_filename, BWG()->options->png_quality);
      imagedestroy($image);
    }
    elseif ($type == 1) {
      $image = imagecreatefromgif($original_filename);
      imageColorAllocateAlpha($watermark_image, 0, 0, 0, 127);
      imagecopy($watermark_image, $image, 0, 0, 0, 0, $width, $height);
      imagettftext($watermark_image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imagealphablending($watermark_image, FALSE);
      imagesavealpha($watermark_image, TRUE);
      imagegif($watermark_image, $dest_filename);
      imagedestroy($image);
    }
    imagedestroy($watermark_image);
    @ini_restore('memory_limit');
  }

  public static function set_image_watermark($original_filename, $dest_filename, $watermark_url, $watermark_height, $watermark_width, $watermark_position) {
    if ( !empty($watermark_url) ) {
      $original_filename = htmlspecialchars_decode($original_filename, ENT_COMPAT | ENT_QUOTES);
      $dest_filename = htmlspecialchars_decode($dest_filename, ENT_COMPAT | ENT_QUOTES);
      $watermark_url = htmlspecialchars_decode($watermark_url, ENT_COMPAT | ENT_QUOTES);

      @ini_set('memory_limit', '-1');
      list($width, $height, $type) = getimagesize($original_filename);
      list($width_watermark, $height_watermark, $type_watermark) = getimagesize($watermark_url);

      $watermark_width = $width * $watermark_width / 100;
      $watermark_height = $height_watermark * $watermark_width / $width_watermark;

      $watermark_position = explode('-', $watermark_position);
      $top = $height - $watermark_height - 5;
      $left = $width - $watermark_width - 5;
      switch ($watermark_position[0]) {
        case 'top':
          $top = 5;
          break;
        case 'middle':
          $top = ($height - $watermark_height) / 2;
          break;
      }
      switch ($watermark_position[1]) {
        case 'left':
          $left = 5;
          break;
        case 'center':
          $left = ($width - $watermark_width) / 2;
          break;
      }
      if ($type_watermark == 2) {
        $watermark_image = imagecreatefromjpeg($watermark_url);
      }
      elseif ($type_watermark == 3) {
        $watermark_image = imagecreatefrompng($watermark_url);
      }
      elseif ($type_watermark == 1) {
        $watermark_image = imagecreatefromgif($watermark_url);
      }
      else {
        return false;
      }

      $watermark_image_resized = imagecreatetruecolor($watermark_width, $watermark_height);
      imagecolortransparent($watermark_image_resized, imagecolorat($watermark_image,0,0));
      imagecolorallocatealpha($watermark_image_resized, 255, 255, 255, 127);
      imagealphablending($watermark_image_resized, FALSE);
      imagesavealpha($watermark_image_resized, TRUE);
      imagecopyresampled ($watermark_image_resized, $watermark_image, 0, 0, 0, 0, $watermark_width, $watermark_height, $width_watermark, $height_watermark);

      if ($type == 2) {
        $image = imagecreatefromjpeg($original_filename);
        imagecopy($image, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
        if ($dest_filename <> '') {
        imagejpeg ($image, $dest_filename, BWG()->options->jpeg_quality);
        } else {
        header('Content-Type: image/jpeg');
        imagejpeg($image, null, BWG()->options->jpeg_quality);
        };
        imagedestroy($image);
      }
      elseif ($type == 3) {
        $image = imagecreatefrompng($original_filename);
        imagepalettetotruecolor($image);
        imagecopy($image, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
        imagealphablending($image, FALSE);
        imagesavealpha($image, TRUE);
        imagepng($image, $dest_filename, BWG()->options->png_quality);
        imagedestroy($image);
      }
      elseif ($type == 1) {
        $image = imagecreatefromgif($original_filename);
        $tempimage = imagecreatetruecolor($width, $height);
        imagecopy($tempimage, $image, 0, 0, 0, 0, $width, $height);
        imagecopy($tempimage, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
        imagegif($tempimage, $dest_filename);
        imagedestroy($image);
        imagedestroy($tempimage);
      }
      imagedestroy($watermark_image);
      @ini_restore('memory_limit');
    }
  }

  public static function bwg_image_recover_all($gallery_id, $limit = '') {
    $thumb_width = BWG()->options->upload_thumb_width;
    $width = BWG()->options->upload_img_width;
    global $wpdb;
    $prepareArgs = array();
    $where = ($gallery_id) ? ' `gallery_id` = ' . $gallery_id : 1;
    if ( $gallery_id ) {
      $where = ' `gallery_id` = %d';
      $prepareArgs[] = $gallery_id;
    } else {
      $where = 1;
    }
    $search = WDWLibrary::get('s', '');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    $limitstart = '';
    if ( !$limit ) {
      $limitstart = ' LIMIT 50 OFFSET %d';
      $prepareArgs[] = $limit;
    }
    $images = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where . $limitstart, $prepareArgs));
    if ( !empty( $images ) ) {
      foreach ( $images as $image ) {
        if ( preg_match( '/EMBED/', $image->filetype ) == 1 ) {
          continue;
        }
        self::recover_image( $image, $thumb_width, $width, ($gallery_id == 0 ? 'option_page' : 'gallery_page') );
      }
    }
    self::update_image_modified_date( $where );
  }

  /**
   * @param $file_path
   * @return bool
   */
  public static function repair_image_original($file_path) {
    $succeed = true;
    if ( !file_exists( $file_path ) ) {
      $dir = dirname( $file_path );
      if ( !is_dir( $dir ) ) {
        $succeed = mkdir( $dir, 0755 );
      }
      if ( $succeed ) {
        $main_file = str_replace( '/.original', '', $file_path );
        if ( file_exists( $main_file ) ) {
          $succeed = copy( $main_file, $file_path );
        }
        else {
          $succeed = false;
        }
      }
    }
    return $succeed;
  }

  public static function recover_image($image, $thumb_width, $width, $page) {
    if ( preg_match('/EMBED/', $image->filetype) == 1 ) {
      return;
    }
    $filename = htmlspecialchars_decode(BWG()->upload_dir . $image->image_url, ENT_COMPAT | ENT_QUOTES);
    $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image->thumb_url, ENT_COMPAT | ENT_QUOTES);
    $original_filename = str_replace('/thumb/', '/.original/', $thumb_filename);
    if ( WDWLibrary::repair_image_original($original_filename) ) {
      if ( file_exists( $original_filename ) || file_exists( $filename ) ) {
        if ( !file_exists( $original_filename ) ) {
          copy( $filename, $original_filename );
        }
        $original_image = wp_get_image_editor( $original_filename );
        if ( !is_wp_error( $original_image ) ) {
          $get_size = $original_image->get_size();
          $width_orig = $get_size[ 'width' ];
          $height_orig = $get_size[ 'height' ];
          $original_image->set_quality( BWG()->options->image_quality );
          self::recover_image_size( $width_orig, $height_orig, $width, $original_image, $filename );
          self::recover_image_size( $width_orig, $height_orig, $thumb_width, $original_image, $thumb_filename);

          $resolution_thumb = self::get_thumb_size( $image->thumb_url );

          $where = "id = " . $image->id;
          self::update_thumb_dimansions( $resolution_thumb, $where );
        }
        else {
          copy( $original_filename, $filename );
          copy( $original_filename, $thumb_filename );
        }
      }

    }
    if ($page == 'gallery_page') {
      ?>
      <script language="javascript">
        var image_src = window.parent.document.getElementById("image_thumb_<?php echo $image->id; ?>").src;
        document.getElementById("image_thumb_<?php echo $image->id; ?>").src = image_src;
      </script>
      <?php
    }
  }

  public static function recover_image_size($width_orig, $height_orig, $width, $original_image, $filename) {
    $percent = $width_orig / $width;
    $height = $height_orig / $percent;
    $original_image->resize($width, $height, false);
    $original_image->save($filename);
  }

  public static function detect_thumb( $detination ) {
    if (strpos($detination, '/thumb/') !== false) {
      return true;
    }
    return false;
  }

  public static function update_thumb_dimansions( $resolution_thumb, $where ) {
    global $wpdb;
    $update = $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `resolution_thumb` = "%s"  WHERE ' . $where, $resolution_thumb));
  }

  public static function resize_image($source, $destination, $max_width, $max_height) {
    $image = wp_get_image_editor( $source );
    if ( ! is_wp_error( $image ) ) {
      $image_size = $image->get_size();
      $img_width = $image_size[ 'width' ];
      $img_height = $image_size[ 'height' ];
      $scale = min( $max_width / $img_width, $max_height / $img_height );
      if ( ($scale >= 1) || (($max_width == NULL) && ($max_height == NULL)) ) {
        if ( $source !== $destination ) {
          if(self::detect_thumb($destination)) {
            self::$thumb_dimansions = intval($img_width)."x".intval($img_height);
          }
          return copy( $source, $destination );
        }
        return true;
      }
      else {
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        if(self::detect_thumb($destination)) {
          self::$thumb_dimansions = intval($new_width)."x".intval($new_height);
        }
        $image->set_quality( BWG()->options->image_quality );
        $image->resize( $new_width, $new_height, false );
        $saved = $image->save( $destination );
        return !is_wp_error($saved);
      }
    }
    else {
      if ( $source !== $destination ) {
        return copy( $source, $destination );
      }
      return true;
    }
  }

  public static function bwg_hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    }
    else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);
    return $rgb;
  }

  public static function bwg_imagettfbboxdimensions($font_size, $font_angle, $font, $text) {
    $box = @ImageTTFBBox($font_size, $font_angle, $font, $text) or die;
    $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
    $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
    $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
    $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
    return array(
      "width"  => ($max_x - $min_x),
      "height" => ($max_y - $min_y)
    );
  }

  /**
   * Return given file metadata.
   * 
   * @param $file
   *
   * @return array|bool
   */
  public static function read_image_metadata( $file ) {
    if (!file_exists($file)) {
      return false;
    }
    list( , , $sourceImageType ) = getimagesize($file);
    $meta = array(
      'aperture' => 0,
      'credit' => '',
      'camera' => '',
      'caption' => '',
      'created_timestamp' => 0,
      'copyright' => '',
      'focal_length' => 0,
      'iso' => 0,
      'shutter_speed' => 0,
      'title' => '',
      'orientation' => 0,
      'tags' => '',
    );
    if ( is_callable( 'iptcparse' ) ) {
      getimagesize( $file, $info );
      if ( ! empty( $info['APP13'] ) ) {
        $iptc = iptcparse( $info['APP13'] );
        if ( ! empty( $iptc['2#105'][0] ) ) {
          $meta['title'] = trim( $iptc['2#105'][0] );
        }
        elseif ( ! empty( $iptc['2#005'][0] ) ) {
          $meta['title'] = trim( $iptc['2#005'][0] );
        }
        if ( ! empty( $iptc['2#025'] ) ) {
          $meta['tags'] = json_encode($iptc['2#025']);
        }
        if ( ! empty( $iptc['2#120'][0] ) ) {
          $caption = trim( $iptc['2#120'][0] );
          if ( empty( $meta['title'] ) ) {
            mbstring_binary_safe_encoding();
            $caption_length = strlen( $caption );
            reset_mbstring_encoding();
            if ( $caption_length < 80 ) {
              $meta['title'] = $caption;
            } else {
              $meta['caption'] = $caption;
            }
          } elseif ( $caption != $meta['title'] ) {
            $meta['caption'] = $caption;
          }
        }
        if ( ! empty( $iptc['2#110'][0] ) ) {
          $meta['credit'] = trim( $iptc['2#110'][0] );
        }
        elseif ( ! empty( $iptc['2#080'][0] ) ) {
          $meta['credit'] = trim( $iptc['2#080'][0] );
        }
        if ( ! empty( $iptc['2#055'][0] ) and ! empty( $iptc['2#060'][0] ) ) {
          $meta['created_timestamp'] = strtotime( $iptc['2#055'][0] . ' ' . $iptc['2#060'][0] );
        }
        if ( ! empty( $iptc['2#116'][0] ) ) {
          $meta['copyright'] = trim( $iptc['2#116'][0] );
        }
      }
    }
    if ( is_callable( 'exif_read_data' ) && in_array( $sourceImageType, apply_filters( 'wp_read_image_metadata_types', array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) ) ) ) {
      $exif = @exif_read_data( $file );
      if ( empty( $meta['title'] ) && ! empty( $exif['Title'] ) ) {
        $meta['title'] = trim( $exif['Title'] );
      }
      if ( ! empty( $exif['ImageDescription'] ) ) {
        mbstring_binary_safe_encoding();
        $description_length = strlen( $exif['ImageDescription'] );
        reset_mbstring_encoding();
        if ( empty( $meta['title'] ) && $description_length < 80 ) {
          $meta['title'] = trim( $exif['ImageDescription'] );
          if ( empty( $meta['caption'] ) && ! empty( $exif['COMPUTED']['UserComment'] ) && trim( $exif['COMPUTED']['UserComment'] ) != $meta['title'] ) {
            $meta['caption'] = trim( $exif['COMPUTED']['UserComment'] );
          }
        } elseif ( empty( $meta['caption'] ) && trim( $exif['ImageDescription'] ) != $meta['title'] ) {
          $meta['caption'] = trim( $exif['ImageDescription'] );
        }
      } elseif ( empty( $meta['caption'] ) && ! empty( $exif['Comments'] ) && trim( $exif['Comments'] ) != $meta['title'] ) {
        $meta['caption'] = trim( $exif['Comments'] );
      }
      if ( empty( $meta['credit'] ) ) {
        if ( ! empty( $exif['Artist'] ) ) {
          $meta['credit'] = trim( $exif['Artist'] );
        } elseif ( ! empty($exif['Author'] ) ) {
          $meta['credit'] = trim( $exif['Author'] );
        }
      }
      if ( empty( $meta['copyright'] ) && ! empty( $exif['Copyright'] ) ) {
        $meta['copyright'] = trim( $exif['Copyright'] );
      }
      if ( ! empty( $exif['FNumber'] ) ) {
        $meta['aperture'] = round( wp_exif_frac2dec( $exif['FNumber'] ), 2 );
      }
      if ( ! empty( $exif['Model'] ) ) {
        $meta['camera'] = trim( $exif['Model'] );
      }
      if ( empty( $meta['created_timestamp'] ) && ! empty( $exif['DateTimeDigitized'] ) ) {
        $meta['created_timestamp'] = wp_exif_date2ts( $exif['DateTimeDigitized'] );
      }
      if ( ! empty( $exif['FocalLength'] ) ) {
        $meta['focal_length'] = (string) wp_exif_frac2dec( $exif['FocalLength'] );
      }
      if ( ! empty( $exif['ISOSpeedRatings'] ) ) {
        $meta['iso'] = is_array( $exif['ISOSpeedRatings'] ) ? reset( $exif['ISOSpeedRatings'] ) : $exif['ISOSpeedRatings'];
        $meta['iso'] = trim( $meta['iso'] );
      }
      if ( ! empty( $exif['ExposureTime'] ) ) {
        $meta['shutter_speed'] = (string) wp_exif_frac2dec( $exif['ExposureTime'] );
      }
      if ( ! empty( $exif['Orientation'] ) ) {
        $meta['orientation'] = $exif['Orientation'];
      }
    }
    foreach ( array( 'title', 'caption', 'credit', 'copyright', 'camera', 'iso' ) as $key ) {
      if ( $meta[ $key ] && ! seems_utf8( $meta[ $key ] ) ) {
        $meta[ $key ] = utf8_encode( $meta[ $key ] );
      }
    }
    foreach ( $meta as &$value ) {
      if ( is_string( $value ) ) {
        $value = wp_kses_post( $value );
      }
    }
    return $meta;
  }

  /**
   * Validate integer value.
   *
   * @param $value
   */
  public static function validate_integers( &$value ) {
    $value = (int) $value;
  }

  /**
   * Get shortcode defauls.
   *
   * @param array $params
   *
   * @return array   $defauls
   */
  public static function get_shortcode_option_params( $params ) {
    $theme_id = self::get_default_theme_id();
    $use_option_defaults = (isset($params['use_option_defaults']) && $params['use_option_defaults'] == 1) ? TRUE : FALSE;
    $from = (isset($params['from']) && $params['from'] == 'widget' ) ? TRUE : FALSE;
    $defaults = array(
      'gallery_type' => isset($params['gallery_type']) ? $params['gallery_type'] : 'thumbnails',
      'gallery_id' => isset($params['gallery_id']) ? $params['gallery_id'] : 0,
      'gal_title' => isset($params['gal_title']) ? $params['gal_title'] : '',
      'album_id' => isset($params['album_id']) ? $params['album_id'] : 0,
      'tag' => isset($params['tag']) ? $params['tag'] : 0,
      'theme_id' => isset($params['theme_id']) ? $params['theme_id'] : $theme_id,
      'image_enable_page' => 0,
      'images_per_page' => 0,
      'thumb_width' => BWG()->options->thumb_width,
      'thumb_height' => BWG()->options->thumb_height,
      'watermark_type' => (($from) ? BWG()->options->watermark_type : ($use_option_defaults ? BWG()->options->watermark_type : (isset($params['watermark_type']) ? $params['watermark_type'] : 'none'))),
      'watermark_text' => (($from) ? urlencode(BWG()->options->watermark_text) : ($use_option_defaults ? urlencode(BWG()->options->watermark_text) : (isset($params['watermark_text']) ? urlencode($params['watermark_text']) : ''))),
      'watermark_font_size' => (($from) ? BWG()->options->watermark_font_size : ($use_option_defaults ? BWG()->options->watermark_font_size : (isset($params['watermark_font_size']) ? $params['watermark_font_size'] : 12))),
      'watermark_font' => (($from) ? BWG()->options->watermark_font : ($use_option_defaults ? BWG()->options->watermark_font : (isset($params['watermark_font']) ? $params['watermark_font'] : 'Arial'))),
      'watermark_color' => (($from) ? BWG()->options->watermark_color : ($use_option_defaults ? BWG()->options->watermark_color : (isset($params['watermark_color']) ? $params['watermark_color'] : 'FFFFFF'))),
      'watermark_link' => (($from) ? urlencode(BWG()->options->watermark_link) : ($use_option_defaults ? urlencode(BWG()->options->watermark_link) : (isset($params['watermark_link']) ? urlencode($params['watermark_link']) : ''))),
      'watermark_url' => (($from) ? BWG()->options->watermark_url : ($use_option_defaults ? BWG()->options->watermark_url : (isset($params['watermark_url']) ? $params['watermark_url'] : ''))),
      'watermark_width' => (($from) ? BWG()->options->watermark_width: ($use_option_defaults ? BWG()->options->watermark_width : (isset($params['watermark_width']) ? $params['watermark_width'] : 90))),
      'watermark_height' => (($from) ? BWG()->options->watermark_height : ($use_option_defaults ? BWG()->options->watermark_height : (isset($params['watermark_height']) ? $params['watermark_height'] : 90))),
      'watermark_opacity' => (($from) ? BWG()->options->watermark_opacity : ($use_option_defaults ? BWG()->options->watermark_opacity : (isset($params['watermark_opacity']) ? $params['watermark_opacity'] : 30))),
      'watermark_position' => (($from) ? BWG()->options->watermark_position : ($use_option_defaults ? BWG()->options->watermark_position : (isset($params['watermark_position']) ? $params['watermark_position'] : 'bottom-right'))),
    );

    $defaults['thumb_click_action'] = self::get_option_value('thumb_click_action', 'thumb_click_action', 'thumb_click_action', $from || $use_option_defaults, $params);
    $defaults['thumb_link_target'] = self::get_option_value('thumb_link_target', 'thumb_link_target', 'thumb_link_target', $from || $use_option_defaults, $params);
    $defaults['popup_fullscreen'] = (bool) self::get_option_value('popup_fullscreen', 'popup_fullscreen', 'popup_fullscreen', $from || $use_option_defaults, $params);
    $defaults['popup_width'] = self::get_option_value('popup_width', 'popup_width', 'popup_width', $from || $use_option_defaults, $params);
    $defaults['popup_height'] = self::get_option_value('popup_height', 'popup_height', 'popup_height', $from || $use_option_defaults, $params);
    $defaults['popup_effect'] = self::get_option_value('popup_effect', 'popup_effect', 'popup_type', $from || $use_option_defaults, $params);
    $defaults['popup_effect_duration'] = self::get_option_value('popup_effect_duration', 'popup_effect_duration', 'popup_effect_duration', $from || $use_option_defaults, $params);
    $defaults['popup_autoplay'] = (bool) self::get_option_value('popup_autoplay', 'popup_autoplay', 'popup_autoplay', $from || $use_option_defaults, $params);
    $defaults['popup_interval'] = self::get_option_value('popup_interval', 'popup_interval', 'popup_interval', $from || $use_option_defaults, $params);
    $defaults['popup_enable_filmstrip'] = (bool) self::get_option_value('popup_enable_filmstrip', 'popup_enable_filmstrip', 'popup_enable_filmstrip', $from || $use_option_defaults, $params);
    $defaults['popup_filmstrip_height'] = self::get_option_value('popup_filmstrip_height', 'popup_filmstrip_height', 'popup_filmstrip_height', $from || $use_option_defaults, $params);
    $defaults['popup_enable_ctrl_btn'] = (bool) self::get_option_value('popup_enable_ctrl_btn', 'popup_enable_ctrl_btn', 'popup_enable_ctrl_btn', $from || $use_option_defaults, $params);
    $defaults['popup_enable_fullscreen'] = (bool) self::get_option_value('popup_enable_fullscreen', 'popup_enable_fullscreen', 'popup_enable_fullscreen', $from || $use_option_defaults, $params);
    $defaults['popup_enable_comment'] = (bool) self::get_option_value('popup_enable_comment', 'popup_enable_comment', 'popup_enable_comment', $from || $use_option_defaults, $params);
    $defaults['popup_enable_email'] = (bool) self::get_option_value('popup_enable_email', 'popup_enable_email', 'popup_enable_email', $from || $use_option_defaults, $params);
    $defaults['popup_enable_captcha'] = (bool) self::get_option_value('popup_enable_captcha', 'popup_enable_captcha', 'popup_enable_captcha', $from || $use_option_defaults, $params);
    $defaults['gdpr_compliance'] = (bool) self::get_option_value('gdpr_compliance', 'gdpr_compliance', 'gdpr_compliance', $from || $use_option_defaults, $params);
    $defaults['comment_moderation'] = (bool) self::get_option_value('comment_moderation', 'comment_moderation', 'comment_moderation', $from || $use_option_defaults, $params);
    $defaults['popup_enable_info'] = (bool) self::get_option_value('popup_enable_info', 'popup_enable_info', 'popup_enable_info', $from || $use_option_defaults, $params);
    $defaults['popup_info_always_show'] = (bool) self::get_option_value('popup_info_always_show', 'popup_info_always_show', 'popup_info_always_show', $from || $use_option_defaults, $params);
    $defaults['popup_info_full_width'] = (bool) self::get_option_value('popup_info_full_width', 'popup_info_full_width', 'popup_info_full_width', $from || $use_option_defaults, $params);
    $defaults['autohide_lightbox_navigation'] = (bool) self::get_option_value('autohide_lightbox_navigation', 'autohide_lightbox_navigation', 'autohide_lightbox_navigation', $from || $use_option_defaults, $params);
    $defaults['popup_hit_counter'] = (bool) self::get_option_value('popup_hit_counter', 'popup_hit_counter', 'popup_hit_counter', $from || $use_option_defaults, $params);
    $defaults['popup_enable_rate'] = (bool) self::get_option_value('popup_enable_rate', 'popup_enable_rate', 'popup_enable_rate', $from || $use_option_defaults, $params);
    $defaults['popup_enable_fullsize_image'] = (bool) self::get_option_value('popup_enable_fullsize_image', 'popup_enable_fullsize_image', 'popup_enable_fullsize_image', $from || $use_option_defaults, $params);
    $defaults['popup_enable_download'] = (bool) self::get_option_value('popup_enable_download', 'popup_enable_download', 'popup_enable_download', $from || $use_option_defaults, $params);
    $defaults['show_image_counts'] = (bool) self::get_option_value('show_image_counts', 'show_image_counts', 'show_image_counts', $from || $use_option_defaults, $params);
    $defaults['enable_loop'] = (bool) self::get_option_value('enable_loop', 'enable_loop', 'enable_loop', $from || $use_option_defaults, $params);
    $defaults['enable_addthis'] = (bool) self::get_option_value('enable_addthis', 'enable_addthis', 'enable_addthis', $from || $use_option_defaults, $params);
    $defaults['addthis_profile_id'] = self::get_option_value('addthis_profile_id', 'addthis_profile_id', 'addthis_profile_id', $from || $use_option_defaults, $params);
    $defaults['popup_enable_facebook'] = (bool) self::get_option_value('popup_enable_facebook', 'popup_enable_facebook', 'popup_enable_facebook', $from || $use_option_defaults, $params);
    $defaults['popup_enable_twitter'] = (bool) self::get_option_value('popup_enable_twitter', 'popup_enable_twitter', 'popup_enable_twitter', $from || $use_option_defaults, $params);
    $defaults['popup_enable_pinterest'] = (bool) self::get_option_value('popup_enable_pinterest', 'popup_enable_pinterest', 'popup_enable_pinterest', $from || $use_option_defaults, $params);
    $defaults['popup_enable_tumblr'] = (bool) self::get_option_value('popup_enable_tumblr', 'popup_enable_tumblr', 'popup_enable_tumblr', $from || $use_option_defaults, $params);
    $defaults['popup_enable_ecommerce'] = (bool) self::get_option_value('popup_enable_ecommerce', 'popup_enable_ecommerce', 'popup_enable_ecommerce', $from || $use_option_defaults, $params);

    switch ($defaults['gallery_type']) {
      case 'thumbnails': {
        $defaults['thumb_width'] = self::get_option_value('thumb_width', 'thumb_width', 'thumb_width', $use_option_defaults, $params);
        $defaults['thumb_height'] = self::get_option_value('thumb_height', 'thumb_height', 'thumb_height', $use_option_defaults, $params);
        $defaults['image_column_number'] = abs(intval(self::get_option_value('image_column_number', 'image_column_number', 'image_column_number', $use_option_defaults, $params)));
        $defaults['image_enable_page'] = self::get_option_value('image_enable_page', 'image_enable_page', 'image_enable_page', $use_option_defaults, $params);
        $defaults['images_per_page'] = abs(intval(self::get_option_value('images_per_page', 'images_per_page', 'images_per_page', $use_option_defaults, $params)));
        $defaults['load_more_image_count'] = self::get_option_value('load_more_image_count', 'load_more_image_count', 'load_more_image_count', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('sort_by', 'sort_by', 'sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('order_by', 'order_by', 'order_by', $use_option_defaults, $params);
        $defaults['show_search_box'] = self::get_option_value('show_search_box', 'show_search_box', 'show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('placeholder', 'placeholder', 'placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('search_box_width', 'search_box_width', 'search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('show_sort_images', 'show_sort_images', 'show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('show_tag_box', 'show_tag_box', 'show_tag_box', $use_option_defaults, $params);
        $defaults['showthumbs_name'] = self::get_option_value('showthumbs_name', 'showthumbs_name', 'showthumbs_name', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('show_gallery_description', 'show_gallery_description', 'show_gallery_description', $use_option_defaults, $params);
        $defaults['image_title'] = self::get_option_value('image_title', 'image_title', 'image_title_show_hover', $from || $use_option_defaults, $params);
        $defaults['show_thumb_description'] = self::get_option_value('show_thumb_description', 'show_thumb_description', 'show_thumb_description', $use_option_defaults, $params);
        $defaults['play_icon'] = self::get_option_value('play_icon', 'play_icon', 'play_icon', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('gallery_download', 'gallery_download', 'gallery_download', $use_option_defaults, $params);
        $defaults['ecommerce_icon'] = self::get_option_value('ecommerce_icon_show_hover', 'ecommerce_icon', 'ecommerce_icon_show_hover', $use_option_defaults, $params);
	  }
	  break;
      case 'thumbnails_masonry': {
        $defaults['masonry_hor_ver'] = self::get_option_value('masonry_hor_ver', 'masonry_hor_ver', 'masonry', $use_option_defaults, $params);
        $defaults['show_masonry_thumb_description'] = self::get_option_value('show_masonry_thumb_description', 'show_masonry_thumb_description', 'show_masonry_thumb_description', $use_option_defaults, $params);
        $defaults['thumb_width'] = self::get_option_value('masonry_thumb_size', 'thumb_width', 'masonry_thumb_size', $use_option_defaults, $params);
        $defaults['thumb_height'] = self::get_option_value('thumb_height', 'thumb_height', 'thumb_height', $use_option_defaults, $params);
        $defaults['image_column_number'] = abs(intval(self::get_option_value('masonry_image_column_number', 'image_column_number', 'masonry_image_column_number', $use_option_defaults, $params)));
        $defaults['image_enable_page'] = self::get_option_value('masonry_image_enable_page', 'image_enable_page', 'masonry_image_enable_page', $use_option_defaults, $params);
        $defaults['images_per_page'] = abs(intval(self::get_option_value('masonry_images_per_page', 'images_per_page', 'masonry_images_per_page', $use_option_defaults, $params)));
        $defaults['load_more_image_count'] = self::get_option_value('masonry_load_more_image_count', 'load_more_image_count', 'masonry_load_more_image_count', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('masonry_sort_by', 'sort_by', 'masonry_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('masonry_order_by', 'order_by', 'masonry_order_by', $use_option_defaults, $params);
        $defaults['show_search_box'] = self::get_option_value('masonry_show_search_box', 'show_search_box', 'masonry_show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('masonry_placeholder', 'placeholder', 'masonry_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('masonry_search_box_width', 'search_box_width', 'masonry_search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('masonry_show_sort_images', 'show_sort_images', 'masonry_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('masonry_show_tag_box', 'show_tag_box', 'masonry_show_tag_box', $use_option_defaults, $params);
        $defaults['showthumbs_name'] = self::get_option_value('masonry_show_gallery_title', 'showthumbs_name', 'masonry_show_gallery_title', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('masonry_show_gallery_description', 'show_gallery_description', 'masonry_show_gallery_description', $use_option_defaults, $params);
        $defaults['image_title'] = self::get_option_value('image_title', 'image_title', 'masonry_image_title', $from || $use_option_defaults, $params);
        $defaults['play_icon'] = self::get_option_value('masonry_play_icon', 'play_icon', 'masonry_play_icon', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('masonry_gallery_download', 'gallery_download', 'masonry_gallery_download', $use_option_defaults, $params);
        $defaults['ecommerce_icon'] = self::get_option_value('masonry_ecommerce_icon_show_hover', 'ecommerce_icon', 'masonry_ecommerce_icon_show_hover', $use_option_defaults, $params);
	  }
	  break;
      case 'thumbnails_mosaic': {
        $defaults['mosaic_hor_ver'] = self::get_option_value('mosaic_hor_ver', 'mosaic_hor_ver', 'mosaic', $use_option_defaults, $params);
        $defaults['resizable_mosaic'] = self::get_option_value('resizable_mosaic', 'resizable_mosaic', 'resizable_mosaic', $use_option_defaults, $params);
        $defaults['mosaic_total_width'] = self::get_option_value('mosaic_total_width', 'mosaic_total_width', 'mosaic_total_width', $use_option_defaults, $params);
        $defaults['thumb_width'] = self::get_option_value('mosaic_thumb_size', 'thumb_width', 'mosaic_thumb_size', $use_option_defaults, $params);
        $defaults['thumb_height'] = self::get_option_value('mosaic_thumb_size', 'thumb_height', 'mosaic_thumb_size', $use_option_defaults, $params);
        $defaults['image_enable_page'] = self::get_option_value('mosaic_image_enable_page', 'image_enable_page', 'mosaic_image_enable_page', $use_option_defaults, $params);
        $defaults['images_per_page'] = abs(intval(self::get_option_value('mosaic_images_per_page', 'images_per_page', 'mosaic_images_per_page', $use_option_defaults, $params)));
        $defaults['load_more_image_count'] = self::get_option_value('mosaic_load_more_image_count', 'load_more_image_count', 'mosaic_load_more_image_count', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('mosaic_sort_by', 'sort_by', 'mosaic_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('mosaic_order_by', 'order_by', 'mosaic_order_by', $use_option_defaults, $params);
        $defaults['show_search_box'] = self::get_option_value('mosaic_show_search_box', 'show_search_box', 'mosaic_show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('mosaic_placeholder', 'placeholder', 'mosaic_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('mosaic_search_box_width', 'search_box_width', 'mosaic_search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('mosaic_show_sort_images', 'show_sort_images', 'mosaic_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('mosaic_show_tag_box', 'show_tag_box', 'mosaic_show_tag_box', $use_option_defaults, $params);
        $defaults['showthumbs_name'] = self::get_option_value('mosaic_show_gallery_title', 'showthumbs_name', 'mosaic_show_gallery_title', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('mosaic_show_gallery_description', 'show_gallery_description', 'mosaic_show_gallery_description', $use_option_defaults, $params);
        $defaults['image_title'] = self::get_option_value('mosaic_image_title', 'image_title', 'mosaic_image_title_show_hover', $from || $use_option_defaults, $params);
        $defaults['play_icon'] = self::get_option_value('mosaic_play_icon', 'play_icon', 'mosaic_play_icon', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('mosaic_gallery_download', 'gallery_download', 'mosaic_gallery_download', $use_option_defaults, $params);
        $defaults['ecommerce_icon'] = self::get_option_value('mosaic_ecommerce_icon_show_hover', 'ecommerce_icon', 'mosaic_ecommerce_icon_show_hover', $use_option_defaults, $params);
	  }
	  break;
      case 'slideshow': {
        $defaults['slideshow_effect'] = self::get_option_value('slideshow_effect', 'slideshow_effect', 'slideshow_type', $use_option_defaults, $params);
        $defaults['slideshow_interval'] = self::get_option_value('slideshow_interval', 'slideshow_interval', 'slideshow_interval', $use_option_defaults, $params);
        $defaults['slideshow_width'] = self::get_option_value('slideshow_width', 'slideshow_width', 'slideshow_width', $use_option_defaults, $params);
        $defaults['slideshow_height'] = self::get_option_value('slideshow_height', 'slideshow_height', 'slideshow_height', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('slideshow_sort_by', 'sort_by', 'slideshow_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('slideshow_order_by', 'order_by', 'slideshow_order_by', $use_option_defaults, $params);
        $defaults['enable_slideshow_autoplay'] = self::get_option_value('enable_slideshow_autoplay', 'enable_slideshow_autoplay', 'slideshow_enable_autoplay', $use_option_defaults, $params);
        $defaults['enable_slideshow_shuffle'] = self::get_option_value('enable_slideshow_shuffle', 'enable_slideshow_shuffle', 'slideshow_enable_shuffle', $use_option_defaults, $params);
        $defaults['enable_slideshow_ctrl'] = self::get_option_value('enable_slideshow_ctrl', 'enable_slideshow_ctrl', 'slideshow_enable_ctrl', $use_option_defaults, $params);
        $defaults['autohide_slideshow_navigation'] = self::get_option_value('autohide_slideshow_navigation', 'autohide_slideshow_navigation', 'autohide_slideshow_navigation', $use_option_defaults, $params);
        $defaults['enable_slideshow_filmstrip'] = self::get_option_value('enable_slideshow_filmstrip', 'enable_slideshow_filmstrip', 'slideshow_enable_filmstrip', $use_option_defaults, $params);
        $defaults['slideshow_filmstrip_height'] = self::get_option_value('slideshow_filmstrip_height', 'slideshow_filmstrip_height', 'slideshow_filmstrip_height', $use_option_defaults, $params);
        $defaults['slideshow_enable_title'] = self::get_option_value('slideshow_enable_title', 'slideshow_enable_title', 'slideshow_enable_title', $from || $use_option_defaults, $params);
        $defaults['slideshow_title_position'] = self::get_option_value('slideshow_title_position', 'slideshow_title_position', 'slideshow_title_position', $from || $use_option_defaults, $params);
        $defaults['slideshow_title_full_width'] = self::get_option_value('slideshow_title_full_width', 'slideshow_title_full_width', 'slideshow_title_full_width', $from || $use_option_defaults, $params);
        $defaults['slideshow_enable_description'] = self::get_option_value('slideshow_enable_description', 'slideshow_enable_description', 'slideshow_enable_description', $from || $use_option_defaults, $params);
        $defaults['slideshow_description_position'] = self::get_option_value('slideshow_description_position', 'slideshow_description_position', 'slideshow_description_position', $from || $use_option_defaults, $params);
        $defaults['enable_slideshow_music'] = self::get_option_value('enable_slideshow_music', 'enable_slideshow_music', 'slideshow_enable_music', $from || $use_option_defaults, $params);
        $defaults['slideshow_music_url'] = self::get_option_value('slideshow_music_url', 'slideshow_music_url', 'slideshow_audio_url', $from || $use_option_defaults, $params);
        $defaults['slideshow_effect_duration'] = self::get_option_value('slideshow_effect_duration', 'slideshow_effect_duration', 'slideshow_effect_duration', $use_option_defaults, $params);
        $defaults['slideshow_interval'] = (int) self::get_option_value('slideshow_interval', 'slideshow_interval', 'slideshow_interval', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('slideshow_gallery_download', 'gallery_download', 'slideshow_gallery_download', $use_option_defaults, $params);
	  }
	  break;
      case 'image_browser': {
        $defaults['image_browser_width'] = self::get_option_value('image_browser_width', 'image_browser_width', 'image_browser_width', $use_option_defaults, $params);
        $defaults['image_browser_title_enable'] = self::get_option_value('image_browser_title_enable', 'image_browser_title_enable', 'image_browser_title_enable', $use_option_defaults, $params);
        $defaults['image_browser_description_enable'] = self::get_option_value('image_browser_description_enable', 'image_browser_description_enable', 'image_browser_description_enable', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('image_browser_sort_by', 'sort_by', 'image_browser_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('image_browser_order_by', 'order_by', 'image_browser_order_by', $use_option_defaults, $params);
        $defaults['showthumbs_name'] = self::get_option_value('image_browser_show_gallery_title', 'showthumbs_name', 'image_browser_show_gallery_title', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('image_browser_show_gallery_description', 'show_gallery_description', 'image_browser_show_gallery_description', $use_option_defaults, $params);
        $defaults['show_search_box'] = self::get_option_value('image_browser_show_search_box', 'show_search_box', 'image_browser_show_search_box', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('image_browser_show_sort_images', 'show_sort_images', 'image_browser_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('image_browser_show_tag_box', 'show_tag_box', 'image_browser_show_tag_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('image_browser_placeholder', 'placeholder', 'image_browser_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('image_browser_search_box_width', 'search_box_width', 'image_browser_search_box_width', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('image_browser_gallery_download', 'gallery_download', 'image_browser_gallery_download', $use_option_defaults, $params);
	   }
	  break;
      case 'blog_style': {
        $defaults['blog_style_width'] = self::get_option_value('blog_style_width', 'blog_style_width', 'blog_style_width', $use_option_defaults, $params);
        $defaults['blog_style_title_enable'] = self::get_option_value('blog_style_title_enable', 'blog_style_title_enable', 'blog_style_title_enable', $use_option_defaults, $params);
        $defaults['blog_style_images_per_page'] = self::get_option_value('blog_style_images_per_page', 'blog_style_images_per_page', 'blog_style_images_per_page', $use_option_defaults, $params);
        $defaults['blog_style_load_more_image_count'] = self::get_option_value('blog_style_load_more_image_count', 'blog_style_load_more_image_count', 'blog_style_load_more_image_count', $use_option_defaults, $params);
        $defaults['blog_style_enable_page'] = self::get_option_value('blog_style_enable_page', 'blog_style_enable_page', 'blog_style_enable_page', $use_option_defaults, $params);
        $defaults['blog_style_description_enable'] = self::get_option_value('blog_style_description_enable', 'blog_style_description_enable', 'blog_style_description_enable', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('blog_style_sort_by', 'sort_by', 'blog_style_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('blog_style_rder_by', 'order_by', 'blog_style_order_by', $use_option_defaults, $params);
        $defaults['showthumbs_name'] = self::get_option_value('blog_style_show_gallery_title', 'showthumbs_name', 'blog_style_show_gallery_title', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('blog_style_show_gallery_description', 'show_gallery_description', 'blog_style_show_gallery_description', $use_option_defaults, $params);
        $defaults['show_search_box'] = self::get_option_value('blog_style_show_search_box', 'show_search_box', 'blog_style_show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('blog_style_placeholder', 'placeholder', 'blog_style_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('blog_style_search_box_width', 'search_box_width', 'blog_style_search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('blog_style_show_sort_images', 'show_sort_images', 'blog_style_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('blog_style_show_tag_box', 'show_tag_box', 'blog_style_show_tag_box', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('blog_style_gallery_download', 'gallery_download', 'blog_style_gallery_download', $use_option_defaults, $params);
		}
	  break;
      case 'carousel': {
        $defaults['carousel_interval'] = self::get_option_value('carousel_interval', 'carousel_interval', 'carousel_interval', $use_option_defaults, $params);
        $defaults['carousel_width'] = self::get_option_value('carousel_width', 'carousel_width', 'carousel_width', $use_option_defaults, $params);
        $defaults['carousel_height'] = self::get_option_value('carousel_height', 'carousel_height', 'carousel_height', $use_option_defaults, $params);
        $defaults['carousel_image_column_number'] = self::get_option_value('carousel_image_column_number', 'carousel_image_column_number', 'carousel_image_column_number', $use_option_defaults, $params);
        $defaults['carousel_image_par'] = self::get_option_value('carousel_image_par', 'carousel_image_par', 'carousel_image_par', $use_option_defaults, $params);
        $defaults['enable_carousel_title'] = self::get_option_value('enable_carousel_title', 'enable_carousel_title', 'carousel_enable_title', $use_option_defaults, $params);
        $defaults['enable_carousel_autoplay'] = self::get_option_value('enable_carousel_autoplay', 'enable_carousel_autoplay', 'carousel_enable_autoplay', $use_option_defaults, $params);
        $defaults['carousel_r_width'] = self::get_option_value('carousel_r_width', 'carousel_r_width', 'carousel_r_width', $use_option_defaults, $params);
        $defaults['carousel_fit_containerWidth'] = self::get_option_value('carousel_fit_containerWidth', 'carousel_fit_containerWidth', 'carousel_fit_containerWidth', $use_option_defaults, $params);
        $defaults['carousel_prev_next_butt'] = self::get_option_value('carousel_prev_next_butt', 'carousel_prev_next_butt', 'carousel_prev_next_butt', $use_option_defaults, $params);
        $defaults['carousel_play_pause_butt'] = self::get_option_value('carousel_play_pause_butt', 'carousel_play_pause_butt', 'carousel_play_pause_butt', $use_option_defaults, $params);
        $defaults['sort_by'] = self::get_option_value('carousel_sort_by', 'sort_by', 'carousel_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('carousel_order_by', 'order_by', 'carousel_order_by', $use_option_defaults, $params);
        $defaults['showthumbs_name'] = self::get_option_value('carousel_show_gallery_title', 'showthumbs_name', 'carousel_show_gallery_title', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('carousel_show_gallery_description', 'show_gallery_description', 'carousel_show_gallery_description', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('carousel_gallery_download', 'gallery_download', 'carousel_gallery_download', $use_option_defaults, $params);
      }
	  break;
      case 'album_compact_preview': {
        $defaults['compuct_album_column_number'] = self::get_option_value('compuct_album_column_number', 'compuct_album_column_number', 'album_column_number', $use_option_defaults, $params);
        $defaults['compuct_album_thumb_width'] = self::get_option_value('compuct_album_thumb_width', 'compuct_album_thumb_width', 'album_thumb_width', $use_option_defaults, $params);
        $defaults['compuct_album_thumb_height'] = self::get_option_value('compuct_album_thumb_height', 'compuct_album_thumb_height', 'album_thumb_height', $use_option_defaults, $params);
        $defaults['compuct_album_image_column_number'] = self::get_option_value('compuct_album_image_column_number', 'compuct_album_image_column_number', 'album_image_column_number', $use_option_defaults, $params);
        $defaults['compuct_album_image_thumb_width'] = self::get_option_value('compuct_album_image_thumb_width', 'compuct_album_image_thumb_width', 'album_image_thumb_width', $use_option_defaults, $params);
        $defaults['compuct_album_image_thumb_height'] = self::get_option_value('compuct_album_image_thumb_height', 'compuct_album_image_thumb_height', 'album_image_thumb_height', $use_option_defaults, $params);
        $defaults['compuct_album_enable_page'] = self::get_option_value('compuct_album_enable_page', 'compuct_album_enable_page', 'album_enable_page', $use_option_defaults, $params);
        $defaults['compuct_albums_per_page'] = self::get_option_value('compuct_albums_per_page', 'compuct_albums_per_page', 'albums_per_page', $use_option_defaults, $params);
        $defaults['compuct_album_images_per_page'] = self::get_option_value('compuct_album_images_per_page', 'compuct_album_images_per_page', 'album_images_per_page', $use_option_defaults, $params);
		$defaults['album_sort_by'] = self::get_option_value('compact_album_sort_by', 'all_album_sort_by', 'compact_album_sort_by', $use_option_defaults, $params);
		$defaults['album_order_by'] = self::get_option_value('compact_album_order_by', 'all_album_order_by', 'compact_album_order_by', $use_option_defaults, $params);
		$defaults['sort_by'] = self::get_option_value('album_sort_by', 'sort_by', 'album_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('album_order_by', 'order_by', 'album_order_by', $use_option_defaults, $params);
		$defaults['show_search_box'] = self::get_option_value('album_show_search_box', 'show_search_box', 'album_show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('album_placeholder', 'placeholder', 'album_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('album_search_box_width', 'search_box_width', 'album_search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('album_show_sort_images', 'show_sort_images', 'album_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('album_show_tag_box', 'show_tag_box', 'album_show_tag_box', $use_option_defaults, $params);
        $defaults['show_album_name'] = self::get_option_value('show_album_name', 'show_album_name', 'show_album_name', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('album_show_gallery_description', 'show_gallery_description', 'album_show_gallery_description', $use_option_defaults, $params);
        $defaults['compuct_album_title'] = self::get_option_value('compuct_album_title', 'compuct_album_title', 'album_title_show_hover', $use_option_defaults, $params);
        $defaults['compuct_album_view_type'] = self::get_option_value('compuct_album_view_type', 'compuct_album_view_type', 'album_view_type', $use_option_defaults, $params);
        $defaults['compuct_album_image_title'] = self::get_option_value('compuct_album_image_title', 'compuct_album_image_title', 'album_image_title_show_hover', $use_option_defaults, $params);
        $defaults['compuct_album_mosaic_hor_ver'] = self::get_option_value('compuct_album_mosaic_hor_ver', 'compuct_album_mosaic_hor_ver', 'album_mosaic', $use_option_defaults, $params);
        $defaults['compuct_album_resizable_mosaic'] = self::get_option_value('compuct_album_resizable_mosaic', 'compuct_album_resizable_mosaic', 'album_resizable_mosaic', $use_option_defaults, $params);
        $defaults['compuct_album_mosaic_total_width'] = self::get_option_value('compuct_album_mosaic_total_width', 'compuct_album_mosaic_total_width', 'album_mosaic_total_width', $use_option_defaults, $params);
        $defaults['play_icon'] = self::get_option_value('album_play_icon', 'play_icon', 'album_play_icon', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('album_gallery_download', 'gallery_download', 'album_gallery_download', $use_option_defaults, $params);
        $defaults['ecommerce_icon'] = self::get_option_value('album_ecommerce_icon_show_hover', 'ecommerce_icon', 'album_ecommerce_icon_show_hover', $use_option_defaults, $params);
	  }
	  break;
      case 'album_masonry_preview': {
        $defaults['masonry_album_column_number'] = self::get_option_value('masonry_album_column_number', 'masonry_album_column_number', 'album_masonry_column_number', $use_option_defaults, $params);
        $defaults['masonry_album_thumb_width'] = self::get_option_value('masonry_album_thumb_width', 'masonry_album_thumb_width', 'album_masonry_thumb_width', $use_option_defaults, $params);
        $defaults['masonry_album_image_column_number'] = self::get_option_value('masonry_album_image_column_number', 'masonry_album_image_column_number', 'album_masonry_image_column_number', $use_option_defaults, $params);
        $defaults['masonry_album_image_thumb_width'] = self::get_option_value('masonry_album_image_thumb_width', 'masonry_album_image_thumb_width', 'album_masonry_image_thumb_width', $use_option_defaults, $params);
        $defaults['masonry_album_enable_page'] = self::get_option_value('masonry_album_enable_page', 'masonry_album_enable_page', 'album_masonry_enable_page', $use_option_defaults, $params);
        $defaults['masonry_albums_per_page'] = self::get_option_value('masonry_albums_per_page', 'masonry_albums_per_page', 'albums_masonry_per_page', $use_option_defaults, $params);
        $defaults['masonry_album_images_per_page'] = self::get_option_value('masonry_album_images_per_page', 'masonry_album_images_per_page', 'album_masonry_images_per_page', $use_option_defaults, $params);
		$defaults['album_sort_by'] = self::get_option_value('masonry_album_sort_by', 'all_album_sort_by', 'masonry_album_sort_by', $use_option_defaults, $params);
        $defaults['album_order_by'] = self::get_option_value('masonry_album_order_by', 'all_album_order_by', 'masonry_album_order_by', $use_option_defaults, $params);
		$defaults['sort_by'] = self::get_option_value('album_masonry_sort_by', 'sort_by', 'album_masonry_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('album_masonry_order_by', 'order_by', 'album_masonry_order_by', $use_option_defaults, $params);
		$defaults['show_search_box'] = self::get_option_value('album_masonry_show_search_box', 'show_search_box', 'album_masonry_show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('album_masonry_placeholder', 'placeholder', 'album_masonry_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('album_masonry_search_box_width', 'search_box_width', 'album_masonry_search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('album_masonry_show_sort_images', 'show_sort_images', 'album_masonry_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('album_masonry_show_tag_box', 'show_tag_box', 'album_masonry_show_tag_box', $use_option_defaults, $params);
        $defaults['show_album_name'] = self::get_option_value('show_album_masonry_name', 'show_album_name', 'show_album_masonry_name', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('album_masonry_show_gallery_description', 'show_gallery_description', 'album_masonry_show_gallery_description', $use_option_defaults, $params);
        $defaults['image_title'] = self::get_option_value('album_image_title', 'image_title', 'album_masonry_image_title', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('album_masonry_gallery_download', 'gallery_download', 'album_masonry_gallery_download', $use_option_defaults, $params);
        $defaults['ecommerce_icon'] = self::get_option_value('album_masonry_ecommerce_icon_show_hover', 'ecommerce_icon', 'album_masonry_ecommerce_icon_show_hover', $use_option_defaults, $params);
	  }
	  break;
      case 'album_extended_preview': {
        $defaults['extended_album_height'] = self::get_option_value('extended_album_height', 'extended_album_height', 'extended_album_height', $use_option_defaults, $params);
        $defaults['extended_album_column_number'] = self::get_option_value('extended_album_column_number', 'extended_album_column_number', 'extended_album_column_number', $use_option_defaults, $params);
        $defaults['extended_album_thumb_width'] = self::get_option_value('extended_album_thumb_width', 'extended_album_thumb_width', 'album_extended_thumb_width', $use_option_defaults, $params);
        $defaults['extended_album_thumb_height'] = self::get_option_value('extended_album_thumb_height', 'extended_album_thumb_height', 'album_extended_thumb_height', $use_option_defaults, $params);
        $defaults['extended_album_image_column_number'] = self::get_option_value('extended_album_image_column_number', 'extended_album_image_column_number', 'album_extended_image_column_number', $use_option_defaults, $params);
        $defaults['extended_album_image_thumb_width'] = self::get_option_value('extended_album_image_thumb_width', 'extended_album_image_thumb_width', 'album_extended_image_thumb_width', $use_option_defaults, $params);
        $defaults['extended_album_image_thumb_height'] = self::get_option_value('extended_album_image_thumb_height', 'extended_album_image_thumb_height', 'album_extended_image_thumb_height', $use_option_defaults, $params);
        $defaults['extended_album_enable_page'] = self::get_option_value('extended_album_enable_page', 'extended_album_enable_page', 'album_extended_enable_page', $use_option_defaults, $params);
        $defaults['extended_albums_per_page'] = self::get_option_value('extended_albums_per_page', 'extended_albums_per_page', 'albums_extended_per_page', $use_option_defaults, $params);
        $defaults['extended_album_images_per_page'] = self::get_option_value('extended_album_images_per_page', 'extended_album_images_per_page', 'album_extended_images_per_page', $use_option_defaults, $params);
		$defaults['album_sort_by'] = self::get_option_value('extended_album_sort_by', 'all_album_sort_by', 'extended_album_sort_by', $use_option_defaults, $params);
		$defaults['album_order_by'] = self::get_option_value('extended_album_order_by', 'all_album_order_by', 'extended_album_order_by', $use_option_defaults, $params);
		$defaults['sort_by'] = self::get_option_value('album_extended_sort_by', 'sort_by', 'album_extended_sort_by', $use_option_defaults, $params);
        $defaults['order_by'] = self::get_option_value('album_extended_order_by', 'order_by', 'album_extended_order_by', $use_option_defaults, $params);
		$defaults['show_search_box'] = self::get_option_value('album_extended_show_search_box', 'show_search_box', 'album_extended_show_search_box', $use_option_defaults, $params);
        $defaults['placeholder'] = self::get_option_value('album_extended_placeholder', 'placeholder', 'album_extended_placeholder', $use_option_defaults, $params);
        $defaults['search_box_width'] = self::get_option_value('album_extended_search_box_width', 'search_box_width', 'album_extended_search_box_width', $use_option_defaults, $params);
        $defaults['show_sort_images'] = self::get_option_value('album_extended_show_sort_images', 'show_sort_images', 'album_extended_show_sort_images', $use_option_defaults, $params);
        $defaults['show_tag_box'] = self::get_option_value('album_extended_show_tag_box', 'show_tag_box', 'album_extended_show_tag_box', $use_option_defaults, $params);
        $defaults['show_album_name'] = self::get_option_value('show_album_extended_name', 'show_album_name', 'show_album_extended_name', $use_option_defaults, $params);
        $defaults['show_gallery_description'] = self::get_option_value('album_extended_show_gallery_description', 'show_gallery_description', 'album_extended_show_gallery_description', $use_option_defaults, $params);
		$defaults['extended_album_description_enable'] = self::get_option_value('extended_album_description_enable', 'extended_album_description_enable', 'extended_album_description_enable', $use_option_defaults, $params);
        $defaults['extended_album_view_type'] = self::get_option_value('extended_album_view_type', 'extended_album_view_type', 'album_extended_view_type', $use_option_defaults, $params);
        $defaults['extended_album_image_title'] = self::get_option_value('extended_album_image_title', 'extended_album_image_title', 'album_extended_image_title_show_hover', $use_option_defaults, $params);
        $defaults['extended_album_mosaic_hor_ver'] = self::get_option_value('extended_album_mosaic_hor_ver', 'extended_album_mosaic_hor_ver', 'album_mosaic', $use_option_defaults, $params);
        $defaults['extended_album_resizable_mosaic'] = self::get_option_value('extended_album_resizable_mosaic', 'extended_album_resizable_mosaic', 'album_resizable_mosaic', $use_option_defaults, $params);
        $defaults['extended_album_mosaic_total_width'] = self::get_option_value('extended_album_mosaic_total_width', 'extended_album_mosaic_total_width', 'album_mosaic_total_width', $use_option_defaults, $params);
        $defaults['play_icon'] = self::get_option_value('album_extended_play_icon', 'play_icon', 'album_extended_play_icon', $use_option_defaults, $params);
        $defaults['gallery_download'] = self::get_option_value('album_extended_gallery_download', 'gallery_download', 'album_extended_gallery_download', $use_option_defaults, $params);
        $defaults['ecommerce_icon'] = self::get_option_value('album_extended_ecommerce_icon_show_hover', 'ecommerce_icon', 'album_extended_ecommerce_icon_show_hover', $use_option_defaults, $params);
	  }
	  break;
    }
    return array_merge($params, $defaults);
  }

  /**
   * @param string $name - name in shortcode params
   * @param string $inherit_from - name of param to get value if $name does ot exist
   * @param string $option_name
   * @param $use_option_defaults
   * @param $params
   * @return mixed
   */
  public static function get_option_value($name, $inherit_from, $option_name, $use_option_defaults, $params) {
    if ( !$use_option_defaults ) {
      if ( isset($params[$name]) ) {
        return $params[$name];
      }
      else if ( isset($params[$inherit_from]) ) {
        return $params[$inherit_from];
      }
    }

    return BWG()->options->$option_name;
  }

  /**
   * Get font families.
   *
   * @param bool $font
   *
   * @return array|bool|string
   */
  public static function get_fonts($font = FALSE) {
    $fonts = array(
      'arial' => 'Arial',
      'Lucida grande' => 'Lucida grande',
      'segoe ui' => 'Segoe ui',
      'tahoma' => 'Tahoma',
      'trebuchet ms' => 'Trebuchet ms',
      'verdana' => 'Verdana',
      'cursive' =>'Cursive',
      'fantasy' => 'Fantasy',
      'monospace' => 'Monospace',
      'serif' => 'Serif',
    );

    if ( $font === FALSE ) {
      return $fonts;
    }
    else {
      if ( in_array($font, $fonts ) ) {
        return $font;
      }
      else {
        return 'arial';
      }
    }
  }

  /**
   * No items.
   *
   * @param $title
   * @param $number
   *
   * @return string
   */
  public static function no_items($title, $colspan_count = 0) {
    $title = ($title != '') ? strtolower($title) : 'items';
    ob_start();
    ?><tr class="no-items">
    <td class="colspanchange" <?php echo $colspan_count ? 'colspan="' . $colspan_count . '"' : ''?>><?php echo sprintf(__('No %s found.', BWG()->prefix), $title); ?></td>
    </tr><?php
    return ob_get_clean();
  }

	/**
	* Clean page prefix.
	*
	* @param  string $str
	* @return string $str
	*/
	public static function clean_page_prefix($str = '') {
		$str = str_replace('_' . BWG()->prefix, '', $str);
		$str = ucfirst($str);

		return $str;
	}

  // A callback function to add a custom hidden field to our taxonomy
  public static function bwg_old_tag_edit_form_fields( $tag ) {
    // Check for existing taxonomy meta for the term you're editing
    $t_id = $tag->term_id; // Get the ID of the term you're editing
    $term = get_term($t_id, 'bwg_tag');
    ?>
    <input type="hidden" name="old_tag" value="<?php echo isset($term->slug) ? $term->slug : ''; ?>" />
    <?php
  }

  /**
   * Register custom taxonomies to use in plugin.
   */
	public static function register_custom_taxonomies() {
	  // Register bwg_tag taxonomy.
    self::create_bwg_tag();
    // Add the fields to the bwg_tags taxonomy, using our callback function
    add_action( 'edit_tag_form_fields', array('WDWLibrary', 'bwg_old_tag_edit_form_fields'), 10, 2 );
    // Set Photo Gallery menu as parent for bwg_tag.
    add_action('parent_file', array('WDWLibrary', 'menu_highlight'));
    // Save/update bwg_tag.
    add_action('edited_bwg_tag', array('WDWLibrary', 'update_bwg_tag'), 10, 2);
    add_action('create_bwg_tag', array('WDWLibrary', 'update_bwg_tag'), 10, 2);
    // Delete bwg_tag.
    add_action('delete_bwg_tag', array('WDWLibrary', 'delete_bwg_tag'), 10, 3);

    if ('bwg_tag' == self::get('taxonomy')) {
      //add_action( 'admin_notices', array( 'WDWLibrary', 'topbar' ) );
    }
  }

  public static function create_bwg_tag() {
    register_taxonomy('bwg_tag', null, array(
      'public' => TRUE,
      'show_ui' => TRUE,
      'show_in_nav_menus' => FALSE,
      'show_tagcloud' => TRUE,
      'hierarchical' => FALSE,
      'label' => __('Gallery Tags', BWG()->prefix),
      'query_var' => TRUE,
      'rewrite' => TRUE));
  }

  public static function update_bwg_tag($term_id) {
    $old_tag = self::get('old_tag','');
    // Create custom post (type is tag).
    $term = get_term($term_id, 'bwg_tag');
    $custom_post_params = array(
      'id' => $term_id,
      'title' => $term->name,
      'slug' => $term->slug,
      'type' => array(
        'post_type' => 'tag',
        'mode' => '',
      ),
    );
    $post = get_page_by_path($old_tag, OBJECT, BWG()->prefix . '_tag');
    if (!empty($post)) {
      wp_delete_post($post->ID, TRUE);
    }
    WDWLibrary::bwg_create_custom_post($custom_post_params);
  }

  public static function delete_bwg_tag($term_id, $tt_id, $deleted_term) {
	  global $wpdb;
      $wpdb->query( $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $term_id) );
      WDWLibrary::bwg_remove_custom_post( array( 'slug' => $deleted_term->slug, 'post_type' => 'bwg_tag') );
  }

  public static function menu_highlight( $parent_file ) {
    global $current_screen;
    $taxonomy = $current_screen->taxonomy;
    if ('bwg_tag' == $taxonomy) {
      $parent_file = 'galleries_bwg';
    }
    return $parent_file;
  }

  /**
   * Sort array by array.
   *
   * @param array $array
   * @param array $orderArray
   *
   * @return array
   */
  public static function sortArrayByArray( array $array, array $orderArray ) {
    $ordered = array();
    foreach ( $orderArray as $key ) {
      if ( array_key_exists($key, $array) ) {
        $ordered[$key] = $array[$key];
        unset($array[$key]);
      }
    }

    return $ordered + $array;
  }

	/**
	* Get all addons.
	*
	* @return array $addons
	*/
	public static function get_all_addons_path() {
		$addons = array(
					'photo-gallery-facebook/photo-gallery-facebook.php',
				);
		return $addons;
	}

	/**
   * Deactivate all addons.
   *
   * @return bool $addon
   */
	public static function deactivate_all_addons($additional_plugin = FALSE) {
		include_once( BWG()->abspath . 'wp-admin/includes/plugin.php' );
		$addons = WDWLibrary::get_all_addons_path();
    if ( $additional_plugin ) {
      array_push($addons, $additional_plugin);
    }
		foreach ( $addons as $addon ) {
			if( is_plugin_active( $addon ) ) {
				deactivate_plugins( $addon );
			}
		}
	}

    /**
     * Check external link.
     *
     * @param  string $link
     *
     * @return bool
     */
    public static function check_external_link( $link ) {
      if ( is_string($link) && preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $link) ) {
        return TRUE;
      }

      return FALSE;
    }

    /**
    * Check external link.
    *
    * @param  string $where
    * @return array
    */
    public static function update_image_modified_date( $where = '', $prepareArgs = array() ) {
      if ( strpos($where, 'pr_' ) !== FALSE ) {
        // Newly added image.
        return;
      }
      global $wpdb;

      $time = time();
      $newPrepareArgs = $prepareArgs;
      array_unshift($newPrepareArgs , $time);
      $update = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `modified_date` = "%d" WHERE ' . $where, $newPrepareArgs) );
      $items = $wpdb->get_results( $wpdb->prepare('SELECT `gallery_id`, `thumb_url` FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where, $prepareArgs) );
      if ( !empty($items) ) {
        $thumbs_str = '';
        foreach ( $items as $item ) {
          $thumbs_str = "'" . $item->thumb_url . "',";
        }
        $thumbs_str = rtrim($thumbs_str,',');

        $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_gallery` SET `modified_date` = "' . time() . '" WHERE `preview_image` IN (' . $thumbs_str . ') OR `random_preview_image` IN (' . $thumbs_str . ')', array('%d','%s','%s')));
        $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_album` SET `modified_date` = "' . time() . '" WHERE `preview_image` IN (' . $thumbs_str . ') OR `random_preview_image` IN (' . $thumbs_str . ')', array('%d','%s','%s')));
      }

      return array('status' => $update, 'modified_date' => $time );
    }

    /**
     * Get description and title from gallery or album tables.
     *
     * @param string $type
     * @param int $id
     *
     * @return string
     */
    public static function get_album_gallery_title_description( $type, $id ) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_'.$type.' WHERE id="%d"', $id));
        if($row) {
            return $row;
        }
        return '';
    }

    /**
     * Adds date modified to image url to avoid caching.
     *
     * @param $url
     * @param $date_modified
     *
     * @return string
     */
    public static function image_url_version($url, $date_modified) {
      if ( !empty($url) && $date_modified && !WDWLibrary::check_external_link($url) && BWG()->options->enable_date_parameter) {
        $key = '?bwg=';
        if ( strpos($url, $key) > 0 ) {
          $url_tmp = explode($key, $url);
          $url = $url_tmp[0];
        }

        return $url . '?bwg=' . $date_modified;
      }

      return $url;
    }

  /**
   * Get thumb dimensions.
   *
   * @param string $thumb_url
   *
   * @return string
   */
  public static function get_thumb_size( $thumb_url ) {
    $resolution_thumb = '';
    if ( file_exists( BWG()->upload_dir . $thumb_url ) ) {
      $thumb_image = wp_get_image_editor( BWG()->upload_dir . $thumb_url );
      if ( !is_wp_error( $thumb_image ) ) {
        $get_thumb_size = $thumb_image->get_size();
        $resolution_thumb = $get_thumb_size["width"] . "x" . $get_thumb_size["height"];
      }
    }
    return $resolution_thumb;
  }

  public static function bwg_session_start() {
    if (session_id() == '' || (function_exists('session_status') && (session_status() == PHP_SESSION_NONE))) {
      @session_start();
    }
  }

  /**
   * Images bulk actions.
   *
   * @return array
   */
  public static function image_actions( $gallery_type = '' ) {
    if ( $gallery_type == 'google_photos'
      || $gallery_type == 'instagram' ) {
      $image_actions = array();
    }
    else {
      $image_actions = array(
        'image_resize' => array(
          'title' => __('Resize', BWG()->prefix),
          'bulk_action' => __('resized', BWG()->prefix),
          'disabled' => (BWG()->wp_editor_exists ? '' : 'disabled="disabled"'),
        ),
        'image_recreate_thumbnail' => array(
          'title' => __('Recreate thumbnail', BWG()->prefix),
          'bulk_action' => __('recreated', BWG()->prefix),
          'disabled' => (BWG()->wp_editor_exists ? '' : 'disabled="disabled"'),
        ),
        'image_rotate_left' => array(
          'title' => __('Rotate left', BWG()->prefix),
          'bulk_action' => __('rotated left', BWG()->prefix),
          'disabled' => (BWG()->wp_editor_exists ? '' : 'disabled="disabled"'),
        ),
        'image_rotate_right' => array(
          'title' => __('Rotate right', BWG()->prefix),
          'bulk_action' => __('rotated right', BWG()->prefix),
          'disabled' => (BWG()->wp_editor_exists ? '' : 'disabled="disabled"'),
        ),
        'image_set_watermark' => array(
          'title' => __('Set watermark', BWG()->prefix),
          'bulk_action' => __('edited', BWG()->prefix),
          'disabled' => (BWG()->wp_editor_exists ? '' : 'disabled="disabled"'),
        ),
        'image_reset' => array(
          'title' => __('Reset', BWG()->prefix),
          'bulk_action' => __('reset', BWG()->prefix),
          'disabled' => '',
        ),
      );
    }
    $image_actions += array(
      'image_edit_alt' => array(
        'title' => __('Edit Alt/Title', BWG()->prefix),
        'bulk_action' => __('edited', BWG()->prefix),
        'disabled' => '',
      ),
      'image_edit_description' => array(
        'title' => __('Edit description', BWG()->prefix),
        'bulk_action' => __('edited', BWG()->prefix),
        'disabled' => '',
      ),
      'image_edit_redirect' => array(
        'title' => __('Edit redirect URL', BWG()->prefix),
        'bulk_action' => __('edited', BWG()->prefix),
        'disabled' => '',
      ),
      'image_add_tag' => array(
        'title' => __('Add/Remove tag', BWG()->prefix),
        'bulk_action' => __('edited', BWG()->prefix),
        'disabled' => '',
      ),
      'image_publish' => array(
        'title' => __('Publish', BWG()->prefix),
        'bulk_action' => __('published', BWG()->prefix),
        'disabled' => '',
      ),
      'image_unpublish' => array(
        'title' => __('Unpublish', BWG()->prefix),
        'bulk_action' => __('unpublished', BWG()->prefix),
        'disabled' => '',
      ),
      'image_delete' => array(
        'title' => __('Delete', BWG()->prefix),
        'bulk_action' => __('deleted', BWG()->prefix),
        'disabled' => '',
      ),
    );

    if ( function_exists('BWGEC') ) {
      $image_actions['set_image_pricelist'] = array(
        'title' => __('Add pricelist', BWG()->prefix),
        'bulk_action' => __('edited', BWG()->prefix),
        'disabled' => '',
      );
      $image_actions['remove_pricelist_all'] = array(
        'title' => __('Remove pricelist', BWG()->prefix),
        'bulk_action' => __('edited', BWG()->prefix),
        'disabled' => '',
      );
    }

    return $image_actions;
  }

  public static function allowed_upload_types( $type = '' ) {
    if ( $type ) {
      switch ( $type ) {
        case 'jpg':
        case 'jpeg':
        case 'gif':
        case 'png':
        case 'svg':
          return TRUE;
          break;
      }
    }

    return FALSE;
  }

  /**
   * Generate top bar.
   *
   * @return string Top bar html.
   */
  public static function topbar() {
    $page = self::get('page');
    $taxonomy = self::get('taxonomy');
    $user_guide_link = 'https://help.10web.io/hc/en-us/articles/';
    $show_content = true;
    $show_guide_link = true;
    $show_head = false;
    if ('bwg_tag' == $taxonomy) {
      $user_guide_link .= '360016080271-Creating-and-Applying-Image-Tags';
    }
    else {
      switch ( $page ) {
        case 'galleries_bwg':
          {
            $user_guide_link .= '360016079391-Creating-Galleries';
            break;
          }
        case 'albums_bwg':
          {
            $user_guide_link .= '360015860512-Creating-Gallery-Groups';
            break;
          }
        case 'tags_bwg':
          {
            $user_guide_link .= '360016080271-Creating-and-Applying-Image-Tags';
            break;
          }
        case 'options_bwg':
          {
            $user_guide_link .= '360015860912-Configuring-Photo-Gallery-Options';
            break;
          }
        case 'themes_bwg':
          {
            $user_guide_link .= '360016082231-Editing-Photo-Gallery-Themes';
            break;
          }
        case 'comments_bwg':
          {
            $user_guide_link .= '360016082451-Managing-Image-Comments-and-Ratings';
            break;
          }
        case 'ratings_bwg':
          {
            $user_guide_link .= '360016082451-Managing-Image-Comments-and-Ratings';
            break;
          }
        case 'licensing_bwg':
          {
            $user_guide_link .= '360016079391-Creating-Galleries';
            break;
          }
        default:
          {
            return '';
            break;
          }
      }
    }
    $user_guide_link .= BWG()->utm_source;
    $show_content = $show_content && !BWG()->is_pro;
    $support_forum_link = 'https://wordpress.org/support/plugin/photo-gallery/#new-post';
    $premium_link = BWG()->plugin_link . BWG()->utm_source;
    wp_enqueue_style(BWG()->prefix . '-roboto');
    wp_enqueue_style(BWG()->prefix . '-pricing');
    ob_start();
    ?>
    <div class="wrap">
      <h1 class="bwg-head-notice">&nbsp;</h1>
      <div class="bwg-topbar-container">
        <?php
        if ($show_content) {
          ?>
          <div class="bwg-topbar bwg-topbar-content">
            <div class="bwg-topbar-content-container">
              <div class="bwg-topbar-content-title">
                <?php _e('Photo Gallery Premium', BWG()->prefix); ?>
              </div>
              <div class="bwg-topbar-content-body">
                <?php _e('Get more stunning views with fully customizable themes, powerful lightbox and much more.', BWG()->prefix); ?>
              </div>
            </div>
            <div class="bwg-topbar-content-button-container">

            </div>
          </div>
          <?php
        }
        ?>
        <div class="bwg-topbar_cont">
          <div class="bwg-topbar bwg-topbar-links">
            <div class="bwg-topbar-links-container">
              <?php
              if ( $show_guide_link ) {
                ?>
                <a href="<?php echo $user_guide_link; ?>" target="_blank" class="bwg-topbar_user_guid">
                  <div class="bwg-topbar-links-item">
                    <?php _e('User guide', BWG()->prefix); ?>
                  </div>
                </a>
                <?php
              }?>
            </div>
          </div>
          <?php
          if (!BWG()->is_pro) {
            ?>
            <div class="bwg-topbar bwg-topbar-links bwg-topbar_support_forum">
              <div class="bwg-topbar-links-container">
                <a href="<?php echo $support_forum_link; ?>" target="_blank" class="bwg-topbar_support_forum">
                  <div class="bwg-topbar-links-item">
                    <img src="<?php echo  BWG()->plugin_url . '/css/images/help.svg'; ?>" class="help_icon" />
                    <?php _e('Ask a question', BWG()->prefix); ?>
                  </div>
                </a>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    <?php if ( $show_head ) {
      $menus = array(
        'manage' => array(
          'href' => add_query_arg( array('page' => 'manage' . BWG()->menu_postfix ), admin_url('admin.php')),
          'name' => __('Forms', BWG()->prefix)
        ),
        'addons' => array(
          'href' => add_query_arg( array('page' => 'addons' . BWG()->menu_postfix ), admin_url('admin.php')),
          'name' => __('Add-ons', BWG()->prefix)
        ),
        'pricing' => array(
          'href' => add_query_arg( array('page' => 'pricing' . BWG()->menu_postfix ), admin_url('admin.php')),
          'name' => __('Premium Version', BWG()->prefix) .' <span class="bwg-upgrade">' . __('Upgrade', BWG()->prefix) . '</span>'
        ),
      );
      ?>
      <style>#wpbody-content>div:not(.wrap), .wrap .notice:not(#wd_bp_notice_cont) { display: none; }</style>
      <div class="bwg-head">
        <div><img src="<?php echo BWG()->plugin_url . '/images/FormMaker.png'; ?>"></div>
        <ul class="bwg-breadcrumbs">
          <?php
          foreach ( $menus as $key => $item ) {
            if ( BWG()->is_pro && $key == 'pricing' ) {
              continue;
            }
            ?>
            <li class="bwg-breadcrumb-item">
              <a class="bwg-breadcrumb-item-link<?php echo ( $key == $page ) ? ' bwg-breadcrumb-item-link-active' : ''; ?>" href="<?php echo $item['href']; ?>"><?php echo $item['name']; ?></a>
            </li>
            <?php
          }
          ?>
        </ul>
      </div>
    <?php }	?>
    </div>
    <?php
    echo ob_get_clean();
  }

  /**
   * Generate top bar user guide section.
   *
   * @return string top bar user guide section html.
   */
  public static function user_guide_icon() {
    $page = self::get('page');
    $taxonomy = self::get('taxonomy');
    $user_guide_link = 'https://help.10web.io/hc/en-us/articles/';
    $show_content = true;
    $show_guide_link = true;
    if ('bwg_tag' == $taxonomy) {
      $user_guide_link .= '360016080271-Creating-and-Applying-Image-Tags';
    }
    else {
      switch ( $page ) {
        case 'galleries_bwg':
        {
          $user_guide_link .= '360016079391-Creating-Galleries';
          break;
        }
        case 'albums_bwg':
        {
          $user_guide_link .= '360015860512-Creating-Gallery-Groups';
          break;
        }
        case 'tags_bwg':
        {
          $user_guide_link .= '360016080271-Creating-and-Applying-Image-Tags';
          break;
        }
        case 'options_bwg':
        {
          $user_guide_link .= '360015860912-Configuring-Photo-Gallery-Options';
          break;
        }
        case 'themes_bwg':
        {
          $user_guide_link .= '360016082231-Editing-Photo-Gallery-Themes';
          break;
        }
        case 'comments_bwg':
        {
          $user_guide_link .= '360016082451-Managing-Image-Comments-and-Ratings';
          break;
        }
        case 'ratings_bwg':
        {
          $user_guide_link .= '360016082451-Managing-Image-Comments-and-Ratings';
          break;
        }
        case 'licensing_bwg':
        {
          $user_guide_link .= '360016079391-Creating-Galleries';
          break;
        }
        default:
        {
          return '';
          break;
        }
      }
    }
    $user_guide_link .= BWG()->utm_source;
    wp_enqueue_style(BWG()->prefix . '-roboto');
    wp_enqueue_style(BWG()->prefix . '-pricing');
    ob_start();
    if ( $show_guide_link ) {
      ?>
      <a href="<?php echo $user_guide_link; ?>" target="_blank" class="bwg-topbar_user_guid">
        <img class="wd-question-mark" src="<?php echo BWG()->plugin_url . '/images/question_mark.svg';?>">
      </a>
      <?php
    }?>
    <?php
    echo ob_get_clean();
  }

  /**
   * Generate top bar user guide section.
   *
   * @return string top bar user guide section html.
   */
  public static function topbar_upgrade_ask_question() {
    $support_forum_link = 'https://wordpress.org/support/plugin/photo-gallery/#new-post';
    $premium_link = BWG()->plugin_link . BWG()->utm_source;
    wp_enqueue_style(BWG()->prefix . '-roboto');
    wp_enqueue_style(BWG()->prefix . '-pricing');
    ob_start();
    ?>
      <div class="wd-list-view-header-free-right">
        <p class="upgrade-header"><?php _e('Unleash the full benefits and features', BWG()->prefix); ?></p>
        <p class="upgrade-text"><?php _e('of the Premium Plugin', BWG()->prefix); ?></p>
        <a class="upgrade-button" href="<?php echo $premium_link; ?>" target="_blank"><?php _e( 'Upgrade Now', BWG()->prefix ); ?></a>
      </div>
      <a class="wd-list-view-ask-question" href="<?php echo $support_forum_link; ?>" target="_blank"><?php _e('Ask a question', BWG()->prefix); ?></a>
    <?php
    echo ob_get_clean();
  }

  /**
   * Generate ask question static fixed button.
   *
   * @return string ask question html.
   */
  public static function ask_question() {
    $support_forum_link = 'https://wordpress.org/support/plugin/photo-gallery/#new-post';
    ob_start();
    ?>
    <a class="wd-list-view-ask-question" href="<?php echo $support_forum_link; ?>" target="_blank"><?php _e('Ask a question', BWG()->prefix); ?></a>
    <?php
    echo ob_get_clean();
  }

  // TODO. This function should be replaced with WP functionality in another version. At the moment it is not.
  /**
   *  Get privacy_policy_url
   *
   * @return string $url
   */
  public static function get_privacy_policy_url() {
    $permalink = '';
    $post_id = get_option( 'wp_page_for_privacy_policy' );
    if ( $post_id ) {
      $post = get_post( $post_id, OBJECT );
      if ( !empty($post) && $post->post_status == 'publish' ) {
        $permalink = get_permalink( $post_id );
      }
    }
    return $permalink;
  }

  /**
   * Check if is preview of Elementor builder.
   *
   * @return bool
   */
  public static function elementor_is_active() {
    if ( in_array( self::get('action', ''), array('elementor', 'elementor_ajax') ) || self::get('elementor-preview', '') ) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get galleries.
   *
   * @return array
   */
  public static function get_galleries() {
    global $wpdb;
    $query = "SELECT `id`, `name` FROM `" . $wpdb->prefix . "bwg_gallery` WHERE `published`=1 ORDER BY `name`";
    $rows = $wpdb->get_results($query);

    $galleries = array();
    $galleries[0] = __('All images', BWG()->prefix);
    foreach ( $rows as $row ) {
      $galleries[$row->id] = $row->name;
    }

    return $galleries;
  }

  /**
   * Get gallery groups.
   *
   * @return array
   */
  public static function get_gallery_groups() {
    global $wpdb;
    $query = "SELECT `id`, `name` FROM `" . $wpdb->prefix . "bwg_album` WHERE `published`=1 ORDER BY `name`";
    $rows = $wpdb->get_results($query);

    $gallery_groups = array();
    $gallery_groups[0] = __('All galleries', BWG()->prefix);
    foreach ( $rows as $row ) {
      $gallery_groups[$row->id] = $row->name;
    }

    return $gallery_groups;
  }

  /**
   * Get themes.
   *
   * @return array
   */
  public static function get_theme_rows_data() {
    global $wpdb;
    $query = "SELECT `id`, `name` FROM `" . $wpdb->prefix . "bwg_theme` ORDER BY `default_theme` DESC, `name`";
    $rows = $wpdb->get_results($query);

    $themes = array();
    foreach ( $rows as $row ) {
      $themes[$row->id] = $row->name;
    }

    return $themes;
  }

  /**
   * Get default theme id.
   *
   * @return null|string
   */
  public static function get_default_theme() {
    global $wpdb;
    $query = "SELECT `id` FROM `" . $wpdb->prefix . "bwg_theme` WHERE `default_theme`=1";
    $id = $wpdb->get_var($query);

    return $id;
  }

  public static function get_tags() {
    global $wpdb;
    $query ="SELECT * FROM ".$wpdb->prefix."terms as A LEFT JOIN ".$wpdb->prefix ."term_taxonomy as B ON A.term_id = B.term_id WHERE B.taxonomy='bwg_tag'";
    $rows = $wpdb->get_results($query);

    $tags = array();
    $tags[0] = __('All tags', BWG()->prefix);
    foreach ( $rows as $row ) {
      $tags[$row->term_id] = $row->name;
    }

    return $tags;
  }

  public static function unique_number() {
    $use_random_number = ( WDWLibrary::elementor_is_active() ) ? TRUE : FALSE;
    if ($use_random_number) {
      return mt_rand();
    }
    else {
      global $bwg;
      $bwg_unique = $bwg;
      $bwg++;
      return $bwg_unique;
    }
  }

  public static function error_message_ids() {
	  return array( 26, 27 );
  }

  /**
   * Get gallery allowed types.
   * @return array
   */
  public static function get_gallery_allowed_types() {
    $types = array(
        'thumbnails',
        'thumbnails_masonry',
        'thumbnails_mosaic',
        'slideshow',
        'image_browser',
        'blog_style',
        'carousel',
        'album_compact_preview',
        'album_masonry_preview',
        'album_extended_preview'
    );

    return $types;
  }

  /**
   * Refresh access token.
   *
   * @return array|bool|mixed|WP_Error
   */
  public static function refresh_instagram_access_token( $instagram_access_token = '', $row = array() ) {
    $response = wp_remote_get('https://graph.instagram.com/refresh_access_token/?grant_type=ig_refresh_token&access_token=' . $instagram_access_token );
    $response = json_decode(wp_remote_retrieve_body($response), TRUE);
    if ( !empty($response['expires_in']) && !empty($response['access_token']) ) {
      $row->instagram_access_token = $response['access_token'];
      $row->instagram_access_token_start_in = time();;
      $row->instagram_access_token_expires_in = $response['expires_in'];
      update_option('wd_bwg_options', json_encode($row));

      return $response;
    }
    return '';
  }

/*
 * Get instagram embed types.
 */
  public static function get_instagram_types() {
    $types = array('EMBED_OEMBED_INSTAGRAM_IMAGE', 'EMBED_OEMBED_INSTAGRAM_VIDEO', 'EMBED_OEMBED_INSTAGRAM_POST');

    return $types;
  }
}
