<?php 
/**
 * Custom Meta Box Class
 *
 * The Meta Box Class is used by including it in your plugin files and using its methods to 
 * create custom meta boxes for custom post types. It is meant to be very simple and 
 * straightforward. For name spacing purposes, All Types metabox ( meaning you can do anything with it )
 * is used. 
 *
 * This class is derived from Meta Box script by Rilwis<rilwis@gmail.com> version 3.2. which later was forked 
 * by Cory Crowley (email: cory.ivan@gmail.com) The purpose of this class is not to rewrite the script but to 
 * modify and change small things and adding a few field types that i needed to my personal preference. 
 * The original author did a great job in writing this class, so all props goes to him.
 * 
 * @version 3.1.1
 * @copyright 2011 - 2013
 * @author Ohad Raz (email: arunkushwaha87@gmail.com)
 * @link http://en.bainternet.info
 * 
 * @license GNU General Public LIcense v3.0 - license.txt
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package MY Meta Box Class
 */

if ( ! class_exists( 'AT_Meta_Box') ) :

/**
 * All Types Meta Box class.
 *
 * @package All Types Meta Box
 * @since 1.0
 *
 * @todo Nothing.
 */
class AT_Meta_Box {
  
  /**
   * Holds meta box object
   *
   * @var object
   * @access protected
   */
  protected $_meta_box;
  
  /**
   * Holds meta box fields.
   *
   * @var array
   * @access protected
   */
  protected $_prefix;
  
  /**
   * Holds Prefix for meta box fields.
   *
   * @var array
   * @access protected
   */
  protected $_fields;
  
  /**
   * Use local images.
   *
   * @var bool
   * @access protected
   */
  protected $_Local_images;
  
  /**
   * SelfPath to allow themes as well as plugins.
   *
   * @var string
   * @access protected
   * @since 1.6
   */
  protected $SelfPath;
  
  /**
   * $field_types  holds used field types
   * @var array
   * @access public
   * @since 2.9.7
   */
  public $field_types = array();

  /**
   * $inGroup  holds groupping boolean
   * @var boolean
   * @access public
   * @since 2.9.8
   */
  public $inGroup = false;

  /**
   * Constructor
   *
   * @since 1.0
   * @access public
   *
   * @param array $meta_box 
   */
  public function __construct ( $meta_box ) {
    
    // If we are not in admin area exit.
    if ( ! is_admin() )
      return;
    
    //load translation
    add_filter('init', array($this,'load_textdomain'));

    // Assign meta box values to local variables and add it's missed values.
    $this->_meta_box = $meta_box;
    $this->_prefix = (isset($meta_box['prefix'])) ? $meta_box['prefix'] : ''; 
    $this->_fields = $this->_meta_box['fields'];
    $this->_Local_images = (isset($meta_box['local_images'])) ? true : false;
    $this->add_missed_values();
    if (isset($meta_box['use_with_theme']))
      if ($meta_box['use_with_theme'] === true){
        $this->SelfPath = get_stylesheet_directory_uri() . '/meta-box-class';
      }elseif($meta_box['use_with_theme'] === false){
        $this->SelfPath = plugins_url( 'meta-box-class', plugin_basename( dirname( __FILE__ ) ) );
      }else{
        $this->SelfPath = $meta_box['use_with_theme'];
      }
    else{
      $this->SelfPath = plugins_url( 'meta-box-class', plugin_basename( dirname( __FILE__ ) ) );
    }
    
    // Add metaboxes
    add_action( 'add_meta_boxes', array( $this, 'add' ) );
    //add_action( 'wp_insert_post', array( $this, 'save' ) );
    add_action( 'save_post', array( $this, 'save' ) );
    // Load common js, css files
    // Must enqueue for all pages as we need js for the media upload, too.
    add_action( 'admin_print_styles', array( $this, 'load_scripts_styles' ) );
    //limit File type at upload
    add_filter('wp_handle_upload_prefilter', array($this,'Validate_upload_file_type'));

  }
  
  /**
   * Load all Javascript and CSS
   *
   * @since 1.0
   * @access public
   */
  public function load_scripts_styles() {
    
    // Get Plugin Path
    $plugin_path = $this->SelfPath;
    
    
    //only load styles and js when needed
    /* 
     * since 1.8
     */
    global $typenow;
    if (in_array($typenow,$this->_meta_box['pages']) && $this->is_edit_page()){
      // Enqueue Meta Box Style
      wp_enqueue_style( 'at-meta-box', $plugin_path . '/css/meta-box.css' );
      
      // Enqueue Meta Box Scripts
      wp_enqueue_script( 'at-meta-box', $plugin_path . '/js/meta-box.js', array( 'jquery' ), null, true );

      // Make upload feature work event when custom post type doesn't support 'editor'
      if ($this->has_field('image') || $this->has_field('file')){
        wp_enqueue_script( 'media-upload' );
        add_thickbox();
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-sortable' );
      }
      // Check for special fields and add needed actions for them.
      
      //this replaces the ugly check fields methods calls
      foreach (array('upload','color','date','time','code','select') as $type) {
        call_user_func ( array( $this, 'check_field_' . $type ));
      }
    }
    
  }
  
  /**
   * Check the Field select, Add needed Actions
   *
   * @since 2.9.8
   * @access public
   */
  public function check_field_select() {
    
    // Check if the field is an image or file. If not, return.
    if ( ! $this->has_field( 'select' ))
      return;
      $plugin_path = $this->SelfPath;
      // Enqueu JQuery UI, use proper version.
      
      // Enqueu JQuery select2 library, use proper version.
      wp_enqueue_style('at-multiselect-select2-css', $plugin_path . '/js/select2/select2.css', array(), null);
      wp_enqueue_script('at-multiselect-select2-js', $plugin_path . '/js/select2/select2.js', array('jquery'), false, true);
  }

