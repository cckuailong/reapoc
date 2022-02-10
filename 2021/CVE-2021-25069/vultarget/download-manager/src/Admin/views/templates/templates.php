<?php

if(!defined("ABSPATH")) die();
?>

<div class="wrap w3eden">


<?php

require_once __DIR__.'/header.php';



    //List link, page and emaol templates
    require_once __DIR__.'/list-templates.php';

    require_once __DIR__.'/email-template-settings.php';

 ?>


</div>



<style>
    #tagstable td{ vertical-align: top; }
    div.notice, .updated{ display: none; }
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

    /* Page content */
    .w3econtent {
        padding: 0;
        margin-bottom: -4px;
    }
    .btn-group .btn-status input{
        display: none;
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
            $('#preview').attr('src', 'edit.php?action=email_template_preview&id=user-signup&etmpl='+$(this).val()+"&__empnonce=<?php echo wp_create_nonce(WPDM_PRI_NONCE); ?>");
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



