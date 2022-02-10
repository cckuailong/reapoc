<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:20
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <a href="#" data-toggle="modal" data-target="#newtagmodal" class="btn btn-success pull-right btn-xs"><i class="fa fa-plus-circle"></i> <?php _e( "Add New Tag", "download-manager" ); ?></a>
        <?php echo  esc_attr__( 'Custom Template Tags', WPDM_TEXT_DOMAIN ); ?>
    </div>
    <table class="table table-striped" id="tagstable">
        <thead>
        <tr>
            <th><?php _e( "Tag", "download-manager" ) ?></th>
            <th><?php _e( "Value", "download-manager" ) ?></th>
            <th><?php _e( "Action", "download-manager" ) ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="3">
                <?php wpdmpro_required(); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="modal fade" id="newtagmodal" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
    <div class="modal-dialog">

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
                    <button disabled="disabled" type="submit" id="newtagformsubmit" style="width: 180px" class="btn btn-success btn-lg"><?php echo __( "Save Tag", "download-manager" ) ?></button>
                </div>
            </div>

    </div>
</div>
