jQuery( document ).ready( function() {

    var $selector = jQuery( '.bulk-award-type' );

    $selector.select2();

    $selector.on( "select2:select", function ( e ) { 
        if( e.params.data.id == 'points' )
        {
            jQuery( '.bulk-award-point' ).fadeIn();
            jQuery( '.bulk-award-badge' ).fadeOut();
            jQuery( '.bulk-award-rank' ).fadeOut();
            jQuery( '.tools-revoke-btn' ).remove();
            jQuery( '.tools-bulk-assign-award-btn' ).addClass( 'award-points' );
            jQuery( '.tools-bulk-assign-award-btn' ).removeClass( 'award-badges' );
            jQuery( '.tools-bulk-assign-award-btn' ).removeClass( 'award-ranks' );
            jQuery( '.tools-bulk-assign-award-btn' ).html(`Update <span class="dashicons dashicons-update mycred-button1"></span> `);
        }
        else if( e.params.data.id == 'badges' )
        {
            jQuery( '.bulk-award-badge' ).fadeIn();
            jQuery( '.bulk-award-point' ).fadeOut();
            jQuery( '.bulk-award-rank' ).fadeOut();
            jQuery( '.tools-bulk-assign-award-btn' ).after(
                `<button class="button button-large large button-primary tools-revoke-btn" style="margin-left: 10px;">
                    <span class="dashicons dashicons-update mycred-button1"></span> 
                    Revoke
                </button>`
            );
            jQuery( '.tools-bulk-assign-award-btn' ).html(`<span class="dashicons dashicons-update mycred-button1"></span> Award`);
            jQuery( '.tools-bulk-assign-award-btn' ).addClass( 'award-badges' );
            jQuery( '.tools-bulk-assign-award-btn' ).removeClass( 'award-points' );
            jQuery( '.tools-bulk-assign-award-btn' ).removeClass( 'award-ranks' );
        }
        else if( e.params.data.id == 'ranks' )
        {
            jQuery( '.bulk-award-rank' ).fadeIn();
            jQuery( '.bulk-award-point' ).fadeOut();
            jQuery( '.bulk-award-badge' ).fadeOut();
            jQuery( '.tools-revoke-btn' ).remove();
            jQuery( '.tools-bulk-assign-award-btn' ).addClass( 'award-ranks' );
            jQuery( '.tools-bulk-assign-award-btn' ).removeClass( 'award-points' );
            jQuery( '.tools-bulk-assign-award-btn' ).removeClass( 'award-badges' );
            jQuery( '.tools-bulk-assign-award-btn' ).html(`Update <span class="dashicons dashicons-update mycred-button1"></span>`);
        }
    } );

    //Log Entry
    var $logEntry = jQuery( '.log-entry' ).is( ':checked' );

    if( $logEntry )
        jQuery( '.log-entry-row' ).show();
    else
        jQuery( '.log-entry-row' ).hide();

    jQuery(".log-entry").change(function() {
        if( this.checked ) 
            jQuery( '.log-entry-row' ).show();
        else
            jQuery( '.log-entry-row' ).hide();
    });


    //Pointtype
    $selector = jQuery( '.bulk-award-pt' );
    $selector.select2( {
        
    } );

    //Users
    $selector = jQuery( '.bulk-users' );
    $selector.select2();

    var $awardToAll = jQuery( '.award-to-all' ).is( ':checked' );

    if( !$awardToAll )
        jQuery( '.users-row' ).show();
    else
        jQuery( '.users-row' ).hide();

    jQuery(".award-to-all").change(function() {
        if( !this.checked ) 
            jQuery( '.users-row' ).show();
        else
            jQuery( '.users-row' ).hide();
    });

    //User Roles
    $selector = jQuery( '.bulk-roles' );
    $selector.select2();


    //Badges
    $selector = jQuery( '.bulk-badges' );
    $selector.select2();

    //Ranks
    $selector = jQuery( '.bulk-ranks' );
    $selector.select2();

    //Bulk Assign AJAX
    jQuery( document ).on( 'click', '.tools-bulk-assign-award-btn', function(e){

        e.preventDefault();

        var $confirm;

        var $selectedType = jQuery( '.bulk-award-type' ).find( ':selected' ).val();
        var $pointsToAward = jQuery( '[name="bulk_award_point"]' ).val();
        var $pointType = jQuery( '[name="bulk_award_pt"]' ).val();
        var $logEntry = jQuery( '.log-entry' ).prop('checked');
        var $logEntryText = jQuery( '[name="log_entry_text"]' ).val();
        var $awardToAllUsers = jQuery( '.award-to-all' ).prop('checked');
        var $users = JSON.stringify( jQuery( '[name="bulk_users"]' ).val() );
        var $user_roles = JSON.stringify( jQuery( '[name="bulk_roles"]' ).val() );
        
        if( $pointsToAward < 0 )
            $confirm = confirm( mycredTools.revokeConfirmText );
        else
            $confirm = confirm( mycredTools.awardConfirmText );

        if( !$confirm )
            return false;

        //Ranks 
        var $rankToAward = jQuery( '.bulk-ranks' ).find( ':selected' ).val();

        //Badges
        var $badgesToAward = JSON.stringify( jQuery( '[name="bulk_badges"]' ).val() );

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mycred-tools-assign-award',
                selected_type: $selectedType,
                points_to_award: $pointsToAward,
                point_type: $pointType,
                log_entry: $logEntry,
                log_entry_text: $logEntryText,
                award_to_all_users: $awardToAllUsers,
                users: $users,
                user_roles: $user_roles,
                //Ranks
                rank_to_award: $rankToAward,
                //Badges
                badges_to_award: $badgesToAward

            },
            beforeSend: function()
            {
                jQuery( '.tools-bulk-assign-award-btn' ).find( 'span' ).css( 'display','inherit' );
            },
            success: function( data )
            {
                jQuery( '.tools-bulk-assign-award-btn' ).find( 'span' ).hide();

                if( data.success === true && $pointsToAward < 0 )
                {
                    alert( mycredTools.successfullyDeducted );
                    resetForm();
                    return;
                }

                if( data.success === true )
                {
                    alert( mycredTools.successfullyAwarded );
                    resetForm();
                }

                if( data.success == 'pointsRequired' )
                {
                    alert( mycredTools.pointsRequired );
                }
                if( data.success == 'logEntryRequired' )
                {
                    alert( mycredTools.logEntryRequired );
                }
                if( data.success == 'userOrRoleIsRequired' )
                {
                    alert( mycredTools.userOrRoleIsRequired );
                }
                if( data.success == 'badgesFieldRequried' )
                {
                    alert( mycredTools.badgesFieldRequried );
                }
            }
        })
    } );

    //jQuery Bulk Revoke
    jQuery( document ).on( 'click', '.tools-revoke-btn', function(e){

        e.preventDefault();
        
        var $confirm = confirm( mycredTools.revokeConfirmText );

        if( !$confirm )
            return false;

        var $selectedType = jQuery( '.bulk-award-type' ).find( ':selected' ).val();
        var $badgesToRevoke = JSON.stringify( jQuery( '[name="bulk_badges"]' ).val() );
        var $awardToAllUsers = jQuery( '.award-to-all' ).prop('checked');
        var $users = JSON.stringify( jQuery( '[name="bulk_users"]' ).val() );
        var $user_roles = JSON.stringify( jQuery( '[name="bulk_roles"]' ).val() );


        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mycred-tools-assign-award',
                selected_type: $selectedType,
                revoke: 'revoke',
                badges_to_revoke: $badgesToRevoke,
                award_to_all_users: $awardToAllUsers,
                users: $users,
                user_roles: $user_roles,
            },
            beforeSend: function()
            {
                jQuery( '.tools-revoke-btn' ).find( 'span' ).css( 'display','inherit' );
            },
            success: function( data )
            {
                jQuery( '.tools-revoke-btn' ).find( 'span' ).hide();

                if( data.success === true )
                {
                    alert( mycredTools.successfullyRevoked );
                    resetForm();
                }
                if( data.success == 'userOrRoleIsRequired' )
                {
                    alert( mycredTools.userOrRoleIsRequired );
                }
                if( data.success == 'badgesFieldRequried' )
                {
                    alert( mycredTools.badgesFieldRequried );
                }
            }
        });
    } );
} );

//Reset Form
function resetForm()
{
    var $selectedValue = jQuery('.bulk-award-type').val();
    jQuery(".mycred-tools-ba-award-form").trigger('reset');
    jQuery('#bulk-users').val(null).trigger('change');
    jQuery('#bulk-roles').val(null).trigger('change');
    jQuery('#bulk-badges').val(null).trigger('change');
    jQuery('#bulk-ranks').val(null).trigger('change');
    jQuery(".log-entry").removeAttr("checked");
    jQuery( '.log-entry-row' ).hide();
    jQuery(".award-to-all").removeAttr("checked");
    jQuery( '.users-row' ).show();
    jQuery('.bulk-award-type').val( $selectedValue );
}