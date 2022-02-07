<?php	


/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


if(!current_user_can('manage_options')){

    return;
}



if(empty($_POST['post_grid_hidden']))
	{
		$post_grid_layout_content = get_option( 'post_grid_layout_content' );


	}
else
	{	
	
		$nonce = $_POST['_wpnonce'];
	
		if(wp_verify_nonce( $nonce, 'nonce_layout_content' ) && $_POST['post_grid_hidden'] == 'Y') {
			//Form data sent
			
			//$post_grid_layout_content = stripslashes_deep($_POST['post_grid_layout_content']);			
			$post_grid_layout_content = get_option( 'post_grid_layout_content' );
			
			if(empty($post_grid_layout_content)){
				$post_grid_layout_content = array();
				}
				
			if(!empty($_POST['post_grid_layout_content']) && is_array($_POST['post_grid_layout_content'])){
				$post_grid_layout_content_new = stripslashes_deep($_POST['post_grid_layout_content']);
				}
			else{
				$post_grid_layout_content_new = array();
				}
				
			
			$post_grid_layout_content = array_merge($post_grid_layout_content, $post_grid_layout_content_new);
			update_option('post_grid_layout_content', $post_grid_layout_content);
		

			?>
			<div class="updated"><p><strong><?php _e('Changes Saved.', 'post-grid' ); ?></strong></p></div>
	
			<?php
			} 

	}

?>

