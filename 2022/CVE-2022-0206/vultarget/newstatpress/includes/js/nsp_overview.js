(function($) {
  $( "#nsp_error-overview").hide();  
  $.post(nsp_externalAjax_overview.ajaxurl, {
    action : 'nsp_external',   
    VAR: "overview",
    KEY: nsp_externalAjax_overview.Key,
    PAR: "0",
    TYP: "HTML",
    
    // send the nonce along with the request
    postCommentNonce : nsp_externalAjax_overview.postCommentNonce    
  }, 
  function(data,status){
    $( "#nsp_loader-overview").hide();
    $( "#nsp_result-overview" ).html( data );
  }, "html").fail(function(error) { 
        $( "#nsp_loader-overview").hide(); 
        $( "#nsp_error-overview").show();        
      });
})(jQuery);  
