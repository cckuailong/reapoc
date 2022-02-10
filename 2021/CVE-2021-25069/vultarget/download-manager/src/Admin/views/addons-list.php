<style>

    .note{
        color: #888888;

    }
    input{
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
    select{
        min-width: 150px;
    }



    .w3eden .panel-footer{
        line-height: 22px;
    }
    .w3eden .btn-group.btn-group-xs .btn{
        font-size: 9px;
        padding: 2px 7px;
        line-height: 18px;
        height: 22px;
    }
    .well{ box-shadow: none !important; background: #FFFFFF !important; }

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

    #modalcontents .wrap h2{ display: none; }

    .list-group-item:hover{
        background: #fafafa;
    }
    .panel-default .panel-body{
        font-size: 9pt;
    }
    .panel-default .panel-footer{
        font-size: 8pt;
    }
    .addonlist.panel-default .panel-body a{
        white-space: nowrap;
        overflow: hidden;
        display: inline-block;
        max-width: 98%;
        text-overflow: ellipsis;
    }
    .list-group-item{
        border-radius: 3px !important;

    }
    .w3eden a:hover,
    .w3eden a{
        text-decoration: none !important;
    }
    #filter-mods a{
        font-size: 8pt;
        padding: 5px 15px;
    }
    .updated{
        display: none;
    }
</style>
<link rel="stylesheet" href="<?php echo  WPDM_BASE_URL ?>assets/css/settings-ui.css" />
<div class="wrap w3eden">
    <div id="wpdm-admin-page-header">
        <div id="wpdm-admin-main-header">
            <div class="media">
                <!--<div class="pull-right">
                    <button class="btn btn-primary btn-simple btn-sm ttip" title="" id="reload" data-original-title="Reload"><i class="fa fa-sync"></i></button>
                </div>-->
                <div class="media-body">
                    <div class="media">
                        <div class="media-body">
                            <h3>
                                <i class="fa fa-plug color-purple"></i> WordPress Download Manager Add-ons
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="wpdm-admin-sub-header" class="text-center">
            <?php if(is_array($cats)){ ?>
            <ul class="nav nav-pills" id="filter-mods"><li class="active"><a href="#all" rel="all">All Add-Ons</a></li>

                <?php
                foreach($cats as $cat){
                    echo "<li><a href='#' rel='{$cat->slug}'>{$cat->name}</a></li>";
                }
                ?>
            </ul>
            <?php } ?>
        </div>
    </div>

    <div class="container-fluid wpdm-admin-page-content">
        <div class="row" id="addonlist">

            <div class="container">
            <div  class="row">
            <?php
            if(is_array($data)) {
                foreach ($data as $package) {
                    //wppmdd($package);
                    $files = (array)$package->files;
                    $file = str_replace(".zip", "", array_shift($files));
                    $file = explode("/", $file);
                    $file = end($file);
                    $plugininfo = wpdm_plugin_data($file);

                    $linklabel = ($plugininfo) ? '<i class="fa fa-sync"></i> Re-Install' : '<i class="fa fa-plus-circle"></i> Install';
                    ?>
                    <div class="col-xl-3 col-md-4 col-sm-6 col-xs-12 all <?php echo implode(" ", (array)$package->categories); ?>">

                        <div class="addonlist panel panel-default">
                            <div class="panel-body" style="height: 90px">
                                <div class="media">
                                    <!--div class="pull-left">
                                        <img style="width: 64px" src="<?php //echo $package->thumbnail; ?>" alt="Thumb"/>
                                    </div-->
                                    <div class="media-body">
                                        <b><a target="_blank" title="<?php echo $package->post_title; ?>" class="ttip"
                                              href="<?php echo $package->link; ?>"><?php echo $package->post_title; ?></a></b><br/>
                                        <?php echo $package->excerpt; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if ($package->price > 0) { ?>
                                    <div class="btn-group btn-group-xs">
                                        <a class="btn btn-info btn-purchase" data-toggle="modal" data-backdrop="true"
                                           data-target="#addonmodal" href="#" rel="<?php echo $package->ID; ?>"
                                           style="border: 0;border-radius: 2px;"><i class="fa fa-shopping-cart"></i>
                                            &nbsp;Buy Now</a><span
                                                class="btn btn-secondary"><?php echo $package->currency . $package->price; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="btn-group btn-group-xs">
                                        <a class="btn btn-success" target="_blank" href="<?php echo $package->link; ?>"><?php echo $linklabel; ?></a><span
                                                class="btn btn-secondary">Free</span>
                                    </div>
                                <?php } ?>
                                <span class="note pull-left"><i class="fa fa-server"
                                                                aria-hidden="true"></i> &nbsp;<?php echo $package->version; ?></span>
                            </div>
                        </div>


                    </div>
                    <?php
                }
            if(is_array($data)) {
            } else {
                \WPDM\__\Session::clear('wpdm_addon_store_data');
                \WPDM\__\Session::clear('wpdm_addon_store_cats');
            }
            ?>

        </div>
            </div>
    </div>

</div>
<?php } else {
    \WPDM\__\Session::clear('wpdm_addon_store_data');
    \WPDM\__\Session::clear('wpdm_addon_store_cats');
    ?>

    <div class="alert alert-danger" style="margin: 20px"><?php _e('Failed to connect with server!','download-manager'); ?></div>

<?php } ?>
<div class="modal fade" id="addonmodal" tabindex="-1" role="dialog" aria-labelledby="addonmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Add-On Installer</h4>
            </div>
            <div class="modal-body" id="modalcontents">
                <i class="fas fa-sun  fa-spin"></i> Please Wait...
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
        jQuery('.nav-pills a').click(function(){
            jQuery('#addonlist .all').fadeOut();
            jQuery('.'+this.rel).fadeIn();
            jQuery('#prcbtn').hide();
            jQuery('.nav-pills li').removeClass('active');
            jQuery(this).parent().addClass('active');
        });

        jQuery(".btn-install, .btn-purchase").click(function(){
            jQuery('#modalcontents').html('<i class="fas fa-sun  fa-spin"></i> Please Wait...');
        });
        jQuery('#addonmodal').on('shown.bs.modal', function (e) {
            if(jQuery(e.relatedTarget).hasClass('btn-purchase')){
                jQuery('.modal-dialog').css('width','800px');
                jQuery('.modal-footer').css('margin',0);
                jQuery('.modal-footer .btn-danger').html('<i class="fas fa-sun  fa-spin"></i> Please Wait...');
                jQuery('#modalcontents').css('padding',0).css('background','#f2f2f2').html("<iframe onload=\"jQuery('.modal-footer .btn-danger').html('Continue Shopping...');jQuery('#adddding').hide();jQuery('#prcbtn, #adddd').show();\" style='width: 0;padding-top: 20px; background: #f2f2f2;height: 0px;border: 0' src='https://www.wpdownloadmanager.com/?addtocart="+e.relatedTarget.rel+"'></iframe><div style='padding: 50px;text-align: center;' id='adddding'>Adding Item To Cart...</div><div style='display: none;padding: 50px;text-align: center;' id='adddd'><i class='fa fa-check-circle'></i> Item Added To Cart.</div>");
            }
        })


    });
</script>

