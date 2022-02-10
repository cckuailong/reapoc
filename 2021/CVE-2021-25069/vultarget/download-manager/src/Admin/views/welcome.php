<script type="text/javascript" src="<?php echo includes_url();?>/js/jquery/jquery.form.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo WPDM_BASE_URL ?>assets/bootstrap3/css/bootstrap.css" />
<script  src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Overpass:300,400,700' rel='stylesheet' type='text/css'>
<link href="<?php echo WPDM_BASE_URL . 'assets/fontawesome/css/all.css'; ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo plugins_url('/download-manager/assets/css/front3.css'); ?>" />
<style>
    .w3eden .lead,
    .w3eden .btn,
    .w3eden p{
        font-family: 'Overpass', sans-serif;
        font-weight: 300;
    }
    .w3eden .media-body a, .w3eden *:not(.fa){
        font-family: 'Overpass', sans-serif;
    }

    .r{
        font-family: 'Overpass', sans-serif;
        font-weight: 300;
        font-size: 11pt;
    }

    .r b{
        display: block;
        clear: both;
        margin-bottom: 5px;
    }

    .w3eden input{
        padding: 7px;
    }
    #wphead{
        border-bottom:0px;
    }
    #screen-meta-links{
        display: none;
    }
    .wrap{
        margin: 0px;
        padding: 0px;
    }
    #wpbody{
        margin-left: -19px;
    }
    .w3eden select{
        min-width: 150px;
    }
    .w3eden  .media-body b{
        font-size: 11pt;
        margin-bottom: 5px;
    }

    .w3eden .media .btn-success{
        margin-top: 3px;
    }

    .wpdm-loading {
        background: url('<?php  echo plugins_url('download-manager/assets/images/wpdm-settings.png'); ?>') center center no-repeat;
        width: 16px;
        height: 16px;
        /*border-bottom: 2px solid #2a2dcb;*/
        /*border-left: 2px solid #ffffff;*/
        /*border-right: 2px solid #c30;*/
        /*border-top: 2px solid #3dd269;*/
        /*border-radius: 100%;*/

    }

    .w3eden .label-info {
        background-color: #5bc0de;
        display: inline-block;
        line-height: 15px;
        padding: 7px 12px 5px;
        font-family: 'Overpass', sans-serif;
    }

    .w3eden .btn{
        border-radius: 0.2em !important;
    }
    .well{ box-shadow: none !important; background: #FFFFFF !important; } .btn{ border: 0 !important; }

    .w3eden .nav-pills a{
        background: #f5f5f5;
    }

    #addonmodal{ background: rgba(0,0,0,0.7); z-index: 9999; }

    #addonmodal .modal-dialog{
        margin-top: 100px;

    }

    .w3eden .form-control,
    .w3eden .nav-pills a{
        border-radius: 0.2em !important;
        box-shadow: none !important;
        font-size: 9pt !important;
    }

    .wpdm-spin{
        -webkit-animation: spin 2s infinite linear;
        -moz-animation: spin 2s infinite linear;
        -ms-animation: spin 2s infinite linear;
        -o-animation: spin 2s infinite linear;
        animation: spin 2s infinite linear;
    }

    @keyframes "spin" {
        from {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
            -moz-transform: rotate(359deg);
            -o-transform: rotate(359deg);
            -ms-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-moz-keyframes spin {
        from {
            -moz-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -moz-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-webkit-keyframes "spin" {
        from {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-ms-keyframes "spin" {
        from {
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -ms-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-o-keyframes "spin" {
        from {
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -o-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    .panel-heading h3.h{
        font-size: 11pt;
        font-weight: 700;
        margin: 0;
        padding: 5px 10px;
        font-family: 'Open Sans';
    }

    .panel-heading .btn.btn-primary{
        margin-top: -4px;
        margin-right: -6px;
        border-radius: 3px;
        border:1px solid rgba(255,255,255,0.8);
        -webkit-transition: all 400ms ease-in-out;
        -moz-transition: all 400ms ease-in-out;
        -o-transition: all 400ms ease-in-out;
        transition: all 400ms ease-in-out;
    }

    .panel-heading .btn.btn-primary:hover{
        margin-top: -4px;
        margin-right: -6px;
        border-radius: 3px;
        border:1px solid rgba(255,255,255,1);

    }

    .alert-info {
        background-color: #DFECF7 !important;
        border-color: #B0D1EC !important;
    }

    ul.nav li a:active,
    ul.nav li a:focus,
    ul.nav li a{
        outline: none !important;
    }

    .w3eden .nav-pills li.active a,
    .btn-primary,
    .w3eden .panel-primary > .panel-heading{
        background-image: linear-gradient(to bottom, #2081D5 0px, #1B6CB2 100%) !important;
    }
    .w3eden .panel-default > .panel-heading {
        background-image: linear-gradient(to bottom, #F5F5F5 0px, #E1E1E1 100%);
        background-repeat: repeat-x;
    }

    #modalcontents .wrap h2{ display: none; }

</style>

<div class="wrap w3eden">


    <div class="container-fluid" style="margin-top: 10px;max-width: 1200px">
         <div class="row">
             <div class="col-md-12">
                 <div class="well text-center" style="padding: 30px 95px">
                     <img src="<?php echo WPDM_BASE_URL; ?>assets/images/wpdm-welcome.png" style="max-width: 100%" />
                 </div>

             </div>
             <?php //if(!function_exists('wpdm_tinymce')){ ?>
             <div class="col-md-12 lead text-center" style="padding: 20px 0">
                 <h3>Recommended Plugins</h3>
                 Let's install the following add-ons to make your WordPress Download Manager more awesome<br/></div>
                 <div class="col-md-4">
                     <div class="panel panel-default">
                         <div  class="panel-body">
                             <div class="media">

                                 <div class="media-body">
                                     <b style="display: block;font-size: 15pt;margin-bottom: 10px"><a target="_blank" href="https://www.wpdownloadmanager.com/download/gutenberg-blocks/">Gutenberg Blocks</a></b>
                                     <p style="font-size: 12pt;margin-bottom: 0">Use gutenberg blocks to embed packages and categories</p>
                                 </div>
                             </div>
                         </div>
                         <div style="line-height: 30px;" class="panel-footer">
                             <a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=wpdm-gutenberg-blocks&TB_iframe=true&width=600&height=550')?>" class="btn btn-primary btn-block thickbox open-plugin-details-modal">Install</a>
                         </div>
                     </div>
                 </div>

             <div class="col-md-4">
                 <div class="panel panel-default">
                     <div  class="panel-body">
                         <div class="media">

                             <div class="media-body">
                                 <b style="display: block;font-size: 15pt;margin-bottom: 10px"><a target="_blank" href="https://www.wpdownloadmanager.com/download/premium-package-wordpress-digital-store-solution/">Premium Packages</a></b>
                                 <p style="font-size: 12pt;margin-bottom: 0">Easy and secure way to sell your digital products.</p>
                             </div>
                         </div>
                     </div>
                     <div style="line-height: 30px;" class="panel-footer">
                         <a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=wpdm-premium-packages&TB_iframe=true&width=600&height=550')?>" class="btn btn-primary btn-block thickbox open-plugin-details-modal">Install</a>
                     </div>
                 </div>
             </div>

             <div class="col-md-4">
                 <div class="panel panel-default">
                     <div  class="panel-body">
                         <div class="media">

                             <div class="media-body">
                                 <b style="display: block;font-size: 15pt;margin-bottom: 10px"><a target="_blank" href="https://wpliveforms.com">Live Forms - The best form builder</a></b>
                                 <p style="font-size: 12pt;margin-bottom: 0">We specially request you to try this, we assure you, you will be more than happy!</p>
                             </div>
                         </div>
                     </div>
                     <div style="line-height: 30px;" class="panel-footer">
                         <a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=liveforms&TB_iframe=true&width=600&height=550')?>" class="btn btn-success btn-block thickbox open-plugin-details-modal">Install</a>
                     </div>
                 </div>
             </div>




            <div style="clear: both"></div>


             <?php //} ?>

             <div class="col-md-6">
                 <div class="well">
                     <div class="media">
                         <div class="pull-right">
                             <a href="https://www.wpdownloadmanager.com/downloads/free-add-ons/" target="_blank" class="btn btn-info">Explore Free Add-ons <i class="fa fa-angle-double-right"></i></a>
                         </div>
                         <div class="media-body">
                             <b>Free Add-ons</b><br/>
                             There are more free add-ons
                         </div> </div>
                 </div>
             </div>

             <div class="col-md-6">
                 <div class="well">
                     <div class="media">
                         <div class="pull-right">
                             <a href="https://wpattire.com" target="_blank" class="btn btn-info">Get WP Attire <i class="fa fa-angle-double-right"></i></a>
                         </div>
                         <div class="media-body">
                             <b>All-in-One WordPress Theme</b><br/>
                             You search for the best theme ends here
                         </div> </div>
                 </div>

             </div>

             <div class="col-md-12 lead">
                 <hr/>
                 <h3>What's New?</h3>
                 What new with WordPress Download Manager v3.2:
             </div>

             <div class="col-md-4 r">

                     <b>Asset Manager</b>
                     Rebuilt asset manager with new options and ui.

             </div>
             <div class="col-md-4 r">
                 <b>Refined Codebase</b>
                 Recoded from ground with lots of improvements.

             </div>
             <div class="col-md-4 r">

                 <b>More Features</b>
                 Improved admin UI and front-end short-code templates, one click updates for wpdm add-ons, improved gutenberg compatibility.
             </div>


             <div class="col-md-12 lead">
                 <hr/>
                 Lets start: Admin Menu <i class="fa fa-angle-double-right"></i> <a href="<?php echo admin_url('edit.php?post_type=wpdmpro'); ?>">Downloads</a> <i class="fa fa-angle-double-right"></i> <a href="<?php echo admin_url('post-new.php?post_type=wpdmpro'); ?>">Add New</a>
             </div>

         </div>

    </div>
    <div class="modal fade" id="addonmodal" tabindex="-1" role="dialog" aria-labelledby="addonmodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add-On Installer</h4>
                </div>
                <div class="modal-body" id="modalcontents">
                    <i class="fa fa-spinner fa-spin"></i> Please Wait...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <a type="button" id="prcbtn" target="_blank" href="https://www.wpdownloadmanager.com/cart/" class="btn btn-success" style="display: none" onclick="jQuery('#addonmodal').modal('hide')">Checkout</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    jQuery(function(){


        jQuery(".btn-install, .btn-purchase").click(function(){
            jQuery('#modalcontents').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
        });
        jQuery('#addonmodal').on('shown.bs.modal', function (e) {
            if(jQuery(e.relatedTarget).hasClass('btn-install')){
                jQuery('.modal-dialog').css('width','500px');
                jQuery('.modal-footer .btn-danger').html('Close');
                jQuery('#modalcontents').css('padding','20px').css('background','#ffffff');
                jQuery.post(ajaxurl,{action:'wpdm-install-addon', addon: e.relatedTarget.rel}, function(res){
                    notice = "<div class='alert alert-info'>For any reason, if auto installation failed, close this popup, click on add-on title, download the add-on from our site, then install manually as you do for regular plugins.</div>"
                    jQuery('#modalcontents').html(res.replace('Return to Plugin Installer','')+notice);
                })
            }

            if(jQuery(e.relatedTarget).hasClass('btn-purchase')){
                jQuery('.modal-dialog').css('width','800px');
                jQuery('.modal-footer').css('margin',0);
                jQuery('.modal-footer .btn-danger').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
                jQuery('#modalcontents').css('padding',0).css('background','#f2f2f2').html("<iframe onload=\"jQuery('.modal-footer .btn-danger').html('Continue Shopping...');jQuery('#prcbtn').show();\" style='width: 100%;padding-top: 20px; background: #f2f2f2;height: 300px;border: 0' src='https://www.wpdownloadmanager.com/?addtocart="+e.relatedTarget.rel+"'></iframe>");
            }
        })


    });
</script>


