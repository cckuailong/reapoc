(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

     jQuery.fn.aysModal = function(action){
        let jQuerythis = jQuery(this);
        switch(action){
            case 'hide':
                jQuery(this).find('.ays-modal-content').css('animation-name', 'zoomOut');
                setTimeout(function(){
                    jQuery(document.body).removeClass('modal-open');
                    jQuery(document).find('.ays-modal-backdrop').remove();
                    jQuerythis.hide();
                }, 250);
            break;
            case 'show': 
            default:
                jQuerythis.show();
                jQuery(this).find('.ays-modal-content').css('animation-name', 'zoomIn');
                jQuery(document).find('.modal-backdrop').remove();
                jQuery(document.body).append('<div class="ays-modal-backdrop"></div>');
                jQuery(document.body).addClass('modal-open');
            break;
        }
    }

    $(document).ready(function () {
        $(document).on("input", 'input', function (e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
        $(document).on("keydown", function (e) {
            if (e.target.nodeName == "TEXTAREA") {
                return true;
            }
            if (e.keyCode == 13) {
                return false;
            }
        });

        $(document).on('change', '.ays_toggle_checkbox', function (e) {
            let state = $(this).prop('checked');
            let parent = $(this).parents('.ays_toggle_parent');
            if($(this).hasClass('ays_toggle_slide')){
                switch (state) {
                    case true:
                        parent.find('.ays_toggle_target').slideDown(250);
                        break;
                    case false:
                        parent.find('.ays_toggle_target').slideUp(250);
                        break;
                }
            }else{
                switch (state) {
                    case true:
                        parent.find('.ays_toggle_target').show(250);
                        break;
                    case false:
                        parent.find('.ays_toggle_target').hide(250);
                        break;
                }
            }
        });

        let $_navTabs = $(document).find('.nav-tab'),
            $_navTabContent = $(document).find('.nav-tab-content');
        $(document).find('#sccp_post_types').select2();
        $(document).find('#sccp_post_types_1').select2();
        $(document).find('#sccp_post_types_2').select2();
        $('[id^=ays_users_roles_]').select2();        

        $_navTabs.on('click', function (e) {
            e.preventDefault();
            let active_tab = $(this).attr('data-tab');
            $_navTabs.each(function () {
                $(this).removeClass('nav-tab-active');
            });
            $_navTabContent.each(function () {
                $(this).removeClass('nav-tab-content-active');
            });
            $(this).addClass('nav-tab-active');
            $(document).find('.nav-tab-content' + $(this).attr('href')).addClass('nav-tab-content-active');
            $(document).find("[name='sccp_tab']").val(active_tab);
        });

        $(document).find('.ays-close').on('click', function () {
              $(document).find('.ays-modal').aysModal('hide');
        });

        $(document).find('#blocked_ips').DataTable();

        $('[data-toggle="tooltip"]').tooltip();

        $(document).on('click', '.ays_all', function(){
            var ays_all_checkboxes = $(this).closest('#tab2').find('.modern-checkbox-options');
            if($(this).is(':checked')) {
                ays_all_checkboxes.prop('checked', true);
            } else {
                ays_all_checkboxes.removeAttr('checked');   
                $('.ays_all_mess').removeAttr('checked');
                $('.ays_all_audio').removeAttr('checked');
            }
            ays_all_checkboxes.trigger("change");
        });       

        $(document).on('click', '.ays_all_mess', function(){
            var ays_all_mess_checkboxes = $(this).closest('#tab2').find('.modern_checkbox_mess').not(':disabled');
            if($(this).is(':checked')) {
                ays_all_mess_checkboxes.prop('checked', true);
            } else {
                ays_all_mess_checkboxes.removeAttr('checked');
            }
        });       

        $(document).on('click', '.ays_all_audio', function(){
            var ays_all_audio_checkboxes = $(this).closest('#tab2').find('.modern_checkbox_audio').not(':disabled');
            if($(this).is(':checked')) {
                ays_all_audio_checkboxes.prop('checked', true);
            } else {
                ays_all_audio_checkboxes.removeAttr('checked');
            }
        });

        $(document).find('#sccp_start-date-filter').on('change', function(e) {
            $('#ays_sccp_export_filter').submit();
            e.preventDefault();
        });

        $(document).find('#sccp_end-date-filter').on('change', function(e) {
            $('#ays_sccp_export_filter').submit();
            e.preventDefault();
        });

        $(document).on('change.select2', '#sccp_id-filter', function(e) {
            $('#ays_sccp_export_filter').submit();
            e.preventDefault();
        });
        $(document).find('#ays_sccp_export_filter').on('submit', function(e) {
            e.preventDefault();
            var $this = $('#sccp_export_filters');
            var action = 'ays_sccp_results_export_filter';
            var sccp_id = $('#sccp_id-filter').val();

            var date_from = $('#sccp_start-date-filter').val() || $('#sccp_start-date-filter').attr('min');
            var date_to = $('#sccp_end-date-filter').val() || $('#sccp_end-date-filter').attr('max');
        
            $this.find('div.ays-sccp-preloader').css('display', 'flex');
            $.ajax({
                url: sccp.ajax,
                method: 'post',
                dataType: 'json',
                data: {
                    action: action,
                    sccp_id: sccp_id,
                    date_from: date_from,
                    date_to: date_to

                },
                success: function(response) {
                    $this.find('div.ays-sccp-preloader').css('display', 'none');
                    $this.find(".export_results_count span").text(response.results);
                }
            });
        });

        let sccpSel2;

        $(document).find('.ays-sccp-export-filters').on('click', function(e) {
            let $this = $('#sccp_export_filters');
            $this.find('div.ays-sccp-preloader').css('display', 'flex');
            $this.aysModal('show');
            e.preventDefault();
            let action = 'ays_sccp_show_filters';
            $.ajax({
                url: sccp.ajax,
                method: 'post',
                dataType: 'json',
                data: {
                   action: action
                },
                success: function(res) {
                    $this.find('div.ays-sccp-preloader').css('display', '');
                    let newSccpSelect = "";

                    for (let q in res.shortcode) {
                        newSccpSelect += '<option value="'+ res.shortcode[q].subscribe_id +'">'+ res.shortcode[q].subscribe_id +'</option>';
                    }

                    let sccpSel = $this.find('#sccp_id-filter').html(newSccpSelect);
                    sccpSel2 = sccpSel.select2({
                        dropdownParent: sccpSel.parent(),
                        closeOnSelect: true,
                        allowClear: false
                    });
                    
                    $(document).on('click', '.select2-selection__choice__remove', function(){
                        sccpSel2.select2("close");
                    });
                    
                    $this.find(".export_results_count span").text(res.count);
                    $this.find('.ays-modal-body').show();
                },
                error: function() {
                    swal.fire({
                        type: 'info',
                        html: "<h2>Can't load resource.</h2><br><h6>Maybe something went wrong.</h6>"
                    }).then(function(res){
                        $(document).find('#ays-export-filters div.ays-sccp-preloader').css('display', 'none');
                        $this.aysModal('hide');
                    });
                }
            });
        });

        $(document).on('click', '.ays_sccpid_clear', function(){
            sccpSel2.val(null).trigger('change');
            return false;
        });

        $(document).find('.sccp_results_export-action').on('click', function(e) {
            e.preventDefault();
            let $this = $('#ays-export-filters');
            $this.find('div.ays-sccp-preloader').css('display', 'flex');
            let action = 'ays_sccp_results_export_file';
            let sccp_id = $('#sccp_id-filter').val();
            var type = $(this).data('type');
            var date_from = $('#sccp_start-date-filter').val() || $('#sccp_start-date-filter').attr('min');
            var date_to = $('#sccp_end-date-filter').val() || $('#sccp_end-date-filter').attr('max');
            $.post({
                url: sccp.ajax,
                dataType: 'json',
                data: { 
                    action: action,
                    type: type,
                    sccp_id: sccp_id,
                    date_from: date_from, 
                    date_to: date_to
                },
                success: function(response) {
                    if (response.status) {
                        switch (response.type) {
                            case 'xlsx':
                                var options = {
                                    fileName: "sccp_results_export",
                                    header: true
                                };
                                var tableData = [{
                                    "sheetName": "Sccp results",
                                    "data": response.data
                                }];
                                Jhxlsx.export(tableData, options);
                                break;
                            case 'csv':
                                $('#download').attr({
                                    'href': response.file,
                                    'download': "exported_sccp.csv",
                                })[0].click();
                                break;
                            case 'json':
                                var text = JSON.stringify(response.data);
                                var data = new Blob([text], {type: "application/" + response.type});
                                var fileUrl = window.URL.createObjectURL(data);
                                $('#download').attr({
                                    'href': fileUrl,
                                    'download': "sccp_results_export." + response.type,
                                })[0].click();
                                window.URL.revokeObjectURL(fileUrl);
                                break;
                            default:
                                break;
                        }
                    }
                    $this.find('div.ays-sccp-preloader').css('display', 'none');
                }
            });
        });

        var unread_result_parent = $(document).find(".unread-result").parent().parent();

        if (unread_result_parent != undefined) {
            unread_result_parent.css({"font-weight":"bold"});
        }

        var checkbox = $('.modern-checkbox-options');
        for (var i = 0; i < checkbox.length; i++) {

            var classname = checkbox[i].className.split(' ');
            if (checkbox[i].checked == true) {
                $('.' + classname[1] + '-mess').attr('disabled', false);
                $('.' + classname[1] + '-audio').attr('disabled', false);
            } else {
                $('.' + classname[1] + '-mess').attr('disabled', true);
                $('.' + classname[1] + '-audio').attr('disabled', true);
            }
        }
        checkbox.change(function () {

            var classname = this.className.split(' ');            
            if (this.checked == true) {
                $('.' + classname[1] + '-mess').attr('disabled', false);
                $('.' + classname[1] + '-audio').attr('disabled', false);
            } else {
                $('.' + classname[1] + '-mess').attr('checked', false);
                $('.' + classname[1] + '-mess').attr('disabled', true);
                $('.' + classname[1] + '-audio').attr('checked', false);
                $('.' + classname[1] + '-audio').attr('disabled', true);
            }

        });

        $(document).on('click', '.upload_audio', function (e) {
            openSCCPMusicMediaUploader(e, $(this));
        });        


        let heart_interval = setInterval(function () {
            $(document).find('.ays_heart_beat i.ays_fa').toggleClass('ays_pulse');
        }, 1000);



        //--------------preview
        
        $('#reset_to_default').on('click', function () {
            setTimeout(function(){
                if($(document).find('#sccp_custom_css').length > 0){
                    if(wp.codeEditor){
                        $(document).find('#sccp_custom_css').next('.CodeMirror').remove();
                        $(document).find('#sccp_custom_css').val('');
                        wp.codeEditor.initialize($(document).find('#sccp_custom_css'), cm_settings);
                    }
                }
            }, 100);

             $('#ays_tooltip').css({
                "background-image": "unset", 
                "padding": "5", 
                "opacity": "1"
            });

            $('#bg_color').val('#ffffff').change();
            $('#text_color').val('#ff0000').change();
            $('#border_color').val('#b7b7b7').change();
            $('#boxshadow_color').val('rgba(0,0,0,0)').change();
            $('#ays-sccp-bg-img').attr('src', '').change();
            $('input#ays_sccp_bg_image').val('');
            $('#sccp_bg_image_container').hide().change();
            $('#sccp_bg_image').show().change();
            $('.sccp_opacity_demo_val').val(1);
            $('#font_size').val(12).change();
            $('#border_width').val(1).change();
            $('#border_radius').val(3).change();
            $('#border_style').val('solid').change();
            $('#tooltip_position').val('mouse').change();
            $('#ays_sccp_custom_class').val('');
            $('#sscp_timeout').val(1000);
            $('#ays_tooltip_padding').val(5);
            $('#ays_sccp_tooltip_bg_image_position').val('center center').change();
            $('#ays_sccp_box_shadow_x_offset').val(0).change();
            $('#ays_sccp_box_shadow_y_offset').val(0).change();
            $('#ays_sccp_box_shadow_z_offset').val(15).change();
        });
        
        $(document).on('input', '.sccp_opacity_demo_val', function(){
            $(document).find('#ays_tooltip').css('opacity', $(this).val());
        });

        $('#bg_color').wpColorPicker({
            defaultColor: '#ffffff',
            change: function(event, ui) {
                $('#ays_tooltip').css('background-color', ui.color.toString());
            }
        });
        $('#text_color').wpColorPicker({
            defaultColor: '#ff0000',
            change: function(event, ui) {
                $('#ays_tooltip, #ays_tooltip>*').css('color', ui.color.toString())
            }
        });
        $('#border_color').wpColorPicker({
            defaultColor: '#b7b7b7',
            change: function(event, ui) {
                $('#ays_tooltip').css('border-color', ui.color.toString())
            }
        });
        $('#boxshadow_color').wpColorPicker({
            defaultColor: 'rgba(0,0,0,0)',
            change: function(event, ui) {
                var x_offset = $(document).find('input#ays_sccp_box_shadow_x_offset').val() + "px ";
                var y_offset = $(document).find('input#ays_sccp_box_shadow_y_offset').val() + "px ";
                var z_offset = $(document).find('input#ays_sccp_box_shadow_z_offset').val() + "px ";

                var box_shadow = x_offset + y_offset + z_offset;

                $('#ays_tooltip').css('box-shadow', ui.color.toString() + ' ' + box_shadow + ' 1px ');
            }
        });
        $('#font_size').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip, #ays_tooltip>*').css('font-size', val + 'px')
        });
        $('#border_width').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip').css('border-width', val + 'px')
        });
        $('#border_radius').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip').css('border-radius', val + 'px')
        });
        $('#border_style').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip').css('border-style', val)
        });
        $('#ays_tooltip_padding').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip').css('padding', val)
        });        

        $('#ays_sccp_tooltip_bg_image_position').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip').css('background-position', val)
        });

        $('#ays_sccp_tooltip_bg_image_object_fit').on('change', function () {
            let val = $(this).val();
            $('#ays_tooltip').css('background-size', val)
        });

        $(document).find('#ays_sccp_box_shadow_x_offset, #ays_sccp_box_shadow_y_offset, #ays_sccp_box_shadow_z_offset').on('change', function () {
           
            var x_offset = $(document).find('input#ays_sccp_box_shadow_x_offset').val() + "px ";
            var y_offset = $(document).find('input#ays_sccp_box_shadow_y_offset').val() + "px ";
            var z_offset = $(document).find('input#ays_sccp_box_shadow_z_offset').val() + "px ";

            var box_shadow = x_offset + y_offset + z_offset;
            $(document).find('#ays_tooltip').css('box-shadow', $(document).find('#boxshadow_color').val() + ' ' + box_shadow + ' 1px ');
           
        });

        $('#ays_tooltip').children().css('font-size', $('#font_size').val() + 'px');
        $('#ays_tooltip').children().css('margin', "0");


        //----------end preview

        function openSCCPMediaUploader(e, element) {
            e.preventDefault();
            let aysUploader = wp.media({
                title: 'Upload',
                button: {
                    text: 'Upload'
                },
                multiple: false
            }).on('select', function () {
                let attachment = aysUploader.state().get('selection').first().toJSON();
                $('.sccp_upload_audio').html('<audio id="sccp_audio" controls><source src="' + attachment.url + '" type="audio/mpeg"></audio>');                
                $('.upload_audio_url').val(attachment.url);
            }).open();

            return false;
        }

        function openSCCPMusicMediaUploader(e, element) {
            e.preventDefault();
            let aysUploader = wp.media({
                title: 'Upload music',
                button: {
                    text: 'Upload'
                },
                library: {
                    type: 'audio'
                },
                multiple: false
            }).on('select', function () {
                let attachment = aysUploader.state().get('selection').first().toJSON();
                $('.sccp_upload_audio').html('<audio id="sccp_audio" controls><source src="' + attachment.url + '" type="audio/mpeg"></audio><button type="button" class="close ays_close" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
                $('.sccp_upload_audio').show();
                $('.upload_audio_url').val(attachment.url);
            }).open();
            return false;
        }

        $(document).on('click', '.ays_close', function () {
            $('#sccp_audio').trigger('pause'); // Stop playing        
            $('.sccp_upload_audio').hide();
            $('.upload_audio_url').val('');

        });
            
        //    AV block content
        $("input[type='text'].sccp_blockcont_shortcode").on("click", function () {
           $(this).select();
        });

        $("label.ays_actDect").on("click", function () {
            var date_id = $(this).find('input[id*="ays-sccp-date-"]').data('id');
            
            $(this).find('#ays-sccp-date-from-' + date_id + ', #ays-sccp-date-to-' + date_id).datetimepicker({
                controlType: 'select',
                oneLine: true,
                dateFormat: "yy-mm-dd",
                timeFormat: "HH:mm:ss"
            });
        });

        $(document).find('.sccp_schedule_date').datetimepicker({
            controlType: 'select',
            oneLine: true,
            dateFormat: "yy-mm-dd",
            timeFormat: "HH:mm:ss"
        });

        let id = $('.all_block_contents').data('last-id');
        $(document).on('click', '.add_new_block_content', function () {
            var last_id = $('.blockcont_one').last().attr('id');
            if (last_id == undefined) {
                last_id = id;
            } else {
                last_id = last_id.substring(7);
            }

            if (id == last_id) {
                id++;
            }
            var content = '';
            for (var key in sccp.bc_user_role) {            
               content += "<option  value='" + key + "' >" + sccp.bc_user_role[key]['name'] + "</option>";              
            }
               
            $('.all_block_contents').prepend(' <div class="blockcont_one" id="blocont' + id + '">\n' +
                '                    <div class="copy_protection_container form-group row ays_bc_row">\n' +
                '                        <div class="col">\n' +
                '                            <label for="sccp_blockcont_shortcode" class="sccp_bc_label">Shortcode</label>\n' +
                '                            <input type="text"  name="sccp_blockcont_shortcode[]" class="ays-text-input sccp_blockcont_shortcode select2_style" value="[ays_block id=\'' + id + '\'] Content [/ays_block]" readonly>\n' +
                '                            <input type="hidden"  name="sccp_blockcont_id[]" value="' + id + '">\n' +
                '                        </div>\n' +
                '                        <div class="col">\n' +
                '                           <div class="input-group bc_count_limit">\n' +
                '                               <div class="bc_count">\n' +
                '                                   <label for="sccp_blockcont_pass" class="sccp_bc_label">Password</label>\n' +
                '                               </div>\n' +
                '                               <div class="bc_limit">\n' +
                '                                   <label for="sccp_blockcont_limit_' + id + '" class="sccp_bc_limit">Limit<a class="ays_help" data-toggle="tooltip"\n' +
                '                                  title="Choose the maximum amount of the usage of the password">\n' +
                '                                    <i class="ays_fa ays_fa_info_circle"></i>\n' +
                '                                </a></label>\n' +
                '                                <input type="number" id="sccp_blockcont_limit_' + id + '" name="bc_pass_limit_' + id + '" >\n' +
                '                               </div>\n' +
                '                           </div>\n' +
                '                               <div class="input-group">\n' +
                '                                   <input type="password"  name="sccp_blockcont_pass[]" class="ays-text-input select2_style form-control">\n' +
                '                                   <div class="input-group-append ays_inp-group">\n' +
                '                                       <span class="input-group-text show_password">\n' +
                '                                           <i class="ays_fa fa-eye" aria-hidden="true"></i>\n' +
                '                                       </span>\n' +                
                '                                   </div>\n' +                
                '                               </div>\n' +                
                '                        </div>\n' +
                '                        <div>\n' +
                '                           <p style="margin-top:60px;">OR</p>\n' +
                '                        </div>\n' +
                '                        <div class="col">\n' +
                '                           <label for="sccp_blockcont_roles" class="sccp_bc_label">Except</label>\n' +
                '                           <div class="input-group">\n' +
                '                                <select name="ays_users_roles_'+id+'[]" class="ays_bc_users_roles" id="ays_users_roles_'+id+'" multiple>\n' +
                                                    content +
                '                                </select>\n' +
                '                            </div>\n' +
                '                       </div>\n' +
                '                       <div class="col">\n' +
                '                           <label for="sccp_blockcont_schedule" style="margin-left: 35px;">Schedule</label>\n' +
                '                           <div class="input-group">\n' +
                '                               <label style="display: flex;" class="ays_actDect"><span style="font-size:small;margin-right: 4px;">From</span>\n' +
                '                                   <input type="text" id="ays-sccp-date-from-'+id+'" data-id="'+id+'" class="ays-text-input ays-text-input-short sccp_schedule_date" name="bc_schedule_from_'+id+'" value="">\n' +
                '                               <div class="input-group-append">\n' +
                '                                       <label for="ays-sccp-date-from-'+id+'" style="height: 34px; padding: 5px 10px;" class="input-group-text">\n' +
                '                                            <span><i class="ays_fa ays_fa_calendar"></i></span>\n' +
                '                                        </label>\n' +
                '                                    </div>\n' +
                '                               </label>\n' +
                '                               <label style="display: flex;" class="ays_actDect"><span style="font-size:small;margin-right: 21px;">To</span>\n' +
                '                                   <input type="text" id="ays-sccp-date-to-'+id+'" data-id="'+id+'" class="ays-text-input ays-text-input-short sccp_schedule_date" name="bc_schedule_to_'+id+'" value="">\n' +
                '                               <div class="input-group-append">\n' +
                '                                       <label for="ays-sccp-date-to-'+id+'" style="height: 34px; padding: 5px 10px;" class="input-group-text">\n' +
                '                                            <span><i class="ays_fa ays_fa_calendar"></i></span>\n' +
                '                                        </label>\n' +
                '                                    </div>\n' +
                '                               </label>\n' +
                '                           </div>\n' +
                '                       </div>\n' +
                '                       <div>\n' +
                '                            <br>\n' +
                '                            <p class="blockcont_delete_icon"><i class="ays_fa fa-trash-o" aria-hidden="true"></i></p>\n' +
                '                        </div>' +
                '                    </div>\n' +
                '                </div>');
            
            id++;
            $('[id^=ays_users_roles_]').select2();            
            $("input[type='text'].sccp_blockcont_shortcode").on("click", function () {
                 $(this).select();
            });

            $("label.ays_actDect").on("click", function () {
                var date_id = $(this).find('input[id*="ays-sccp-date-"]').data('id');
                
                $(this).find('#ays-sccp-date-from-' + date_id + ', #ays-sccp-date-to-' + date_id).datetimepicker({
                    controlType: 'select',
                    oneLine: true,
                    dateFormat: "yy-mm-dd",
                    timeFormat: "HH:mm:ss"
                });
            });

            $(document).find('.sccp_schedule_date').datetimepicker({
                controlType: 'select',
                oneLine: true,
                dateFormat: "yy-mm-dd",
                timeFormat: "HH:mm:ss"
            });
            
        });
        
        // AV Block Subscribe
        $('.sccp_blocksub').on('change', function () {
            if ($(this).prop('checked')) {
                $(this).parent().children('.sccp_blocksub_hid').val('on');
            }else{
                $(this).parent().children('.sccp_blocksub_hid').val('off');
            }
        });
        let sub_id = $('.all_block_subscribes').data('last-id');
        let check_id = $('.ays_data_checker').val();
        $(document).on('click', '.add_new_block_subscribe', function () {
            var last_sub_id = $('.blockcont_one').last().attr('id');
            if (last_sub_id == undefined) {
                last_sub_id = sub_id;
                sub_id = parseInt($('.all_block_subscribes').data('last-id'));
            }
            if(check_id == 'false'){
                sub_id ++;
            }

            $('.all_block_subscribes').prepend(' <div class="blockcont_one" id="blocksub' + sub_id + '">\n' +
                '    <div class="copy_protection_container row ays_bc_row">\n' +
                '        <div class="col sccp_block_sub">\n' +
                '            <div class="sccp_block_sub_label_inp">\n'+
                '               <div class="sccp_block_sub_label">\n'+
                '                   <label for="sccp_block_subscribe_shortcode_' + sub_id + '" class="sccp_bc_label">Shortcode</label>\n' +
                '               </div>\n' +
                '               <div class="sccp_block_sub_inp">\n'+
                '                   <input type="text"  name="sccp_block_subscribe_shortcode[]" id="sccp_block_subscribe_shortcode_' + sub_id + '" class="ays-text-input sccp_blockcont_shortcode select2_style" value="[ays_block_subscribe id=\'' + sub_id + '\'] Content [/ays_block_subscribe]" readonly>\n' +
                '                   <input type="hidden"  name="sccp_blocksub_id[]" value="' + sub_id + '">\n' +
                '               </div>\n' +
                '               <hr>\n'+
                '               <div class="copy_protection_container row">\n'+
                '                  <div class="col-sm-4">\n'+
                '                      <label for="sccp_enable_block_sub_name_field_'+sub_id+'">'+ sccpLangObj.nameField+'</label>\n'+
                '                      <a class="ays_help" data-toggle="tooltip" title="'+sccpLangObj.title+'">\n'+
                '                            <i class="ays_fa ays_fa_info_circle"></i>\n'+
                '                       </a>\n'+
                '                  </div>\n'+
                '                  <div class="col-sm-8">\n'+
                '                      <input type="checkbox" class="modern-checkbox" id="sccp_enable_block_sub_name_field_'+sub_id+'" name="sccp_enable_block_sub_name_field['+sub_id+'][]"  value="true">\n'+
                '                  </div>\n'+
                '               </div> \n'+
                '            </div>\n' +
                '            <div class="sccp_block_sub_inp_row">\n'+
                '               <div class="sccp_pro" title="This feature will available in PRO version">\n'+
                '                   <div class="pro_features sccp_general_pro">\n'+
                '                       <div>\n'+
                '                           <p style="font-size: 16px !important;">\n'+
                '                               This feature is available only in \n' +
                '                               <a href="https://ays-pro.com/index.php/wordpress/secure-copy-content-protection" target="_blank" class="text-danger ml-2" title="PRO feature"> \n' +
                '                                   PRO version!!!\n' +
                '                               </a>\n' +
                '                           </p>\n' +
                '                       </div>\n' +
                '                   </div>\n' +
                '                   <div class="sccp_block_sub_label">\n'+
                '                      <label for="sccp_require_verification_' + sub_id + '" class="sccp_bc_label">Require verification</label>\n' +
                '                   </div>\n' +
                '                   <div class="sccp_block_sub_inp">\n'+
                '                       <input type="checkbox"  name="sccp_subscribe_require_verification[]" id="sccp_require_verification_' + sub_id + '" class="ays-text-input sccp_blocksub select2_style" value="on">\n' +
                '                       <input type="hidden"  name="sub_require_verification[]" class="sccp_blocksub_hid" value="off">\n' +
                '                   </div>\n' +
                '               </div>\n' +
                '            </div>\n' +
                '        </div>\n' +                
                '       <div>\n' +
                '            <br>\n' +
                '            <p class="blockcont_delete_icon"><i class="ays_fa fa-trash-o" aria-hidden="true"></i></p>\n' +
                '        </div>' +
                '    </div>\n' +
                '</div>');
            if(check_id != "false"){
                sub_id++;
            }                

            $('.sccp_blocksub').on('change', function () {
                if ($(this).prop('checked')) {
                    $(this).parent().children('.sccp_blocksub_hid').val('on');
                }else{
                    $(this).parent().children('.sccp_blocksub_hid').val('off');
                }
            });

            $("input[type='text'].sccp_blockcont_shortcode").on("click", function () {
               $(this).select();
            });
        });
       
        $(document).on('click', '.blocksub_delete_icon', function () {
            var real_del = confirm('Do you want to delete?');
            if (real_del == true) {
                var id = $(this).closest('.blockcont_one').attr('id');
                if (id == undefined) {
                    id = 0;
                } else {
                    id = id.substring(8); 
                    var lastval = $('.deleted_ids').val().toString();
                    var lastval_check = lastval != '' ? lastval.toString() + ',' : '';
                    var last_val = lastval_check + id.toString();
                    $('.deleted_ids').val(last_val);
                }
                
                $(this).parent().parent().parent().css({
                    'animation-name': 'slideOutLeft',
                    'animation-duration': '.4s', 
                    'box-shadow': '2px 0px 8px #bfb2b2'
                });
                var a = $(this);
                setTimeout(function(){
                    a.parent().parent().parent().remove();
                }, 400);
            }
            
        });
       
        $(document).on('click', '.blockcont_delete_icon', function () {
            var real_del = confirm('Do you want to delete?');
            if (real_del == true) {
                var id = $(this).closest('.blockcont_one').attr('id');
                if (id == undefined) {
                    id = 0;
                } else {
                    id = id.substring(7); 
                    var lastval = $('.deleted_ids').val().toString();
                    lastval = lastval.toString() + ',' + id.toString();
                    $('.deleted_ids').val(lastval);
                }
                
                $(this).parent().parent().parent().css({
                    'animation-name': 'slideOutLeft',
                    'animation-duration': '.4s', 
                    'box-shadow': '2px 0px 8px #bfb2b2'
                });
                var a = $(this);
                setTimeout(function(){
                    a.parent().parent().parent().remove();
                }, 400);
            }
            
        });

        var count = 1;
        $(document).on('click', '.show_password', function () {

            if (count % 2) {
                $(this).parent().parent().find('input').attr('type', 'text');
            } else {
                $(this).parent().parent().find('input').attr('type', 'password');
            }
            count++;
        });        

        //--------------AV end
        
        $(document).on('click', '.ays-edit-sccp-bg-img', function (e) {
            openSccpMediaUploader(e, $(this));
        });

        $(document).on('click', 'a.add-sccp-bg-image', function (e) {
            openSccpMediaUploader(e, $(this));
        });

        $(document).on('click', '.ays-remove-sccp-bg-img', function () {
            $(this).parent().find('img#ays-sccp-bg-img').attr('src', '');
            $(this).parent().parent().find('input#ays_sccp_bg_image').val('');
            $(this).parent().fadeOut();
            $(this).parent().parent().find('a.add-sccp-bg-image').show();
            $(document).find('#ays_tooltip').css({'background-image': 'none'});
        });

        setTimeout(function(){
            if($(document).find('#sccp_custom_css').length > 0){
                if(wp.codeEditor)
                    wp.codeEditor.initialize($(document).find('#sccp_custom_css'), cm_settings);
            }
        }, 500);

        $(document).find('a[href="#tab5"]').on('click', function (e) {        
            setTimeout(function(){
                if($(document).find('#sccp_custom_css').length > 0){
                    if(wp.codeEditor){
                        $(document).find('#sccp_custom_css').next('.CodeMirror').remove();
                        wp.codeEditor.initialize($(document).find('#sccp_custom_css'), cm_settings);
                    }
                }
            }, 500);
        });

        function openSccpMediaUploader(e, element) {
            e.preventDefault();
            let aysUploader = wp.media({
                title: 'Upload',
                button: {
                    text: 'Upload'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            }).on('select', function () {
                let attachment = aysUploader.state().get('selection').first().toJSON();
                if(element.hasClass('add-sccp-bg-image')){
                    element.parent().find('.ays-sccp-bg-image-container').fadeIn();
                    element.parent().find('img#ays-sccp-bg-img').attr('src', attachment.url);
                    element.next().val(attachment.url);
                    $(document).find('.ays-tooltip-live-container').css({'background-image': 'url("'+attachment.url+'")'});
                    element.hide();
                }else if(element.hasClass('ays-edit-sccp-bg-img')){
                    element.parent().find('.ays-sccp-bg-image-container').fadeIn();
                    element.parent().find('img#ays-sccp-bg-img').attr('src', attachment.url);
                    $(document).find('#ays_sccp_bg_image').val(attachment.url);
                    $(document).find('.ays-tooltip-live-container').css({'background-image': 'url("'+attachment.url+'")'});
                }else{
                    element.text('Edit Image');
                    element.parent().parent().find('.ays-sccp-image-container').fadeIn();
                    element.parent().parent().find('img#ays-sccp-img').attr('src', attachment.url);
                    $('input#ays-sccp-image').val(attachment.url);
                }
            }).open();

            return false;
        }

        //Hide results
        $('.if-ays-sccp-hide-results').css("display", "flex").hide();
        if ($('#sccp_access_disable_js').prop('checked')) {
            $('.if-ays-sccp-hide-results').fadeIn();
        }
        $('#sccp_access_disable_js').on('change', function () {
            $('.if-ays-sccp-hide-results').fadeToggle();
        });

        //Hide results
        $('.if-ays-sccp-hide-css-input').css("display", "flex").hide();
        if ($('#sccp_exclude_css_selector').prop('checked')) {
            $('.if-ays-sccp-hide-css-input').fadeIn();
        }
        $('#sccp_exclude_css_selector').on('change', function () {
            $('.if-ays-sccp-hide-css-input').fadeToggle();
        });

        $(document).on('click', '.ays_confirm_del', function(e){            
            e.preventDefault();
            var confirm = window.confirm('Are you sure you want to delete this report?');
            if(confirm === true){
                window.location.replace($(this).attr('href'));
            }
        });

        $(document).keydown(function(event) {
            var editButton = $(document).find("input.ays-sccp-save-comp");
            if (!(event.which == 83 && event.ctrlKey) && !(event.which == 19)){
                return true;  
            }
            editButton.trigger("click");
            event.preventDefault();
            return false;
        });

        // Notice bar
        var toggle_ddmenu = $(document).find('.toggle_ddmenu');
        toggle_ddmenu.on('click', function () {
            var ddmenu = $(this).next();
            var state = ddmenu.attr('data-expanded');
            switch (state) {
                case 'true':
                    $(this).find('.ays_fa').css({
                        transform: 'rotate(0deg)'
                    });
                    ddmenu.attr('data-expanded', 'false');
                    break;
                case 'false':
                    $(this).find('.ays_fa').css({
                        transform: 'rotate(90deg)'
                    });
                    ddmenu.attr('data-expanded', 'true');
                    break;
            }
        });

        // Tabs 
        if($(document).find('.ays-top-menu').width() <= $(document).find('div.ays-top-tab-wrapper').width()){
            $(document).find('.ays_menu_left,.ays_menu_right').css('display', 'flex');
        }
        $(window).resize(function(){
            if($(document).find('.ays-top-menu').width() < $(document).find('div.ays-top-tab-wrapper').width()){
                $(document).find('.ays_menu_left,.ays_menu_right').css('display', 'flex');
            }else{
                $(document).find('.ays_menu_left,.ays_menu_right').css('display', 'none');
                $(document).find('div.ays-top-tab-wrapper').css('transform', 'translate(0px)');
            }
        });
        var menuItemWidths0 = [];
        var menuItemWidths = [];
        $(document).find('.ays-top-tab-wrapper .nav-tab').each(function(){
            var $this = $(this);
            menuItemWidths0.push($this.outerWidth());
        });

        for(var i = 0; i < menuItemWidths0.length; i+=2){
            if(menuItemWidths0.length <= i+1){
                menuItemWidths.push(menuItemWidths0[i]);
            }else{
                menuItemWidths.push(menuItemWidths0[i]+menuItemWidths0[i+1]);
            }
        }
        var menuItemWidth = 0;
        for(var i = 0; i < menuItemWidths.length; i++){
            menuItemWidth += menuItemWidths[i];
        }
        menuItemWidth = menuItemWidth / menuItemWidths.length;

        $(document).on('click', '.ays_menu_left', function(){
            var scroll = parseInt($(this).attr('data-scroll'));
            scroll -= menuItemWidth;
            if(scroll < 0){
                scroll = 0;
            }
            $(document).find('div.ays-top-tab-wrapper').css('transform', 'translate(-'+scroll+'px)');
            $(this).attr('data-scroll', scroll);
            $(document).find('.ays_menu_right').attr('data-scroll', scroll);
        });
        $(document).on('click', '.ays_menu_right', function(){
            var scroll = parseInt($(this).attr('data-scroll'));
            var howTranslate = $(document).find('div.ays-top-tab-wrapper').width() - $(document).find('.ays-top-menu').width();
            howTranslate += 7;
            if(scroll == -1){
                scroll = menuItemWidth;
            }
            scroll += menuItemWidth;
            if(scroll > howTranslate){
                scroll = Math.abs(howTranslate);
            }
            $(document).find('div.ays-top-tab-wrapper').css('transform', 'translate(-'+scroll+'px)');
            $(this).attr('data-scroll', scroll);
            $(document).find('.ays_menu_left').attr('data-scroll', scroll);
        });


         $(document).find('.nav-tab-wrapper a.nav-tab').on('click', function (e) {
            if(! $(this).hasClass('no-js')){
                let elemenetID = $(this).attr('href');
                let active_tab = $(this).attr('data-tab');
                $(document).find('.nav-tab-wrapper a.nav-tab').each(function () {
                    if ($(this).hasClass('nav-tab-active')) {
                        $(this).removeClass('nav-tab-active');
                    }
                });
                $(this).addClass('nav-tab-active');
                $(document).find('.ays-sccp-tab-content').each(function () {
                    if ($(this).hasClass('ays-sccp-tab-content-active'))
                        $(this).removeClass('ays-sccp-tab-content-active');
                });
                $(document).find("[name='ays_sccp_tab']").val(active_tab);
                $('.ays-sccp-tab-content' + elemenetID).addClass('ays-sccp-tab-content-active');
                e.preventDefault();
            }
        });


        var wp_editor_height = $(document).find('.sccp_wp_editor_height');

        if ( wp_editor_height.length > 0 ) {
            var wp_editor_height_val = wp_editor_height.val();
            if ( wp_editor_height_val != '' && wp_editor_height_val != 0 ) {
                var ays_sccp_wp_editor = setInterval( function() {
                    if (document.readyState === 'complete') {
                        $(document).find('.wp-editor-wrap .wp-editor-container iframe , .wp-editor-container textarea.wp-editor-area').css({
                            "height": wp_editor_height_val + 'px'
                        });
                        clearInterval(ays_sccp_wp_editor);
                    }
                } , 500);
            }
        }


    });
})(jQuery);
