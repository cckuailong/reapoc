

<div class="wrap w3eden">

<div class="panel panel-default" id="wpdm-wrapper-panel">
<div class="panel-heading">
<b><i class="fa fa-magic color-purple"></i> &nbsp; <?php echo __( "Templates" , "download-manager" ); ?></b>
    <div class="pull-right">
<a href="edit.php?post_type=wpdmpro&page=templates&_type=page&task=NewTemplate" class="btn btn-sm btn-info"><i class="fa fa-file"></i> <?php echo __( "Create Page Template" , "download-manager" ); ?></a> <a href="edit.php?post_type=wpdmpro&page=templates&_type=link&task=NewTemplate" class="btn btn-sm btn-primary"><i class="fa fa-link"></i> <?php echo __( "Create Link Template" , "download-manager" ); ?></a>
    </div>
    <div style="clear: both"></div>
</div>
    <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
    <li <?php if(!isset($_GET['_type'])||$_GET['_type']=='link'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=link" id="link"><?php _e( "Link Templates" , "download-manager" ); ?></a></li>
    <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='page'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=page" id="page"><?php _e( "Page Templates" , "download-manager" ); ?></a></li>
    <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='email'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=email" id="email"><?php _e( "Email Templates" , "download-manager" ); ?></a></li>
    <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='custom-tags'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=custom-tags" id="custom-tags"><?php _e( "Custom Tags" , "download-manager" ); ?></a></li>
    </ul>
<div class="tab-content panel-body">
<?php if(wpdm_query_var('_type') !== 'custom-tags'){ ?>
    <?php if(!isset($_GET['_type']) || $_GET['_type']!='email'){ ?>
        <blockquote  class="alert alert-info" style="margin-bottom: 10px">
            <?php echo __( "Pre-designed templates can't be deleted or edited from this section. But you can clone any of them and edit as your own. If you seriously want to edit any pre-designed template you have to edit those directly edting php files at /download-manager/templates/ dir" , "download-manager" ); ?>
        </blockquote>
    <?php } ?>


    <?php
    $ttype = isset($_GET['_type'])?wpdm_query_var('_type'):'link';
    if($ttype != 'email'){
        $ctpls = WPDM\Admin\Menu\Templates::Dropdown(array('data_type' => 'ARRAY', 'type' => $ttype));
        $ctemplates = maybe_unserialize(get_option("_fm_{$ttype}_templates",true));
    if(is_array($ctemplates))
        $ctemplates = array_keys($ctemplates);
    if(!is_array($ctemplates)) $ctemplates = array();
    $tplstatus = maybe_unserialize(get_option("_fm_{$ttype}_template_status"));

    foreach($ctpls as $ctpl => $title){
        $tplid = str_replace(".php","",$ctpl);
        $status = isset($tplstatus[$tplid])?(int)$tplstatus[$tplid]:1;
    ?>

        <div class="col-md-3">
            <div class="panel panel-default" id="template-<?php echo $ttype; ?>-<?php echo $ctpl; ?>">
                <div class="panel-body">
                    <div class="btn-group template-status"  data-toggle="buttons">
                        <label title="<?php echo __( "Activate Template", "download-manager" ) ?>" class="btn btn-<?php echo $status === 1?'success active':'secondary'; ?> btn-sm btn-status <?php echo str_replace(".php","",$ctpl); ?>" data-value="1" data-id="<?php echo str_replace(".php","",$ctpl); ?>"><input type="radio" <?php checked($status,1); ?> name="<?php echo $ctpl; ?>-status" value="1"/><i class="fa fa-check"></i></label><label title="<?php echo __( "Disable Template", "download-manager" ) ?>" class="btn btn-<?php echo $status === 0?'danger active':'secondary'; ?> btn-sm btn-status <?php echo str_replace(".php","",$ctpl); ?>" data-value="0" data-id="<?php echo str_replace(".php","",$ctpl); ?>"><input type="radio" name="<?php echo $ctpl; ?>-status" <?php checked($status,0); ?> value="0"/><i class="fa fa-times"></i></label>
                    </div>
                    <img src="<?php echo WPDM_BASE_URL.'assets/template-previews/link-template-audio.png' ?>" alt="<?php echo $title; ?>" />
                </div>
                <div class="panel-footer">
                    <input class="form-control input-tplid" type="text" readonly="readonly" onclick="this.select()" value="<?php echo $tplid; ?>" />
                </div>
                <div class="panel-footer">
                    <?php echo $title; ?>
                </div>
                <div class="panel-footer">
                    <a data-toggle="modal" href="#" data-href="admin-ajax.php?action=template_preview&_type=<?php echo $ttype; ?>&template=<?php echo $ctpl; ?>" data-target="#preview-modal" rel="<?php echo $ctpl; ?>" class="template_preview btn btn-sm btn-success"><i class="fa fa-desktop"></i> Preview</a>
                    <?php if(!in_array($ctpl, $ctemplates)){ ?>
                        <a href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=NewTemplate&clone=<?php echo $ctpl; ?>" class="btn btn-sm btn-primary"><i class="fa fa-copy"></i> <?php echo __( "Clone" , "download-manager" ); ?></a>
                    <?php } else { ?>
                        <a href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=EditTemplate&tplid=<?php echo $ctpl; ?>" class="btn btn-sm btn-info"><i class="fas fa-pencil-alt"></i> <?php echo __( "Edit" , "download-manager" ); ?></a>
                        <a data-ttype="<?php echo $ttype; ?>" data-tplid="<?php echo $ctpl; ?>" href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=DeleteTemplate&tplid=<?php echo $ctpl; ?>" class="submitdelete delete-template btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> <?php echo __( "Delete" , "download-manager" ); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>

    <?php
    }} else {
        $templates = \WPDM\__\Email::templates();
    foreach($templates as $ctpl => $template){
        ?>
        <tr valign="top" class="author-self status-inherit" id="post-8">
            <td class="column-icon media-icon" style="text-align: left;">
                <?php echo $template['label']; ?> ( <?php _e( "To:" , "download-manager" ); ?> <?php echo ucfirst($template['for']); ?> )

            </td>
            <td>
                <?php echo $ctpl; ?>
            </td>
            <td style="text-align: right">

    <a href="edit.php?post_type=wpdmpro&page=templates&_type=email&task=EditEmailTemplate&id=<?php echo $ctpl; ?>" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i> <?php echo __( "Edit" , "download-manager" ); ?></a>

            </td>


        </tr>
    <?php
    }}
    ?>




    <?php if($ttype == 'email'){ ?>
    <form method="post" id="emlstform">
        <div class="panel panel-default">
            <div class="panel-heading">Email Settings</div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php _e( "Email Template" , "download-manager" ); ?>
                            <select name="__wpdm_email_template" class="form-control wpdm-custom-select" style="width: 200px" id="etmpl">
                                <?php
                                $eds = \WPDM\__\FileSystem::scanDir(WPDM_BASE_DIR.'email-templates');
                                $__wpdm_email_template = get_option('__wpdm_email_template', "default.html");
                                $__wpdm_email_setting = maybe_unserialize(get_option('__wpdm_email_setting'));
                                foreach ($eds as $file) {
                                    if(strstr($file, ".html")) {
                                        ?>
                                        <option value="<?php echo basename($file); ?>" <?php selected($__wpdm_email_template, basename($file)); ?> ><?php echo ucfirst(str_replace(".html", "", basename($file))); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <?php _e( "Logo URL" , "download-manager" ); ?>
                            <?php echo wpdm_media_field(array('placeholder' => __("Logo URL" , "download-manager"), 'name' => '__wpdm_email_setting[logo]', 'id' => 'logo-url', 'value' => (isset($__wpdm_email_setting['logo'])?$__wpdm_email_setting['logo']:''))); ?>
                        </div>
                        <div class="form-group">
                            <?php _e( "Banner/Background Image URL" , "download-manager" ); ?>
                            <?php echo wpdm_media_field(array('placeholder' => __("Banner/Background Image URL" , "download-manager"), 'name' => '__wpdm_email_setting[banner]', 'id' => 'banner-url', 'value' => (isset($__wpdm_email_setting['banner'])?$__wpdm_email_setting['banner']:''))); ?>
                            <div class="xbubble" style="margin-top: 5px;box-shadow: none;z-index: 999">
                                <img class="bselect" src="https://wpdmcdn.s3.amazonaws.com/emails/brush.jpg" style="height: 32px;margin: 2px" />
                                <img class="bselect" src="https://wpdmcdn.s3.amazonaws.com/emails/a.jpg" style="height: 32px;margin: 2px" />
                                <img class="bselect" src="https://wpdmcdn.s3.amazonaws.com/emails/crain.jpg" style="height: 32px;margin: 2px" />
                                <img class="bselect" src="https://wpdmcdn.s3.amazonaws.com/emails/c.jpg" style="height: 32px;margin: 2px" />
                                <img class="bselect" src="https://wpdmcdn.s3.amazonaws.com/emails/z.jpg" style="height: 32px;margin: 2px" />
                                <img class="bselect" src="https://wpdmcdn.s3.amazonaws.com/emails/oilpaint.jpg" style="height: 32px;margin: 2px" />
                            </div>
                        </div>
                        <div class="form-group">
                            <?php _e( "Footer Text" , "download-manager" ); ?>
                            <textarea name="__wpdm_email_setting[footer_text]" class="form-control"><?php echo isset($__wpdm_email_setting['footer_text'])?stripslashes($__wpdm_email_setting['footer_text']):'';?></textarea>
                        </div>
                        <div class="form-group">
                            <?php _e( "Facebook Page URL" , "download-manager" ); ?>
                            <input type="text" name="__wpdm_email_setting[facebook]" value="<?php echo isset($__wpdm_email_setting['facebook'])?($__wpdm_email_setting['facebook']):'';?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <?php _e( "Twitter Profile URL" , "download-manager" ); ?>
                            <input type="text" name="__wpdm_email_setting[twitter]" value="<?php echo isset($__wpdm_email_setting['twitter'])?$__wpdm_email_setting['twitter']:'';?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <?php _e( "Youtube Profile URL" , "download-manager" ); ?>
                            <input type="text" name="__wpdm_email_setting[youtube]" value="<?php echo isset($__wpdm_email_setting['youtube'])?$__wpdm_email_setting['youtube']:'';?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="w3econtainer">
                            <div class="w3erow">
                                <div class="w3ecolumn w3eleft">
                                    <span class="w3edot" style="background:#ED594A;"></span>
                                    <span class="w3edot" style="background:#FDD800;"></span>
                                    <span class="w3edot" style="background:#5AC05A;"></span>
                                </div>
                                <div class="w3ecolumn w3emiddle">
                                    /email-templates/<span id="etplname"></span>
                                </div>
                                <div class="w3ecolumn w3eright">
                                    <div style="float:right">
                                        <span class="w3ebar"></span>
                                        <span class="w3ebar"></span>
                                        <span class="w3ebar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="w3econtent">
                                <iframe style="margin: 0;width: 100%;height: 550px;border-radius: 3px" id="preview" src="edit.php?action=email_template_preview&id=user-signup&etmpl=<?php echo $__wpdm_email_template; ?>">

                                </iframe>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-primary" id="emsbtn" style="width: 180px;"><i class="fas fa-hdd"></i> <?php _e( "Save Changes" , "download-manager" ); ?></button>
            </div>
        </div>
    </form>
    <?php } ?>
<?php } else { ?>
    <div style="text-align: right;padding: 10px;"><a href="#" data-toggle="modal" data-target="#newtagmodal" class="btn btn-success"><i class="fa fa-plus-circle"></i> <?php _e( "Add New Tag", "download-manager" ); ?></a></div>
    <table class="table table-striped" id="tagstable">
        <thead>
        <tr>
            <th><?php _e( "Tag", "download-manager" ) ?></th>
            <th><?php _e( "Value", "download-manager" ) ?></th>
            <th><?php _e( "Action", "download-manager" ) ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        $tags_dir = $upload_dir.'/wpdm-custom-tags/';
        if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
        $custom_tags = scandir($tags_dir);
        foreach ($custom_tags as $custom_tag){
            if(strstr($custom_tag, '.tag')) {
                $content = file_get_contents($tags_dir.$custom_tag);
                $custom_tag = str_replace(".tag", "", $custom_tag);
                ?>
                <tr id="row_<?php echo $custom_tag; ?>">
                    <td>[<?php echo $custom_tag; ?>]</td>
                    <td><pre style="background: #ffffff;border-radius: 3px;font-size: 10px"><?php echo htmlspecialchars(stripslashes($content)); ?></pre></td>
                    <td style="width: 220px">
                        <a href="#" class="btn btn-info tag-edit" data-tag="<?php echo $custom_tag; ?>"><?php _e( "Edit", "download-manager" ); ?></a>
                        <a href="#" class="btn btn-danger tag-delete" data-tag="<?php echo $custom_tag; ?>"><?php _e( "Delete", "download-manager" ); ?></a>
                    </td>
                </tr>
                <?php
            }
        } ?>
        </tbody>
    </table>

<?php } ?>
    </div>
    </div>

    <div class="modal fade" id="newtagmodal" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" id="newtagform">
                <input type="hidden" name="action" value="wpdm_save_custom_tag">
                <input type="hidden" name="__ctxnonce" value="<?php echo wp_create_nonce(NONCE_KEY); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e( "New Tag" , "download-manager" ); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" id="tag_name" name="ctag[name]" class="form-control input-lg" placeholder="<?php echo __( "Tag Name", "download-manager" ) ?>" />
                    </div>
                    <div class="form-group">
                        <textarea id="tag_value" placeholder="<?php echo __( "Tag Value", "download-manager" ) ?>" class="form-control" style="height: 100px" name="ctag[value]"></textarea>
                        <em class="note"><?php echo __( "No php code, only text, html, css and js", "download-manager" ); ?></em>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="newtagformsubmit" style="width: 180px" class="btn btn-success btn-lg"><?php echo __( "Save Tag", "download-manager" ) ?></button>
                </div>
            </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e( "Template Preview" , "download-manager" ); ?></h4>
                </div>
                <div class="modal-body" id="preview-area">

                </div>
                <div class="modal-footer text-left" style="text-align: left">
                    <div class='alert alert-info'><?php _e( "This is a preview, original template color scheme may look little different, but structure will be same" , "download-manager" ); ?></div>
                </div>
            </div>
        </div>
    </div>


<style>
    #tagstable td{ vertical-align: top; }
    div.notice, .updated{ display: none; }
    img{ max-width: 100%; }
    .input-tplid{ background: #ffffff !important; }
    .xbubble
    {
        position: relative;
        padding: 10px;
        background: #eeeeee;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    .xbubble:after
    {
        content: '';
        position: absolute;
        border-style: solid;
        border-width: 0 10px 10px;
        border-color: #eeeeee transparent;
        display: block;
        width: 0;
        z-index: 1;
        top: -10px;
        left: 17px;
    }
    .w3ebselect{
        padding: 2px;
        border-radius: 2px;
        background: #ffffff;
        cursor: pointer;
    }

    .w3econtainer {
        border: 3px solid #333;
        border-radius: 4px;
        background: #333;
    }

    /* Container for columns and the top "toolbar" */
    .w3erow {
        padding: 7px 10px 10px;
        background: #333;
        color: #8db4b6;
        letter-spacing: 0.5px;
        font-size: 10px;
        font-weight: 400;
    }

    /* Create three unequal columns that floats next to each other */
    .w3ecolumn {
        float: left;
    }

    .w3eleft {
        width: 60px;
    }

    .w3eright {
        width: 10%;
    }

    .w3emiddle {
        width: calc(90% - 60px);
    }

    /* Clear floats after the columns */
    .w3erow:after {
        content: "";
        display: table;
        clear: both;
    }

    /* Three dots */
    .w3edot {
        margin-top: 4px;
        height: 12px;
        width: 12px;
        background-color: #bbb;
        border-radius: 50%;
        display: inline-block;
    }

    /* Style the input field */
    input[type=text].w3e {
        width: 100%;
        border-radius: 3px;
        border: none;
        background-color: white;
        margin-top: -8px;
        height: 25px;
        color: #666;
        padding: 5px;
    }

    /* Three bars (hamburger menu) */
    .w3ebar {
        width: 17px;
        height: 3px;
        background-color: #aaa;
        margin: 3px 0;
        display: block;
    }

    .panel-body{
        position: relative;
    }
    .panel-body .btn-group.template-status{
        position: absolute;
        right: 10px;
        top: 10px;
    }


    /* Page content */
    .w3econtent {
        padding: 0;
        margin-bottom: -4px;
    }

</style>
<script>



    jQuery(function($){
        $('.bselect').click(function(){
            $('#banner-url').val(this.src);
        });
        <?php if(isset($ttype)) { ?>
        $('.template_preview').click(function(){
            $('#preview-area').html("<i class='fa fa-spin fa-spinner'></i> Loading Preview...").load($(this).attr('data-href'));
        });
        $('#etmpl').on('change', function () {
            $('#preview').attr('src', 'edit.php?action=email_template_preview&id=user-signup&etmpl='+$(this).val());
            $('#etplname').html($(this).val());
        });
        $('#etplname').html($('#etmpl').val());
        $('#emlstform').submit(function (e) {
            e.preventDefault();
            $('#emsbtn').html('<i class="fa fa-sync fa-spin"></i> <?php _e( "Saving..." , "download-manager" ); ?>');
            $(this).ajaxSubmit({
                url: ajaxurl+"?action=wpdm_save_email_setting",
                success: function (res) {
                    $('#emsbtn').html('<i class="fas fa-hdd"></i> <?php _e( "Save Changes" , "download-manager" ); ?>');
                    document.getElementById('preview').contentDocument.location.reload(true);
                }
            });
        });

        $('.btn-status').on('click', function () {
            var v = $(this).data('value');
            var c = '.'+$(this).data('id');
            var $this = this;
            $.post(ajaxurl, {action: 'update_template_status', template: $(this).data('id'), type: '<?php echo $ttype; ?>', status: v}, function (res) {
                $(c).removeClass('btn-danger').removeClass('btn-success').addClass('btn-secondary');
                if(v==1)
                    $($this).addClass('btn-success').removeClass('btn-secondary');
                else
                    $($this).addClass('btn-danger').removeClass('btn-secondary');
            });


        });


        $('.delete-template').on('click', function (e) {
            if(!confirm('<?php _e( "Are you sure?" , "download-manager" ); ?>')) return false;
            e.preventDefault();
            var rowid = '#template-'+$(this).data('ttype')+"-"+$(this).data('tplid');
            $(this).html('<i class="fa fa-times fa-spin"></i> Delete');
            $.get(ajaxurl, {action: 'wpdm_delete_template', ttype: $(this).data('ttype'), tplid: $(this).data('tplid')}, function (res) {
                $(rowid).remove();
            });
        });
        <?php } ?>
        $('#newtagform').submit(function (e) {
            e.preventDefault();
            var obtnlbl = $('#newtagformsubmit').html();
            $('#newtagformsubmit').html("<i class='fa fa-sun fa-spin'></i>").attr('disabled', 'disabled');
            $(this).ajaxSubmit({
                url: ajaxurl,
                resetForm: true,
                success: function (response) {
                    $('#newtagformsubmit').html(obtnlbl).removeAttr('disabled');
                    $('#row_'+response.name).hide();
                    $('#tagstable tbody').append(
                        '                <tr id="row_'+response.name+'">\n' +
                        '                    <td>['+response.name+']</td>\n' +
                        '                    <td><pre  style="background: #ffffff;border-radius: 3px;font-size: 10px">'+response.value+'</pre></td>\n' +
                        '                    <td style="width: 220px">\n' +
                        '                        <a href="#" class="btn btn-info tag-edit" data-tag="'+response.name+'"><?php _e("Edit", "download-manager"); ?></a>\n' +
                        '                        <a href="#" class="btn btn-danger tag-delete" data-tag="'+response.name+'"><?php _e("Delete", "download-manager"); ?></a>\n' +
                        '                    </td>\n' +
                        '                </tr>'
                    );
                    $('#newtagmodal').modal('hide');
                }
            });
        });
        $('body').on('click', '.tag-edit', function () {
            $('#newtagmodal').modal('show');
            WPDM.blockUI('#newtagform');
            $.get(ajaxurl, {tag: $(this).data('tag'), action: 'wpdm_edit_custom_tag'}, function (response) {
                $('#tag_name').val(response.name);
                $('#tag_value').val(response.value);
                WPDM.unblockUI('#newtagform');
            })
        });
        $('body').on('click', '.tag-delete', function (e) {
            e.preventDefault();
            if(!confirm('<?php echo __( "Are you sure?", "download-manager" ); ?>')) return false;
            var tag = $(this).data('tag');
            $.get(ajaxurl, {tag: $(this).data('tag'), action: 'wpdm_delete_custom_tag'}, function (response) {
                $('#row_'+tag).hide();
            })
        });
    });

</script>
</div>



