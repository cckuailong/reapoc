jQuery.post(
  nsp_variablesAjax_yvisits.ajaxurl,
  {
    // here we declare the parameters to send along with the request
    // this means the following action hooks will be fired:
    // wp_ajax_nopriv_myajax-submit and wp_ajax_myajax-submit
    action : 'nsp_variables',
 
    // other parameters can be added along with "action"
    VAR    : nsp_variablesAjax_yvisits.VAR,
    URL    : nsp_variablesAjax_yvisits.URL,
    LIMIT  : nsp_variablesAjax_yvisits.LIMIT,
    FLAG   : nsp_variablesAjax_yvisits.FLAG,
    
    // send the nonce along with the request
    postCommentNonce : nsp_variablesAjax_yvisits.postCommentNonce      
  },

  function( response ) {
    document.getElementById(nsp_variablesAjax_yvisits.VAR).innerHTML=response;
  }
); 
