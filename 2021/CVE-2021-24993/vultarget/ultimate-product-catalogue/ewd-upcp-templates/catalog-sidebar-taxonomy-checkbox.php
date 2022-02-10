<div class='ewd-upcp-catalog-sidebar-<?php echo $this->taxonomy_type; ?> <?php echo ( $this->is_taxonomy_selected() ? 'ewd-upcp-taxonomy-selected' : '' ); ?>' data-taxonomy_id='<?php echo $this->taxonomy_term->term_id; ?>'>

	<?php $this->maybe_print_taxonomy_image(); ?>

	<input type='checkbox' name='<?php echo $this->taxonomy_type; ?>' id='<?php echo $this->taxonomy_type; ?>-<?php echo $this->taxonomy_term->term_id; ?>' value='<?php echo $this->taxonomy_term->term_id; ?>' <?php echo ( $this->is_taxonomy_selected() ? 'checked' : '' ); ?> >

	<label class='ewd-upcp-catalog-sidebar-taxonomy-label' for='<?php echo $this->taxonomy_type; ?>-<?php echo $this->taxonomy_term->term_id; ?>'> 

		<span><?php echo $this->taxonomy_term->name; ?> <span class='ewd-upcp-catalog-sidebar-taxonomy-count'> (<?php echo $this->taxonomy_term->catalog_count; ?>)</span></span>
	
	</label>

	<?php if ( $this->taxonomy_has_collapsible_children() ) { ?>

		<span class='ewd-upcp-taxonomy-collapsible-children <?php echo ( $this->get_option( 'styling-sidebar-start-collapsed' ) ? 'ewd-upcp-taxonomy-collapsible-children-hidden' : '' ); ?>'></span>

	<?php } ?>

	<?php $this->maybe_print_taxonomy_description(); ?>

</div>