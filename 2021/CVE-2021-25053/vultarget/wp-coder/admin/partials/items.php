<?php
/**
 * Items
 *
 * @package     Wow_Plugin
 * @subpackage
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_transient( 'wow_items_code' );
$items = false;

if ( $items == false ) {
	$url          = 'https://wow-estore.com/a-plugins/items/';
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
		height: 320px;
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

	.install {
		float: right;
	}
</style>

<h3>Have any idea about CSS and JS item? Write us now on email support@wow-company.com or via Support page.</h3>

<div class="theme-browser">
    <div class="themes">
		<?php
		$image = 'https://wow-estore.com/a-plugins/items/img/';
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
                    </h2>
                    <div class="theme-actions">
						<?php if ( ! empty( $value[3] ) ) : ?>
                            <a class="button activate" href="<?php echo esc_url( $value[3] ); ?>">Get Now</a>
						<?php endif; ?>

                    </div>
                </div>
            </div>
		<?php } ?>
    </div>
</div>