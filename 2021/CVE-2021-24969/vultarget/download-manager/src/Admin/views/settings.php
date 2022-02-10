<?php

use WPDM\Admin\Menu\Settings;

if (!defined("ABSPATH")) die("Shit happens!");
?>
<style>
    #wpdm-admin-page-container {
        display: flex;
        padding-left: 280px;
        padding-top: 64px;
    }

    #wpdm-admin-page-sidebar {
        width: 260px;
        height: 1000px;
        background: #f6f7f9;
        border-right: 1px solid #c1c5d2;
        padding: 30px;
        position: fixed;
        margin-left: -260px;
        overflow: auto;
    }

    #wpdm-admin-page-body {
        padding: 30px;
        width: 800px;
        max-width: 100%;
    }

</style>
<div class="wrap w3eden">
    <form method="post" id="wdm_settings_form">
        <?php

        $actions = [
            ['type' => "submit", "class" => "primary btn-full-height", "name" => '<i class="sinc far fa-hdd"></i> ' . __("Save Settings", "download-manager")]
        ];

        WPDM()->admin->pageHeader(esc_attr__('Settings', WPDM_TEXT_DOMAIN), 'cog sinc color-purple', [], $actions, ['class' => 'pr-0']);

        ?>



        <?php
        wp_nonce_field(WPDMSET_NONCE_KEY, '__wpdms_nonce');
        ?>
        <div class="panel panel-default" id="wpdm-wrapper-panel">

            <div id="wpdm-admin-page-container">

                <div id="wpdm-admin-page-sidebar">
                    <div data-simplebar ss-container>
                        <ul id="tabs" class="nav nav-pills nav-stacked settings-tabs">
                            <?php Settings::renderMenu($tab = wpdm_query_var('tab', ['validate' => 'txt', 'default' => 'basic'])); ?>
                        </ul>
                    </div>
                </div>
                <div id="wpdm-admin-page-body">
                    <div class="tab-content">
                        <div class="alert alert-success"
                             style="max-width: 300px !important;display: none;position: fixed; right: 15px;top: 80px;background: #ffffff !important;cursor: pointer"
                             id="wpdm_message"></div>
                        <div id="wpdm_notify" style="position: fixed; right: 15px;top: 95px;cursor: pointer"></div>

                        <input type="hidden" name="task" id="task" value="wdm_save_settings"/>
                        <input type="hidden" name="action" id="action" value="wpdm_settings"/>
                        <input type="hidden" name="section" id="section" value="<?php echo $tab; ?>"/>
                        <div id="fm_settings">
                            <?php
                            global $stabs;
                            if (isset($stabs[$tab], $stabs[$tab]['callback']))
                                call_user_func($stabs[$tab]['callback']);
                            else
                                echo "<div class='panel panel-danger'><div class='panel-body color-red'><i class='fa fa-exclamation-triangle'></i> " . __("Something is wrong!", "download-manager") . "</div></div>";
                            ?>
                        </div>

                        <div class="panel panel-default" style="border-top: 1px solid #dddddd !important;">
                            <div class="panel-body text-right">
                                <button type="submit" style="min-width:200px" class="btn btn-info btn-lg"><i
                                            class="sinc far fa-hdd"></i>
                                    &nbsp;<?php _e("Save Settings", "download-manager"); ?></button>
                            </div>
                        </div>

                        <br>

                    </div>
                </div>

            </div>

            <div class="panel-body settings-panel-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-9">

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </form>

    <script type="text/javascript">
        jQuery(function ($) {
            var $body = $('body');
            $body.on('click', '#wpdm_message.alert-success', function () {
                $(this).fadeOut();
            });

            $('select:not(.system-ui)').chosen({disable_search_threshold: 4});
            $("ul#tabs li").click(function () {

            });
            $('#wpdm_message').removeClass('hide').hide();
            $("ul#tabs li a").click(function () {
                ///jQuert("ul#tabs li").removeClass('active')
                $("ul#tabs li").removeClass("active");
                $(this).parent('li').addClass('active');
                $('#wdms_loading').addClass('wpdm-spin');
                $(this).append('<span class="wpdm-loading wpdm-spin pull-right" id="wpdm-lsp"></span>')
                var section = this.id;
                $.post(ajaxurl, {action: 'wpdm_settings', section: this.id}, function (res) {
                    $('#fm_settings').html(res);
                    $('#section').val(section)
                    $('#wdms_loading').removeClass('wpdm-spin');
                    $('select:not(.system-ui)').chosen({disable_search_threshold: 4});
                    window.history.pushState({
                        "html": res,
                        "pageTitle": "response.pageTitle"
                    }, "", "edit.php?post_type=wpdmpro&page=settings&tab=" + section);
                    $('#wpdm-lsp').fadeOut(function () {
                        $(this).remove();
                    });
                });
                return false;
            });

            window.onpopstate = function (e) {
                if (e.state) {
                    $("#fm_settings").html(e.state.html);
                    //document.title = e.state.pageTitle;
                }
            };


            $('#wdm_settings_form').submit(function () {

                $('.sinc').removeClass('far fa-hdd').addClass('fas fa-sun fa-spin');

                $(this).ajaxSubmit({
                    url: ajaxurl,
                    beforeSubmit: function (formData, jqForm, options) {
                        $('.wpdm-ssb').addClass('wpdm-spin');
                        $('#wdms_loading').addClass('wpdm-spin');
                    },
                    success: function (responseText, statusText, xhr, $form) {
                        var section = $('input#section').val();
                        WPDM.notify("<div style='margin-bottom: 5px;text-transform: uppercase'><strong>" + $('#' + section).html() + ":</strong></div>" + responseText, 'info', '#wpdm_notify', 10000);
                        $('.wpdm-ssb').removeClass('wpdm-spin');
                        $('.sinc').removeClass('fas fa-sun fa-spin').addClass('far fa-hdd');
                        $('#wdms_loading').removeClass('wpdm-spin');
                    }
                });

                return false;
            });

            $('body').on("click", '.nav-tabs a', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            function adjustSidebarHeight() {
                var abh = $('#wpadminbar').height();
                $('#wpdm-admin-page-sidebar').css('height', (window.innerHeight - abh - 62) + 'px');
            }

            adjustSidebarHeight();
            $(window).on('resize', function () {
                adjustSidebarHeight();
            });


        });

    </script>

