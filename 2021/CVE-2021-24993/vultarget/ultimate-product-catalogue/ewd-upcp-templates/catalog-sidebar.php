<?php $this->maybe_print_sidebar_toggle(); ?>

<div class='ewd-upcp-catalog-sidebar ewd-upcp-catalog-sidebar-checkbox-style-<?php echo $this->get_option( 'styling-sidebar-checkbox-style' ); ?> <?php echo ( ! $this->get_option( 'disable-toggle-sidebar-on-mobile' ) == 'hierarchical' ? ' ewd-upcp-catalog-sidebar-hidden' : '' ); ?>'>

	<?php $this->maybe_print_sidebar_clear_all(); ?>

	<?php $this->print_sidebar_items(); ?>

</div>