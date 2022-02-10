<?php


namespace WPDM\Admin\Menu;


use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\FileSystem;
use WPDM\Package\Package;
use WPDM\WordPressDownloadManager;

class Packages
{

    var $sanitize = array(
            'icon' => 'url',
            'version' => 'txt',
            'link_label' => 'txt',
            'package_size' => 'txt',
            'view_count' => 'int',
            'download_count' => 'int',
    );

    function __construct()
    {

        add_action('wp_ajax_wpdm_admin_upload_file', array($this, 'uploadFile'));
        add_action('save_post', array($this, 'savePackage'));
	    add_action('before_delete_post', array($this, 'deleteFiles'), 10, 2);

        add_action('manage_wpdmpro_posts_columns', array($this, 'columnsTH'));
        add_action('manage_wpdmpro_posts_custom_column', array($this, 'columnsTD'), 10, 2);

        add_filter( 'request', array($this, 'orderbyDownloads') );
        add_filter( 'manage_edit-wpdmpro_sortable_columns', array($this, 'sortableDownloads') );

        add_filter('post_row_actions', array($this, 'rowActions'), 10, 2);

        add_action('admin_footer', array($this, 'footerScripts'));

        add_action("admin_init", [$this, 'duplicate']);

    }

    function savePackage($post)
    {
        if(!current_user_can('edit_post', $post)) return;
        if (get_post_type() != 'wpdmpro' || !isset($_POST['file'])) return;

        // Deleted old zipped file
        $zipped = get_post_meta($post, "__wpdm_zipped_file", true);
        if($zipped!='' && file_exists($zipped)) { @unlink($zipped); }

        $cdata = get_post_custom($post);
        $donot_delete_meta = array('__wpdm_favs', '__wpdm_masterkey');
        foreach ($cdata as $k => $v) {
            $tk = str_replace("__wpdm_", "", $k);
            if (!isset($_POST['file'][$tk]) && $tk !== $k && !in_array($k, $donot_delete_meta))
                delete_post_meta($post, $k);

        }

        foreach ($_POST['file'] as $meta_key => $meta_value) {
            $key_name = "__wpdm_" . $meta_key;
            if($meta_key == 'package_dir' && $meta_value != '') {
                $meta_value = file_exists($meta_value) && get_post_meta($post->ID, $key_name, true) === $meta_value ? $meta_value : Crypt::decrypt($meta_value);
                $meta_value = realpath($meta_value);
                $meta_value = Crypt::encrypt($meta_value);
            }
            if($meta_key == 'package_size' && (double)$meta_value == 0) $meta_value = "";
            if($meta_key == 'files'){
                $meta_value = array_unique($meta_value);
                foreach ($meta_value as &$value){
                    $value = wpdm_escs($value);
                }
            } else
                $meta_value = is_array($meta_value)?wpdm_sanitize_array($meta_value):wpdm_escs($meta_value);
            update_post_meta($post, $key_name, $meta_value);
        }

        if(get_post_meta($post, '__wpdm_masterkey', true) == '')
            update_post_meta($post, '__wpdm_masterkey', uniqid());

        if (isset($_POST['reset_key']) && $_POST['reset_key'] == 1)
            update_post_meta($post, '__wpdm_masterkey', uniqid());

        if(isset($_REQUEST['reset_udl'])) WPDM()->downloadHistory->resetUserDownloadCount($post, 'all');
        do_action('wpdm_admin_update_package',$post, $_POST['file']);
    }

	function deleteFiles($post_id, $post)
	{
		$files = WPDM()->package->getFiles($post_id, false);
		foreach ($files as $file) {
			$file = WPDM()->fileSystem->locateFile($file);
			@unlink($file);
		}
	}

    function duplicate()
    {
        if(wpdm_query_var('wpdm_duplicate', 'int') > 0 && get_post_type(wpdm_query_var('wpdm_duplicate')) === 'wpdmpro') {
            if(!current_user_can('edit_posts') || !wp_verify_nonce(wpdm_query_var('__copynonce'), NONCE_KEY)) wp_die(esc_attr__( 'You are not authorized!', 'download-manager' ));
            Package::copy(wpdm_query_var('wpdm_duplicate', 'int'));
            wp_redirect("edit.php?post_type=wpdmpro");
            die();
        }
    }


