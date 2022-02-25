<?php if(!defined('ABSPATH')) exit; // Exit if accessed directlys

class PerfectSurveyAssets extends PerfectSurveyCore
{

  public function wp_init()
  {
    add_action('admin_enqueue_scripts', array($this, 'init_backend_scripts'));

    add_action('wp_enqueue_scripts', array($this, 'init_frontend_scripts') );

    add_action('admin_head', array($this,'init_js'));

    add_action('wp_head', array($this,'init_js'));

    add_action('add_meta_boxes', array($this, 'render_select_sidebar'));

    add_action('wp_loaded', array($this, 'render_survey_widget_dashboard'));
  }

  /**
  * load plugin assets in frontend
  *
  * @return PerfectSurveyAssets
  */
  public function init_css_frontend()
  {
    prsv_register_css('survey-style', '/resources/frontend/assets/css/survey-front-end.css');
  }


  /**
  * Add javascript to frontend
  */
  public function init_js()
  {
    global $post;
    echo '<script type="text/javascript">var wp_post = '.($post ? json_encode($post) : '{ID : null }').';</script>';
    echo '<script type="text/javascript">var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
  }

  public function init_frontend_scripts()
  {
    prsv_register_css('featherlight', '/resources/frontend/assets/css/featherlight.css');
    prsv_register_css('jquery-ui','/resources/frontend/assets/css/jquery-ui.css');
    prsv_register_css('survey-style-front', '/resources/frontend/assets/css/survey-front-end.css');
    prsv_register_css('survey-style-css', '/resources/backend/assets/css/survey-general.css');
    if ( ! wp_script_is( 'jquery', 'enqueued' ))
    {
      prsv_register_js( 'jquery' );
    }
    prsv_register_js('jquery-ui-datepicker');
    prsv_register_js('survey-sweetalert', '/resources/frontend/assets/js/sweetalert.min.js', true,  array( 'jquery' ), '1.0.0');
    prsv_register_js('featherlight', '/resources/frontend/assets/js/featherlight.js', true,  array( 'jquery' ), '1.7.9');
    prsv_register_js('survey-common', '/resources/frontend/assets/js/survey.common.js', true,  array( 'jquery' ), '1.0.0');
    prsv_register_js('survey-main', '/resources/frontend/assets/js/survey.main.js', true,  array( 'jquery' ), '1.0.0');
  }

  /**
  * load plugin assets
  *
  * @return PerfectSurveyAssets
  */
  public function init_backend_scripts()
  {
    $screen = get_current_screen();

    if ($screen->post_type == PRSV_POST_TYPE)
    {
      wp_enqueue_media();
      add_filter('wp_default_editor', 'prsv_get_default_editor');
      
      // add color picker
      if ( ! wp_script_is( 'wp-color-picker', 'enqueued' ))
      {
        prsv_register_js('wp-color-picker');
        prsv_register_css('wp-color-picker');
      }
      // add sortable jquery ui
      if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ))
      {
        prsv_register_js('jquery-ui-sortable');
      }
      // add datatable
      prsv_register_js('datatable', '/resources/backend/assets/js/datatables.min.js');
      prsv_register_css('datatable-style','/resources/backend/assets/css/datatables.css');
      // add survey dependences
      prsv_register_css('survey-style', '/resources/backend/assets/css/survey-general.css');
      prsv_register_js('survey-common', '/resources/backend/assets/js/survey.common.js');
      prsv_register_js('survey-main', '/resources/backend/assets/js/survey.main.js');
    }

    if ($screen->id == 'post' OR $screen->id == 'page')
    {
      // add survey global dependences for shortcode button
      prsv_register_css('survey-shortcode', '/resources/backend/assets/css/survey.shortcode.css');
      prsv_register_js('survey-shortcode', '/resources/backend/assets/js/survey.shortcode.js');
    }

    return $this;
  }

  /**
  * Render List of shortcode in editor
  *
  * @return List with shortcodes of all surveys
  */
  public function render_select_sidebar() {
    $screens = array('post', 'page');

    foreach ($screens as $screen) {

      add_meta_box(
        'all-survey-select', __('All survey', 'perfect-survey'),

        function() {
          $shortcode = prsv_get_post_type_model()->get_all_surveys(true);
          $haveSurveys = prsv_get_post_type_model()->count_total_surveys(true);
          if ($shortcode) {
            foreach ($shortcode as $s) {
              echo "<div class='survey_row_lister'>";
              echo "<div class='survey_row_cell'>";
              echo "<span class='survey_title_meta_row'>".$s['post_title']."</span>";
              echo "</div>";
              echo "<div class='survey_row_cell'>";
              echo "<input type='text' onfocus='this.select();' readonly='readonly' value='[perfect_survey id=\"".$s['ID']."\"]' class='large-text code'>";
              echo "</div>";
              echo "<div class='survey_row_cell'>";
              echo "<a href='post.php?post=".$s['ID']."&action=edit' target='wp-preview-50' class='button button-primary button-large'>". __('Edit') ."<span class='screen-reader-text'>". __('Edit') ."</span></a>";
              echo "</div>";
              echo "</div>";
            }
          } else {
            echo '<div class="no_survey_inpage_article">';
            echo '<p class="survey-empty-message"> '.__("This page is empty because there are no active surveys", 'perfect-survey').' </p>';
            echo '<a href="post-new.php?post_type=ps" class="button button-primary button-large">'.__("Create a new survey", 'perfect-survey').' </a>';
            echo "</div>";
          }
        } , $screen, 'normal', 'high'
      );
    }
  }


  /*
  * Render Widget in Wordpress dashboard
  *
  * Return widget
  */
  public function render_survey_widget_dashboard() {
    add_action('wp_dashboard_setup', 'survey_dashboard_widgets');
    add_action( 'admin_enqueue_scripts', 'survey_dashboard_widgets_style' );
    function survey_dashboard_widgets_style() {
    	prsv_register_css('survey-widget', '/resources/backend/assets/css/survey.widget.css');
    }
    function survey_dashboard_widgets() {
      global $wp_meta_boxes;
      wp_add_dashboard_widget('survey_summary_widget', __('Analyze Results', 'perfect-survey').': '.__('Perfect Survey', 'perfect-survey') , 'survey_content_widget');
    }
    function survey_content_widget() {
      echo '<div class="rower_survey_widget_general">';
      echo '<div class="rower_survey_widget">';
      echo '<div class="rower_survey_widget_block">'.__('Total Response', 'perfect-survey').'</div>';
      echo '<div class="rower_survey_widget_block_number">'.prsv_get_post_type_model()->count_total_response(true).'</div>';
      echo "</div>";
      echo '<div class="rower_survey_widget">';
      echo '<div class="rower_survey_widget_block">'.__('Total Surveys', 'perfect-survey').'</div>';
      echo '<div class="rower_survey_widget_block_number">'.prsv_get_post_type_model()->count_total_surveys(true).'</div>';
      echo "</div>";
      echo '<div class="rower_survey_widget">';
      echo '<div class="rower_survey_widget_block">'.__('Total Questions', 'perfect-survey').'</div>';
      echo '<div class="rower_survey_widget_block_number">'.prsv_get_post_type_model()->count_total_questions(true).'</div>';
      echo "</div>";
      echo "</div>";
      echo '<div class="footer_survey_widget_general">';
      echo '<a href="edit.php?post_type=ps&page=pswp_stats" class="button">'.__("Statistics", 'perfect-survey').' </a>';
      echo '<a href="post-new.php?post_type=ps" class="button button-primary">'.__("Create a new survey", 'perfect-survey').' </a>';
      echo "</div>";
    }
  }
}
