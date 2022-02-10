(function ($) {
    $(document).ready(function () {
        $.extend(true, $.validationEngineLanguage.allRules, swpm_validationEngine_localization);
        $(".swpm-validate-form").validationEngine('attach');
    });
})(jQuery);