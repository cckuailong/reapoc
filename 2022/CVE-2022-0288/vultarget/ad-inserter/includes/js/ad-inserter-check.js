jQuery(document).ready(function($) {
  $("#blocked-warning.warning-enabled").removeClass ('warning-enabled');
  $("#blocked-warning").hide ();

  var css_version = $('#ai-data').css ('font-family').replace(/[\"\']/g, '');
  if (css_version.indexOf ('.') == - 1) $("#blocked-warning").show ();
});