    function uploadFile(){
        check_ajax_referer(NONCE_KEY);
        if(!current_user_can('upload_files')) die('-2');

        $name = isset($_FILES['package_file']['name']) && !isset($_REQUEST["chunks"])?sanitize_text_field($_FILES['package_file']['name']):wpdm_query_var('name', 'txt');

        $ext = FileSystem::fileExt($name);

        if(WPDM()->fileSystem->isBlocked($name, $_FILES['package_file']['tmp_name'])) die('-3');

        do_action("wpdm_before_upload_file", $_FILES['package_file']);

        @set_time_limit(0);

        if(!file_exists(UPLOAD_DIR)){
            WPDM()->createDir();
        }

        if(file_exists(UPLOAD_DIR.$name) && get_option('__wpdm_overwrrite_file',0)==1){
            @unlink(UPLOAD_DIR.$name);
        }
        if(file_exists(UPLOAD_DIR.$name) && !isset($_REQUEST["chunks"]))
            $filename = time().'wpdm_'.$name;
        else
            $filename = $name;

        //$filename = esc_html($filename);

        if(get_option('__wpdm_sanitize_filename', 0) == 1)
            $filename = sanitize_file_name($filename);
        else {
            $filename = str_replace(["/", "\\"], "_", $filename);
        }

        if(isset($_REQUEST["chunks"])) $this->chunkUploadFile(UPLOAD_DIR.$filename);
        else {
            move_uploaded_file($_FILES['package_file']['tmp_name'], UPLOAD_DIR . $filename);
            do_action("wpdm_after_upload_file", UPLOAD_DIR . $filename);
        }

        //$filename = apply_filters("wpdm_after_upload_file", $filename, UPLOAD_DIR);

        echo "|||".$filename."|||";
        exit;
    }


