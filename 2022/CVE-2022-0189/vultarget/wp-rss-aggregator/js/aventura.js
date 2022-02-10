/**
 * Base functions and classes for Aventura namespace.
 * Creates that top-level namespace.
 * Depends on Xdn.
 */

;(function($, window, document, undefined) {
    // This is the base, top level namespace
    window.Aventura = window.Aventura || {};
})(jQuery, top, document);




;(function($, window, document, undefined) {
    var Aventura_Wp_Admin_Notices = Xdn.Object.Configurable.extend({
        
        attach:                 function() {
            var noticeClass, btnCloseClass, nonceElementClass, ajaxUrl, actionCode, dismissModeClassPrefix;
            var me = this;
            
            if ( !( noticeClass = this.getOptions( 'notice_class' ) ) )
                console.error( 'Could not initialize admin notices: "notice_class" option must be specified' );
            
            if ( !( btnCloseClass = this.getOptions( 'btn_close_class' ) ) )
                console.error( 'Could not initialize admin notices: "btn_close_class" must be specified' );
            
            if ( !( nonceElementClass = this.getOptions( 'nonce_class' ) ) )
                console.error( 'Could not initialize admin notices: "nonce_class" must be specified' );
            
            if ( !( ajaxUrl = this.getOptions( 'ajax_url' ) ) )
                console.error( 'Could not initialize admin notices: "ajax_url" must be specified' );

            if ( !( actionCode = this.getOptions( 'action_code' ) ) )
                console.error( 'Could not initialize admin notices: "action_code" must be specified' );

            if ( !( dismissModeClassPrefix = this.getOptions( 'dismiss_mode_class_prefix' ) ) )
                console.error( 'Could not initialize admin notices: "dismiss_mode_class_prefix" must be specified' );
            
            // Look through each notice
            $( '.'+noticeClass ).each(function(i, el) {
                var isDismissableAjax;
                var isDismissableFrontend;
                var isDismissable = !$(el).hasClass(dismissModeClassPrefix+'none');
                if (!isDismissable) return;

                isDismissableAjax = $(el).hasClass(dismissModeClassPrefix+'ajax');
                isDismissableFrontend = $(el).hasClass(dismissModeClassPrefix+'front');

                $(el).find('.'+btnCloseClass).on( 'click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (isDismissableFrontend) {
                        me.hideNotice(el);
                        return;
                    }

                    if (isDismissableAjax) {
                        $.post(ajaxUrl, {
                            // The name of the function to fire on the server
                            action: actionCode,
                            // The nonce value to send for the security check
                            nonce: $.trim( $(el).find('.'+nonceElementClass).text() ),
                            // The ID of the notice itself
                            notice_id: $(el).attr('id')
                        }, function (response) {
                            // Unsuccessful
                            if ( response !== '1' ) {
                                $(el).removeClass('updated').addClass('error');
                                console.error( response );
                                return;
                            }

                            me.hideNotice(el);
                        });

                        return;
                    }
                });
            });
        },

        hideNotice:             function(el) {
            $(el).remove();
        }
    });
    Xdn.assignNamespace(Aventura_Wp_Admin_Notices, 'Aventura.Wp.Admin.Notices');   
    
    var globalNotices;
    Aventura.Wp.Admin.Notices.getGlobal = function() {
        globalNotices = globalNotices || (function() {
            return new Aventura.Wp.Admin.Notices();
        })();
        return globalNotices;
    }
})(jQuery, top, document);