  /**
   * Check the Field Upload, Add needed Actions
   *
   * @since 1.0
   * @access public
   */
  public function check_field_upload() {
    
    // Check if the field is an image or file. If not, return.
    if ( ! $this->has_field( 'image' ) && ! $this->has_field( 'file' ) )
      return;
    
    // Add data encoding type for file uploading.  
    add_action( 'post_edit_form_tag', array( $this, 'add_enctype' ) );
    
  }
  
  /**
   * Add data encoding type for file uploading
   *
   * @since 1.0
   * @access public
   */
  public function add_enctype () {
    printf(' enctype="multipart/form-data" encoding="multipart/form-data" ');
  }
  
  /**
   * Check Field Color
   *
   * @since 1.0
   * @access public
   */
  public function check_field_color() {
    
    if ( $this->has_field( 'color' ) && $this->is_edit_page() ) {
      wp_enqueue_style( 'wp-color-picker' );
      wp_enqueue_script( 'wp-color-picker' );
    }
  }
  
  /**
   * Check Field Date
   *
   * @since 1.0
   * @access public 
   */
  public function check_field_date() {
    
    if ( $this->has_field( 'date' ) && $this->is_edit_page() ) {
      // Enqueu JQuery UI, use proper version.
	  $plugin_path = $this->SelfPath;
      wp_enqueue_style( 'at-jquery-ui-css', $plugin_path .'/js/jquery-ui/jquery-ui.css' );
      wp_enqueue_script( 'jquery-ui');
      wp_enqueue_script( 'jquery-ui-datepicker');
    }
  }
  
  /**
   * Check Field Time
   *
   * @since 1.0
   * @access public
   */
  public function check_field_time() {
    
    if ( $this->has_field( 'time' ) && $this->is_edit_page() ) {
      $plugin_path = $this->SelfPath;
      // Enqueu JQuery UI, use proper version.
      wp_enqueue_style( 'at-jquery-ui-css', $plugin_path .'/js/jquery-ui/jquery-ui.css' );
      wp_enqueue_script( 'jquery-ui');
      wp_enqueue_script( 'at-timepicker', $plugin_path .'/js/jquery-ui/jquery-ui-timepicker-addon.js', array( 'jquery-ui-slider','jquery-ui-datepicker' ),false,true );
    }
  }
  
  /**
   * Check Field code editor
   *
   * @since 2.1
   * @access public
   */
  public function check_field_code() {
    
    if ( $this->has_field( 'code' ) && $this->is_edit_page() ) {
      $plugin_path = $this->SelfPath;
      // Enqueu codemirror js and css
      wp_enqueue_style( 'at-code-css', $plugin_path .'/js/codemirror/codemirror.css',array(),null);
      wp_enqueue_style( 'at-code-css-dark', $plugin_path .'/js/codemirror/solarizedDark.css',array(),null);
      wp_enqueue_style( 'at-code-css-light', $plugin_path .'/js/codemirror/solarizedLight.css',array(),null);
      wp_enqueue_script('at-code-js',$plugin_path .'/js/codemirror/codemirror.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-xml',$plugin_path .'/js/codemirror/xml.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-javascript',$plugin_path .'/js/codemirror/javascript.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-css',$plugin_path .'/js/codemirror/css.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-clike',$plugin_path .'/js/codemirror/clike.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-php',$plugin_path .'/js/codemirror/php.js',array('jquery'),false,true);
      
    }
  }
  