    function chunkUploadFile($destFilePath){

        if($destFilePath === '') return;
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $out = @fopen("{$destFilePath}.part", $chunk == 0 ? "wb" : "ab");

        if ($out) {
            // Read binary input stream and append it to temp file
            $in = @fopen($_FILES['package_file']['tmp_name'], "rb");

            if ($in) {
                while ($buff = fread($in, 4096))
                    fwrite($out, $buff);
            } else
                die('-3');

            @fclose($in);
            @fclose($out);

            @unlink($_FILES['package_file']['tmp_name']);
        } else {
            die('-3'.$destFilePath);
        }

        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$destFilePath}.part", $destFilePath);
            do_action("wpdm_after_upload_file", $destFilePath);
        }
    }


    function columnsTH($defaults) {
        if(get_post_type()!='wpdmpro') return $defaults;
        $img['image'] = "<span class='wpdm-th-icon ttip' style='font-size: 0.8em'><i  style='font-size: 80%' class='far fa-image'></i></span>";
        __::array_splice_assoc( $defaults, 1, 0, $img );
        $otf['download_count'] = "<span class='wpdm-th-icon ttip' style='font-size: 0.8em'><i  style='font-size: 80%' class='fas fa-arrow-down'></i></span>";
        $otf['wpdmembed'] = esc_attr__( 'Shortcode', 'download-manager' );
        __::array_splice_assoc( $defaults, 3, 0, $otf );
        return $defaults;
    }


    function columnsTD($column_name, $post_ID) {
        if(get_post_type()!='wpdmpro') return;
        if ($column_name == 'download_count') {

            echo current_user_can(WPDM_ADMIN_CAP) || get_the_author_meta('ID') === get_current_user_id()?(int)get_post_meta($post_ID, '__wpdm_download_count', true):'&mdash;';

        }
	    if ($column_name == 'wpdmembed') {

		    echo "<div class='w3eden'><div class='input-group short-code-wpdm'><input readonly=readonly class='form-control bg-white' onclick='this.select();' value=\"[wpdm_package id='$post_ID']\" id='sci{$post_ID}' /><div class='input-group-btn'><button type='button' onclick=\"WPDM.copy('sci{$post_ID}')\" class='btn btn-secondary'><i class='fa fa-copy'></i></button></div></div></div>";
		    //echo "<div class='w3eden'><button type='button' href='#' data-toggle='modal' data-target='#embModal' data-pid='{$post_ID}' class='btn btn-secondary btn-embed'><i class='fa fa-bars'></i></button></div>";

	    }
        if ($column_name == 'image') {
            if(has_post_thumbnail($post_ID))
                echo get_the_post_thumbnail( $post_ID, 'thumbnail', array('class'=>'img60px') );
            else {
                $icon = get_post_meta($post_ID,'__wpdm_icon', true);
                if($icon!=''){
                    $icon = $icon;
                    echo "<img src='$icon' class='img60px' alt='Icon' />";
                }
            }
        }
    }


    function orderbyDownloads( $vars ) {

        if ( isset( $vars['orderby'] ) && 'download_count' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => '__wpdm_download_count',
                'orderby' => 'meta_value_num'
            ) );
        }

        return $vars;
    }

    function sortableDownloads( $columns ) {

        if(get_post_type()!='wpdmpro') return $columns;

        $columns['download_count'] = 'download_count';

        return $columns;
    }


    function rowActions($actions, $post)
    {
        if($post->post_type == 'wpdmpro' && current_user_can(WPDM_ADMIN_CAP)) {
            $actions['duplicate'] = '<a title="' . __( "Duplicate" , "download-manager" ) . '" href="' . admin_url("/?wpdm_duplicate={$post->ID}&__copynonce=".wp_create_nonce(NONCE_KEY)) . '" class="wpdm_duplicate w3eden">'.esc_attr__( 'Duplicate', 'download-manager' ).'</a>';
            $actions['view_stats'] = '<a title="' . __( "Stats" , "download-manager" ) . '" href="edit.php?post_type=wpdmpro&page=wpdm-stats&pid=' . $post->ID . '" class="view_stats w3eden"><i class="fas fa-chart-pie color-blue"></i></a>';
            if($post->post_status == 'publish')
                $actions['download_link'] = '<a title="' . __( "Master Download URL" , "download-manager" ) . '" href="#" class="gdl_action w3eden" data-mdlu="'.WPDM()->package->getMasterDownloadURL($post->ID).'" data-toggle="modal" data-target="#gdluModal" data-pid="'.$post->ID.'"><i class="far fa-arrow-alt-circle-down color-purple"></i></a>';
        }
        return $actions;
    }


    function footerScripts(){
        global $pagenow;
        if(wpdm_query_var('post_type') === 'wpdmpro' && $pagenow === 'edit.php') {
            ?>

            <style>
                .w3eden #edlModal .modal-content,
                .w3eden #gdluModal .modal-content{
                    padding: 20px;
                    border-radius: 15px;
                }
                .w3eden #edlModal .modal-content .modal-header i,
                .w3eden #gdluModal .modal-content .modal-header i{
                    margin-right: 6px;
                }
                .w3eden #gdluModal .modal-content .modal-footer,
                .w3eden #gdluModal .modal-content .modal-header,
                .w3eden #edlModal .modal-content .modal-footer,
                .w3eden #edlModal .modal-content .modal-header{
                    border: 0;
                }
            </style>

            <div class="w3eden">
                <div class="modal fade" tabindex="-1" role="dialog" id="embModal" style="display: none">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title"><i class="fa fa-paste color-green"></i> <?php _e("Embed Package", "download-manager"); ?></h4>
                            </div>
                            <div class="modal-body">

                                <div class="input-group input-group-lg">
                                    <input type="text" value="[wpdm_package id='{{ID}}']" id="cpsc" readonly="readonly" class="form-control bg-white" style="font-family: monospace;font-weight: bold;text-align: center">
                                    <div class="input-group-btn">
                                        <button style="padding-left: 30px;padding-right: 30px" onclick="WPDM.copy('cpsc');" type="button" class="btn btn-secondary"><i class="fa fa-copy"></i> <?php echo esc_attr__( 'Copy', 'download-manager' );?></button>
                                    </div>
                                </div>
                                <div class="alert alert-info" style="margin-top: 20px">
                                    <?php echo esc_attr__( 'If you are on Gutenberg Editor or elementor, you may use gutenberg block or elementor add-on for wpdm to embed wpdm packages and categories or generate another available layouts', 'download-manager' ); ?>
                                </div>

                                <div class="panel panel-default card-plain">
                                    <div class="panel-heading">
                                        <?php echo esc_attr__( 'Go To Page', 'download-manager' );?>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-9"><?php wp_dropdown_pages(['class' => 'form-control wpdm-custom-select', 'id' => 'gotopg']); ?></div>
                                            <div class="col-md-3"><button onclick="location.href='post.php?action=edit&post='+jQuery('#gotopg').val()" type="button" class="btn btn-secondary btn-block"><?php echo esc_attr__( 'Go', 'download-manager' ); ?></button></div>
                                        </div>

                                    </div>
                                    <div class="panel-footer bg-white">
                                        <a href="post-new.php?post_type=page"><?php echo esc_attr__( 'Create new page', 'download-manager' );?></a>
                                    </div>
                                </div>


                                    <?php if(!defined('__WPDM_GB__')) { ?>
                                        <a class="btn btn-block btn-secondary thickbox open-plugin-details-modal" href="<?php echo admin_url('/plugin-install.php?tab=plugin-information&plugin=wpdm-gutenberg-blocks&TB_iframe=true&width=600&height=550')?>"><?php echo esc_attr__( 'Install Gutenberg Blocks by WordPress Download Manager', 'download-manager' );?></a>
                                    <?php } ?>
                                    <?php if(!defined('__WPDM_ELEMENTOR__')) { ?>
                                        <a class="btn btn-block btn-secondary thickbox open-plugin-details-modal" style="margin-top: 10px" href="<?php echo admin_url('/plugin-install.php?tab=plugin-information&plugin=wpdm-elementor&TB_iframe=true&width=600&height=550')?>"><?php echo esc_attr__( 'Install Download Manager Addons for Elementor', 'download-manager' );?></a>
                                    <?php } ?>
                                <?php if(!function_exists('LiveForms')) { ?>
                                    <a class="btn btn-block btn-info thickbox open-plugin-details-modal" style="margin-top: 10px" href="<?php echo admin_url('/plugin-install.php?tab=plugin-information&plugin=liveforms&TB_iframe=true&width=600&height=550')?>"><?php echo esc_attr__( 'Install The Best WordPress Contact Form Builder', 'download-manager' );?></a>
                                <?php } ?>




                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e("Close", "download-manager"); ?></button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <div class="modal fade" tabindex="-1" role="dialog" id="gdluModal" style="display: none">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title"><i class="far fa-arrow-alt-circle-down color-purple"></i> <?php _e("Generate Download Link", "download-manager"); ?></h4>
                            </div>
                            <div class="modal-body">


                                <div class="panel panel-default">
                                    <div class="panel-heading"><?php _e("Master Download Link:", "download-manager"); ?></div>
                                    <div class="panel-body"><input readonly="readonly" onclick="this.select()"
                                                                   type="text" class="form-control color-purple"
                                                                   style="background: #fdfdfd;font-size: 10px;text-align: center;font-family: monospace;font-weight: bold;"
                                                                   id="mdl"/></div>
                                </div>

                                <div class="panel panel-default ttip" style="opacity: 0.3" title="Available with the pro version only">
                                    <div class="panel-heading">Generate Temporary Download Link</div>
                                    <div class="panel-body">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Usage Limit:</label>
                                                <input disabled="disabled" min="1" class="form-control" id="ulimit" type="number" placeholder="<?php echo __( "Count", "download-manager" ) ?>" value="3">
                                            </div>
                                            <div class="col-md-5">
                                                <label>Expire After:</label>
                                                <div class="input-group">
                                                    <input disabled="disabled" id="exmisd" min="0.5" step="0.5" class="form-control"
                                                           type="number" value="600" style="width: 50%;display: inline-block;">
                                                    <select disabled="disabled" id="expire_multiply" class="form-control wpdm-custom-select" style="min-width: 50%;max-width: 50% !important;display: inline-block;margin-left: -1px">
                                                        <option value="60">Mins</option>
                                                        <option value="3600">Hours</option>
                                                        <option value="86400">Days</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label>&nbsp;</label><br/>
                                                <button  disabled="disabled" class="btn btn-secondary btn-block"
                                                        style="height: 34px" type="button">Generate
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="panel-footer">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?php echo __( "Direct download:", "download-manager" ); ?></div>
                                            <input type="text" id="tmpgdl" value="" class="form-control color-green"
                                                   readonly="readonly" onclick="this.select()"
                                                   style="background: #fdfdfd;font-size: 10px;text-align: center;font-family: monospace;font-weight: bold;"
                                                   placeholder="Click Generate Button">
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?php echo __( "Download page:", "download-manager" ); ?></div>
                                            <input type="text" id="tmpgdlp" value="" class="form-control color-green"
                                                   readonly="readonly" onclick="this.select()"
                                                   style="background: #fdfdfd;font-size: 10px;text-align: center;font-family: monospace;font-weight: bold;"
                                                   placeholder="Click Generate Button">
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

            </div>
            <script>
                jQuery(function ($) {


                    var tdlpid;
                    $('.gdl_action').on('click', function () {
                        tdlpid = $(this).attr('data-pid');
                        $('#mdl').val($(this).attr('data-mdlu'));
                        $('#tmpgdl').val('');
                        $('#tmpgdlp').val('');
                    });

                    $('body').on('click', '.btn-embed', function (){
                        var sc = "[wpdm_package id='{{ID}}']";
                        sc = sc.replace("{{ID}}", $(this).data('pid'));
                        console.log(sc);
                        $('#cpsc').val(sc);
                    });


                });
            </script>

            <?php
        }

        if($pagenow === 'themes.php' || $pagenow === 'theme-install.php'){
            if(!file_exists(ABSPATH.'/wp-content/themes/attire/')) {
                ?>
                <script>
                    jQuery(function ($) {
                        $('.page-title-action').after('<a href="<?php echo admin_url('/theme-install.php?search=attire'); ?>" class="hide-if-no-js page-title-action" style="border: 1px solid #0f9cdd;background: #13aef6;color: #ffffff;">Suggested Theme</a>');
                    });
                </script>
                <?php
            }
        }

    }




}
