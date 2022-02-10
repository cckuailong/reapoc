<?php defined( 'ABSPATH' ) or die( "you do not have access to this page!" ); ?>
	<div class="cmplz-tools-row">
		<div>
			<a href="<?php echo add_query_arg( array('page' => 'cmplz-proof-of-consent'), admin_url( 'admin.php' ) ) ?>">
				<?php _e( "Proof of consent", 'complianz-gdpr' ); ?>
			</a>
		</div>
		<div class="cmplz-last-updated-poc"><?php
			$docs = COMPLIANZ::$document->get_cookie_snapshot_list();
			if ( empty($docs) ) {
				_e("Not generated yet", "complianz-gdpr");
			} else {
				$last_cookie_statement = reset($docs );
				$time = $last_cookie_statement['time'];
				$last_updated = date( cmplz_short_date_format(), $time );
				printf(__('Last update %s', "complianz-gdpr"), $last_updated );
			}
			?>
		</div>
	</div>

	<div class="cmplz-tools-row">
		<div><?php _e( "Export personal data", 'complianz-gdpr' ); ?></div>
		<div>
			<a href="<?php echo admin_url( 'export-personal-data.php' ) ?>">
				<?php _e( "Export", 'complianz-gdpr' ); ?>
			</a>
		</div>
	</div>

	<div class="cmplz-tools-row">
		<div><?php _e( "Erase personal data", 'complianz-gdpr' ); ?></div>
		<div>
			<a href="<?php echo admin_url( 'erase-personal-data.php' ) ?>">
				<?php _e( "Erase",
					'complianz-gdpr' ); ?>
			</a>
		</div>
	</div>

	<?php if ( class_exists( 'WooCommerce' ) ) { ?>
	<div class="cmplz-tools-row">
		<div><?php _e( "Webshop privacy", 'complianz-gdpr' ); ?></div>
		<div>
			<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=account' ) ?>">
				<?php _e( "Settings", 'complianz-gdpr' ); ?>
			</a>
		</div>
	</div>
	<?php } ?>

<?php
require_once( apply_filters('cmplz_free_templates_path', cmplz_path . 'templates/' ) .'dashboard/tools-conditional.php');





