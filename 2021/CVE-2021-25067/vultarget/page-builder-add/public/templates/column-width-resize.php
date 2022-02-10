<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($columns < '4' && $columns >= '2') { ?> 
		<style>
			@media screen and (max-width: 580px) {
				#<?php echo $row["rowID"]; ?> .pops-column {width:100% !important; }
			}
		</style> <?php
	} elseif ($columns <= '7' && $columns >= '4') {?> 
		<style>
			@media screen and (max-width: 680px) {
				#<?php echo $row["rowID"]; ?> .pops-column {width:50% !important; }
			}
		</style> <?php
	} elseif ($columns === '8') {?> 
		<style>
			@media screen and (max-width: 680px) {
				#<?php echo $row["rowID"]; ?> .pops-column {width:25% !important; }
			}
		</style> <?php
	}
 ?>