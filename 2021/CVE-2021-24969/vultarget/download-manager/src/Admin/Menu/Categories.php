<?php
/**
 * User: shahnuralam
 * Date: 11/9/15
 * Time: 7:30 PM
 */

namespace WPDM\Admin\Menu;


class Categories
{

    function __construct(){
        add_action( 'wpdmcategory_add_form_fields', array($this,'MetaFields'), 10, 2 );
        add_action( 'wpdmcategory_edit_form_fields', array($this,'MetaFieldsEdit'), 10, 2 );

        add_action( 'edited_wpdmcategory', array($this,'saveMetaData'), 10, 2 );
        add_action( 'create_wpdmcategory', array($this,'saveMetaData'), 10, 2 );

        add_action( 'admin_init', array($this,'AdminInit') );


    }

    function adminInit(){
        add_filter("manage_edit-wpdmcategory_columns", array($this,'CategoryIDColumnHead'));
        add_filter("manage_wpdmcategory_custom_column", array($this,'CategoryIDColumnData'), 10, 3);
    }


    function categoryIDColumnHead($columns) {
        $columns['tag_ID'] = 'ID<style>#tag_ID, .tag_ID{ width: 70px !important; }</style>';
        return $columns;
    }

    function categoryIDColumnData($c, $column_name, $term_id) {

        if ($column_name == 'tag_ID') {
            return $term_id;
        }
        return $c;
    }

