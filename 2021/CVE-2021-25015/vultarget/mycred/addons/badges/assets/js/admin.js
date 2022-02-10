jQuery(document).ready(function (){

    //Validation before switching to Open Badge
    jQuery(document).on( 'click', '#mycred-badge-is-open-badge', function (){
        if ( jQuery('input#mycred-badge-is-open-badge').is(':checked') ) {
            if ( confirm('Activating Open Badge loss will all Levels of this badge.') ) {
                return true;
            } else {
                return false;
            }
        }
    } );

    //Switch all to open badge
    jQuery(document).on( 'click', '#switch-all-to-open-badge', function (e){
        e.preventDefault();
        if ( confirm('Activating Open Badge loss will all Levels of this badge.') ) {
            jQuery.ajax({
                url: ajaxurl,
                data: {
                    action: 'mycred_switch_all_to_open_badge',
                },
                type: 'POST',
                beforeSend: function() {
                    jQuery('.mycred-switch-all-badges-icon').css("display", "inherit");;
                },
                success:function(data) {
                    jQuery('.mycred-switch-all-badges-icon').hide();
                    alert( data );
                }
            })
        } else {
            return false;
        }
    } );
})