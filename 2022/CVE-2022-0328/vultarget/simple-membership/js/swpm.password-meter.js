(function($){
    function check_pass_strength() {
        var type = $('#swpm-profile-page').attr('type'), pass1 = $('#pass1').val(), pass2 = $('#pass2').val(), strength;
        if (type === 'edit' && pass1.length === 0 && pass2.length === 0){            
            $('#pass1').closest('tr').removeClass('form-required');
        }
        else{
            $('#pass1').closest('tr').addClass('form-required');
        }        
        $('#pass-strength-result').removeClass('short bad good strong');
        if ( ! pass1 ) {
            $('#pass-strength-result').html( pwsL10n.empty );
            return;
        }

        strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass2 );
        switch ( strength ) {
            case 2:
                $('#pass-strength-result').addClass('bad').html( pwsL10n['bad'] );
                break;
            case 3:
                $('#pass-strength-result').addClass('good').html( pwsL10n['good'] );
                break;
            case 4:
                $('#pass-strength-result').addClass('strong').html( pwsL10n['strong'] );
                break;
            case 5:
                $('#pass-strength-result').addClass('short').html( pwsL10n['mismatch'] );
                break;
            default:
                $('#pass-strength-result').addClass('short').html( pwsL10n['short'] );
        }
    }

    $(document).ready( function() {
        $('#pass1').val('').keyup( check_pass_strength );
        $('#pass2').val('').keyup( check_pass_strength );
        $('#pass-strength-result').show();
    });

})(jQuery);