    function metaFields() {
        ?>
        <div class="form-field w3eden">
            <div class="panel panel-default card-plain panel-plain">
                <div class="panel-heading"><label><?php _e( 'Category Image:', 'download-manager' ); ?></label></div>
                <div class="panel-body">
                    <div class="input-group">
                        <input type="text" id="catimurl" placeholder="<?php _e( "Image URL" , "download-manager" ); ?>" class="form-control" name="__wpdmcategory[icon]" value="">
                        <div class="input-group-btn">
                            <button data-uploader_button_text="Insert" data-uploader_title="<?php _e('Select Category Image', 'download-manager'); ?>" id="catim" type="button" class="btn btn-info"><?php _e('Browse', 'download-manager'); ?></button>
                        </div>
                    </div>
                    <script type="text/javascript">

                        jQuery(document).ready(function() {

                            var file_frame;

                            jQuery('body').on('click', '#catim', function( event ){

                                event.preventDefault();

                                // If the media frame already exists, reopen it.
                                if ( file_frame ) {
                                    file_frame.open();
                                    return;
                                }

                                // Create the media frame.
                                file_frame = wp.media.frames.file_frame = wp.media({
                                    title: jQuery( this ).data( 'uploader_title' ),
                                    button: {
                                        text: jQuery( this ).data( 'uploader_button_text' )
                                    },
                                    multiple: false  // Set to true to allow multiple files to be selected
                                });

                                // When an image is selected, run a callback.
                                file_frame.on( 'select', function() {
                                    // We set multiple to false so only get one image from the uploader
                                    attachment = file_frame.state().get('selection').first().toJSON();
                                    var imgurl = attachment.url;
                                    jQuery('#catimurl').val(imgurl);

                                });

                                // Finally, open the modal
                                file_frame.open();
                                return false;
                            });





                            jQuery('.del_adp').click(function(){
                                if(confirm('Are you sure?')){
                                    jQuery('#'+jQuery(this).attr('rel')).fadeOut().remove();
                                }

                            });

                        });

                    </script>
                </div>
            </div>


        </div>
        <div class="form-field w3eden">
            <div class="panel panel-default card-plain panel-plain">
                <div class="panel-heading"><label><?php _e( 'Access:', 'wpdmcategory' ); ?></label></div>
                <div class="panel-body">
                    <p class="description"><?php _e( "Select the roles who should have access to the packages under this category" , "download-manager" ); ?></p>

                    <div class="row">
                    <label class="col-md-4"><input name="__wpdmcategory[access][]" type="checkbox" value="guest"> <?php echo __( "All Visitors" , "download-manager" ); ?></label>
                    <?php
                    global $wp_roles;
                    $roles = array_reverse($wp_roles->role_names);
                    foreach( $roles as $role => $name ) { ?>
                        <label class="col-md-4"><input name="__wpdmcategory[access][]" type="checkbox" value="<?php echo $role; ?>"  > <?php echo $name; ?></label>
                    <?php } ?>
                    </div>
                </div>
            </div>


        </div>
        <?php if(!file_exists(get_stylesheet_directory().'/taxonomy-wpdmcategory.php') && !file_exists(get_template_directory().'/taxonomy-wpdmcategory.php')){ ?>
        <div class="form-field w3eden">
            <div class="panel panel-default card-plain panel-plain">
                <div class="panel-heading"><?php echo __( "Category Page Template" , "download-manager" ); ?></div>
                <div class="panel-body">
                    <div class="panel panel-default panel-light" id="cpi">
                        <label  class="panel-heading" style="display: block;border-bottom: 1px solid #e5e5e5;margin-bottom: -1px"><input type="radio" name="__wpdmcategory[style]"  checked="checked" value="global"> <?php echo __( "Use Global", "download-manager" ) ?></label>
                        <label  class="panel-heading"  style="display: block;border-bottom: 1px solid #ddd;border-top: 1px solid #ddd;border-radius: 0;"><input type="radio" name="__wpdmcategory[style]"  value="basic"> <?php echo __( "Do Not Apply", "download-manager" ) ?></label>
                        <div class="panel-body">
                            <?php echo __( "Keep current template as it is provided with the active theme", "download-manager" ); ?>
                        </div>
                        <label  class="panel-heading" style="display: block;border-bottom: 1px solid #ddd;border-top: 1px solid #ddd;border-radius: 0;"><input type="radio" name="__wpdmcategory[style]" value="ltpl"> <?php echo __( "Use Link Template", "download-manager" ) ?></label>
                        <div class="panel-body">
                            <?php

                            $cpage_global = maybe_unserialize(get_option('__wpdm_cpage'));
                            $cpage_global = !is_array($cpage_global) ? [ 'template' => 'link-template-default', 'cols' => 2, 'colsphone' => 1, 'colspad' => 1, 'heading' => 1 ] : $cpage_global;
                            $cpage = maybe_unserialize(get_term_meta(wpdm_query_var('tag_ID', 'int'), '__wpdm_pagestyle', true));
                            $cpage = !is_array($cpage) ? $cpage_global : $cpage;
                            ?>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label><?php echo __( "Link Template:" , "download-manager" ); ?></label><br/>
                                        <?php
                                        echo WPDM()->packageTemplate->dropdown(array('name' => '__wpdmcategory[pagestyle][template]', 'selected' => $cpage['template'], 'class' => 'form-control wpdm-custom-select' ));
                                        ?>
                                    </div>
                                    <div class="col-md-3">
                                        <label><?php echo __( "Items Per Page:" , "download-manager" ); ?></label><br/>
                                        <input type="number" class="form-control" name="__wpdmcategory[pagestyle][items_per_page]" value="<?php echo isset($cpage['items_per_page']) ? $cpage['items_per_page'] : 12; ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label><?php echo __( "Toolbar:" , "download-manager" ); ?></label>
                                        <div class="input-group" style="display: flex">
                                            <label class="form-control" style="margin: 0;"><input type="radio" name="__wpdmcategory[pagestyle][heading]" value="1" <?php checked($cpage['heading'], 1); ?>> <?php echo __( "Show", "download-manager" ) ?></label>
                                            <label class="form-control" style="margin: 0 0 0 -1px;"><input type="radio" name="__wpdmcategory[pagestyle][heading]" value="0" <?php checked($cpage['heading'], 0); ?>> <?php echo __( "Hide", "download-manager" ) ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo __( "Number of Columns", "download-manager" ) ?>:</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <select class="form-control wpdm-custom-select" name="__wpdmcategory[pagestyle][cols]">
                                                <option value="1">1 Col</option>
                                                <option value="2" <?php selected(2, $cpage['cols']) ?> >2 Cols</option>
                                                <option value="3" <?php selected(3, $cpage['cols']) ?> >3 Cols</option>
                                                <option value="4" <?php selected(4, $cpage['cols']) ?> >4 Cols</option>
                                                <option value="6" <?php selected(6, $cpage['cols']) ?>>6 Cols</option>
                                                <option value="12" <?php selected(12, $cpage['cols']) ?>>12 Cols</option>
                                            </select><div class="input-group-addon">
                                                <i class="fa fa-laptop"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <select class="form-control wpdm-custom-select" name="__wpdmcategory[pagestyle][colspad]">
                                                <option value="1">1 Col</option>
                                                <option value="2" <?php selected(2, $cpage['colspad']) ?> >2 Cols</option>
                                                <option value="3" <?php selected(3, $cpage['colspad']) ?> >3 Cols</option>
                                                <option value="4" <?php selected(4, $cpage['colspad']) ?> >4 Cols</option>
                                                <option value="6" <?php selected(6, $cpage['colspad']) ?>>6 Cols</option>
                                                <option value="12" <?php selected(12, $cpage['colspad']) ?>>12 Cols</option>
                                            </select><div class="input-group-addon">
                                                <i class="fa fa-tablet-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <select class="form-control wpdm-custom-select" name="__wpdmcategory[pagestyle][colsphone]">
                                                <option value="1">1 Col</option>
                                                <option value="2" <?php selected(2, $cpage['colsphone']) ?> >2 Cols</option>
                                                <option value="3" <?php selected(3, $cpage['colsphone']) ?> >3 Cols</option>
                                                <option value="4" <?php selected(4, $cpage['colsphone']) ?> >4 Cols</option>
                                                <option value="6" <?php selected(6, $cpage['colsphone']) ?>>6 Cols</option>
                                                <option value="12" <?php selected(12, $cpage['colsphone']) ?>>12 Cols</option>
                                            </select><div class="input-group-addon">
                                                <i class="fa fa-mobile"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>select{min-width: auto !important;}</style>





            </div>
        </div>
        <?php } ?>
        <?php
    }

