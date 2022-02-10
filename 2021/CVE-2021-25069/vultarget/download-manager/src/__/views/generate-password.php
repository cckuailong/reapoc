<?php if(!defined('ABSPATH')) die(); ?>

<div class="w3eden">

    <div class="modal fade" tabindex="-1" role="dialog" id="generatepass">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="pfs panel panel-default card card-default" style="margin: 15px 0">
                        <div class="panel-heading card-header"><b><?php _e( "Select Options" , "download-manager" ); ?></b></div>
                        <div class="panel-body card-body">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <b><?php _e( "Number of passwords:" , "download-manager" ); ?></b><Br/>
                                    <input class="form-control" type="number" id='pcnt' value="">
                                </div><div class="form-group">
                                    <b><?php _e( "Number of chars for each password:" , "download-manager" ); ?></b><Br/>
                                    <input  class="form-control" type="number" id='ncp' value="">
                                </div>
                            </div>
                            <div  class="col-md-6">
                                <b><?php _e( "Valid Chars:" , "download-manager" ); ?></b><br />
                                <label><input type="checkbox" id="ls" value="1" checked="checked"> <?php _e( "Small Letters" , "download-manager" ); ?></label><br/>
                                <label><input type="checkbox" id="lc" value="1"> <?php _e( "Capital Letters" , "download-manager" ); ?></label><br/>
                                <label><input type="checkbox" id="nm" value="1"> <?php _e( "Numbers" , "download-manager" ); ?></label><br/>
                                <label><input type="checkbox" id="sc" value="1"> <?php _e( "Special Chars" , "download-manager" ); ?></label><br/>
                            </div>
                        </div>
                        <div class="panel-footer card-footer">
                            <input type="button" id="gpsc" class="btn btn-success" value="Generate" />
                        </div>
                    </div>

                    <div class="pfs panel panel-default card card-default">
                        <div class="panel-heading card-header"><b><?php _e( "Generated Passwords" , "download-manager" ); ?></b></div>
                        <div class="panel-body card-body">
                            <textarea id="ps" class="form-control"></textarea>
                        </div>
                        <div class="panel-footer card-footer">
                            <input type="button" id="pins" data-target="#<?php echo wpdm_query_var('id'); ?>" class="btn btn-primary" value="<?php _e( "Insert Password(s)" , "download-manager" ); ?>" />
                        </div>
                    </div>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>



