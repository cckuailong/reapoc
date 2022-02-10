/**
 * Notifications client-side handling.
 * Depends on Aventura.Wp.Admin.Notices.
 */

;(function($, window, document, undefined) {
    var globalVars = adminNoticeGlobalVars || {};
    var notices = Aventura.Wp.Admin.Notices.getGlobal();
    notices.setOptions(globalVars)
            .setOptions('ajax_url', ajaxurl);
    notices.attach();
})(jQuery, top, document);