    function metaFieldsEdit() {
        $MetaData = get_option( "__wpdmcategory" );
        $MetaData = maybe_unserialize($MetaData);
        $icon = get_term_meta(wpdm_query_var('tag_ID', 'int'), '__wpdm_icon', true);
        if($icon == '')
            $icon = isset($MetaData[$_GET['tag_ID']]['icon'])?$MetaData[$_GET['tag_ID']]['icon']:'';

        ?>

        <tr>
            <td colspan="2">
                <div class="form-field w3eden">
                    <div class="panel panel-default card-plain">
                        <div class="panel-heading">
                            <label><?php _e( 'Category Image:', 'download-manager' ); ?></label>
                        </div>
                        <div class="panel-body">

                            <div class="input-group">
                                <input type="text" id="catimurl" placeholder="<?php _e( "Image URL" , "download-manager" ); ?>" class="form-control" name="__wpdmcategory[icon]" value="<?php echo $icon; ?>">
                                <div class="input-group-btn">
                                    <button data-uploader_button_text="Insert" data-uploader_title="<?php _e('Select Category Image', 'download-manager'); ?>" id="catim" type="button" class="btn btn-info"><?php _e('Browse', 'download-manager'); ?></button>
                                </div>
                            </div>

                            <script type="text/javascript">

                                jQuery(document).ready(function() {

                                    var file_frame;

                                    jQuery('body').on('click', '#catim', function( event ){

                                        event.preventDefault();

                                        // If the media frame already exists, reopen it.
                                        if ( file_frame ) {
                                            file_frame.open();
                                            return;
                                        }

                                        // Create the media frame.
                                        file_frame = wp.media.frames.file_frame = wp.media({
                                            title: jQuery( this ).data( 'uploader_title' ),
                                            button: {
                                                text: jQuery( this ).data( 'uploader_button_text' )
                                            },
                                            multiple: false  // Set to true to allow multiple files to be selected
                                        });

                                        // When an image is selected, run a callback.
                                        file_frame.on( 'select', function() {
                                            // We set multiple to false so only get one image from the uploader
                                            attachment = file_frame.state().get('selection').first().toJSON();
                                            var imgurl = attachment.url;
                                            jQuery('#catimurl').val(imgurl);

                                        });

                                        // Finally, open the modal
                                        file_frame.open();
                                        return false;
                                    });





                                    jQuery('.del_adp').click(function(){
                                        if(confirm('Are you sure?')){
                                            jQuery('#'+jQuery(this).attr('rel')).fadeOut().remove();
                                        }

                                    });

                                });

                            </script>
                        </div>
                    </div>
                </div>
                <div class="form-field w3eden">
                    <div class="panel panel-default card-plain">
                        <div class="panel-heading">
                            <label><?php _e( 'Access:', 'wpdmcategory' ); ?></label>
                        </div>
                        <div class="panel-body">
                            <p class="description" style="margin-bottom: 10px"><?php _e( "Select the roles who should have access to the packages under this category" , "download-manager" ); ?></p>
                            <ul class="row">
                                <input name="__wpdmcategory[access][]" type="hidden" value="__wpdm__" />
                                <?php

                                $currentAccess = maybe_unserialize(get_term_meta(wpdm_query_var('tag_ID', 'int'), '__wpdm_access', true));
                                if(!is_array($currentAccess))
                                    $currentAccess = isset($MetaData[$_GET['tag_ID']])?$MetaData[$_GET['tag_ID']]['access']:array();

                                $selz = '';
                                if(  $currentAccess ) $selz = (in_array('guest',$currentAccess))?'checked=checked':'';
                                ?>

                                <li class="col-md-4"><label><input name="__wpdmcategory[access][]" type="checkbox" value="guest" <?php echo $selz  ?>><?php echo __( "All Visitors" , "download-manager" ); ?></label></li>
                                <?php
                                global $wp_roles;
                                $roles = array_reverse($wp_roles->role_names);
                                foreach( $roles as $role => $name ) {



                                    if(  $currentAccess ) $sel = (in_array($role,$currentAccess))?'checked=checked':'';
                                    else $sel = '';



                                    ?>
                                    <li class="col-md-4"><label><input name="__wpdmcategory[access][]" type="checkbox" value="<?php echo $role; ?>" <?php echo $sel  ?>> <?php echo $name; ?></label></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if(!file_exists(get_stylesheet_directory().'/taxonomy-wpdmcategory.php') && !file_exists(get_template_directory().'/taxonomy-wpdmcategory.php')){
                    $style_global = get_option('__wpdm_cpage_style', 'basic');
                    $style = get_term_meta(wpdm_query_var('tag_ID', 'int'), '__wpdm_style', true);
                    if(!$style) $style = 'global';
                    $style = in_array($style, ['basic', 'ltpl', 'global']) ? $style : $style_global;
                    ?>
                    <div class="form-field w3eden">
                        <div class="panel panel-default card-plain panel-plain">
                            <div class="panel-heading"><?php echo __( "Category Page Template" , "download-manager" ); ?></div>
                            <div class="panel-body">
                                <div class="panel panel-default panel-light" id="cpi">
                                    <label  class="panel-heading" style="display: block;border-bottom: 1px solid #e5e5e5;margin-bottom: -1px"><input type="radio" name="__wpdmcategory[style]"  <?php checked($style,'global'); ?> value="global"> <?php echo __( "Use Global", "download-manager" ) ?></label>
                                    <label  class="panel-heading"  style="display: block;border-bottom: 1px solid #ddd;border-top: 1px solid #ddd;border-radius: 0;"><input type="radio" name="__wpdmcategory[style]"  <?php checked($style,'basic'); ?> value="basic"> <?php echo __( "Do Not Apply", "download-manager" ) ?></label>
                                    <div class="panel-body">
                                        <?php echo __( "Keep current template as it is provided with the active theme", "download-manager" ); ?>
                                    </div>
                                    <label  class="panel-heading" style="display: block;border-bottom: 1px solid #ddd;border-top: 1px solid #ddd;border-radius: 0;"><input type="radio" name="__wpdmcategory[style]" <?php checked($style,'ltpl'); ?> value="ltpl"> <?php echo __( "Use Link Template", "download-manager" ) ?></label>
                                    <div class="panel-body">
                                        <?php

                                        $cpage_global = maybe_unserialize(get_option('__wpdm_cpage'));
                                        $cpage_global = !is_array($cpage_global) ? [ 'template' => 'link-template-default', 'cols' => 2, 'colsphone' => 1, 'colspad' => 1, 'heading' => 1 ] : $cpage_global;
                                        $cpage = maybe_unserialize(get_term_meta(wpdm_query_var('tag_ID', 'int'), '__wpdm_pagestyle', true));
                                        $cpage = !is_array($cpage) ? $cpage_global : $cpage;
                                        ?>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label><?php echo __( "Link Template:" , "download-manager" ); ?></label><br/>
                                                    <?php
                                                    echo WPDM()->packageTemplate->dropdown(array('name' => '__wpdmcategory[pagestyle][template]', 'selected' => $cpage['template'], 'class' => 'form-control wpdm-custom-select' ));
                                                    ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label><?php echo __( "Items Per Page:" , "download-manager" ); ?></label><br/>
                                                    <input type="number" class="form-control" name="__wpdmcategory[pagestyle][items_per_page]" value="<?php echo isset($cpage['items_per_page']) ? $cpage['items_per_page'] : 12; ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label><?php echo __( "Toolbar:" , "download-manager" ); ?></label>
                                                    <div class="input-group" style="display: flex">
                                                        <label class="form-control" style="margin: 0;"><input type="radio" name="__wpdmcategory[pagestyle][heading]" value="1" <?php checked($cpage['heading'], 1); ?>> <?php echo __( "Show", "download-manager" ) ?></label>
                                                        <label class="form-control" style="margin: 0 0 0 -1px;"><input type="radio" name="__wpdmcategory[pagestyle][heading]" value="0" <?php checked($cpage['heading'], 0); ?>> <?php echo __( "Hide", "download-manager" ) ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo __( "Number of Columns", "download-manager" ) ?>:</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <select class="form-control wpdm-custom-select" name="__wpdmcategory[pagestyle][cols]">
                                                            <option value="1">1 Col</option>
                                                            <option value="2" <?php selected(2, $cpage['cols']) ?> >2 Cols</option>
                                                            <option value="3" <?php selected(3, $cpage['cols']) ?> >3 Cols</option>
                                                            <option value="4" <?php selected(4, $cpage['cols']) ?> >4 Cols</option>
                                                            <option value="6" <?php selected(6, $cpage['cols']) ?>>6 Cols</option>
                                                            <option value="12" <?php selected(12, $cpage['cols']) ?>>12 Cols</option>
                                                        </select><div class="input-group-addon">
                                                            <i class="fa fa-laptop"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <select class="form-control wpdm-custom-select" name="__wpdmcategory[pagestyle][colspad]">
                                                            <option value="1">1 Col</option>
                                                            <option value="2" <?php selected(2, $cpage['colspad']) ?> >2 Cols</option>
                                                            <option value="3" <?php selected(3, $cpage['colspad']) ?> >3 Cols</option>
                                                            <option value="4" <?php selected(4, $cpage['colspad']) ?> >4 Cols</option>
                                                            <option value="6" <?php selected(6, $cpage['colspad']) ?>>6 Cols</option>
                                                            <option value="12" <?php selected(12, $cpage['colspad']) ?>>12 Cols</option>
                                                        </select><div class="input-group-addon">
                                                            <i class="fa fa-tablet-alt"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <select class="form-control wpdm-custom-select" name="__wpdmcategory[pagestyle][colsphone]">
                                                            <option value="1">1 Col</option>
                                                            <option value="2" <?php selected(2, $cpage['colsphone']) ?> >2 Cols</option>
                                                            <option value="3" <?php selected(3, $cpage['colsphone']) ?> >3 Cols</option>
                                                            <option value="4" <?php selected(4, $cpage['colsphone']) ?> >4 Cols</option>
                                                            <option value="6" <?php selected(6, $cpage['colsphone']) ?>>6 Cols</option>
                                                            <option value="12" <?php selected(12, $cpage['colsphone']) ?>>12 Cols</option>
                                                        </select><div class="input-group-addon">
                                                            <i class="fa fa-mobile"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <style>select{min-width: auto !important;}</style>
                        </div>
                    </div>
                <?php } ?>
            </td>
        </tr>

        <?php
    }

    function saveMetaData( $term_id ) {
        if ( isset( $_POST['__wpdmcategory'] ) ) {
            //$MetaData = get_option( "__wpdmcategory" );
            //$MetaData = maybe_unserialize($MetaData);
            foreach ($_POST['__wpdmcategory'] as $metaKey => $metaValue){
              update_term_meta($term_id, "__wpdm_".$metaKey, $metaValue);
            }
            //$MetaData[$term_id] = $_POST['__wpdmcategory'];
            //update_option( "__wpdmcategory", $MetaData );
        }
    }

}
