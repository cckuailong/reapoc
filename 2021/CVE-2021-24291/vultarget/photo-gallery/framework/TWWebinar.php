<?php


class TWPGWebinar {
  private $menu_postfix;
  private $title;
  private $description;
  /**
   * @var 'youtube', 'image'
   */
  private $preview_type;
  private $preview_url;
  private $button_text;
  private $button_link;
  private $dismiss_url;

  /**
   * TWWebinar constructor.
   * @param $args array
   */
  public function __construct( $args = array() ) {
    $this->menu_postfix = isset( $args[ 'menu_postfix' ] ) ? $args[ 'menu_postfix' ] : '';
    $this->title = isset( $args[ 'title' ] ) ? $args[ 'title' ] : '';
    $this->description = isset( $args[ 'description' ] ) ? $args[ 'description' ] : '';
    $this->preview_type = isset( $args[ 'preview_type' ] ) ? $args[ 'preview_type' ] : '';
    $this->preview_url = isset( $args[ 'preview_url' ] ) ? $args[ 'preview_url' ] : '';
    $this->button_text = isset( $args[ 'button_text' ] ) ? $args[ 'button_text' ] : '';
    $this->button_link = isset( $args[ 'button_link' ] ) ? $args[ 'button_link' ] : '';
    $this->dismiss_url = add_query_arg( array( 'action' => 'tenweb_webinar_dismiss' ), admin_url( 'admin-ajax.php' ) );
    add_action( 'wp_ajax_tenweb_webinar_dismiss', array( $this, 'dismiss' ) );
    // Check the page to show banner.
    if ( ( !isset($_GET['page']) || ( preg_match("/^$this->menu_postfix/", esc_html( $_GET['page'] )) === 0 && preg_match("/$this->menu_postfix$/", esc_html( $_GET['page'] )) === 0 ) ) || ( isset($_GET['task']) && !strpos(esc_html($_GET['task']), 'edit') === TRUE && !(strpos(esc_html($_GET['task']), 'display') > -1)) ) {
      return;
    }
    add_action( 'admin_notices', array( $this, 'view' ) );
  }

  public function view() {
    $meta_value = get_option('tenweb_webinar_status');
    if ( $meta_value !== '' && $meta_value !== FALSE ) {
      return;
    }
    ob_start();
    wp_enqueue_style('tw-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800');
    ?>
    <style>
      .tw-webinar-wrap {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        background-color: #ffffff;
        padding: 15px 30px 15px 15px;
        box-shadow: 0px 2px 8px #323A4514;
        border-radius: 15px;
        position: relative;
        font-family: 'Open Sans';
      }
      .tw-webinar-image {
        padding: 5px;
      }
      .tw-webinar-image > iframe,
      .tw-webinar-image > img {
        border-radius: 15px;
      }
      .tw-webinar-body {
        flex: 1;
        padding: 5px;
      }
      .tw-webinar-body .tw-webinar-title {
        font-size: 34px;
        font-weight: 800;
        color: #333B46;
        line-height: 28px;
        letter-spacing: 0.28px;
        padding: 5px;
      }
      .tw-webinar-body .tw-webinar-description {
        font-size: 22px;
        color: #333B46;
        padding: 5px;
        line-height: 28px;
        letter-spacing: 0.18px;
      }
      .tw-webinar-button {
        padding: 5px;
      }
      .tw-webinar-button a {
        background-color: #2160B5;
        color: #ffffff;
        font-size: 20px;
        font-weight: 500;
        border-radius: 30px;
        padding: 15px 85px;
        text-decoration: none;
        letter-spacing: 0.17px;
        line-height: 27px;
      }
      .wd_tenweb_notice_dissmiss.notice-dismiss:hover:before, .wd_tenweb_notice_dissmiss.notice-dismiss:active:before, .wd_tenweb_notice_dissmiss.notice-dismiss:focus:before {
        color: #72777c;
      }
      @media only screen and (max-width: 1440px) {
        .tw-webinar-body .tw-webinar-title {
          font-size: 30px;
          letter-spacing: 0.25px;
        }
        .tw-webinar-body .tw-webinar-description {
          font-size: 18px;
          letter-spacing: 0.15px;
        }
      }
      @media only screen and (max-width: 1024px) {
        .tw-webinar-button {
          display: flex;
          flex: 1 100%;
          justify-content: flex-end;
        }
        .tw-webinar-button a {
          font-size: 18px;
          padding: 15px 60px;
        }
      }
      @media only screen and (max-width: 425px) {
        .tw-webinar-body .tw-webinar-title {
          font-size: 22px;
          text-align: center;
          letter-spacing: 0.18px;
        }
        .tw-webinar-body .tw-webinar-description {
          font-size: 16px;
          text-align: center;
          letter-spacing: 0.13px;
          line-height: 24px;
        }
        .tw-webinar-wrap > *:not(.notice-dismiss) {
          display: flex;
          flex: 1 100%;
          justify-content: center;
          flex-wrap: wrap;
        }
        .tw-webinar-image > iframe,
        .tw-webinar-image > img {
          width: 260px;
          height: 146.25px;
        }
      }
    </style>
    <div class="wrap tw-webinar-wrap">
      <?php
      if ( 'youtube' === $this->preview_type ) {
        ?>
        <div class="tw-webinar-image">
          <iframe width="200" height="112.5" src="https://www.youtube.com/embed/<?php echo $this->preview_url; ?>?controls=0&rel=0&modestbranding=1&autohide=1&showinfo=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <?php
      }
      else {
        ?>
        <div class="tw-webinar-image">
        </div>
        <?php
      }
      ?>
      <div class="tw-webinar-body">
        <div class="tw-webinar-title">
          <?php echo $this->title; ?>
        </div>
        <div class="tw-webinar-description">
          <?php echo $this->description; ?>
        </div>
      </div>
      <div class="tw-webinar-button">
        <a href="<?php echo $this->button_link; ?>" target="_blank"><?php echo $this->button_text; ?></a>
      </div>
      <button type="button" class="wd_tenweb_notice_dissmiss notice-dismiss" onclick="jQuery(this).closest('.tw-webinar-wrap').attr('style', 'display: none !important;'); jQuery.post('<?php echo $this->dismiss_url; ?>');">
    </div>
    <?php
    $view = ob_get_clean();
    echo $view;
  }

  public function dismiss() {
    update_option('tenweb_webinar_status', '1', 'no');
  }
}