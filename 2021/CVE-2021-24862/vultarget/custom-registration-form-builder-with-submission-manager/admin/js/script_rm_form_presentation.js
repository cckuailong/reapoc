var formStyleApp = angular.module('formStyleApp', []);
formStyleApp.controller('formStyleCtrl', function ($scope, $http) {
    $scope.selectedElement = '';
    $scope.styles = {};
    var styleAttrs = ["background-image", "border-width", "border-color", "border-radius"];
    var elementIds = ["rm_style_container", "rm_textfield", "rm_btnfield","rm_field_label","rm_section_name"];
    $scope.selectForm = function () {
        $scope.selectedElement = 'rm_style_container';
        jQuery(".rm_style_action").css('display', 'block');
        jQuery(".rm_style_action").addClass('form_top_right');
        jQuery(".rm_style_action").removeClass('form_field_right btn_field_right');
    }
    $scope.selectTextField = function () {
        $scope.selectedElement = 'rm_textfield';
        jQuery(".rm_style_action").css('display', 'block');
        jQuery(".rm_style_action").addClass('form_field_right');
        jQuery(".rm_style_action").removeClass('form_top_right btn_field_rights');
    }
    $scope.selectButtonField = function () {
        $scope.selectedElement = 'rm_btnfield';
        jQuery(".rm_style_action").css('display', 'block');
        jQuery(".rm_style_action").addClass('btn_field_right');
        jQuery(".rm_style_action").removeClass('form_top_right form_field_right');
    }
    $scope.saveStyles = function () {
        /* Prapring json object */
        for (var i = 0; i < elementIds.length; i++) {
            if (elementIds[i] == "rm_style_container") {
                $scope.styles.style_form = jQuery("#" + elementIds[i]).attr('style');
            }
            if (elementIds[i] == "rm_textfield") {
                $scope.styles.style_textfield = jQuery("#" + elementIds[i]).attr('style');

            }
            if (elementIds[i] == "rm_btnfield") {
                $scope.styles.style_btnfield = jQuery("#" + elementIds[i]).attr('style');
                $scope.styles.form_submit_btn_label = jQuery("#" + elementIds[i]).val();
            }

            if(elementIds[i]=="rm_field_label"){
                $scope.styles.style_label = jQuery("#" + elementIds[i]).attr('style');
            }
            
            if(elementIds[i]=="rm_section_name"){
                $scope.styles.style_section = jQuery("#" + elementIds[i]).attr('style');
            }
            
            
        }

        $scope.styles.form_id = jQuery('#rm_form_id').val();
        $scope.styles.placeholder_css= jQuery("#rm_custom_style").html();
        

        $scope.styles.btn_hover_color= jQuery("#rm_btnfield").attr("data-btn-hover-color");
        $scope.styles.field_bg_focus_color= jQuery("#rm_textfield").attr("data-field-bg-focus-color");
        $scope.styles.text_focus_color= jQuery("#rm_textfield").attr("data-field-text-focus-color");
    
        if($scope.styles.form_id=='login'){
            $scope.saveLoginFormSettings();
        }
        else{
            $scope.saveFormSettings();
        }
        
        
    }
    
    $scope.saveLoginFormSettings= function(){
        $http({
            method: 'POST',
            url: ajaxurl + "?action=rm_login_field_view_sett",
            data: JSON.stringify($scope.styles),
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).success(function (data) {
            if (data.toString().trim() === 'saved' || data.toString().trim() === 'not saved') {                
                var redirect_url = "?page=rm_login_field_manage";
                /*check if rdrto param is set in url*/
                var url = window.location.search;
                var pairs = url.split('&'), pair;
                for(var i = 0; i < pairs.length; i++) {
                    pair = pairs[i].split("=");
                    if(pair[0] === 'rdrto') {
                        redirect_url = "?page="+pair[1];
                        break;
                    }
                }
                window.location = redirect_url;
            }
        }).error(function (error) {
        });
    }
    
    $scope.saveFormSettings= function(){
        $http({
            method: 'POST',
            url: ajaxurl + "?action=rm_save_form_view_sett&rm_ajaxnonce="+pr_data.ajaxnonce,
            data: JSON.stringify($scope.styles),
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).success(function (data) {
            if (data.toString().trim() === 'saved' || data.toString().trim() === 'not saved') {                
                var redirect_url = "?page=rm_form_sett_manage";
                /*check if rdrto param is set in url*/
                var url = window.location.search;
                var pairs = url.split('&'), pair;
                for(var i = 0; i < pairs.length; i++) {
                    pair = pairs[i].split("=");
                    if(pair[0] === 'rdrto') {
                        redirect_url = "?page="+pair[1];
                        break;
                    }
                }
                window.location = redirect_url+'&rm_form_id=' + $scope.styles.form_id;
            }
        }).error(function (error) {
        });
    }
    
    $scope.resetAll = function () {
        /* Reset all the properties */
        
       
        $scope.styles.style_form='';
        $scope.styles.style_textfield='';
        $scope.styles.style_btnfield='';
        $scope.styles.form_submit_btn_label='';
        $scope.styles.style_label='';
        
        $scope.styles.form_id = jQuery('#rm_form_id').val();
        $scope.styles.placeholder_css= '';
        $scope.styles.btn_hover_color= '';
        $scope.styles.field_bg_focus_color= '';
        $scope.styles.text_focus_color= '';
        $scope.styles.style_section='';
        
        if($scope.styles.form_id=='login'){
            $http({
            method: 'POST',
            url: ajaxurl + "?action=rm_login_form_view_sett",
            data: JSON.stringify($scope.styles),
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
            }).success(function (data) {
                if (data.toString().trim() === 'saved' || data.toString().trim() === 'not saved')
                    location.reload();
            }).error(function (error) {
            });
        }
        else{
            $http({
            method: 'POST',
            url: ajaxurl + "?action=rm_save_form_view_sett&rm_ajaxnonce="+pr_data.ajaxnonce,
            data: JSON.stringify($scope.styles),
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).success(function (data) {
            if (data.toString().trim() === 'saved' || data.toString().trim() === 'not saved')
                location.reload();
        }).error(function (error) {
        });
        }
        
        
    }
});
formStyleApp.service('MediaUploader', function () {
    this.openUploader = function () {
        var mediaUploader;
        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: pr_data.upload_btn_title,
            button: {
                text: pr_data.upload_btn_title
            }, multiple: false});
        return mediaUploader;
    }
});
formStyleApp.directive('styleActionBox', function (MediaUploader) {
    return {
        restrict: 'E',
        scope: {
            ngModel: '=ngModel',
            selectedElement: '=selectedElement',
            elText: '=elText',
            elForm: '=elForm',
            elBtn: '=elBtn',
        },
        template: jQuery("#rm_styling_options").html(),
        link: function (scope, element, attributes, controller)
        {
            var selectedElement = attributes.selectedElement;
            var text_hover = '';
            var btn_hover = '';
            scope.executeAction = function () {
               // jQuery("[id=" + selectedElement + "]").css('border-style', 'solid');
                if (jQuery.trim(scope.styles.border_color)){
                    jQuery("[id=" + selectedElement + "]").css('border-color', "#" + scope.styles.border_color);
                    if(!jQuery.trim(scope.styles.border_width)){
                        var el = jQuery("[id=" + selectedElement + "]");
                        if(!el.css('border-width'))
                            el.css('border-width', "1px");
                }
                }
                if (jQuery.trim(scope.styles.border_radius)){
                    jQuery("[id=" + selectedElement + "]").css('border-radius', scope.styles.border_radius);
                    if(!jQuery.trim(scope.styles.border_width)){
                        var el = jQuery("[id=" + selectedElement + "]");
                        if(!el.css('border-width'))
                            el.css('border-width', "1px");
                    }
                }
                if (jQuery.trim(scope.styles.border_width))
                    jQuery("[id=" + selectedElement + "]").css('border-width', +scope.styles.border_width);
                
                if (jQuery.trim(scope.styles.background_color))
                    jQuery("[id=" + selectedElement + "]").css('background-color', "#" + scope.styles.background_color);
                if (scope.styles.btn_label)
                    jQuery("[id=" + selectedElement + "]").val(scope.styles.btn_label);
                if (scope.styles.btn_label == '')
                    jQuery("[id=" + selectedElement + "]").val('Submit');
                //alert( scope.styles.btn_font_color + "papa");
                if (scope.styles.btn_font_color)
                    jQuery("[id=" + selectedElement + "]").css('color', "#" + scope.styles.btn_font_color);
                // Operation specific to Form 
                if (selectedElement == "rm_style_container") {
                    // Check for Padding
                    if (scope.styles.padding)
                        jQuery("[id=" + selectedElement + "]").css('padding', scope.styles.padding);

                    // Section Background Color
                    if(scope.styles.section_bg_color)
                        jQuery("#rm_section_name").css('background-color', "#" +scope.styles.section_bg_color);

                    // Section Text Color
                    if(scope.styles.section_text_color)
                        jQuery("#rm_section_name").css('color', "#" +scope.styles.section_text_color);

                    // Section Text Color
                    if(scope.styles.section_text_style)
                        jQuery("#rm_section_name").css('font-style', scope.styles.section_text_style);
                }
                // Operation specific to Field 
                if (selectedElement == "rm_textfield") {
                    // Check for Label color
                    if (scope.styles.label_color)
                        jQuery("[id=rm_field_label]").css('color', "#" + scope.styles.label_color);
                    // Field Text color
                    if (scope.styles.text_color)
                        jQuery("[id=" + selectedElement + "]").css('color', "#" + scope.styles.text_color);
                    // Place holder color
                    if (scope.styles.placeholder_color) {
                        var placeholder_color = "#" + scope.styles.placeholder_color;
                        var style = '<style>' + '::-webkit-input-placeholder {color:' + placeholder_color + ';} ::-moz-placeholder {color:' + placeholder_color + ';} ::-ms-input-placeholder {color:' + placeholder_color + ';}</style>';
                        jQuery("#rm_custom_style").html(style);
                    }

                    // Outline color
                    if(scope.styles.text_outline_color)
                        jQuery("[id=" + selectedElement + "]").css('outline', "1px solid #" + scope.styles.text_outline_color);      

                }
                // Check for Border Style
                if (scope.styles.border_style){
                    jQuery("[id=" + selectedElement + "]").css('border-style', scope.styles.border_style);
                if(!jQuery.trim(scope.styles.border_width)){
                        var el = jQuery("[id=" + selectedElement + "]");
                        if(!el.css('border-width'))
                            el.css('border-width', "1px");
                    }
            }
                // Check for Border Style
                if (scope.styles.image_repeat)
                    jQuery("[id=" + selectedElement + "]").css('background-repeat', scope.styles.image_repeat);
            }
            scope.close = function () {
                jQuery(".rm_style_action").css('display', 'none');
            }
            scope.mediaUploader = function () {
                var mediaUploader = MediaUploader.openUploader();
                mediaUploader.on('select', function () {
                    attachments = mediaUploader.state().get('selection');
                    attachments.map(function (attachment) {
                        attachment = attachment.toJSON();
                        jQuery("#" + selectedElement).css('background-image', "url('" + attachment.sizes.full.url + "')");
                        console.log(selectedElement);
                    });
                });
                // Open the uploader dialog
                mediaUploader.open();
            }
            scope.removeBackImage = function () {
                jQuery("[id=" + selectedElement + "]").css('background-image', '');
            }
            scope.$watch('styles.btn_hover_color', function (newValue, oldValue) {
                if (angular.isDefined(newValue)) {
                   jQuery("[id=" + selectedElement + "]").removeClass("rm_btn_focus"); 
                   oldValue=  jQuery("#" + selectedElement).css('background-color');
                    if (newValue != undefined) {
                        newValue= "#" + newValue;
                        if (selectedElement == "rm_btnfield") {
                            jQuery("[id=" + selectedElement + "]").attr('data-btn-hover-color',newValue);
                            jQuery("[id=" + selectedElement + "]").hover(function () {
                                jQuery("[id=" + selectedElement + "]").css('background-color',  newValue);
                            }, function () { 
                                if(scope.styles.background_color!=undefined)
                                    jQuery("[id=" + selectedElement + "]").css('background-color', "#" + scope.styles.background_color);
                                else
                                    jQuery("[id=" + selectedElement + "]").css('background-color',oldValue);
                            })
                        }
                    } 
                } 
            });

            scope.$watch('styles.text_focus_color', function (newValue, oldValue) {
                if (angular.isDefined(newValue)) {
                    oldValue=jQuery("[id=" + selectedElement + "]").css("color");
                    jQuery("[id=" + selectedElement + "]").removeClass("rm_field_focus_text");
                    newValue= "#" + newValue;
                    if (selectedElement == "rm_textfield") {
                        jQuery("[id=" + selectedElement + "]").attr('data-field-text-focus-color',newValue);

                        jQuery("[id=" + selectedElement + "]").focus(function(){

                            jQuery(this).css("color",newValue);
                        });

                        jQuery("[id=" + selectedElement + "]").blur(function(){

                            jQuery(this).css("color",oldValue);
                        });
                    }
                }
            });

             scope.$watch('styles.field_bg_focus_color', function (newValue, oldValue) {
                if(angular.isDefined(newValue)){
                    jQuery("[id=" + selectedElement + "]").removeClass("rm_field_focus_bg");
                    oldValue=jQuery("[id=" + selectedElement + "]").css("background-color");

                    if (newValue != undefined) {
                        newValue= "#" + newValue;
                        if (selectedElement == "rm_textfield") {
                            jQuery("[id=" + selectedElement + "]").attr('data-field-bg-focus-color',newValue);

                            jQuery("[id=" + selectedElement + "]").focus(function(){

                                jQuery(this).css("background-color",newValue);
                            });

                            jQuery("[id=" + selectedElement + "]").blur(function(){

                                jQuery(this).css("background-color",oldValue);
                            });
                        }
                    }
                }
                
            });
        }
    };
})
function msieversion() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
    {
        version = parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
        console.log(version);
        if (version < 9) {
            alert(pr_data.older_ie);
        } else  // If another browser, return 0
        {
            //alert('otherbrowser');
        }
        return false;
    }
}
jQuery(document).ready(function () {
    msieversion();
});
