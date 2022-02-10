<option value='<?php echo $this->taxonomy_term->term_id; ?>' <?php echo ( $this->is_taxonomy_selected() ? 'selected' : '' ); ?> >

	<?php echo $this->taxonomy_term->name; ?> <span class='ewd-upcp-catalog-sidebar-taxonomy-count'> (<?php echo $this->taxonomy_term->catalog_count; ?>)</span>

</option>