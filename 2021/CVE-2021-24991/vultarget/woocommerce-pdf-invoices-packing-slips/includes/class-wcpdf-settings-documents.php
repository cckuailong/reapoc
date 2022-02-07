<?php
namespace WPO\WC\PDF_Invoices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Settings_Documents' ) ) :

class Settings_Documents {

	function __construct()	{
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'wpo_wcpdf_settings_output_documents', array( $this, 'output' ), 10, 1 );
	}
	
	public function init_settings() {
		$documents = WPO_WCPDF()->documents->get_documents('all');
		foreach ($documents as $document) {
			$document->init_settings();
		}
	}

	public function output( $section ) {
		$section = !empty($section) ? $section : 'invoice';
		if ( !empty( $section ) ) {
			$documents = WPO_WCPDF()->documents->get_documents('all');
			?>
			<div class="wcpdf_document_settings_sections">
				<?php _e( 'Documents', 'woocommerce-pdf-invoices-packing-slips' ); ?>:
				<ul>
					<?php
					foreach ($documents as $document) {
						$title = strip_tags($document->get_title());
						if (empty(trim($title))) {
							$title = '['.__( 'untitled', 'woocommerce-pdf-invoices-packing-slips' ).']';
						}
						printf('<li><a href="%s" class="%s">%s</a></li>', add_query_arg( 'section', $document->get_type() ), $document->get_type() == $section ? 'active' : '', $title );
					}
					?>
				</ul>
			</div>
			<?php
			settings_fields( "wpo_wcpdf_documents_settings_{$section}" );
			do_settings_sections( "wpo_wcpdf_documents_settings_{$section}" );
			submit_button();
		} else {
			$documents = WPO_WCPDF()->documents->get_documents('all');
			?>
			<p><?php _e('All available documents are listed below. Click on a document to configure it.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
			<table class="wcpdf_documents_settings_list">
				<?php
				$c = false;
				foreach ($documents as $document) {
					$title = strip_tags($document->get_title());
					if (empty(trim($title))) {
						$title = __( 'untitled', 'woocommerce-pdf-invoices-packing-slips' );
					}
					?>
					<tr class="<?php echo (($c = !$c)?"odd":"even"); ?>">
						<td class="title"><a href="<?php echo add_query_arg( 'section', $document->get_type() ); ?>"><?php echo $title; ?></a></td>
						<td class="settings-icon"><a href="<?php echo add_query_arg( 'section', $document->get_type() ); ?>"><span class="dashicons dashicons-admin-settings"></span></a></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
	}

}

endif; // class_exists

return new Settings_Documents();