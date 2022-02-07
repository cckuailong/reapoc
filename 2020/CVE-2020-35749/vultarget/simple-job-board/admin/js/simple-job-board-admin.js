/**
 * Simple Job Board Admin Core JS File - V 1.3.0
 *
 * @author PressTigers <support@presstigers.com>, 2016
 *
 * Actions List
 * - Settings' Tabs Toggle Callbacks
 * - Setting & Meta boxes "Job Features" and "Application Form Builder" Callbacks
 * - Color Picker Callback
 * - Settings & Meta Boxes' Fields Sorting Callback
 * - Upload Company Logo Callbacks
 * - Settings & Meta Boxes' Labels Editing Callback
 * - Added prefix for Tabs to avoid from conflict
 * 
 * @since   1.0.0
 * @since   1.3.0   Added "Applicant Column Name" in SJB Form Builder
 * @since   2.7.2   Added wrapper "sjb-tabs-wrap" to avoid form any conflict with other theme/plugin
 */ 
(function ($) { 
    'use strict';

    $(function () {
        var rx_pP = "[\\x21-\\x23\\x25-\\x2A\\x2C-\\x2F\\x3A\\x3B\\x3F\\x40\\x5B-\\x5D\\x5F\\x7B\\x7D\\xA1\\xA7\\xAB\\xB6\\xB7\\xBB\\xBF\\u037E\\u0387\\u055A-\\u055F\\u0589\\u058A\\u05BE\\u05C0\\u05C3\\u05C6\\u05F3\\u05F4\\u0609\\u060A\\u060C\\u060D\\u061B\\u061E\\u061F\\u066A-\\u066D\\u06D4\\u0700-\\u070D\\u07F7-\\u07F9\\u0830-\\u083E\\u085E\\u0964\\u0965\\u0970\\u0AF0\\u0DF4\\u0E4F\\u0E5A\\u0E5B\\u0F04-\\u0F12\\u0F14\\u0F3A-\\u0F3D\\u0F85\\u0FD0-\\u0FD4\\u0FD9\\u0FDA\\u104A-\\u104F\\u10FB\\u1360-\\u1368\\u1400\\u166D\\u166E\\u169B\\u169C\\u16EB-\\u16ED\\u1735\\u1736\\u17D4-\\u17D6\\u17D8-\\u17DA\\u1800-\\u180A\\u1944\\u1945\\u1A1E\\u1A1F\\u1AA0-\\u1AA6\\u1AA8-\\u1AAD\\u1B5A-\\u1B60\\u1BFC-\\u1BFF\\u1C3B-\\u1C3F\\u1C7E\\u1C7F\\u1CC0-\\u1CC7\\u1CD3\\u2010-\\u2027\\u2030-\\u2043\\u2045-\\u2051\\u2053-\\u205E\\u207D\\u207E\\u208D\\u208E\\u2308-\\u230B\\u2329\\u232A\\u2768-\\u2775\\u27C5\\u27C6\\u27E6-\\u27EF\\u2983-\\u2998\\u29D8-\\u29DB\\u29FC\\u29FD\\u2CF9-\\u2CFC\\u2CFE\\u2CFF\\u2D70\\u2E00-\\u2E2E\\u2E30-\\u2E44\\u3001-\\u3003\\u3008-\\u3011\\u3014-\\u301F\\u3030\\u303D\\u30A0\\u30FB\\uA4FE\\uA4FF\\uA60D-\\uA60F\\uA673\\uA67E\\uA6F2-\\uA6F7\\uA874-\\uA877\\uA8CE\\uA8CF\\uA8F8-\\uA8FA\\uA8FC\\uA92E\\uA92F\\uA95F\\uA9C1-\\uA9CD\\uA9DE\\uA9DF\\uAA5C-\\uAA5F\\uAADE\\uAADF\\uAAF0\\uAAF1\\uABEB\\uFD3E\\uFD3F\\uFE10-\\uFE19\\uFE30-\\uFE52\\uFE54-\\uFE61\\uFE63\\uFE68\\uFE6A\\uFE6B\\uFF01-\\uFF03\\uFF05-\\uFF0A\\uFF0C-\\uFF0F\\uFF1A\\uFF1B\\uFF1F\\uFF20\\uFF3B-\\uFF3D\\uFF3F\\uFF5B\\uFF5D\\uFF5F-\\uFF65]|\\uD800[\\uDD00-\\uDD02\\uDF9F\\uDFD0]|\\uD801\\uDD6F|\\uD802[\\uDC57\\uDD1F\\uDD3F\\uDE50-\\uDE58\\uDE7F\\uDEF0-\\uDEF6\\uDF39-\\uDF3F\\uDF99-\\uDF9C]|\\uD804[\\uDC47-\\uDC4D\\uDCBB\\uDCBC\\uDCBE-\\uDCC1\\uDD40-\\uDD43\\uDD74\\uDD75\\uDDC5-\\uDDC9\\uDDCD\\uDDDB\\uDDDD-\\uDDDF\\uDE38-\\uDE3D\\uDEA9]|\\uD805[\\uDC4B-\\uDC4F\\uDC5B\\uDC5D\\uDCC6\\uDDC1-\\uDDD7\\uDE41-\\uDE43\\uDE60-\\uDE6C\\uDF3C-\\uDF3E]|\\uD807[\\uDC41-\\uDC45\\uDC70\\uDC71]|\\uD809[\\uDC70-\\uDC74]|\\uD81A[\\uDE6E\\uDE6F\\uDEF5\\uDF37-\\uDF3B\\uDF44]|\\uD82F\\uDC9F|\\uD836[\\uDE87-\\uDE8B]|\\uD83A[\\uDD5E\\uDD5F]";
        var rx_pS = "[\\x24\\x2B\\x3C-\\x3E\\x5E\\x60\\x7C\\x7E\\xA2-\\xA6\\xA8\\xA9\\xAC\\xAE-\\xB1\\xB4\\xB8\\xD7\\xF7\\u02C2-\\u02C5\\u02D2-\\u02DF\\u02E5-\\u02EB\\u02ED\\u02EF-\\u02FF\\u0375\\u0384\\u0385\\u03F6\\u0482\\u058D-\\u058F\\u0606-\\u0608\\u060B\\u060E\\u060F\\u06DE\\u06E9\\u06FD\\u06FE\\u07F6\\u09F2\\u09F3\\u09FA\\u09FB\\u0AF1\\u0B70\\u0BF3-\\u0BFA\\u0C7F\\u0D4F\\u0D79\\u0E3F\\u0F01-\\u0F03\\u0F13\\u0F15-\\u0F17\\u0F1A-\\u0F1F\\u0F34\\u0F36\\u0F38\\u0FBE-\\u0FC5\\u0FC7-\\u0FCC\\u0FCE\\u0FCF\\u0FD5-\\u0FD8\\u109E\\u109F\\u1390-\\u1399\\u17DB\\u1940\\u19DE-\\u19FF\\u1B61-\\u1B6A\\u1B74-\\u1B7C\\u1FBD\\u1FBF-\\u1FC1\\u1FCD-\\u1FCF\\u1FDD-\\u1FDF\\u1FED-\\u1FEF\\u1FFD\\u1FFE\\u2044\\u2052\\u207A-\\u207C\\u208A-\\u208C\\u20A0-\\u20BE\\u2100\\u2101\\u2103-\\u2106\\u2108\\u2109\\u2114\\u2116-\\u2118\\u211E-\\u2123\\u2125\\u2127\\u2129\\u212E\\u213A\\u213B\\u2140-\\u2144\\u214A-\\u214D\\u214F\\u218A\\u218B\\u2190-\\u2307\\u230C-\\u2328\\u232B-\\u23FE\\u2400-\\u2426\\u2440-\\u244A\\u249C-\\u24E9\\u2500-\\u2767\\u2794-\\u27C4\\u27C7-\\u27E5\\u27F0-\\u2982\\u2999-\\u29D7\\u29DC-\\u29FB\\u29FE-\\u2B73\\u2B76-\\u2B95\\u2B98-\\u2BB9\\u2BBD-\\u2BC8\\u2BCA-\\u2BD1\\u2BEC-\\u2BEF\\u2CE5-\\u2CEA\\u2E80-\\u2E99\\u2E9B-\\u2EF3\\u2F00-\\u2FD5\\u2FF0-\\u2FFB\\u3004\\u3012\\u3013\\u3020\\u3036\\u3037\\u303E\\u303F\\u309B\\u309C\\u3190\\u3191\\u3196-\\u319F\\u31C0-\\u31E3\\u3200-\\u321E\\u322A-\\u3247\\u3250\\u3260-\\u327F\\u328A-\\u32B0\\u32C0-\\u32FE\\u3300-\\u33FF\\u4DC0-\\u4DFF\\uA490-\\uA4C6\\uA700-\\uA716\\uA720\\uA721\\uA789\\uA78A\\uA828-\\uA82B\\uA836-\\uA839\\uAA77-\\uAA79\\uAB5B\\uFB29\\uFBB2-\\uFBC1\\uFDFC\\uFDFD\\uFE62\\uFE64-\\uFE66\\uFE69\\uFF04\\uFF0B\\uFF1C-\\uFF1E\\uFF3E\\uFF40\\uFF5C\\uFF5E\\uFFE0-\\uFFE6\\uFFE8-\\uFFEE\\uFFFC\\uFFFD]|\\uD800[\\uDD37-\\uDD3F\\uDD79-\\uDD89\\uDD8C-\\uDD8E\\uDD90-\\uDD9B\\uDDA0\\uDDD0-\\uDDFC]|\\uD802[\\uDC77\\uDC78\\uDEC8]|\\uD805\\uDF3F|\\uD81A[\\uDF3C-\\uDF3F\\uDF45]|\\uD82F\\uDC9C|\\uD834[\\uDC00-\\uDCF5\\uDD00-\\uDD26\\uDD29-\\uDD64\\uDD6A-\\uDD6C\\uDD83\\uDD84\\uDD8C-\\uDDA9\\uDDAE-\\uDDE8\\uDE00-\\uDE41\\uDE45\\uDF00-\\uDF56]|\\uD835[\\uDEC1\\uDEDB\\uDEFB\\uDF15\\uDF35\\uDF4F\\uDF6F\\uDF89\\uDFA9\\uDFC3]|\\uD836[\\uDC00-\\uDDFF\\uDE37-\\uDE3A\\uDE6D-\\uDE74\\uDE76-\\uDE83\\uDE85\\uDE86]|\\uD83B[\\uDEF0\\uDEF1]|\\uD83C[\\uDC00-\\uDC2B\\uDC30-\\uDC93\\uDCA0-\\uDCAE\\uDCB1-\\uDCBF\\uDCC1-\\uDCCF\\uDCD1-\\uDCF5\\uDD10-\\uDD2E\\uDD30-\\uDD6B\\uDD70-\\uDDAC\\uDDE6-\\uDE02\\uDE10-\\uDE3B\\uDE40-\\uDE48\\uDE50\\uDE51\\uDF00-\\uDFFF]|\\uD83D[\\uDC00-\\uDED2\\uDEE0-\\uDEEC\\uDEF0-\\uDEF6\\uDF00-\\uDF73\\uDF80-\\uDFD4]|\\uD83E[\\uDC00-\\uDC0B\\uDC10-\\uDC47\\uDC50-\\uDC59\\uDC60-\\uDC87\\uDC90-\\uDCAD\\uDD10-\\uDD1E\\uDD20-\\uDD27\\uDD30\\uDD33-\\uDD3E\\uDD40-\\uDD4B\\uDD50-\\uDD5E\\uDD80-\\uDD91\\uDDC0]";


         /* Setting Page -> Tab Menu */

         $('.nav-tab').click(function () {
            $(".tab").removeClass('tab-active');
            $(".tab[data-id='" + $(this).attr('data-id') + "']").addClass("tab-active");
        });
        /* Setting Page -> Tab Menu */
        $('.sjb-tabs-wrap .nav-tab-wrapper a').on("click", function (e) {
            var id = $(e.target).attr("data-id");
            window.location.hash = id;
            //console.log(window.location.hash = id)
            $('.nav-tab').removeClass('nav-tab-active');
            $($(this).attr('data-id', window.location.hash = id )).addClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            return false;
        });
       

        // Font Awesome Icon Picker
        $('.sjb-job-feature-icon').iconpicker({placement: 'bottomRight' });

        /* Display Settings Tabs Previous State on Form Submit */
        if (window.location.hash.length > 0) {
            //$('.sjb-admin-settings').hide();
            var loc = window.location.hash.substring(1);
            $('.nav-tab').each(function(){
                var this_id = $(this).attr("data-id");
                $(this).removeClass('nav-tab-active')
                if(this_id == loc ){
                    $(this).addClass('nav-tab-active')
                }
            })

            $('.tab').each(function(){
                var this_id = $(this).attr("data-id");
                $(this).removeClass('tab-active')
                if(this_id == loc ){
                    $(this).addClass('tab-active')
                }
            })
        }

        var feature_form = $("#job_feature_form");
        var jobapp_form = $("#job_app_form");

        /* Setting Page -> Job Feature Settings */
        $('#settings_addFeature').on("click", function () {

            // Regular expression for all languages' characters
            var rgx_exp = new RegExp(rx_pP + "|" + rx_pS, "g");

            var field_name_raw = $('#settings_jobfeature_name').val(); // Get Raw value.
            var job_feature_value = $('#settings_jobfeature_value').val(); //Job Feature value
            field_name_raw = field_name_raw.trim();    // Remove White Spaces from both ends.
            var fieldName = field_name_raw.split(' ').join('_').toLowerCase().replace(rgx_exp, "_"); //Replace white space with _ & sanitize key. 

            var fieldIcon = $('#settings_job_feature_icon').val();

            if (fieldName != '') {
                var jobfeature_value_textbox;

                if ('' == job_feature_value) {
                    jobfeature_value_textbox = '<input type="hidden" value="empty" name="jobfeature_' + fieldName + '[value]">';
                } else {
                    jobfeature_value_textbox = '<input type="text" value="' + job_feature_value + '" name="jobfeature_' + fieldName + '[value]">';
                }
                if(fieldIcon != undefined && fieldIcon != '' ){

                $('#settings_job_features').append('<li class="sjb-modern-list jobfeature_' + fieldName + '"><strong>Field Name: </strong><label class="sjb-editable-label">' + field_name_raw + '</label>\n\
                    <input type="hidden" name="jobfeature_' + fieldName + '[label]" value="' + field_name_raw + '">\n\
                    ' + jobfeature_value_textbox + ' &nbsp;\n\
                    <input type="text" class="sjb-job-feature-icon iconpicker-element iconpicker-input" name="icon_jobfeature_' + fieldName + '" placeholder="fa ' + fieldIcon + '" value="' + fieldIcon + '" /><span class="input-group-addon"><i class="fa ' + fieldIcon + '"></i></span>\n\
                    <div class="button removeField" >' + application_form.settings_jquery_alerts['delete'] + '</div></li>');
                    $('#settings_job_feature_icon').val("fa fa-briefcase"); //Reset Icon value.
                }
                else{
                $('#settings_job_features').append('<li class="sjb-classic-list jobfeature_' + fieldName + '"><strong>Field Name: </strong><label class="sjb-editable-label">' + field_name_raw + '</label>\n\
                    <input type="hidden" name="jobfeature_' + fieldName + '[label]" value="' + field_name_raw + '">\n\
                    ' + jobfeature_value_textbox + ' &nbsp;\n\
                    <div class="button removeField" >' + application_form.settings_jquery_alerts['delete'] + '</div></li>');

                }

                $('#settings_jobfeature_name').val(""); //Reset Field value.
                $('#settings_jobfeature_value').val(""); //Reset Field value.
                $('.sjb_add_icon_fields .input-group-addon i').removeClass();
                $('.sjb_add_icon_fields .input-group-addon i').addClass("fa fa-briefcase");    
            } else {

                /* Empty Job Feature Alert -> Making Translation Ready String Through Script Locaization */
                alert(application_form.settings_jquery_alerts['empty_feature_name']);
                $('#settings_jobfeature_name').focus(); //Keep focus on this input
            }
        });

        /* Remove Job App or Job Feature Fields */
        $('.settings-fields').on('click', 'li .removeField', function () {
            if ('col-lg-5 col-md-5' === $(this).parent()[0]['className']) {
                $(this).parent().parent('li').remove();     // remove HTML
            } else {
                $(this).parent('li').remove();     // remove HTML
            }
        });

        /* On Click Save button */
        $('#jobfeature_form').on('click', function () {
            feature_form.submit();
        });

        $('#enable_settings_features').on('click', function(e){
            e.preventDefault();
            $('#sjb_settings_features').show();
            $('#sjb_jobpage_features').hide();
            $('#enable_job_feature').val('settingfeatures');
            $(this).removeClass('inactive');
            $(this).addClass('active');
            $('#enable_jobs_features').removeClass('active');
            $('#enable_jobs_features').addClass('inactive');
        });
        $('#enable_jobs_features').on('click', function(e){
            e.preventDefault();
            $('#sjb_jobpage_features').show();
            $('#enable_job_feature').val('jobfeatures');
            $('#sjb_settings_features').hide();
            $(this).removeClass('inactive');
            $(this).addClass('active');
            $('#enable_settings_features').removeClass('active');
            $('#enable_settings_features').addClass('inactive');
        });

        $('#enable_settings_application').on('click', function(e){
            e.preventDefault();
            $('#sjb_settings_application').show();
            $('#sjb_jobpage_application').hide();
            $('#enable_job_apps').val('settingapps');
            $(this).removeClass('inactive');
            $(this).addClass('active');
            $('#enable_jobs_application').removeClass('active');
            $('#enable_jobs_application').addClass('inactive');
        });

        $('#enable_jobs_application').on('click', function(e){
            e.preventDefault();
            $('#sjb_jobpage_application').show();
            $('#sjb_settings_application').hide();
            $('#enable_job_apps').val('jobapps');
            $(this).removeClass('inactive');
            $(this).addClass('active');
            $('#enable_settings_application').removeClass('active');
            $('#enable_settings_application').addClass('inactive');
        });


        /* Setting Page -> Job Application Form Fields */
        $('#app_add_field').on("click", function () {

            // Regular expression for all languages' characters
            var rgx_exp = new RegExp(rx_pP + "|" + rx_pS, "g");

            var app_field_raw = $('#setting_jobapp_name').val(); // Get Raw value.
            var app_field_raw = app_field_raw.trim(); // Remove White Spaces from both ends.
            var app_field_name = app_field_raw.split(' ').join('_').toLowerCase().replace(rgx_exp, "_"); //Replace white space with _.
            var app_field_type = $('#settings-jobapp-field-types').val();
            var field_options = $('#settings_jobapp_field_options');
            var fieldOptions = field_options.val();
            //var isRequired = $("#settings-jobapp-required-field").attr("checked") ? "checked" : "unchecked";
            var isRequired = $("#settings-jobapp-required-field").val();
            if ($("#settings-jobapp-required-field").prop("checked") == true) {
                var isRequired = 'checked'
            }
            else {
                var isRequired = 'unchecked';
            }

            if ($('#settings-jobapp-applicant-columns').is(':checked')) {
                var applicantColumns = 'checked';
            }
            else {
                var applicantColumns = 'unchecked';
            }

            var fieldTypeHtml = $('#settings-jobapp-field-types').html();

            if (app_field_name != '') {

                // Show Options for [Checkbox],[Radio] and [Dropdown]
                var application_field_option;
                if (!('checkbox' === app_field_type || 'dropdown' === app_field_type || 'radio' === app_field_type)) {
                    application_field_option = '<input type="text" name="jobapp_' + app_field_name + '[option]" value="' + fieldOptions + '" placeholder="Option1, option2, option3" style="display:none;">';
                } else {
                    if ('' === fieldOptions) {
                        alert(application_form.settings_jquery_alerts['empty_field_options']);
                        field_options.focus();
                        return false;
                    }

                    application_field_option = '<input type="text" name="jobapp_' + app_field_name + '[option]" value="' + fieldOptions + '" placeholder="Option1, option2, option3">';
                }

                $('#settings_app_form_fields').append('<li class="jobapp_' + app_field_name + '">\n\
                    <div class="col-lg-2 col-md-2"><label>' + app_field_raw + '</label>\n\
                        <input type="hidden" name="jobapp_' + app_field_name + '[label]" value="' + app_field_raw + '">\n\
                    </div>\n\
                    <div class="col-lg-2 col-md-2">\n\
                        <select class="settings_jobapp_field_type" name="jobapp_' + app_field_name + '[type]"  >\n\
                            ' + fieldTypeHtml +
                    '</select>\n\
                    ' + application_field_option + ' \n\
                    </div>\n\
                    <div class="col-lg-5 col-md-5">\n\
                        <label>\n\
                            <span class="sjb-form-group"><input type="checkbox" class="settings-jobapp-required-field"  ' + isRequired + '></span>\n\
                            <input type="hidden"   name="jobapp_' + app_field_name + '[optional]"  value="' + isRequired + '">' + application_form.settings_jquery_alerts['required'] + '&nbsp;\n\
                        </label>\n\
                        &nbsp;<div class="button removeField">' + application_form.settings_jquery_alerts['delete'] + '</div>&nbsp;\n\
                        <label>\n\
                            <input type="radio" class="settings-applicant-columns" name="[applicant_column]" ' + applicantColumns + '>' + application_form.settings_jquery_alerts['applicant_listing_col'] + '\n\
                            <input type="hidden" class="settings-jobapp-applicant-column" name="jobapp_' + app_field_name + '[applicant_column]" value="' + applicantColumns + '">\n\
                        </label>\n\
                    </div></li>');
                $('.jobapp_' + app_field_name + ' .' + app_field_type).attr('selected', 'selected');
                $('#setting_jobapp_name').val('');
                field_options.hide();
                field_options.val('');
                $('#settings-jobapp-field-types').val('section_heading');
                $('#settings_jobapp_required_field').prop('checked', true);
            } else {

                /* Empty Form Field Name Alert -> Making Translation Ready String Through Script Locaization */
                alert(application_form.settings_jquery_alerts['empty_field_name']);
                $('#setting_jobapp_name').focus(); //Keep focus on this input
            }
        });

        // Settings Field Types on Change
        $('#settings_app_form_fields').on('change', 'li .settings_jobapp_field_type', function () {
            var fieldType = $(this).val();

            if ('checkbox' == fieldType || 'dropdown' == fieldType || 'radio' == fieldType) {
                $(this).next().show();
            } else {
                $(this).next().hide();
                $(this).next().val('');
            }
        });

        // Field Types on Change
        $('#settings-jobapp-field-types').on('change', function () {
            var fieldType = $(this).val();

            if ('checkbox' == fieldType || 'dropdown' == fieldType || 'radio' == fieldType) {
                $(this).next().show();
            } else {
                $(this).next().hide();
                $(this).next().val('');
            }
        });

        /* Change the Required & Optional Field Parameter */
        $('#settings_app_form_fields').on("change", '.settings-jobapp-required-field', function () {
            var input = $(this);
            input.attr("checked") ? input.next().val("checked") : input.next().val("unchecked");
        });

        /* Change the Radio Button Check */
        $('#settings_app_form_fields').on("change", 'li .settings-applicant-columns', function () {
            $(".settings-applicant-columns").each(function () {
                var input = $(this);
                if (input.attr("checked")) {
                    input.removeAttr('checked');
                    input.next().val("unchecked");
                }
                else {
                }
            });
            $(this).next().val("checked");
            $(this).prop('checked', true);
        });

        /* Job Application Form Submission */
        $('#jobapp_btn').on('click', function () {
            jobapp_form.submit();
        });

        /**
         * Meta Boxes JS
         */

        /*Job Application Field Type change*/
        $('#jobapp_field_type').on('change', function (e) {
            var fieldType = $(this).val();

            if (fieldType == 'checkbox' || fieldType == 'dropdown' || fieldType == 'radio') {
                $('#jobapp_field_options').show();
            } else {
                $('#jobapp_field_options').hide();
                $('#jobapp_field_options').val('');
            }
        });

        /*Add Application Field (Group Fields)*/
        $('#addField').on("click", function (e) {

            // Regex Experession for all language characters
            var rgx_exp = new RegExp(rx_pP + "|" + rx_pS, "g");
            var fieldNameRaw = $('#jobapp_name').val(); // Get Raw value.
            var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
            var fieldName = fieldNameRaw.split(' ').join('_').toLowerCase().replace(rgx_exp, "_"); //Replace white space with _.
            var fieldType = $('#jobapp_field_type').val();
            var fieldOptions = $('#jobapp_field_options').val();
            var fieldRequired = $("#jobapp_required_field").val();
            if ($("#jobapp_required_field").prop("checked") == true) {
                var fieldRequired = 'checked'
            }
            else {
                var fieldRequired = 'unchecked';
            }

            if ($('#jobapp-applicant-columns').is(':checked')) {
                var applicantColumns = 'checked';
            }
            else {
                var applicantColumns = 'unchecked';
            }

            var fieldTypeHtml = $('#jobapp_field_type').html();

            if (fieldName != '') {
                if (!(fieldType == 'checkbox' || fieldType == 'dropdown' || fieldType == 'radio')) {
                    $('#app_form_fields').append('<li class="' + fieldName + '"><label>' + fieldNameRaw + '</label>\n\
                        <input type="hidden"  name="jobapp_' + fieldName + '[label]" value="' + fieldNameRaw + '">\n\
                        <select class="jobapp_field_type" name="jobapp_' + fieldName + '[type]">' + fieldTypeHtml + '</select>\n\
                        <input type="text" class="' + fieldName + ' jobapp_field_options" name="jobapp_' + fieldName + '[options]" value="' + fieldOptions + '" placeholder="Option1, option2, option3" style="display:none;">\n\
                        <input type="checkbox" class="jobapp-required-field"  ' + fieldRequired + '>\n\
                        <input type="hidden" name="jobapp_' + fieldName + '[optional]" value="' + fieldRequired + '">' + application_form.settings_jquery_alerts['required'] + '&nbsp; \n\
                        <div class="button removeField">' + application_form.settings_jquery_alerts['delete'] + '</div>\n\
                        <input type="radio" class="applicant-columns" name="[applicant_column]" ' + applicantColumns + '>' + application_form.settings_jquery_alerts['applicant_listing_col'] + '\n\
                        <input type="hidden" class="jobapp-applicant-column" name="jobapp_' + fieldName + '[applicant_column]" value="' + applicantColumns + '">\n\
                        </li>');
                    $('.' + fieldName + ' .' + fieldType).attr('selected', 'selected');
                    $('#jobapp_name').val('');
                    $('#jobapp_field_type').val('section_heading');
                    $('#jobapp_required_field').prop("checked", true);
                    $('#jobapp-applicant-columns').prop("checked", false);
                } else {
                    if ('' === fieldOptions) {
                        alert(application_form.settings_jquery_alerts['empty_field_options']);
                        $('#jobapp_field_options').focus();
                        return false;
                    }
                    $('#app_form_fields').append('<li class="' + fieldName + '"><label>' + fieldNameRaw + '</label>\n\
                        <input type="hidden"  name="jobapp_' + fieldName + '[label]" value="' + fieldNameRaw + '">\n\
                        <select class="jobapp_field_type" name="jobapp_' + fieldName + '[type]">' + fieldTypeHtml + '</select>\n\
                        <input type="text" class="' + fieldName + ' jobapp_field_options" name="jobapp_' + fieldName + '[options]" value="' + fieldOptions + '">\n\
                        <input type="checkbox" class="jobapp-required-field" ' + fieldRequired + ' >\n\
                        <input type="hidden" name="jobapp_' + fieldName + '[optional]" value="' + fieldRequired + '">' + application_form.settings_jquery_alerts['required'] + ' &nbsp;\n\
                        <div class="button removeField">' + application_form.settings_jquery_alerts['delete'] + '</div>\n\
                        <input type="radio" class="applicant-columns" name="[applicant_column]" ' + applicantColumns + '>' + application_form.settings_jquery_alerts['applicant_listing_col'] + '\n\
                        <input type="hidden" class="jobapp-applicant-column" name="jobapp_' + fieldName + '[applicant_column]" value="' + applicantColumns + '">\n\</li>');
                    $('.' + fieldName + ' .' + fieldType).attr('selected', 'selected');
                    $('#jobapp_name').val('');
                    $('#jobapp_field_type').val('text');
                    $('#jobapp_field_options').val('');
                    $('#jobapp_field_options').hide();
                    $('#jobapp_required_field').prop("checked", true);
                }
            } else {
                alert(application_form.settings_jquery_alerts['empty_field_name']);
                $('#jobapp_name').focus(); //Keep focus on this input
            }

        });

        /* Job Application Field Type change (added) */
        $('#app_form_fields').on('change', 'li .jobapp_field_type', function () {
            var fieldType = $(this).val();

            if (fieldType == 'checkbox' || fieldType == 'dropdown' || fieldType == 'radio') {
                $(this).next().show();
            } else {
                $(this).next().hide();
            }
        });

        /* Change the Required & Optional Field Parameter*/
        $('#app_form_fields').on("change", 'li .jobapp-required-field', function () {
            var input = $(this);
            input.attr("checked") ? input.next().val("checked") : input.next().val("unchecked");
        });

        /* Change the Radio Button Check */
        $('#app_form_fields').on("change", 'li .applicant-columns', function () {
            $(".applicant-columns").each(function () {
                var input = $(this);
                if (input.attr("checked")) {
                    input.removeAttr('checked');
                    input.next().val("unchecked");
                }
                else {
                }
            });
            $(this).next().val("checked");
            $(this).prop('checked', true);
        });

        // Add Job Feature
        $('#addFeature').click(function () {

            // Regex Experession for all language characters
            var rgx_exp = new RegExp(rx_pP + "|" + rx_pS, "g");

            var fieldNameRaw = $('#jobfeature_name').val(); // Get Raw value.
            var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
            var fieldName = fieldNameRaw.split(' ').join('_').toLowerCase().replace(rgx_exp, "_"); //Replace white space with _.
            var fieldVal = $('#jobfeature_value').val();
            var fieldIcon = $('#job_feature_icon').val();

            var fieldVal = fieldVal.trim();

            if (fieldName != '' && fieldVal != '') {
                if(fieldIcon != undefined && fieldIcon != '' ){
                    $('#job_features').append('<li class="' + fieldName + '"><label class="sjb-editable-label">' + fieldNameRaw + '</label><input type="hidden" name="jobfeature_' + fieldName + '[label]" value="' + fieldNameRaw + '"><input type="text" name="jobfeature_' + fieldName + '[value]" value="' + fieldVal + '" > &nbsp; <input type="text" class="sjb-job-feature-icon iconpicker-element iconpicker-input" name="icon_jobfeature_' + fieldName + '" placeholder="fa ' + fieldIcon + '" value="' + fieldIcon + '" /><span class="input-group-addon"><i class="fa ' + fieldIcon + '"></i></span><div class="button removeField">' + application_form.settings_jquery_alerts['delete'] + '</div></li>');
                    $('#jobfeature_name').val(""); // Reset Field value
                    $('#jobfeature_value').val(""); // Reset Field value
                    $('#job_feature_icon').val("fa fa-briefcase"); //Reset Icon value.
                    $('.sjb_add_icon_fields .input-group-addon i').removeClass();
                    $('.sjb_add_icon_fields .input-group-addon i').addClass("fa fa-briefcase");   
                }
                else {
                    $('#job_features').append('<li class="' + fieldName + '"><label class="sjb-editable-label">' + fieldNameRaw + '</label><input type="hidden" name="jobfeature_' + fieldName + '[label]" value="' + fieldNameRaw + '"><input type="text" name="jobfeature_' + fieldName + '[value]" value="' + fieldVal + '" > &nbsp;<div class="button removeField">' + application_form.settings_jquery_alerts['delete'] + '</div></li>');
                    $('#jobfeature_name').val(""); // Reset Field value
                    $('#jobfeature_value').val(""); // Reset Field value
                }
            } else {
                alert(application_form.settings_jquery_alerts['empty_feature_name']);
                $('#jobfeature_name').focus(); // Keep focus on this input
            }
        });

        // Remove Job app or job Feature Fields
        $('.jobpost_fields').on('click', 'li .removeField', function () {
            $(this).parent('li').remove();
        });
        /* Add Color Picker to all inputs that have 'sjb-color-picker' class */
        $('.sjb-color-picker').wpColorPicker();

        /* Sortable Fields */
        if ($('#settings_job_features , #settings_app_form_fields , #job_features , #app_form_fields').length) {
            $("#settings_job_features , #settings_app_form_fields , #job_features , #app_form_fields").sortable();
        }

        // Upload logo & show url in textbox
        if ($('.simple-job-board-upload-button').length) {
            window.simple_job_board_uploadfield = '';

            // On upload button click -> Show media upload iframe.
            $('.simple-job-board-upload-button').on('click', function () {
                window.simple_job_board_uploadfield = $('.upload_field', $(this).parents('.file_url'));
                tb_show('Upload', 'media-upload.php?type=image&TB_iframe=true', false);

                return false;
            });

            // Show uploaded logo url in textbox
            window.simple_job_board_send_to_editor_backup = window.send_to_editor;            
            window.send_to_editor = function (html) {
                if (window.simple_job_board_uploadfield) {
                    if ($('img', html).length >= 1) {
                        var image_url = $('img', html).attr('src');
                    } else {
                        var image_url = $($(html)[0]).attr('src');
                    }
                    
                    $(window.simple_job_board_uploadfield).val(image_url);
                    window.simple_job_board_uploadfield = '';

                    tb_remove();
                } else {
                    window.simple_job_board_send_to_editor_backup(html);
                }
            }
        }

        /**
         *  Upload Loader Image in Settings
         *  
         *  @since 2.7.0
         */
        if ($('.sjb-loader-image').length) {

            window.simple_job_board_uploadfield = '';
            window.simple_job_board_uploadfield_text = '';
            window.error_element = '';

            // On upload button click -> Show media upload iframe.
            $('.sjb-loader-image').on('click', function () {
                window.simple_job_board_uploadfield = $('.upload_field', $(this).parents('.sjb-loader-sec'));
                window.simple_job_board_uploadfield_text = $('.image_upload_field', $(this).parents('.sjb-loader-sec'));
                window.error_element = $('.invalid-loader-image', $(this).parents('.sjb-loader-sec'));
                window.error_element.text('');
                tb_show('Upload', 'media-upload.php?type=image&TB_iframe=true', false);
                return false;
            });
            window.simple_job_board_send_to_editor_backup = window.send_to_editor;

            window.send_to_editor = function (html) {
                if (window.simple_job_board_uploadfield) {

                    if ($('img', html).length >= 1) {
                        var image_url = $('img', html).attr('src');
                    } else {
                        var image_url = $($(html)[0]).attr('src');
                    }

                    var image_extension = image_url.split('.').pop().toLowerCase();

                    // File Extension Validation
                    if ('gif' === image_extension) {
                        window.error_element.hide();
                        window.error_element.removeClass("invalid").addClass("valid");
                        $(window.simple_job_board_uploadfield).attr('src', image_url);
                        $(window.simple_job_board_uploadfield_text).val(image_url);
                    } else {
                        window.error_element.show();
                        window.error_element.text(application_form.settings_jquery_alerts['invalid_extension']);
                        window.error_element.removeClass("valid").addClass("invalid");
                    }

                    window.simple_job_board_uploadfield = '';
                    tb_remove();
                } else {
                    window.simple_job_board_send_to_editor_backup(html);
                }
            }
        }

        // Check and uncheck the required checkbox
        $('input.jobapp-required-field').on('click', function () {
            if ($(this).prop("checked") == true) {
                $(this).val("checked");
                $(this).attr('checked', 'checked');

            }
            else if ($(this).prop("checked") == false) {
                $(this).val("unchecked");
                $(this).removeAttr('checked');
            }
        });

        $('.settings-jobapp-required-field').on('click', function () {
            if ($(this).prop("checked") == true) {
                $(this).val("checked");
                $(this).attr('checked', 'checked');
            }
            else if ($(this).prop("checked") == false) {
                $(this).val("unchecked");
                $(this).removeAttr('checked');
            }
        });

        // On remove button click -> Remove Image
        $('.remove-loader-image').on('click', function () {
            $('.upload_field').removeAttr('src');
            $('.upload_field').css('border', 'none');
            $('.image_upload_field').val('');
        });

        // Edit Form Builder Labels with class 'sjb-editable-label'
        $(".sjb-editable-label").each(function () {

            // Regex Experession for all language characters
            var rgx_exp = new RegExp(rx_pP + "|" + rx_pS, "g");

            // Reference the Label.
            var label = $(this);

            // Add a TextBox next to the Label.
            label.after('<input type = "text" style = "display:none;">');

            // Reference the TextBox.
            var textbox = label.next();

            // Assign the value of Label to TextBox.
            textbox.val(label.html());

            // On label click
            label.on('click', function () {
                label.hide();
                textbox.show();
                textbox.focus();
            });

            // When focus is lost from TextBox, hide TextBox and show Label.
            textbox.focusout(function () {

                // Get current & parent elements of label
                var label = $(this);
                label.hide();
                label.prev().html(label.val());
                label.next().val(label.val());

                // Key generator for keys
                var key = label.val().trim(); // Remove White Spaces from both ends.
                var key = key.split(' ').join('_').toLowerCase().replace(rgx_exp, "_"); //Replace white space with _.

                if ('app_form_fields' === label.parents(':eq(1)').attr('id')) {
                    var element_class = label.parent().attr('class').split(' ')[0];
                    var element = $('.' + element_class);

                    // Update indexes of all fields
                    label.next().attr('name', 'jobapp_' + key + '[label]');
                    element.find(".jobapp_field_type").attr('name', 'jobapp_' + key + '[type]');
                    element.find(".jobapp-field-options").attr('name', 'jobapp_' + key + '[options]');
                    element.find(".jobapp-optional-field").attr('name', 'jobapp_' + key + '[optional]');
                    element.find(".jobapp-applicant-column").attr('name', 'jobapp_' + key + '[applicant_column]');

                    $('.' + element_class).removeClass(element_class).addClass('jobapp_' + key);
                } else if ('settings_app_form_fields' === label.parents(':eq(2)').attr('id')) {

                    var element_class = label.parents(':eq(1)').attr('class').split(' ')[0];
                    var element = $('.' + element_class);

                    // Update indexes of all fields
                    label.next().attr('name', 'jobapp_' + key + '[label]');
                    element.find(".settings_jobapp_field_type").attr('name', 'jobapp_' + key + '[type]');
                    element.find(".settings-field-options").attr('name', 'jobapp_' + key + '[option]');
                    element.find(".settings-jobapp-optional-field").attr('name', 'jobapp_' + key + '[optional]');
                    element.find(".settings-jobapp-applicant-column").attr('name', 'jobapp_' + key + '[applicant_column]');
                    $('.' + element_class).removeClass(element_class).addClass('jobapp_' + key);
                } else {

                    // Update indexes of all fields                    
                    label.next().attr('name', 'jobfeature_' + key + '[label]');
                    label.next().next().attr('name', 'jobfeature_' + key + '[value]');
                }

                label.prev().show();
            });
        });
    });
})(jQuery);