<div class='ewd-ufaq-permalink'>
	
	<a href='<?php echo esc_attr( $this->permalink ); ?>'>
		
		<?php if ( $this->get_option( 'include-permalink' ) == 'both' or $this->get_option( 'include-permalink' ) == 'text' ) { echo $this->get_label( 'label-permalink' ); } ?>
		<?php if ( $this->get_option( 'include-permalink' ) == 'both' or $this->get_option( 'include-permalink' ) == 'icon' ) { ?> <div class='ewd-ufaq-permalink-image'></div> <?php } ?>
	
	</a>

</div>