<div class="wrap">

	<div id="icon-tools" class="icon32"><br></div><?php echo "<h2>".sprintf(__('%s - Layout Editor', 'post-grid'), post_grid_plugin_name)."</h2>";?>
		<form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="post_grid_hidden" value="Y">
            <?php //settings_fields( 'post_grid_plugin_options' );
                    //do_settings_sections( 'post_grid_plugin_options' );
                
				
				if(!empty($_GET['layout_content'])){
					$layout_content = sanitize_text_field($_GET['layout_content']); 
					}
				else{
					$layout_content = 'flat'; 
					}

				//var_dump($layout_content);
				
				$class_post_grid_functions = new class_post_grid_functions();
				
            ?>



		<div class="layout-editor post-grid-layout-editor para-settings settings-tabs">

            <div class="container-fluid">
                <div class="row">
                    <div class="col col-md-3">

                        <div class="layout-items expandable">

                            <?php

                            $layout_items_group = $class_post_grid_functions->layout_items();
                            foreach($layout_items_group as $group_key=>$group_data){

                                $group_name = $group_data['name'];
                                $group_items = $group_data['items'];
                                ?>
                            <div class="item" id="<?php echo $group_key; ?>">

                                <div class="header">
                                    <span class="expand " title="<?php echo __('Expand or collapse', 'post-grid'); ?>">
                                        <i class="fas fa-expand"></i>
                                        <i class="fas fa-compress"></i>
                                    </span>
                                    <span class="name"><?php echo $group_name; ?></span>


                                </div>
                                <div class="options">
                                    <?php
                                    foreach($group_items as $item_key=>$item_info){

                                        ?>
                                        <div class="button add-element" layout="<?php echo $layout_content; ?>" item_group="<?php echo $group_key; ?>"  item_key="<?php echo $item_key; ?>" ><?php echo $item_info['name']; ?></div>
                                        <?php

                                    }

                                    ?>
                                </div>

                            </div>
                                <?php




                            }
                            ?>

                        </div>

                    </div>
                    <div class="col col-md-4">
                        <div class="layout-list">

                            <?php if(isset($_GET['layout_content'])) {?>
                                <div class="idle  ">
                                    <div class="name"><?php echo __('Content:', 'post-grid'); ?> <?php echo $layout_content; ?></div>

                                    <div class="layer-content">
                                        <div id="layout-container" class="<?php echo $layout_content; ?>">
                                            <?php


                                            if(empty($post_grid_layout_content)){
                                                $layout = $class_post_grid_functions->layout_content($layout_content);
                                            }
                                            else{

                                                if(!empty($post_grid_layout_content[$layout_content])){
                                                    $layout = $post_grid_layout_content[$layout_content];
                                                }
                                                else{
                                                    $layout = array();
                                                }


                                            }




                                            //var_dump($layout);

                                            foreach($layout as $item_id=>$item_info){

                                                $item_key = $item_info['key'];



                                                ?>


                                                <div class="item <?php echo $item_key; ?>" id="item-<?php echo $item_id; ?>" style=" <?php echo $item_info['css']; ?> ">

                                                    <?php

                                                    if($item_key=='thumb'){

                                                        ?>
                                                        <img style="width:100%; height:auto;" src="<?php echo post_grid_plugin_url; ?>assets/admin/images/thumb.png" />
                                                        <?php
                                                    }

                                                    elseif($item_key=='thumb_link'){

                                                        ?>
                                                        <a href="#"><img style="width:100%; height:auto;" src="<?php echo post_grid_plugin_url; ?>assets/admin/images/thumb.png" /></a>
                                                        <?php
                                                    }


                                                    elseif($item_key=='title'){

                                                        ?>
                                                        Lorem Ipsum is simply
                                                        <?php
                                                    }

                                                    elseif($item_key=='title_link'){

                                                        ?>
                                                        <a href="#">Lorem Ipsum is simply</a>
                                                        <?php
                                                    }



                                                    elseif($item_key=='excerpt'){

                                                        ?>
                                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text
                                                        <?php
                                                    }



                                                    else{

                                                        echo $item_info['name'];

                                                    }

                                                    ?>



                                                </div>
                                                <?php
                                            }


                                            ?>
                                        </div>
                                    </div>

                                </div>

                            <?php } ?>

                        </div>

                    </div>
                    <div class="col col-md-4">



                        <div class="clear"></div>

                        <div class="css-editor expandable">

                            <?php

                            $layout_content_list = $class_post_grid_functions->layout_content_list();


                            if(empty($layout)){$layout = array();

                                echo 'you haven\'t selected any layout. please select here';


                                ?>
                                <select class="layout-content">
                                    <option value="">Please select</option>
                                    <option value="create-new"><?php echo __('Create New' , 'post-grid'); ?></option>
                                    <?php

                                    $post_grid_layout_content = get_option('post_grid_layout_content');
                                    if(empty($post_grid_layout_content)){

                                        $layout_content_list = $class_post_grid_functions->layout_content_list();
                                    }
                                    else{

                                        $layout_content_list = $post_grid_layout_content;

                                    }


                                    // $layout_content_list = $class_post_grid_functions->layout_content_list();
                                    ?>

                                    <?php
                                    foreach($layout_content_list as $layout_key=>$layout_info){
                                        ?>
                                        <option <?php if($layout_content==$layout_key) echo 'selected'; else "" ?>  value="<?php echo $layout_key; ?>"><?php echo $layout_key; ?></option>
                                        <?php

                                    }
                                    ?>
                                </select>


                                <script>
                                    jQuery(document).ready(function($)
                                    {

                                        $(document).on('change', '.layout-content', function()
                                        {

                                            var layout = $(this).val();

                                            if(layout=='create-new'){

                                                layout = prompt('(Must be unique) Layout name ?');

                                                //layout = $.now();

                                                if(layout!=null){
                                                    window.location.href = "<?php echo admin_url().'edit.php?post_type=post_grid&page=layout_editor&layout_content=';?>"+layout;
                                                }


                                            }
                                            else{
                                                window.location.href = "<?php echo admin_url().'edit.php?post_type=post_grid&page=layout_editor&layout_content=';?>"+layout;
                                            }




                                        })

                                    })
                                </script>



                                <?php




                            }
                            $i=0;
                            foreach($layout as $key=>$items){

                                ?>
                                <div class="item" id="<?php echo $key; ?>">

                                    <div class="header">
                                        <span class="remove " title="<?php echo __('Remove', 'post-grid'); ?>"><i class="fa fa-times"></i></span>
                                        <span class="move " title="<?php echo __('Move', 'post-grid'); ?>"><i class="fas fa-bars"></i></span>
                                        <span class="expand " title="<?php echo __('Expand or collapse', 'post-grid'); ?>">
                                    <i class="fas fa-expand"></i>
                                    <i class="fas fa-compress"></i>
                                </span>
                                        <span class="name"><?php echo $items['name']; ?></span>


                                    </div>
                                    <div class="options">





                                        <?php

                                        foreach($items as $item_key=>$item_info){


                                            if($item_key=='css'){

                                                ?>
                                                <br />
                                                <?php _e('CSS:', 'post-grid' ); ?> <br />
                                                <a target="_blank" href="https://www.pickplugins.com/demo/post-grid/sample-css-for-layout-editor/"><?php _e('Sample css', 'post-grid' ); ?></a><br />
                                                <textarea autocorrect="off" autocapitalize="off" spellcheck="false"  style="width:100%" class="custom_css" item_id="<?php echo $key; ?>" name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]"><?php echo str_replace(array("\n", "\r"), '', $item_info); ?></textarea><br />



                                                <?php





                                            }
                                            elseif($item_key=='css_hover'){

                                                ?>
                                                <br />
                                                <?php _e('CSS Hover:', 'post-grid' ); ?><br />

                                                <textarea autocorrect="off" autocapitalize="off" spellcheck="false"  style="width:100%" class="custom_css" item_id="<?php echo $items['key']; ?>" name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]"><?php echo str_replace(array("\n", "\r"), '', $item_info); ?></textarea><br />



                                                <?php





                                            }

                                            elseif($item_key=='char_limit'){
                                                ?>

                                                <?php _e('Word limit:', 'post-grid' ); ?> <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $items['char_limit']; ?>" /><br /><br />

                                                <?php

                                            }

                                            elseif($item_key=='custom_class'){
                                                ?>

                                                <?php _e('Custom class:', 'post-grid' ); ?> <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $items['custom_class']; ?>" /><br /><br />

                                                <?php

                                            }



                                            elseif($item_key=='taxonomy'){
                                                ?>

                                                <?php _e('Taxonomy:', 'post-grid' ); ?> <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $items['taxonomy']; ?>" /><br /><br />

                                                <?php

                                            }

                                            elseif($item_key=='taxonomy_term_count'){
                                                ?>

                                                <?php _e('Term count:', 'post-grid' ); ?> <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $items['taxonomy_term_count']; ?>" /><br /><br />

                                                <?php

                                            }





                                            elseif($item_key=='link_target'){


                                                //var_dump($items['link_target']);
                                                ?>

                                                <?php _e('Link target:', 'post-grid' ); ?> <br />

                                                <select name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" >

                                                    <option <?php if($items['link_target']=='_blank') echo 'selected'; ?> value="_blank">_blank</option>
                                                    <option <?php if($items['link_target']=='_parent') echo 'selected'; ?> value="_parent">_parent</option>
                                                    <option <?php if($items['link_target']=='_self') echo 'selected'; ?> value="_self">_self</option>
                                                    <option <?php if($items['link_target']=='_top') echo 'selected'; ?> value="_top">_top</option>
                                                    <option <?php if($items['link_target']=='new') echo 'selected'; ?> value="new">new</option>
                                                </select>
                                                <br />



                                                <?php

                                            }









                                            else{
                                                ?>
                                                <input type="hidden"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $item_info; ?>" />

                                                <?php

                                            }

                                            if($item_key=='field_id'){
                                                ?>

                                                <?php _e('Meta Key:', 'post-grid' ); ?> <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $item_info; ?>" /><br />

                                                <?php

                                            }

                                            if($item_key=='wrapper'){
                                                ?>


                                                <?php //var_dump($item_info);

                                                $key_value = htmlentities($item_info);

                                                if(empty($key_value)){
                                                    $key_value = '%s';
                                                }

                                                ?>
                                                <br />
                                                <?php _e('Wrapper:', 'post-grid' ); ?>
                                                <br />
                                                <?php _e('use %s where you want to repalce the meta value.<pre>&lt;div&gt;Before %s - %s After&lt;/div&gt;</pre>','post-grid' ); ?>
                                                <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $key_value; ?>" /><br />

                                                <?php

                                            }


                                            if($item_key=='html'){
                                                ?>


                                                <?php //var_dump($item_info);

                                                $custom_html = htmlentities($item_info);

                                                if(empty($custom_html)){
                                                    $custom_html = '';
                                                }

                                                ?>
                                                <br />
                                                <?php _e('HTML:', 'post-grid' ); ?><br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo $custom_html; ?>" /><br />

                                                <?php

                                            }







                                            if($item_key=='read_more_text'){
                                                ?>

                                                <?php _e('Read more text:', 'post-grid' ); ?> <br />
                                                <input type="text"  name="post_grid_layout_content[<?php echo $layout_content; ?>][<?php echo $i; ?>][<?php echo $item_key; ?>]" value="<?php echo htmlentities($item_info); ?>" /><br />

                                                <?php

                                            }

                                        }
                                        ?>
                                    </div>
                                </div>

                                <?php

                                $i++;
                            }

                            ?>

                        </div>


                    </div>

                </div>
            </div>
        
        
			<?php
            
            ?>




            

                    
        	<br />

        
       
        
        </div>
    


<script>
 jQuery(document).ready(function($)
	{
		$(function() {
		$( ".css-editor" ).sortable({ handle: '.move' });
		//$( ".items-container" ).disableSelection();
		});

})

</script>

        <p class="submit">
        	<?php wp_nonce_field( 'nonce_layout_content' ); ?>
            <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'post-grid' ); ?>" />
        </p>


		</form>


</div>
