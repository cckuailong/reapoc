<?php
/**
 * WordPress plugin from Wow-Company
 *
 * @package     Wow_Plugin
 * @subpackage  Wow-Company/Plugins
 * @author      Dmytro Lobov <i@wpbiker.com>
 * @copyright   2019 Wow-Company
 * @license     GNU P
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_transient( 'wow_items_code' );
$items = false;

if ( $items == false ) {
	$url          = 'https://wow-estore.com/a-plugins/';
	$request_args = array(
		'method'      => 'POST',
		'timeout'     => 20,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(
			'content-type' => 'application/json'
		),
		'body'        => '',
	);
	$request      = wp_remote_post( $url, $request_args );
	$items        = unserialize( $request['body'] );
	set_transient( 'wow_items_code', $items, 604800 );
}
?>
<style>
  .height_screen {
    height: 270px;
    background: #fff;
  }

  .height_screen img {
    max-width: 100%;
  }

  .height_screen span {
    padding: 10px;
    font-size: 16px;
    font-weight: 500;
    display: block;
  }

  .height_screen a {
    color: #000;
    text-decoration: none;
  }

  .themes {
    overflow: hidden;
  }

  .theme-actions {
    background: rgba(244, 244, 244, 1) !important;
  }
  .theme-name {
    text-align: left !important;
  }
  .install {
    float: right;
  }
</style>
<h3>Several plugins below has free and pro versions you can install it and hopefully useful. Enjoy it.</h3>

<div class="theme-browser">
  <div class="themes">
		<?php
		$image = 'https://wow-estore.com/a-plugins/img/';
		foreach ( $items as $key => $value ) { ?>

      <div class="theme">
        <div class="height_screen">
          <a target="_blank" href="<?php echo esc_url( $value[3] ); ?>" target="_blank"><img
              src="<?php echo esc_url( $image . $value[2] ); ?>"/>
            <span><?php echo esc_attr( $value[1] ); ?></span>
          </a>
        </div>
        <div class="theme-author"></div>
        <div class="theme-id-container">
          <h2 class="theme-name">
            <span><?php echo esc_attr( $value[0] ); ?></span>
						<?php if ( ! empty( $value[5] ) ) : ?>
              <span class="install"><?php echo esc_attr( $value[5] ); ?>+ <?php _e( 'Active Installs' ); ?></span>
						<?php endif; ?>
          </h2>
          <div class="theme-actions">
						<?php if ( ! empty( $value[3] ) ) : ?>
              <a class="button activate" href="<?php echo esc_url( $value[3] ); ?>">Try FREE</a>
						<?php endif; ?>
						<?php if ( ! empty( $value[4] ) ) : ?>
              <a class="button button-primary" href="<?php echo esc_url( $value[4] ); ?>" target="_blank">Get PRO</a>
						<?php endif; ?>
          </div>
        </div>
      </div>
		<?php } ?>
  </div>
</div>

<p class="wow-thank-you">
  <span class="thank-line">= = = = = = =</span><br/>
  Thank you for choosing 'Wow Plugin'<br/>
  Dmyto Lobov<br/>
  <a href="https://wow-estore.com" target="_blank">Wow-Estore.com</a>
</p>