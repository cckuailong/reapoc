(function (w, $) {

  "use strict";

  $(function () {
    $(document).ready(function () {
      wp.media({
        button: {text: 'Insert'}
      });
      $('#add_survey_shortcode').on('change', function () {
        if(this.value != '') {
          wp.media.editor.insert('[perfect_survey id="' + this.value + '"]');
        }
      });
    });

  });

})(window, jQuery);
