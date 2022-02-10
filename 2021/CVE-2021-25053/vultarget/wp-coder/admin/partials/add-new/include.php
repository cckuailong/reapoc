<?php 
	/**
		* Include files
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	$html_code = array(
	'id'   => 'html_code',
	'name' => 'content_html',	
	'type' => 'textarea',
	'val' => isset( $param['content_html'] ) ? $param['content_html'] : '',	
	);
	$html_code_help = array ( 
	'text' => __('Enter your HTML code', 'wpcoder'),
	);
	
?>

<div id="include_file" >
	<?php $count_include = !empty( $param['include'] ) ? count( $param['include'] ) : 0; 
		if ( $count_include > 0 ) {
			for ( $i = 0; $i < $count_include; $i++ ) { ?>
			<div class="wow-container include-file">
				<div class="wow-element">
					Type of file:<br/>
					<select name="param[include][<?php echo $i;?>]">
						<option <?php selected( $param['include'][$i], 'css' ); ?>>css</option>
						<option <?php selected( $param['include'][$i], 'js' ); ?>>js</option>
					</select>
				</div>
				<div class="wow-element">
					URL to file:<br/>
					<input type="text" name="param[include_file][<?php echo $i;?>]" value="<?php echo $param['include_file'][$i]; ?>">
				</div>							
			</div>
			<?php	
			} 
		} 			
	?>
</div>
<div class="submit-bottom">
	<input type="button" value="Add new" class="add-item" onclick="itemadd()"> 
	<input type="button" class="delete-item" value="Delete last" onclick="itemremove()">
</div>