(function($) {

    $.fn.bindWithDelay = function( type, data, fn, timeout, throttle ) {

        if ( typeof data === "function" ) {
            throttle = timeout;
            timeout = fn;
            fn = data;
            data = undefined;
        }

        // Allow delayed function to be removed with fn in unbind function
        fn.guid = fn.guid || ($.guid && $.guid++);

        // Bind each separately so that each element has its own delay
        return this.each(function() {

            var wait = null;

            function cb() {
                var e = $.extend(true, { }, arguments[0]);
                var ctx = this;
                var throttler = function() {
                    wait = null;
                    fn.apply(ctx, [e]);
                };

                if (!throttle) { clearTimeout(wait); wait = null; }
                if (!wait) { wait = setTimeout(throttler, timeout); }
            }

            cb.guid = fn.guid;

            $(this).on(type, data, cb);
        });
    };

})(jQuery);

var REQUEST = new Object();
function chosen_ajaxify(id, ajax_url){
    var div_id = id;
    div_id = div_id.split("-").join("_");
    // if single
    if(jQuery('div#' + div_id + '_chosen').hasClass('chosen-container-single')){
        jQuery('div#' + div_id + '_chosen' + ' .chosen-search input').bindWithDelay('keyup', function(event){
            // ignore arrow key
            if(event.keyCode >= 37 && event.keyCode <= 40){
                return null;
            }
            // ignore enter
            if(event.keyCode == 13){
                return null;
            }
            // abort previous ajax
            if(REQUEST[id] != null){
                REQUEST[id].abort();
            }
            // get keyword and build regex pattern (use to emphasis search result)
            var keyword = jQuery('div#' + div_id + '_chosen' + ' .chosen-search input').val();
            if ( keyword.length < 3 ) {
                return null;
            }
            var keyword_pattern = new RegExp(keyword, 'gi');
            // remove all options of chosen
            jQuery('div#' + div_id + '_chosen ul.chosen-results').empty();
            // remove all options of original select
            jQuery("#"+id).empty();
            REQUEST[id] = jQuery.ajax({
                url: ajax_url + keyword,
                dataType: "json",
                success: function(response){
                    // map, just as in functional programming :). Other way to say "foreach"
                    // add new options to original select
                    jQuery('#'+id).append('<option value=""></option>');
                    jQuery.map(response, function(item){
                        jQuery('#'+id).append('<option value="' + item.value + '">' + item.caption + '</option>');
                    });
                },
                complete: function(){
                    keyword = jQuery('div#' + div_id + '_chosen' + ' .chosen-search input').val();
                    // update chosen
                    jQuery("#"+id).trigger("chosen:updated");
                    // some trivial UI adjustment
                    jQuery('div#' + div_id + '_chosen').removeClass('chosen-container-single-nosearch');

                    jQuery('div#' + div_id + '_chosen' + ' .chosen-search input').val(keyword);
                    jQuery('div#' + div_id + '_chosen' + ' .chosen-search input').removeAttr('readonly');
                    jQuery('div#' + div_id + '_chosen' + ' .chosen-search input').trigger('focus');
                    // emphasis keywords
                    jQuery('div#' + div_id + '_chosen' + ' .active-result').each(function(){
                        var html = jQuery(this).html();
                        jQuery(this).html(html.replace(keyword_pattern, function(matched){
                            return '<em>' + matched + '</em>';
                        }));
                    });
                }
            }, 500);
        });
    } else if(jQuery('div#' + div_id + '_chosen').hasClass('chosen-container-multi')){ // if multi
        jQuery('div#' + div_id + '_chosen' + ' input').bindWithDelay('keyup', function(event){
            // ignore arrow key
            if(event.keyCode >= 37 && event.keyCode <= 40){
                return null;
            }
            // ignore enter
            if(event.keyCode == 13){
                return null;
            }
            if(REQUEST[id] != null){
                REQUEST[id].abort();
            }
            var old_input_width = jQuery('div#' + div_id + '_chosen' + ' input').css('width');
            // get keyword and build regex pattern (use to emphasis search result)
            var keyword = jQuery(this).val();
            if ( keyword.length < 3 ) {
                return null;
            }

            var keyword_pattern = new RegExp(keyword, 'gi');
            // old values and captions
            var old_values = new Array();
            jQuery('#'+id+' option:selected').each(function(){
                old_value = jQuery(this).val();
                old_values.push(old_value);
            });
            // remove all options of chosen
            jQuery('div#' + div_id + '_chosen ul.chosen-results').empty();
            jQuery('option', '#'+id).not(':selected').remove();
            REQUEST[id] = jQuery.ajax({
                url: ajax_url + keyword,
                dataType: "json",
                success: function(response){
                    // map, just as in functional programming :). Other way to say "foreach"
                    jQuery.map(response, function(item){
                        // this is ineffective, is there any "in" syntax in javascript?
                        var found = false;
                        for(i=0; i<old_values[i]; i++){
                            if(old_values[i] == item.value){
                                found = true;
                                break;
                            }
                        }
                        if(!found){
                            jQuery('#'+id).append('<option value="' + item.value + '">' + item.caption + '</option>');
                        }
                    });
                },
                complete: function(response){
                    keyword = jQuery('div#' + div_id + '_chosen' + ' input').val();
                    jQuery("#"+id).trigger("chosen:updated");
                    jQuery('div#' + div_id + '_chosen').removeClass('chosen-container-single-nosearch');
                    jQuery('div#' + div_id + '_chosen' + ' input').val(keyword);
                    jQuery('div#' + div_id + '_chosen' + ' input').removeAttr('readonly');
                    jQuery('div#' + div_id + '_chosen' + ' input').css('width', old_input_width);
                    jQuery('div#' + div_id + '_chosen' + ' input').trigger('focus');
                    // put that underscores
                    jQuery('div#' + div_id + '_chosen' + ' .active-result').each(function(){
                        var html = jQuery(this).html();
                        jQuery(this).html(html.replace(keyword_pattern, function(matched){
                            return '<em>' + matched + '</em>';
                        }));
                    });
                }
            });
        }, 500);
    }
}

function chosen_depend_on(id, id_depend_on, ajax_url){
    var OLD_VALUE = jQuery('#'+id_depend_on).val();
    jQuery('#'+id_depend_on).on( 'change', function(event){
        var val = jQuery(this).val();
        if(val != OLD_VALUE){
            jQuery.ajax({
                'url' : ajax_url + val,
                'dataType' : 'json',
                'success' : function(response){
                    jQuery('#'+id).empty();
                    jQuery.map(response, function(item){
                        jQuery('#'+id).append('<option value="' + item.value + '">' + item.caption + '</option>');
                    });
                    jQuery('#'+id).trigger("chosen:updated");
                    jQuery('#'+id).trigger("change");
                }
            });
        }
    })
}
