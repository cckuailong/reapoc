<div class='ewd-upcp-catalog-sidebar-tags'>

	<div class='ewd-upcp-catalog-sidebar-title <?php echo ( $this->get_option( 'styling-sidebar-title-collapse' ) ? 'ewd-upcp-catalog-sidebar-collapsible' : '' ); ?> <?php echo ( $this->get_option( 'styling-sidebar-start-collapsed' ) ? 'ewd-upcp-sidebar-content-hidden' : '' ); ?>'>
		<?php echo esc_html( $this->get_label( 'label-tags' ) ); ?>
	</div>

	<div>

		<div class='ewd-upcp-catalog-sidebar-content'>

			<?php $this->print_sidebar_tags(); ?>

		</div>
	
	</div>

</div>