  /**
   * Add Meta Box for multiple post types.
   *
   * @since 1.0
   * @access public
   */
  public function add($postType) {
    if(in_array($postType, $this->_meta_box['pages'])){
      add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( $this, 'show' ),$postType, $this->_meta_box['context'], $this->_meta_box['priority'] );
    }
  }
  
  /**
   * Callback function to show fields in meta box.
   *
   * @since 1.0
   * @access public 
   */
  public function show() {
    $this->inGroup = false;
    global $post;

    wp_nonce_field( basename(__FILE__), 'at_meta_box_nonce' );
    echo '<table class="form-table">';
    foreach ( $this->_fields as $field ) {
      $field['multiple'] = isset($field['multiple']) ? $field['multiple'] : false;
      $meta = get_post_meta( $post->ID, $field['id'], !$field['multiple'] );
      $meta = ( $meta !== '' ) ? $meta : @$field['std'];

      if (!in_array($field['type'], array('image', 'repeater','file')))
        $meta = is_array( $meta ) ? array_map( 'esc_attr', $meta ) : esc_attr( $meta );
      
      if ($this->inGroup !== true)
        echo '<tr>';

      if (isset($field['group']) && $field['group'] == 'start'){
        $this->inGroup = true;
        echo '<td><table class="form-table"><tr>';
      }
      
      // Call Separated methods for displaying each type of field.
      call_user_func ( array( $this, 'show_field_' . $field['type'] ), $field, $meta );

      if ($this->inGroup === true){
        if(isset($field['group']) && $field['group'] == 'end'){
          echo '</tr></table></td></tr>';
          $this->inGroup = false;
        }
      }else{
        echo '</tr>';
      }
    }
    echo '</table>';
  }
  
  /**
   * Show Repeater Fields.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_repeater( $field, $meta ) {
    global $post;  
    // Get Plugin Path
    $plugin_path = $this->SelfPath;
    $this->show_field_begin( $field, $meta );
    $class = '';
      if ($field['sortable'])  
        $class = " repeater-sortable";
    echo "<div class='at-repeat".$class."' id='{$field['id']}'>";
    
    $c = 0;
    $meta = get_post_meta($post->ID,$field['id'],true);
    
      if (count($meta) > 0 && is_array($meta) ){
         foreach ($meta as $me){
           //for labling toggles
           $mmm =  isset($me[$field['fields'][0]['id']])? $me[$field['fields'][0]['id']]: "";
           if ( in_array( $field['fields'][0]['type'], array('image','file') ) )
            $mmm = $c +1 ;
           echo '<div class="at-repater-block">'.$mmm.'<br/><table class="repeater-table" style="display: none;">';
           if ($field['inline']){
             echo '<tr class="at-inline" VALIGN="top">';
           }
        foreach ($field['fields'] as $f){
          //reset var $id for repeater
          $id = '';
          $id = $field['id'].'['.$c.']['.$f['id'].']';
          $m = isset($me[$f['id']]) ? $me[$f['id']]: '';
          $m = ( $m !== '' ) ? $m : $f['std'];
          if ('image' != $f['type'] && $f['type'] != 'repeater')
            $m = is_array( $m) ? array_map( 'esc_attr', $m ) : esc_attr( $m);
          //set new id for field in array format
          $f['id'] = $id;
          if (!$field['inline']){
            echo '<tr>';
          } 
          call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, $m);
          if (!$field['inline']){
            echo '</tr>';
          } 
        }
        if ($field['inline']){  
          echo '</tr>';
        }
        echo '</table>';
        if ($field['sortable'])
          echo '<span class="re-control"><img src="'.$plugin_path.'/images/move.png" alt="sort" title="sort" class="at_re_sort_handle" /></span>';

        echo'
        <span class="re-control at-re-toggle"><img src="'.$plugin_path.'/images/edit.png" alt="Edit" title="Edit"/></span> 
        <span class="re-control"><img src="'.$plugin_path.'/images/remove.png" alt="'.__('Remove','mmb').'" title="'.__('Remove','mmb').'" id="remove-'.$field['id'].'"></span>
        <span class="re-control-clear"></span></div>';
        $c = $c + 1;
        }
      }

    echo '<img src="';
    if ($this->_Local_images){
      echo $plugin_path.'/images/add.png';
    }else{
      echo 'http://i.imgur.com/w5Tuc.png';
    }
    echo '" alt="'.__('Add','mmb').'" title="'.__('Add','mmb').'" id="add-'.$field['id'].'"><br/></div>';
    
    //create all fields once more for js function and catch with object buffer
    ob_start();
    echo '<div class="at-repater-block"><table class="repeater-table">';
    if ($field['inline']){
      echo '<tr class="at-inline" VALIGN="top">';
    } 
    foreach ($field['fields'] as $f){
      //reset var $id for repeater
      $id = '';
      $id = $field['id'].'[CurrentCounter]['.$f['id'].']';
      $f['id'] = $id; 
      if (!$field['inline']){
        echo '<tr>';
      }
      if ($f['type'] != 'wysiwyg')
        call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, '');
      else
        call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, '',true);
      if (!$field['inline']){
        echo '</tr>';
      }  
    }
    if ($field['inline']){
      echo '</tr>';
    } 
    echo '</table><img src="'.$plugin_path.'/images/remove.png" alt="'.__('Remove','mmb').'" title="'.__('Remove','mmb').'" id="remove-'.$field['id'].'"></div>';
    $counter = 'countadd_'.$field['id'];
    $js_code = ob_get_clean ();
    $js_code = str_replace("\n","",$js_code);
    $js_code = str_replace("\r","",$js_code);
    $js_code = str_replace("'","\"",$js_code);
    $js_code = str_replace("CurrentCounter","' + ".$counter." + '",$js_code);
    echo '<script>
        jQuery(document).ready(function() {
          var '.$counter.' = '.$c.';
          jQuery("#add-'.$field['id'].'").live(\'click\', function() {
            '.$counter.' = '.$counter.' + 1;
            jQuery(this).before(\''.$js_code.'\');            
            update_repeater_fields();
          });
              jQuery("#remove-'.$field['id'].'").live(\'click\', function() {
                  if (jQuery(this).parent().hasClass("re-control"))
                    jQuery(this).parent().parent().remove();
                  else
                    jQuery(this).parent().remove();
              });
          });
        </script>';
    echo '<br/><style>
.at_re_sort_highlight{min-height: 55px; background-color: #EEEEEE; margin: 2px;}
.re-control-clear{clear: both; display: block;}
.at_re_sort_handle{cursor: move;}
.re-control{float: right; padding: 5px;}    
.at-inline{line-height: 1 !important;}
.at-inline .at-field{border: 0px !important;}
.at-inline .at-label{margin: 0 0 1px !important;}
.at-inline .at-text{width: 70px;}
.at-inline .at-textarea{width: 100px; height: 75px;}
.at-repater-block{background-color: #FFFFFF;border: 1px solid;margin: 2px; min-height: 50px}
</style>';
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Begin Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_begin( $field, $meta) {
    echo "<td class='at-field'".(($this->inGroup === true)? " valign='top'": "").">";
    if ( $field['name'] != '' || $field['name'] != FALSE ) {
      echo "<div class='at-label'>";
        echo "<label for='{$field['id']}'>{$field['name']}</label>";
      echo "</div>";
    }
  }
  
  /**
   * End Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_end( $field, $meta=NULL ,$group = false) {
    //print description
    if ( isset($field['desc']) && $field['desc'] != '' )
      echo "<div class='desc-field'>{$field['desc']}</div>";
    echo "</td>";
  }
  
  /**
   * Show Field Text.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_text( $field, $meta) {  
    $this->show_field_begin( $field, $meta );
    echo "<input type='text' class='at-text".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='30' ".( isset($field['style'])? "style='{$field['style']}'" : '' )."/>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field number.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_number( $field, $meta) {  
    $this->show_field_begin( $field, $meta );
    $step = (isset($field['step']) || $field['step'] != '1')? "step='".$field['step']."' ": '';
    $min = isset($field['min'])? "min='".$field['min']."' ": '';
    $max = isset($field['max'])? "max='".$field['max']."' ": '';
    echo "<input type='number' class='at-number".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='30' ".$step.$min.$max.( isset($field['style'])? "style='{$field['style']}'" : '' )."/>";
    $this->show_field_end( $field, $meta );
  }

  /**
   * Show Field code editor.
   *
   * @param string $field 
   * @author Ohad Raz
   * @param string $meta 
   * @since 2.1
   * @access public
   */
  public function show_field_code( $field, $meta) {
    $this->show_field_begin( $field, $meta );
    echo "<textarea class='code_text".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' data-lang='{$field['syntax']}' ".( isset($field['style'])? "style='{$field['style']}'" : '' )." data-theme='{$field['theme']}'>{$meta}</textarea>";
    $this->show_field_end( $field, $meta );
  }
  
  
  /**
   * Show Field hidden.
   *
   * @param string $field 
   * @param string|mixed $meta 
   * @since 0.1.3
   * @access public
   */
  public function show_field_hidden( $field, $meta) {  
    //$this->show_field_begin( $field, $meta );
    echo "<input type='hidden' ".( isset($field['style'])? "style='{$field['style']}' " : '' )."class='at-text".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' value='{$meta}'/>";
    //$this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field Paragraph.
   *
   * @param string $field 
   * @since 0.1.3
   * @access public
   */
  public function show_field_paragraph( $field) {  
    //$this->show_field_begin( $field, $meta );
    echo '<p>'.$field['value'].'</p>';
    //$this->show_field_end( $field, $meta );
  }
    
  /**
   * Show Field Textarea.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_textarea( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<textarea class='at-textarea large-text".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' ".( isset($field['style'])? "style='{$field['style']}' " : '' )." cols='60' rows='10'>{$meta}</textarea>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field Select.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_select( $field, $meta ) {
    
    if ( ! is_array( $meta ) ) 
      $meta = (array) $meta;
      
    $this->show_field_begin( $field, $meta );
      echo "<select ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-select".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}" . ( $field['multiple'] ? "[]' id='{$field['id']}' multiple='multiple'" : "'" ) . ">";
      foreach ( $field['options'] as $key => $value ) {
        echo "<option value='{$key}'" . selected( in_array( $key, $meta ), true, false ) . ">{$value}</option>";
      }
      echo "</select>";
    $this->show_field_end( $field, $meta );
    
  }
  
  /**
   * Show Radio Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_radio( $field, $meta ) {
    
    if ( ! is_array( $meta ) )
      $meta = (array) $meta;
      
    $this->show_field_begin( $field, $meta );
      foreach ( $field['options'] as $key => $value ) {
        echo "<input type='radio' ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-radio".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> <span class='at-radio-label'>{$value}</span>";
      }
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Checkbox Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_checkbox( $field, $meta ) {
    

    $this->show_field_begin($field, $meta);
    echo "<input type='checkbox' ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='rw-checkbox".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}'" . checked(!empty($meta), true, false) . " />";
    $this->show_field_end( $field, $meta );
      
  }
  
  /**
   * Show Wysiwig Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_wysiwyg( $field, $meta,$in_repeater = false ) {
    $this->show_field_begin( $field, $meta );
    
    if ( $in_repeater )
      echo "<textarea class='at-wysiwyg theEditor large-text".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
    else{
      // Use new wp_editor() since WP 3.3
      $settings = ( isset($field['settings']) && is_array($field['settings'])? $field['settings']: array() );
      $settings['editor_class'] = 'at-wysiwyg'.( isset($field['class'])? ' ' . $field['class'] : '' );
      $id = str_replace( "_","",$this->stripNumeric( strtolower( $field['id']) ) );
      wp_editor( html_entity_decode($meta), $id, $settings);
    }
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show File Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_file( $field, $meta ) {
    wp_enqueue_media();
    $this->show_field_begin( $field, $meta );

    $std      = isset($field['std'])? $field['std'] : array('id' => '', 'url' => '');
    $multiple = isset($field['multiple'])? $field['multiple'] : false;
    $multiple = ($multiple)? "multiFile '" : "";
    $name     = esc_attr( $field['id'] );
    $value    = isset($meta['id']) ? $meta : $std;
    $has_file = (empty($value['url']))? false : true;
    $type     = isset($field['mime_type'])? $field['mime_type'] : '';
    $ext      = isset($field['ext'])? $field['ext'] : '';
    $type     = (is_array($type)? implode("|",$type) : $type);
    $ext      = (is_array($ext)? implode("|",$ext) : $ext);
    $id       = $field['id'];
    $li       = ($has_file)? "<li><a href='{$value['url']}' target='_blank'>{$value['url']}</a></li>": "";

    echo "<span class='simplePanelfilePreview'><ul>{$li}</ul></span>";
    echo "<input type='hidden' name='{$name}[id]' value='{$value['id']}'/>";
    echo "<input type='hidden' name='{$name}[url]' value='{$value['url']}'/>";
    if ($has_file)
      echo "<input type='button' class='{$multiple} button simplePanelfileUploadclear' id='{$id}' value='Remove File' data-mime_type='{$type}' data-ext='{$ext}'/>";
    else
      echo "<input type='button' class='{$multiple} button simplePanelfileUpload' id='{$id}' value='Upload File' data-mime_type='{$type}' data-ext='{$ext}'/>";

    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Image Field.
   *
   * @param array $field 
   * @param array $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_image( $field, $meta ) {
    wp_enqueue_media();
    $this->show_field_begin( $field, $meta );
        
    $std          = isset($field['std'])? $field['std'] : array('id' => '', 'url' => '');
    $name         = esc_attr( $field['id'] );
    $value        = isset($meta['id']) ? $meta : $std;
    
    $value['url'] = isset($meta['src'])? $meta['src'] : $value['url']; //backwords capability
    $has_image    = empty($value['url'])? false : true;
    $w            = isset($field['width'])? $field['width'] : 'auto';
    $h            = isset($field['height'])? $field['height'] : 'auto';
    $PreviewStyle = "style='width: $w; height: $h;". ( (!$has_image)? "display: none;'": "'");
    $id           = $field['id'];
    $multiple     = isset($field['multiple'])? $field['multiple'] : false;
    $multiple     = ($multiple)? "multiFile " : "";

    echo "<span class='simplePanelImagePreview'><img {$PreviewStyle} src='{$value['url']}'><br/></span>";
    echo "<input type='hidden' name='{$name}[id]' value='{$value['id']}'/>";
    echo "<input type='hidden' name='{$name}[url]' value='{$value['url']}'/>";
    if ($has_image)
      echo "<input class='{$multiple} button  simplePanelimageUploadclear' id='{$id}' value='Remove Image' type='button'/>";
    else
      echo "<input class='{$multiple} button simplePanelimageUpload' id='{$id}' value='Upload Image' type='button'/>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Color Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_color( $field, $meta ) {
    
    if ( empty( $meta ) ) 
      $meta = '#';
      
    $this->show_field_begin( $field, $meta );
    if( wp_style_is( 'wp-color-picker', 'registered' ) ) { //iris color picker since 3.5
      echo "<input class='at-color-iris".(isset($field['class'])? " {$field['class']}": "")."' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";  
    }else{
      echo "<input class='at-color".(isset($field['class'])? " {$field['class']}": "")."' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
      echo "<input type='button' class='at-color-select button' rel='{$field['id']}' value='" . __( 'Select a color' ,'apc') . "'/>";
      echo "<div style='display:none' class='at-color-picker' rel='{$field['id']}'></div>";
    }
    $this->show_field_end($field, $meta);
    
  }

  /**
   * Show Checkbox List Field
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_checkbox_list( $field, $meta ) {
    
    if ( ! is_array( $meta ) ) 
      $meta = (array) $meta;
      
    $this->show_field_begin($field, $meta);
    
      $html = array();
    
      foreach ($field['options'] as $key => $value) {
        $html[] = "<input type='checkbox' ".( isset($field['style'])? "style='{$field['style']}' " : '' )."  class='at-checkbox_list".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}[]' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> {$value}";
      }
    
      echo implode( '<br />' , $html );
      
    $this->show_field_end($field, $meta);
    
  }
  
  /**
   * Show Date Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public
   */
  public function show_field_date( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<input type='text'  ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-date".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show time field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_time( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      $ampm = ($field['ampm'])? 'true' : 'false';
      echo "<input type='text'  ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-time".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}' id='{$field['id']}' data-ampm='{$ampm}' rel='{$field['format']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
   /**
   * Show Posts field.
   * used creating a posts/pages/custom types checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   */
  public function show_field_posts($field, $meta) {
    global $post;
    
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    $posts = get_posts($options['args']);
    // checkbox_list
    if ('checkbox_list' == $options['type']) {
      foreach ($posts as $p) {
        echo "<input type='checkbox' ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-posts-checkbox".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}[]' value='$p->ID'" . checked(in_array($p->ID, $meta), true, false) . " /> $p->post_title<br/>";
      }
    }
    // select
    else {
      echo "<select ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-posts-select".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
      if (isset($field['emptylabel']))
        echo '<option value="-1">'.(isset($field['emptylabel'])? $field['emptylabel']: __('Select ...','mmb')).'</option>';
      foreach ($posts as $p) {
        echo "<option value='$p->ID'" . selected(in_array($p->ID, $meta), true, false) . ">$p->post_title</option>";
      }
      echo "</select>";
    }
    
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Show Taxonomy field.
   * used creating a category/tags/custom taxonomy checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 1.0
   * @access public 
   * 
   * @uses get_terms()
   */
  public function show_field_taxonomy($field, $meta) {
    global $post;
    
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    $terms = get_terms($options['taxonomy'], $options['args']);
    
    // checkbox_list
    if ('checkbox_list' == $options['type']) {
      foreach ($terms as $term) {
        echo "<input type='checkbox' ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-tax-checkbox".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}[]' value='$term->slug'" . checked(in_array($term->slug, $meta), true, false) . " /> $term->name<br/>";
      }
    }
    // select
    else {
      echo "<select ".( isset($field['style'])? "style='{$field['style']}' " : '' )." class='at-tax-select".( isset($field['class'])? ' ' . $field['class'] : '' )."' name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
      foreach ($terms as $term) {
        echo "<option value='$term->slug'" . selected(in_array($term->slug, $meta), true, false) . ">$term->name</option>";
      }
      echo "</select>";
    }
    
    $this->show_field_end($field, $meta);
  }

  /**
   * Show conditinal Checkbox Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 2.9.9
   * @access public
   */
  public function show_field_cond( $field, $meta ) {
  
    $this->show_field_begin($field, $meta);
    $checked = false;
    if (is_array($meta) && isset($meta['enabled']) && $meta['enabled'] == 'on'){
      $checked = true;
    }
    echo "<input type='checkbox' class='conditinal_control' name='{$field['id']}[enabled]' id='{$field['id']}'" . checked($checked, true, false) . " />";
    //start showing the fields
    $display = ($checked)? '' :  ' style="display: none;"';
    
    echo '<div class="conditinal_container"'.$display.'><table>';
    foreach ((array)$field['fields'] as $f){
      //reset var $id for cond
      $id = '';
      $id = $field['id'].'['.$f['id'].']';
      $m = '';
      $m = (isset($meta[$f['id']])) ? $meta[$f['id']]: '';
      $m = ( $m !== '' ) ? $m : (isset($f['std'])? $f['std'] : '');
      if ('image' != $f['type'] && $f['type'] != 'repeater')
        $m = is_array( $m) ? array_map( 'esc_attr', $m ) : esc_attr( $m);
        //set new id for field in array format
        $f['id'] = $id;
        echo '<tr>';
        call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, $m);
        echo '</tr>';
    }
    echo '</table></div>';
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Save Data from Metabox
   *
   * @param string $post_id 
   * @since 1.0
   * @access public 
   */
  public function save( $post_id ) {

    global $post_type;
    
    $post_type_object = get_post_type_object( $post_type );

    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                      // Check Autosave
    || ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )        // Check Revision
    || ( ! in_array( $post_type, $this->_meta_box['pages'] ) )                  // Check if current post type is supported.
    || ( ! check_admin_referer( basename( __FILE__ ), 'at_meta_box_nonce') )    // Check nonce - Security
    || ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) )  // Check permission
    {
      return $post_id;
    }
    
    foreach ( $this->_fields as $field ) {
      
      $name = $field['id'];
      $type = $field['type'];
      $old = get_post_meta( $post_id, $name, ! $field['multiple'] );
      $new = ( isset( $_POST[$name] ) ) ? $_POST[$name] : ( ( $field['multiple'] ) ? array() : '' );
            

      // Validate meta value
      if ( class_exists( 'at_Meta_Box_Validate' ) && method_exists( 'at_Meta_Box_Validate', $field['validate_func'] ) ) {
        $new = call_user_func( array( 'at_Meta_Box_Validate', $field['validate_func'] ), $new );
      }
      
      //skip on Paragraph field
      if ($type != "paragraph"){

        // Call defined method to save meta value, if there's no methods, call common one.
        $save_func = 'save_field_' . $type;
        if ( method_exists( $this, $save_func ) ) {
          call_user_func( array( $this, 'save_field_' . $type ), $post_id, $field, $old, $new );
        } else {
          $this->save_field( $post_id, $field, $old, $new );
        }
      }
      
    } // End foreach
  }
  
  /**
   * Common function for saving fields.
   *
   * @param string $post_id 
   * @param string $field 
   * @param string $old 
   * @param string|mixed $new 
   * @since 1.0
   * @access public
   */
  public function save_field( $post_id, $field, $old, $new ) {
    $name = $field['id'];
    delete_post_meta( $post_id, $name );
    if ( $new === '' || $new === array() ) 
      return;
    if ( $field['multiple'] ) {
      foreach ( $new as $add_new ) {
        add_post_meta( $post_id, $name, $add_new, false );
      }
    } else {
      update_post_meta( $post_id, $name, $new );
    }
  }  
  
  /**
   * function for saving image field.
   *
   * @param string $post_id 
   * @param string $field 
   * @param string $old 
   * @param string|mixed $new 
   * @since 1.7
   * @access public
   */
  public function save_field_image( $post_id, $field, $old, $new ) {
    $name = $field['id'];
    delete_post_meta( $post_id, $name );
    if ( $new === '' || $new === array() || $new['id'] == '' || $new['url'] == '')
      return;
    
    update_post_meta( $post_id, $name, $new );
  }
  
  /*
   * Save Wysiwyg Field.
   *
   * @param string $post_id 
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 1.0
   * @access public 
   */
  public function save_field_wysiwyg( $post_id, $field, $old, $new ) {
    $id = str_replace( "_","",$this->stripNumeric( strtolower( $field['id']) ) );
    $new = ( isset( $_POST[$id] ) ) ? $_POST[$id] : ( ( $field['multiple'] ) ? array() : '' );
    $this->save_field( $post_id, $field, $old, $new );
  }
  
  /**
   * Save repeater Fields.
   *
   * @param string $post_id 
   * @param string $field 
   * @param string|mixed $old 
   * @param string|mixed $new 
   * @since 1.0
   * @access public 
   */
  public function save_field_repeater( $post_id, $field, $old, $new ) {
    if (is_array($new) && count($new) > 0){
      foreach ($new as $n){
        foreach ( $field['fields'] as $f ) {
          $type = $f['type'];
          switch($type) {
            case 'wysiwyg':
                $n[$f['id']] = wpautop( $n[$f['id']] ); 
                break;
              default:
                break;
          }
        }
        if(!$this->is_array_empty($n))
          $temp[] = $n;
      }
      if (isset($temp) && count($temp) > 0 && !$this->is_array_empty($temp)){
        update_post_meta($post_id,$field['id'],$temp);
      }else{
        //  remove old meta if exists
        delete_post_meta($post_id,$field['id']);
      }
    }else{
      //  remove old meta if exists
      delete_post_meta($post_id,$field['id']);
    }
  }
  
  /**
   * Save File Field.
   *
   * @param string $post_id 
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 1.0
   * @access public
   */
  public function save_field_file( $post_id, $field, $old, $new ) {
    
    $name = $field['id'];
    delete_post_meta( $post_id, $name );
    if ( $new === '' || $new === array() || $new['id'] == '' || $new['url'] == '')
      return;
    
    update_post_meta( $post_id, $name, $new );
  }
  
  /**
   * Save repeater File Field.
   * @param string $post_id 
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 1.0
   * @access public
   * @deprecated 3.0.7
   */
  public function save_field_file_repeater( $post_id, $field, $old, $new ) {}
  
  /**
   * Add missed values for meta box.
   *
   * @since 1.0
   * @access public
   */
  public function add_missed_values() {
    
    // Default values for meta box
    $this->_meta_box = array_merge( array( 'context' => 'normal', 'priority' => 'high', 'pages' => array( 'post' ) ), (array)$this->_meta_box );

    // Default values for fields
    foreach ( $this->_fields as &$field ) {
      
      $multiple = in_array( $field['type'], array( 'checkbox_list', 'file', 'image' ) );
      $std = $multiple ? array() : '';
      $format = 'date' == $field['type'] ? 'yy-mm-dd' : ( 'time' == $field['type'] ? 'hh:mm' : '' );

      $field = array_merge( array( 'multiple' => $multiple, 'std' => $std, 'desc' => '', 'format' => $format, 'validate_func' => '' ), $field );
    
    } // End foreach
    
  }

  /**
   * Check if field with $type exists.
   *
   * @param string $type 
   * @since 1.0
   * @access public
   */
   public function has_field( $type ) {
    //faster search in single dimention array.
    if (count($this->field_types) > 0){
      return in_array($type, $this->field_types);
    }

    //run once over all fields and store the types in a local array
    $temp = array();
    foreach ($this->_fields as $field) {
      $temp[] = $field['type'];
      if ('repeater' == $field['type']  || 'cond' == $field['type']){
        foreach((array)$field["fields"] as $repeater_field) {
          $temp[] = $repeater_field["type"];  
        }
      }
    }

    //remove duplicates
    $this->field_types = array_unique($temp);
    //call this function one more time now that we have an array of field types
    return $this->has_field($type);
  }

  /**
   * Check if current page is edit page.
   *
   * @since 1.0
   * @access public
   */
  public function is_edit_page() {
    global $pagenow;
    return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
  }
  
  /**
   * Fixes the odd indexing of multiple file uploads.
   *
   * Goes from the format: 
   * $_FILES['field']['key']['index']
   * to
   * The More standard and appropriate:
   * $_FILES['field']['index']['key']
   *
   * @param string $files 
   * @since 1.0
   * @access public
   */
  public function fix_file_array( &$files ) {
    
    $output = array();
    
    foreach ( $files as $key => $list ) {
      foreach ( $list as $index => $value ) {
        $output[$index][$key] = $value;
      }
    }
    
    return $output;
  
  }

  /**
   * Get proper JQuery UI version.
   *
   * Used in order to not conflict with WP Admin Scripts.
   *
   * @since 1.0
   * @access public
   */
  public function get_jqueryui_ver() {
    
    global $wp_version;
    
    if ( version_compare( $wp_version, '3.1', '>=') ) {
      return '1.8.10';
    }
    
    return '1.7.3';
  
  }
  
  /**
   *  Add Field to meta box (generic function)
   *  @author Ohad Raz
   *  @since 1.2
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   */
  public function addField($id,$args){
    $new_field = array('id'=> $id,'std' => '','desc' => '','style' =>'');
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }
  
  /**
   *  Add Text Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addText($id,$args,$repeater=false){
    $new_field = array('type' => 'text','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Text Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Number Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addNumber($id,$args,$repeater=false){
    $new_field = array('type' => 'number','id'=> $id,'std' => '0','desc' => '','style' =>'','name' => 'Number Field','step' => '1','min' => '0');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add code Editor to meta box
   *  @author Ohad Raz
   *  @since 2.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'syntax' =>   // syntax language to use in editor (php,javascript,css,html)
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCode($id,$args,$repeater=false){
    $new_field = array('type' => 'code','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Code Editor Field','syntax' => 'php','theme' => 'defualt');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Hidden Field to meta box
   *  @author Ohad Raz
   *  @since 0.1.3
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addHidden($id,$args,$repeater=false){
    $new_field = array('type' => 'hidden','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Text Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Paragraph to meta box
   *  @author Ohad Raz
   *  @since 0.1.3
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $value  paragraph html
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addParagraph($id,$args,$repeater=false){
    $new_field = array('type' => 'paragraph','id'=> $id,'value' => '');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
    
  /**
   *  Add Checkbox Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCheckbox($id,$args,$repeater=false){
    $new_field = array('type' => 'checkbox','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add CheckboxList Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for select options
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   *  
   *   @return : remember to call: $checkbox_list = get_post_meta(get_the_ID(), 'meta_name', false); 
   *   which means the last param as false to get the values in an array
   */
  public function addCheckboxList($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'checkbox_list','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox List Field','options' =>$options,'multiple' => true,);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Textarea Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addTextarea($id,$args,$repeater=false){
    $new_field = array('type' => 'textarea','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Textarea Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Select Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for select options  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, (array) optional
   *    'multiple' => // select multiple values, optional. Default is false.
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addSelect($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'select','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Select Field','multiple' => false,'options' => $options);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  
  /**
   *  Add Radio Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for radio options
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addRadio($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'radio','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Radio Field','options' => $options);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Date Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'format' => // date format, default yy-mm-dd. Optional. Default "'d MM, yy'"  See more formats here: http://goo.gl/Wcwxn
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addDate($id,$args,$repeater=false){
    $new_field = array('type' => 'date','id'=> $id,'std' => '','desc' => '','format'=>'d MM, yy','name' => 'Date Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Time Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string- field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'format' => // time format, default hh:mm. Optional. See more formats here: http://goo.gl/83woX
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addTime($id,$args,$repeater=false){
    $new_field = array('type' => 'time','id'=> $id,'std' => '','desc' => '','format'=>'hh:mm','name' => 'Time Field', 'ampm' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Color Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addColor($id,$args,$repeater=false){
    $new_field = array('type' => 'color','id'=> $id,'std' => '','desc' => '','name' => 'ColorPicker Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Image Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addImage($id,$args,$repeater=false){
    $new_field = array('type' => 'image','id'=> $id,'desc' => '','name' => 'Image Field','std' => array('id' => '', 'url' => ''),'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add File Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addFile($id,$args,$repeater=false){
    $new_field = array('type' => 'file','id'=> $id,'desc' => '','name' => 'File Field','multiple' => false,'std' => array('id' => '', 'url' => ''));
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add WYSIWYG Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional Default 'width: 300px; height: 400px'
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addWysiwyg($id,$args,$repeater=false){
    $new_field = array('type' => 'wysiwyg','id'=> $id,'std' => '','desc' => '','style' =>'width: 300px; height: 400px','name' => 'WYSIWYG Editor Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Taxonomy Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'taxonomy' =>    // taxonomy name can be category,post_tag or any custom taxonomy default is category
   *    'type' =>  // how to show taxonomy? 'select' (default) or 'checkbox_list'
   *    'args' =>  // arguments to query taxonomy, see http://goo.gl/uAANN default ('hide_empty' => false)  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addTaxonomy($id,$options,$args,$repeater=false){
    $temp = array(
      'args' => array('hide_empty' => 0),
      'tax' => 'category',
      'type' => 'select');
    $options = array_merge($temp,$options);
    $new_field = array('type' => 'taxonomy','id'=> $id,'desc' => '','name' => 'Taxonomy Field','options'=> $options);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add posts Field to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'post_type' =>    // post type name, 'post' (default) 'page' or any custom post type
   *    'type' =>  // how to show posts? 'select' (default) or 'checkbox_list'
   *    'args' =>  // arguments to query posts, see http://goo.gl/is0yK default ('posts_per_page' => -1)  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addPosts($id,$options,$args,$repeater=false){
    $post_type = isset($options['post_type'])? $options['post_type']: (isset($args['post_type']) ? $args['post_type']: 'post');
    $type = isset($options['type'])? $options['type']: 'select';
    $q = array('posts_per_page' => -1, 'post_type' => $post_type);
    if (isset($options['args']) )
      $q = array_merge($q,(array)$options['args']);
    $options = array('post_type' =>$post_type,'type'=>$type,'args'=>$q);
    $new_field = array('type' => 'posts','id'=> $id,'desc' => '','name' => 'Posts Field','options'=> $options,'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add repeater Field Block to meta box
   *  @author Ohad Raz
   *  @since 1.0
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *    'fields' => //fields to repeater  
   */
  public function addRepeaterBlock($id,$args){
    $new_field = array(
      'type'     => 'repeater',
      'id'       => $id,
      'name'     => 'Reapeater Field',
      'fields'   => array(),
      'inline'   => false,
      'sortable' => false
    );
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }

  /**
   *  Add Checkbox conditional Field to Page
   *  @author Ohad Raz
   *  @since 2.9.9
   *  @access public
   *  @param $id string  field id, i.e. the key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'fields' => list of fields to show conditionally.
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCondition($id,$args,$repeater=false){
    $new_field = array(
      'type'   => 'cond',
      'id'     => $id,
      'std'    => '',
      'desc'   => '',
      'style'  =>'',
      'name'   => 'Conditional Field',
      'fields' => array()
    );
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  
  /**
   * Finish Declaration of Meta Box
   * @author Ohad Raz
   * @since 1.0
   * @access public
   */
  public function Finish() {
    $this->add_missed_values();
  }
  
  /**
   * Helper function to check for empty arrays
   * @author Ohad Raz
   * @since 1.5
   * @access public
   * @param $args mixed|array
   */
  public function is_array_empty($array){
    if (!is_array($array))
      return true;
    
    foreach ($array as $a){
      if (is_array($a)){
        foreach ($a as $sub_a){
          if (!empty($sub_a) && $sub_a != '')
            return false;
        }
      }else{
        if (!empty($a) && $a != '')
          return false;
      }
    }
    return true;
  }

  /**
   * Validate_upload_file_type 
   *
   * Checks if the uploaded file is of the expected format
   * 
   * @author Ohad Raz <arunkushwaha87@gmail.com>
   * @since 3.0.7
   * @access public
   * @uses get_allowed_mime_types() to check allowed types
   * @param array $file uploaded file
   * @return array file with error on mismatch
   */
  function Validate_upload_file_type($file) {
    if (isset($_POST['uploadeType']) && !empty($_POST['uploadeType']) && isset($_POST['uploadeType']) && $_POST['uploadeType'] == 'my_meta_box'){
      $allowed = explode("|", $_POST['uploadeType']);
      $ext =  substr(strrchr($file['name'],'.'),1);

      if (!in_array($ext, (array)$allowed)){
        $file['error'] = __("Sorry, you cannot upload this file type for this field.");
        return $file;
      }

      foreach (get_allowed_mime_types() as $key => $value) {
        if (strpos($key, $ext) || $key == $ext)
          return $file;
      }
      $file['error'] = __("Sorry, you cannot upload this file type for this field.");
    }
    return $file;
  }

  /**
   * function to sanitize field id
   * 
   * @author Ohad Raz <arunkushwaha87@gmail.com>
   * @since 3.0.7
   * @access public
   * @param  string $str string to sanitize
   * @return string      sanitized string
   */
  public function idfy($str){
    return str_replace(" ", "_", $str);
    
  }

  /**
   * stripNumeric Strip number form string
   *
   * @author Ohad Raz <arunkushwaha87@gmail.com>
   * @since 3.0.7
   * @access public
   * @param  string $str
   * @return string number less string
   */
  public function stripNumeric($str){
    return trim(str_replace(range(0,9), '', $str) );
  }


  /**
   * load_textdomain 
   * @author Ohad Raz
   * @since 2.9.4
   * @return void
   */
  public function load_textdomain(){
    //In themes/plugins/mu-plugins directory
    //load_textdomain( 'mmb', dirname(__FILE__) . '/lang/' . get_locale() .'.mo' );
  }
} // End Class
endif; // End Check Class Exists