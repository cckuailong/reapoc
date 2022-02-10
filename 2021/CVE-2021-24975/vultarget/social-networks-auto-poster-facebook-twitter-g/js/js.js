jQuery(document).ready(function() { 
  if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) { //## Chrome Autofill is evil
    jQuery(window).load(function(){
        jQuery('input:-webkit-autofill').each(function(){ var text =jQuery(this).val(); var name = jQuery(this).attr('name'); jQuery(this).after(this.outerHTML).remove(); jQuery('input[name=' + name + ']').val(text);});
    });
  }    
  //## Submit Serialized Form - avoid Max.Vars limit.
  jQuery('#nsStFormMisc').submit(function() { var dataA = jQuery('#nsStForm').serialize(); jQuery('#nxsMainFromElementAccts').val(dataA); jQuery('#_wpnonce').val(jQuery('input#nxsSsPageWPN_wpnonce').val()); });
  jQuery('#nsStForm').submit(function() { jQuery('#nsStFormMisc').submit(); return false; });  
  var nxs_isPrevirew = false;   
  jQuery('#post-preview').click(function(event) { nxs_isPrevirew = true; });  
  jQuery('#post').submit(function(event) { if (nxs_isPrevirew == true) return; if (jQuery("#NXS_MetaFieldsIN").length==0) return;
      jQuery('body').append('<form id="nxs_tempForm"></form>'); jQuery("#NXS_MetaFieldsIN").appendTo("#nxs_tempForm");  
      var nxsmf = jQuery('#nxs_tempForm').serialize();  jQuery( "#NXS_MetaFieldsIN" ).remove(); jQuery('#nxs_snapPostOptions').val(nxsmf); //alert(nxsmf);  alert(jQuery('#nxs_snapPostOptions').val()); // return false; 
  });    
  jQuery('.nxs_postEditCtrl').on("change", function(e) { var psst = jQuery('#original_post_status').val(); if (psst=='auto-draft') return; var pid = jQuery('#post_ID').val();  var curr = jQuery(e.target); 
    jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"svEdFlds", cname: curr.attr('name'), cval: curr.val(), pid: pid,  nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
         //console.log(j);          
    }, "html")
  });  
  jQuery('#nxs_ntType').ddslick({ width: 200, imagePosition: "left", selectText: "Select network", onSelected: function (data) {doShowFillBlockX(data.selectedData.value);}});  
  jQuery('.nxs_acctcb').iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'});
  jQuery('.nxsGrpDoChb').iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'});
  jQuery('.nxsGrpDoChb').on('ifClicked', function(event){  if (jQuery(this).attr('type')=='radio') {  jQuery(this).attr('type', 'checkbox'); jQuery(this).val('1'); jQuery(this).iCheck('destroy'); 
    jQuery(this).iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'}); 
    jQuery(this).attr('id', jQuery(this).attr('id').replace('rbtn','do'));
    jQuery('.nxsGrpDoChb').on('ifChanged', function(event){ nxs_showHideMetaBoxBlocks(); }); 
  } });
  
  //## Show/Hide Metabx Blocks
  nxs_showHideMetaBoxBlocks();  
  
  jQuery('.nxsGrpDoChb').on("change", function(e) { jQuery('.nxstbl'+e.target.id).toggle(); });    
  //jQuery('.nxsGrpDoChb').on('ifChanged', function(event){ console.log("ifChanged"+event.target.id);  nxs_showHideMetaBoxBlocks(); });
  
  jQuery('.nxsGrpDoChb').on('ifChanged', function(event){ 
      if (jQuery(this).is(":checked")) { jQuery('.nxstbl'+event.target.id).show();  jQuery('#l'+event.target.id).find('span').html('[-]'); } else { jQuery('.nxstbl'+event.target.id).hide();  jQuery('#l'+event.target.id).find('span').html('[+]'); } 
  });
  //## +- in the post edit  
  jQuery('.nsx_iconedTitle').on("click", function(e) { var divid = jQuery('.nxstb'+jQuery(this).prop('id')); if (divid.length>0) { jQuery(this).find('span').html(jQuery(this).find('span').html() == '[-]' ? '[+]' : '[-]');  divid.toggle(); } });    
   
  //## Handle radiobutton for filters
  jQuery('.iradio_minimal-blue').on('ifChanged', function(event){  nxs_showHideMetaBoxBlocks(); });
  jQuery( ".iCheck-helper" ).mouseover(function() { 
    if (jQuery(this).parent().hasClass('iradio_minimal-blue')) nxs_showPopUpInfo('popShAttFLT', event, jQuery(this).parent().find("input").data('fltinfo'));
  });
  jQuery( ".iCheck-helper" ).mouseout(function() { nxs_hidePopUpInfo('popShAttFLT'); });
  
  jQuery( ".nxs_acctcbR" ).mouseover(function() {  nxs_showPopUpInfo('popShAttFLT', event, jQuery(this).parent().find("input").data('fltinfo')); });
  jQuery( ".nxs_acctcbR" ).mouseout(function() { nxs_hidePopUpInfo('popShAttFLT'); });
  
  jQuery( ".nxsShowQmark" ).mouseover(function() {  nxs_showPopUpInfo(jQuery(this).attr('longdesc'), event);});
  jQuery( ".nxsShowQmark" ).mouseout(function() { nxs_hidePopUpInfo(jQuery(this).attr('longdesc')); });
  
  if (jQuery('.nxs_authPopupIn').length !=0 ) jQuery('.nxs_authPopupIn').scrollTop(jQuery('.nxs_authPopupIn')[0].scrollHeight);
  
  
  jQuery('.riTo_button').click(function() { var ii = jQuery(this).data('ii'); var nt = jQuery(this).data('nt'); var pid = jQuery(this).data('pid'); 
     jQuery('#nxs_gPopupContent').html("<p>Getting Replies from "+nt+" ....</p>" + "<p></p>");
     jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, positionStyle: 'fixed'});  
     jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"importComments", nt:nt, pid:pid, ii:ii, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
          jQuery('#nxs_gPopupContent').html('<p> ' + j + '</p>' +'<input type="button" class="bClose" value="Close" />');     
     }, "html");          
      
      
  });            
  jQuery('.riToTW_button').click(function() { var data = { action: 'rePostToTW', id: jQuery('input#post_ID').val(), ri:1, nid:jQuery(this).attr('alt'), _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; callAjSNAP(data, 'Twitter'); });
  
  //## End of jQuery(document).ready(function()
});

//## API Specific Functions
//## GP
      function nxs_gpGetAllInfo(ii,force){ var u = jQuery('#apGPUName'+ii).val(); var p = jQuery('#apGPPass'+ii).val(); var pstAs = jQuery('#gpPostAs'+ii).val(); var pg = jQuery('#gpWhToPost'+ii).val(); jQuery("#gpPostAs"+ii).focus();
            jQuery('#gp'+ii+'rfrshImg').hide();  jQuery('#gp'+ii+'ldImg').show();
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPages", nt:"GP", u:u, p:p, ii:ii, pg:pg, pstAs:pstAs, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) { jQuery("#gpPostAs"+ii).html(j);  nxs_gpGetWhereToPost(ii,force); 
                   if (jQuery('#gpPostAs'+ii).val()!='a' && jQuery('#gpPostAs'+ii).val()!='p') jQuery('#gpPostAsNm'+ii).val(jQuery('#gpPostAs'+ii+' :selected').text()); else jQuery('#gpPostAsNm'+ii).val('p');
                 } else { jQuery("#nxsGPMsgDiv"+ii).html(j);   jQuery('#gp'+ii+'ldImg').hide();  jQuery('#gp'+ii+'rfrshImg').show(); }
            }, "html")          
      }
      function nxs_gpGetWhereToPost(ii,force){ var u = jQuery('#apGPUName'+ii).val(); var p = jQuery('#apGPPass'+ii).val(); var pstAs = jQuery('#gpPostAs'+ii).val(); var pg = jQuery('#gpWhToPost'+ii).val(); var pstAsNm = jQuery('#gpPostAs'+ii+' :selected').text();
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListWhereToPost", nt:"GP", u:u, p:p, ii:ii, pg:pg, pstAs:pstAs, pstAsNm:pstAsNm, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 jQuery("#gpWhToPost"+ii).html(j);  nxs_gpGetCommCats(ii,force);
            }, "html")          
      }
      function nxs_gpGetCommCats(ii,force){ var u = jQuery('#apGPUName'+ii).val(); var p = jQuery('#apGPPass'+ii).val(); var pstAs = jQuery('#gpPostAs'+ii).val(); var pg = jQuery('#gpWhToPost'+ii).val();
         if (pg.charAt(0)=='c'){ 
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGPCommInfo", nt:"GP", u:u, p:p, ii:ii, pg:pg, pstAs:pstAs, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 jQuery("#gpCommCat"+ii).html(j); jQuery('#nxsGPInfoDivComm'+ii).show();   jQuery('#gp'+ii+'ldImg').hide();  jQuery('#gp'+ii+'rfrshImg').show(); jQuery("#nxsGPMsgDiv"+ii).html("&nbsp;"); 
            }, "html")          
         } else { jQuery('#gp'+ii+'ldImg').hide();  jQuery('#gp'+ii+'rfrshImg').show(); jQuery("#nxsGPMsgDiv"+ii).html("&nbsp;");  }
      }
      function nxs_gpPostAsChange(ii, sObj){  if (sObj.val()!='a' && sObj.val()!='p') jQuery('#gpPostAsNm'+ii).val(jQuery('#gpPostAs'+ii+' :selected').text()); else jQuery('#gpPostAsNm'+ii).val('p');
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#gpPstAsCst'+ii).show(); } 
            else { jQuery('#gp'+ii+'ldImg').show(); jQuery('#gp'+ii+'rfrshImg').hide(); nxs_gpGetWhereToPost(ii,0); }
      }      
      function nxs_gpWhToPostChange(ii, sObj){  jQuery('#nxsGPInfoDivComm'+ii).hide(); if (jQuery('#gpPostAs'+ii).val()!='a' && jQuery('#gpPostAs'+ii).val()!='p') jQuery('#gpPostAsNm'+ii).val(jQuery('#gpPostAs'+ii+' :selected').text()); else jQuery('#gpPostAsNm'+ii).val('p');
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#gpPgIDcst'+ii).show(); }
          if (sObj.val().charAt(0)=='c'){ jQuery('#gp'+ii+'ldImg').show(); jQuery('#gp'+ii+'rfrshImg').hide();  var pg = sObj.val(); nxs_gpGetCommCats(ii,0); }
      }
//## RD
      function nxs_rdGetSRs(ii,force){ var u = jQuery('#apRDUName'+ii).val(); var p = jQuery('#apRDPass'+ii).val(); var rdSR = jQuery('#rdSubReddit'+ii).val(); jQuery("#rdSubReddit"+ii).focus();
            jQuery('#rd'+ii+'rfrshImg').hide();  jQuery('#rd'+ii+'ldImg').show(); jQuery("#nxsRDMsgDiv"+ii).html("&nbsp;"); jQuery("#rdSubReddit"+ii).html("<option value=\"\">Getting SubReddits.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfSubReddits", nt:"RD", u:u, p:p, ii:ii, rdSR:rdSR, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#rdSubReddit"+ii).html(j); else jQuery("#nxsRDMsgDiv"+ii).html(j); jQuery('#rd'+ii+'ldImg').hide(); jQuery('#rd'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_rdSRChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#rdSRIDCst'+ii).show(); } 
      }                  
//## PN
      function nxs_pnGetBoards(ii,force){ var u = jQuery('#apPNUName'+ii).val(); var p = jQuery('#apPNPass'+ii).val(); var pnBoard = jQuery('#pnBoard'+ii).val(); jQuery("#pnBoard"+ii).focus();
            jQuery('#pn'+ii+'rfrshImg').hide();  jQuery('#pn'+ii+'ldImg').show(); jQuery("#nxsPNMsgDiv"+ii).html("&nbsp;"); jQuery("#pnBoard"+ii).html("<option value=\"\">Getting boards.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPNBoards", nt:"PN", u:u, p:p, ii:ii, pnBoard:pnBoard, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#pnBoard"+ii).html(j); else jQuery("#nxsPNMsgDiv"+ii).html(j); jQuery('#pn'+ii+'ldImg').hide(); jQuery('#pn'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_pnBoardChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#pnBRDIDCst'+ii).show(); } 
      }                  
//## TR
      function nxs_trGetBlogs(ii,force){ var u = jQuery('#trappKey'+ii).val(); var p = jQuery('#trAuthUser'+ii).val(); var cBlog = jQuery('#trpgID'+ii).val(); jQuery("#trpgID"+ii).focus();
            jQuery('#tr'+ii+'rfrshImg').hide();  jQuery('#tr'+ii+'ldImg').show(); jQuery("#nxsTRMsgDiv"+ii).html("&nbsp;"); jQuery("#trpgID"+ii).html("<option value=\"\">Getting Blogs.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfBlogs", nt:"TR", u:u, p:p, ii:ii, cBlog:cBlog, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#trpgID"+ii).html(j); else jQuery("#nxsTRMsgDiv"+ii).html(j); jQuery('#tr'+ii+'ldImg').hide(); jQuery('#tr'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_trBlogChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#trInpCst'+ii).show(); } 
      }     
//## LI
      function nxs_liGetPages(ii,force){ var u = jQuery('#liappKey'+ii).val(); var p = jQuery('#liAuthUser'+ii).val(); var cBlog = jQuery('#lipgID'+ii).val(); jQuery("#lipgID"+ii).focus();
            jQuery('#li'+ii+'rfrshImg').hide();  jQuery('#li'+ii+'ldImg').show(); jQuery("#nxsLIMsgDiv"+ii).html("&nbsp;"); jQuery("#lipgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPagesLIV2", nt:"LI", u:u, p:p, ii:ii, cBlog:cBlog, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#lipgID"+ii).html(j); else jQuery("#nxsLIMsgDiv"+ii).html(j); jQuery('#li'+ii+'ldImg').hide(); jQuery('#li'+ii+'rfrshImg').show();
            }, "html")          
      }      
      function nxs_liPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#liInpCst'+ii).show(); } 
      }
      function nxs_li2GetPages(ii,force){ var u = jQuery('#apLIUName'+ii).val(); var p = jQuery('#apLIPass'+ii).val(); jQuery('#nxsLI2InfoDiv'+ii).show(); var pgcID = jQuery('#li2pgID'+ii).val(); var pggID = jQuery('#li2GpgID'+ii).val(); jQuery("#li2pgID"+ii).focus();
            jQuery('#li'+ii+'2rfrshImg').hide();  jQuery('#li'+ii+'2ldImg').show();   jQuery('#li'+ii+'3ldImg').show(); jQuery("#nxsLI2MsgDiv"+ii).html("&nbsp;"); jQuery("#li2pgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPagesNXS", nt:"LI", u:u, p:p, ii:ii, pgcID:pgcID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
               if (j.indexOf('<option')>-1) jQuery("#li2pgID"+ii).html(j); else jQuery("#nxsLI2MsgDiv"+ii).html(j); jQuery('#li'+ii+'2ldImg').hide(); jQuery('#nxsLI2GInfoDiv'+ii).show(); jQuery("#li2GpgID"+ii).html("<option value=\"\">Getting Groups.......</option>");                 
               jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfGroupsNXS", nt:"LI", u:u, p:p, ii:ii, pggID:pggID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                  if (j.indexOf('<option')>-1) jQuery("#li2GpgID"+ii).html(j); else jQuery("#nxsLI2MsgDiv"+ii).html(j);  jQuery('#li'+ii+'3ldImg').hide(); jQuery('#li'+ii+'2rfrshImg').show();
               }, "html")                  
            }, "html")          
      }
      function nxs_li2PageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#li2InpCst'+ii).show(); jQuery("#li2InpCst"+ii).focus(); } 
      }     
      function nxs_li2GPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#li2GInpCst'+ii).show(); jQuery("#li2GInpCst"+ii).focus(); } 
      }
//## XI
      function nxs_xi2GetPages(ii,force){ var u = jQuery('#apXIUName'+ii).val(); var p = jQuery('#apXIPass'+ii).val(); jQuery('#nxsXI2InfoDiv'+ii).show(); var pgcID = jQuery('#xi2pgID'+ii).val(); var pggID = jQuery('#xi2GpgID'+ii).val(); jQuery("#xi2pgID"+ii).focus();
            jQuery('#xi'+ii+'2rfrshImg').hide();  jQuery('#xi'+ii+'2ldImg').show();   jQuery('#xi'+ii+'3ldImg').show(); jQuery("#nxsxi2MsgDiv"+ii).html("&nbsp;"); jQuery("#xi2pgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getPgsList", nt:"XI", u:u, p:p, ii:ii, pgcID:pgcID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
               if (j.indexOf('<option')>-1) jQuery("#xi2pgID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j); jQuery('#xi'+ii+'2ldImg').hide(); jQuery('#nxsXI2GInfoDiv'+ii).show(); jQuery("#xi2GpgID"+ii).html("<option value=\"\">Getting Groups.......</option>");                 
               jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGrpList", nt:"XI", u:u, p:p, ii:ii, pggID:pggID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                  if (j.indexOf('<option')>-1) jQuery("#xi2GpgID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j); pggID = jQuery('#xi2GpgID'+ii).val();  var pgfID = jQuery('#xi2GfID'+ii).val(); 
                  
                  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGrpForums", nt:"XI", u:u, p:p, ii:ii, pggID:pggID, pgfID:pgfID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                     if (j.indexOf('<option')>-1) jQuery("#xi2GfID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j);  jQuery('#xi'+ii+'3ldImg').hide(); jQuery('#xi'+ii+'2rfrshImg').show();
                  }, "html")
                  
                  
               }, "html")
            }, "html")          
      }       
      function nxs_xi2PageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#xi2InpCst'+ii).show(); jQuery("#xi2InpCst"+ii).focus(); } 
      }     
      function nxs_xi2GPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#xi2GInpCst'+ii).show(); jQuery("#xi2GInpCst"+ii).focus(); } 
            else { jQuery('#xi'+ii+'3ldImg').show(); jQuery('#xi'+ii+'2rfrshImg').hide(); 
            
             var pggID = jQuery('#xi2GpgID'+ii).val();  var pgfID = jQuery('#xi2GfID'+ii).val();  var u = jQuery('#apXIUName'+ii).val(); var p = jQuery('#apXIPass'+ii).val();
                  
                  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getGrpForums", nt:"XI", u:u, p:p, ii:ii, pggID:pggID, pgfID:pgfID, force:1, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                     if (j.indexOf('<option')>-1) jQuery("#xi2GfID"+ii).html(j); else jQuery("#nxsXI2MsgDiv"+ii).html(j);  jQuery('#xi'+ii+'3ldImg').hide(); jQuery('#xi'+ii+'2rfrshImg').show();
                  }, "html")
                  
            
            }
      }          
      function nxs_xi2GfChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#xi2GfInpCst'+ii).show(); jQuery("#xi2GfInpCst"+ii).focus(); } 
      }          
//## FB      
      function nxs_fbGetPages(ii,force){ var u = jQuery('#fbappKey'+ii).val(); var p = jQuery('#fbAuthUser'+ii).val(); var pgID = jQuery('#fbpgID'+ii).val(); jQuery("#fbpgID"+ii).focus();
            jQuery('#fb'+ii+'rfrshImg').hide();  jQuery('#fb'+ii+'ldImg').show(); jQuery("#nxsFBMsgDiv"+ii).html("&nbsp;"); jQuery("#fbpgID"+ii).html("<option value=\"\">Getting Pages.......</option>");
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getItFromNT", "fName":"getListOfPages", nt:"FB", u:u, p:p, ii:ii, pgID:pgID, force:force, isOut:1, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  
                 if (j.indexOf('<option')>-1) jQuery("#fbpgID"+ii).html(j); else jQuery("#nxsFBMsgDiv"+ii).html(j); jQuery('#fb'+ii+'ldImg').hide(); jQuery('#fb'+ii+'rfrshImg').show();
            }, "html")          
      }
      function nxs_fbPageChange(ii, sObj){  
          if (sObj.val()=='a'){ sObj.hide(); jQuery('#fbInpCst'+ii).show(); jQuery("#fbInpCst"+ii).focus(); } 
      }                 
      
//## Common Input to DropDown Function.             
      function nxs_InpToDDChange(tObj) { var sObj = jQuery('#'+tObj.data('tid')); sObj.prepend( jQuery("<option/>", { value: tObj.val(), text: tObj.val() })); tObj.hide(); sObj.prop("selectedIndex", 0).trigger('change'); sObj.show(); }            
      function nxs_InpToDDBlur(tObj) {  var sObj = jQuery('#'+tObj.data('tid')); tObj.hide(); sObj.prop("selectedIndex", 0).trigger('change'); sObj.show(); }
//## / API Specific Functions

function nxs_showHideMetaBoxBlocks(){ 
    jQuery('.nxsGrpDoChb').each( function( i, e ) { if (jQuery(this).is(":checked")) { jQuery('.nxstbl'+e.id).show();  jQuery('#l'+e.id).find('span').html('[-]'); } else { jQuery('.nxstbl'+e.id).hide();  jQuery('#l'+e.id).find('span').html('[+]'); } });
}
function nxs_hideMetaBoxBlocks(){ 
    jQuery('.nxsGrpDoChb').each( function( i, e ) { jQuery('.nxstbl'+e.id).hide(); });
}

(function($) {
  $(function() {
     jQuery('#nxs_snapAddNew').bind('click', function(e) { e.preventDefault(); jQuery('#nxs_spPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, follow: [false, false], position: [65, 50]}); });
     jQuery('.nxs_snapAddNew').bind('click', function(e) { e.preventDefault(); var tk = jQuery(this).data('nt'); var gk = jQuery('#nxs_ntType ul li').index( jQuery('#nxs_ntType ul li input[value="'+tk+'"]').parent().parent()); 
       jQuery('#nxs_ntType').ddslick('select', {index: gk }); jQuery('.clNewNTSets').hide(); jQuery('#do'+tk+'Div').show();  jQuery('#nxs_spPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, amsl:400, follow: [false, false], position: [65,'auto']}); 
     });
     jQuery('#showLic').bind('click', function(e) { e.preventDefault(); jQuery('#showLicForm').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, follow: [false, false]}); });                                 
     jQuery('#showLic2').bind('dblclick', function(e) { e.preventDefault(); jQuery('#showLicForm').bPopup({ modalClose: false, appendTo: '#wpbody', opacity: 0.6, follow: [false, false]}); });                                 
     
     jQuery('#showLic2x').bind('click', function(e) { e.preventDefault(); jQuery('#showLicForm').bPopup({ modalClose: false, appendTo: '#wpbody', opacity: 0.6, follow: [false, false]}); });                                 
     jQuery('#checkAPI2x').bind('click', function(e) { e.preventDefault(); jQuery("#checkAPI2xLoadingImg").show(); doLic(); });                                      
     
     jQuery('#nxs_resetSNAPInfoPosts').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPInfoPosts','','','Please wait....','')  });
     jQuery('#nxs_deleteAllSNAPInfo').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('deleteAllSNAPInfo','','','Please wait....','')  });
     
     jQuery('#nxs_resetSNAPQuery').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPQuery','','','Please wait....','')  });
     jQuery('#nxs_resetSNAPCron').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPCron','','','Please wait....','')  });
     jQuery('#nxs_resetSNAPCache').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('resetSNAPCache','','','Please wait....','')  });
     
     jQuery('#nxs_restBackup').bind('click', function(e) { e.preventDefault(); var r = confirm("Are you sure?"); if (r == true) nxs_doAJXPopup('restBackup','','','Please wait....','')  });
     
     /* // Will move it here later for better compatibility
     jQuery('.button-primary[name="update_NS_SNAutoPoster_settings"]').bind('click', function(e) { var str = jQuery('input[name="post_category[]"]').serialize(); jQuery('div.categorydivInd').replaceWith('<input type="hidden" name="pcInd" value="" />'); 
       str = str.replace(/post_category/g, "pk"); jQuery('div.categorydiv').replaceWith('<input type="hidden" name="post_category" value="'+str+'" />');  
     });
     */
  });
})(jQuery);

function nxs_showNewPostFrom() { jQuery('#nxs_popupDiv').bPopup({ modalClose: false, speed: 450, transition: 'slideDown', contentContainer:'#nxs_popupDivCont', loadUrl: 'admin-ajax.php', 'loadData': { "action": "nxs_snap_aj", "nxsact":"getNewPostDlg", "_wpnonce":jQuery('input#nxsSsPageWPN_wpnonce').val() }, loadCallback: function(){ jQuery("#nxsNPLoader").hide();  }, onClose: function(){ jQuery("#nxsNPLoader").show(); }, opacity: 0.6, follow: [false, false]});  }

function nxs_doNP(){ jQuery("#nxsNPLoaderPost").show(); var mNts = []; jQuery('input[name=nxsNPNts]:checked').each(function(i){ mNts[i] = jQuery(this).val(); });
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"doNewPost", mText: jQuery('#nxsNPText').val(), mTitle: jQuery('#nxsNPTitle').val(), mType: jQuery('input[name=nxsNPType]:checked').val(), mLink: jQuery('#nxsNPLink').val(), mImg: jQuery('#nxsNPImg').val(), mNts: mNts, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  jQuery("#nxsNPResult").html(j); jQuery("#nxsNPLoaderPost").hide(); jQuery("#nxsNPCloseBt").val('Close'); }, "html")     
}

function nxs_updtRdBtn(idd){
    jQuery('#rbtn'+idd).attr('type', 'checkbox'); //alert('rbtn'+idd);
}

//## Functions
function nxs_doResetPostSettings(pid){    
  jQuery.post(ajaxurl,{action: 'nxs_delPostSettings', pid: pid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){  window.location = window.location.href.split("#")[0]; }, "html")     
}
function nxs_expSettings(){
  jQuery.generateFile({ filename: 'nx-snap-settings.txt', content: jQuery('input#nxsSsPageWPN_wpnonce').val(), script: 'admin-ajax.php'});
}
// AJAX Functions

function nxs_getItFromNT(nt,ii,outElemID,fName,params){ jQuery("#"+nt+"LoadingImg"+ii).show();
  jQuery.post(ajaxurl,{nt:nt,ii:ii,fName:fName,params:params, nxs_mqTest:"'", action: 'nxs_snap_aj', "nxsact":"getItFromNT", id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    jQuery("#"+outElemID).html(j); jQuery("#"+nt+"LoadingImg"+ii).hide();
  }, "html")
}

function nxs_setRpstAll(t,ed,ii){ jQuery("#nxsLoadingImg"+t+ii).show(); var lpid = jQuery('#'+t+ii+'SetLPID').val();
  jQuery.post(ajaxurl,{t:t,ed:ed,ii:ii, nxs_mqTest:"'", action: 'SetRpstAll', id: 0, lpid:lpid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
    alert('OK. Done.'); jQuery("#nxsLoadingImg"+t+ii).hide();
  }, "html")
}

function nxs_fillTime(dd){ var d=new Date(dd); jQuery('#nxs_aa').val(d.getFullYear()); jQuery('#nxs_mm').val(d.getMonth()+1); jQuery('#nxs_jj').val(d.getDate()); jQuery('#nxs_hh').val(d.getHours()); jQuery('#nxs_mn').val(d.getMinutes()); }
function nxs_makeTimeTxt(){ var m=new Array();m[0]="January";m[1]="February";m[2]="March";m[3]="April";m[4]="May";m[5]="June";m[6]="July";m[7]="August";m[8]="September";m[9]="October";m[10]="November";m[11]="December";  
    return m[jQuery('#nxs_mm').val()-1]+', '+jQuery('#nxs_jj').val()+' '+jQuery('#nxs_aa').val()+' '+jQuery('#nxs_hh').val()+':'+jQuery('#nxs_mn').val()+':00'; 
}

//## Select/Unselect Categories
function nxs_chAllCatsL(ch, divID){ jQuery("#"+divID+" input:checkbox[name='post_category[]']").attr('checked', ch==1); }

function nxs_showPopUpInfo(pid, e, info){ if (info!='undefined') jQuery('div#'+pid).html(jQuery('div#'+pid).data('text')+info); if (!jQuery('div#'+pid).is(":visible")) jQuery('div#'+pid).show().css('top', e.pageY+5).css('left', e.pageX+25).appendTo('body'); }
function nxs_hidePopUpInfo(pid){ jQuery('div#'+pid).hide(); }

function showPopShAtt(imid, e){ if (!jQuery('div#popShAtt'+imid).is(":visible")) jQuery('div#popShAtt'+imid).show().css('top', e.pageY+5).css('left', e.pageX+25).appendTo('body'); }
function hidePopShAtt(imid){ jQuery('div#popShAtt'+imid).hide(); }
function doSwitchShAtt(att, idNum){
  if (att==1) { if (jQuery('#apFBAttch'+idNum).is(":checked")) {jQuery('#apFBAttchShare'+idNum).prop('checked', false);}} else {if( jQuery('#apFBAttchShare'+idNum).is(":checked")) jQuery('#apFBAttch'+idNum).prop('checked', false);}
}      
      
function doShowHideAltFormat(){ if (jQuery('#NS_SNAutoPosterAttachPost').is(':checked')) { 
  jQuery('#altFormat').css('margin-left', '20px'); jQuery('#altFormatText').html('Post Announce Text:'); } else {jQuery('#altFormat').css('margin-left', '0px'); jQuery('#altFormatText').html('Post Text Format:');}
}
function doShowHideBlocks(blID){ /* alert('#do'+blID+'Div'); */ if (jQuery('#apDo'+blID).is(':checked')) jQuery('#do'+blID+'Div').show(); else jQuery('#do'+blID+'Div').hide();}
function doShowHideBlocks1(blID, shhd){ if (shhd==1) jQuery('#do'+blID+'Div').show(); else jQuery('#do'+blID+'Div').hide();}            
function doShowHideBlocks2(blID){ if (jQuery('#apDoS'+blID).val()=='0') { jQuery('#do'+blID+'Div').show(); jQuery('#do'+blID+'A').text('[Hide Settings]'); jQuery('#apDoS'+blID).val('1'); } 
  else { jQuery('#do'+blID+'Div').hide(); jQuery('#do'+blID+'A').text('[Show Settings]'); jQuery('#apDoS'+blID).val('0'); }
}

function doGetHideNTBlock(bl,ii){ if (jQuery('#apDoS'+bl+ii).length<1 || jQuery('#apDoS'+bl+ii).val()=='0') { 
    if (jQuery('#do'+bl+ii+'Div').length<1) {  jQuery("#"+bl+ii+"LoadingImg").show();
      jQuery.post(ajaxurl,{nxsact:'getNTset',nt:bl,ii:ii,action:'nxs_snap_aj', _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';
        //## Show data
        jQuery('#nxsNTSetDiv'+bl+ii).html(j); nxs_doTabsInd('#nxsNTSetDiv'+bl+ii); nxs_V4_filter_mainJS(jQuery);
        jQuery("#"+bl+ii+"LoadingImg").hide(); jQuery('#do'+bl+ii+'Div').show(); jQuery('#do'+bl+ii+'AG').text('[Hide Settings]'); jQuery('#apDoS'+bl+ii).val('1');        
        if (jQuery('#rbtn'+bl.toLowerCase()+ii).attr('type') != 'checkbox') { jQuery('#rbtn'+bl.toLowerCase()+ii).attr('type', 'checkbox');          
        jQuery('#rbtn'+bl.toLowerCase()+ii).iCheck('destroy'); jQuery('#rbtn'+bl.toLowerCase()+ii).iCheck({checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%'});}
      }, "html")
    } else { jQuery('#do'+bl+ii+'Div').show(); jQuery('#do'+bl+ii+'AG').text('[Hide Settings]'); jQuery('#apDoS'+bl+ii).val('1'); }
  } else { jQuery('#do'+bl+ii+'Div').hide(); jQuery('#do'+bl+ii+'AG').text('[Show Settings]'); jQuery('#apDoS'+bl+ii).val('0'); }
}

function nxs_showHideBlock(iid, iclass){jQuery('.'+iclass).hide(); jQuery('#'+iid).show();}
            
function doShowFillBlock(blIDTo, blIDFrm){ jQuery('#'+blIDTo).html(jQuery('#do'+blIDFrm+'Div').html());}
function doCleanFillBlock(blIDFrm){ jQuery('#do'+blIDFrm+'Div').html('');}
            
function doShowFillBlockX(blIDFrm){ jQuery('.clNewNTSets').hide(); jQuery('#do'+blIDFrm+'Div').show(); }
            
function doDuplAcct(nt, blID, blName){ var data = { action:'nxs_snap_aj', nxsact: 'nsDupl', id: 0, nt: nt, id: blID, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; 
  jQuery.post(ajaxurl, data, function(response) { window.location = window.location.href.split("#")[0]; });
}            
            
function doDelAcct(nt, blID, blName){  var answer = confirm("Remove "+blName+" account?");
  if (answer){ var data = {action: 'nxs_snap_aj',"nxsact":"delNTAcc", nt: nt, id: blID, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};     
    jQuery.post(ajaxurl, data, function(response) {  
        if (jQuery('#dom'+nt.toUpperCase()+blID+'Div').length){ jQuery('#dom'+nt.toUpperCase()+blID+'Div').hide(); var num = jQuery('#nxsNumOfAcc_'+nt).html(); num = num-1; jQuery('#nxsNumOfAcc_'+nt).html(num); }
          else  window.location = window.location.href.split("#")[0];  
    });
  }           
}      

function callAjSNAP(data, label) { 
  var style = "position: fixed; display: none; z-index: 1000; top: 50%; left: 50%; background-color: #E8E8E8; border: 1px solid #555; padding: 15px; width: 350px; min-height: 80px; margin-left: -175px; margin-top: -40px; text-align: center; vertical-align: middle;";
  jQuery('body').append("<div id='test_results' style='" + style + "'></div>");
  jQuery('#test_results').html("<p>Sending update to "+label+"</p>" + "<p><img src='http://gtln.us/img/misc/ajax-loader-med.gif' /></p>");
  jQuery('#test_results').show();            
  jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
    jQuery('#test_results').html('<p> ' + response + '</p>' +'<input type="button" class="button" name="results_ok_button" id="results_ok_button" value="OK" />');
    jQuery('#results_ok_button').click(remove_results);
  });            
}
function remove_results() { jQuery("#results_ok_button").unbind("click"); jQuery("#test_results").remove();
  if (typeof document.body.style.maxHeight == "undefined") { jQuery("body","html").css({height: "auto", width: "auto"}); jQuery("html").css("overflow","");}
  document.onkeydown = "";document.onkeyup = "";  return false;
}

function mxs_showHideFrmtInfo(hid){
  if(!jQuery('#'+hid+'Hint').is(':visible')) mxs_showFrmtInfo(hid); else {jQuery('#'+hid+'Hint').hide(); jQuery('#'+hid+'HintInfo').html('Show format info');}
}
function mxs_showFrmtInfo(hid){
  jQuery('#'+hid+'Hint').show(); jQuery('#'+hid+'HintInfo').html('Hide format info'); 
}
function nxs_clLog(){
  jQuery.post(ajaxurl,{action: 'nxs_clLgo', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery("#nxslogDiv").html('');
  }, "html")
}
function nxs_rfLog(){
  jQuery.post(ajaxurl,{action: 'nxs_rfLgo', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery("#nxslogDiv").html(j);
  }, "html")
}
function nxs_prxTest(){  jQuery('#nxs_pchAjax').show();
  jQuery.post(ajaxurl,{action: 'nxs_prxTest', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery('#nxs_pchAjax').hide(); jQuery("#prxList").html(j);  
  }, "html")
}
function nxs_prxGet(){  jQuery('#nxs_pchAjax').show();
  jQuery.post(ajaxurl,{action: 'nxs_prxGet', id: 0, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery('#nxs_pchAjax').hide(); jQuery("#prxList").html(j);  
  }, "html")
}
function nxs_TRSetEnable(ptype, ii){
  //if (ptype=='I'){ jQuery('#apTRMsgTFrmt'+ii).attr('disabled', 'disabled'); jQuery('#apTRDefImg'+ii).removeAttr('disabled'); } else { jQuery('#apTRDefImg'+ii).attr('disabled', 'disabled');  jQuery('#apTRMsgTFrmt'+ii).removeAttr('disabled'); }                
}
function nxsTRURLVal(ii){ var val = jQuery('#apTRURL'+ii).val(); var srch = val.toLowerCase().indexOf('http://www.tumblr.com/blog/');
  if (srch>-1) { jQuery('#apTRURL'+ii).css({"background-color":"#FFC0C0"}); jQuery('#apTRURLerr'+ii).html('<br/>Incorrect URL: Please note that URL of your Tumblr Blog should be your public URL. (i.e. like http://nextscripts.tumblr.com/, not http://www.tumblr.com/blog/nextscripts'); } else { jQuery('#apTRURL'+ii).css({"background-color":"#ffffff"}); jQuery('#apTRURLerr'+ii).text(''); }            
}

function nxs_hideTip(id){  
  jQuery.post(ajaxurl,{action: 'nxs_hideTip', id: id, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
     jQuery('#'+id).hide(); 
  }, "html")
}

function nxs_actDeActTurnOff(objId){ if (jQuery('#'+objId).val()!='1') jQuery('#'+objId+'xd').show(); else jQuery('#'+objId+'xd').hide();}

//## V4 Port
function nxs_svSetAdv(nt,ii,divIn,divOut,loc,isModal){ jQuery(':focus').blur();
    //## jQuery clone fix for empty textareas
    (function (original) { jQuery.fn.clone = function () { var result = original.apply(this, arguments),
      my_textareas = this.find('textarea').add(this.filter('textarea')), result_textareas = result.find('textarea').add(result.filter('textarea')), 
      my_selects = this.find('select').add(this.filter('select')), result_selects = result.find('select').add(result.filter('select'));
      for (var i = 0, l = my_textareas.length; i < l; ++i) jQuery(result_textareas[i]).val( jQuery(my_textareas[i]).val() );    
      for (var i = 0, l = my_selects.length; i < l; ++i) for (var j = 0, m = my_selects[i].options.length; j < m; ++j) if (my_selects[i].options[j].selected === true) result_selects[i].options[j].selected = true;    
      return result;
    }; }) (jQuery.fn.clone); 
    //## /END jQuery clone fix for empty textareas
    if (isModal=='1') { jQuery("#"+divIn).addClass("loading");  jQuery("#nxsSaveLoadingImg"+nt+ii).show(); } else { jQuery("#"+nt+ii+"ldImg").show(); jQuery("#"+nt+ii+"rfrshImg").hide();   }
    if (divIn=='nxsAllAccntsDiv' && jQuery("#nxsAllAccntsDiv").length && jQuery("#nxsSettingsDiv").length) jQuery("#nxsSettingsDiv").appendTo("#nxsAllAccntsDiv");
    
    var isOut=''; if (typeof(divOut)!='undefined' && divOut!='') isOut = '<input type="hidden" name="isOut" value="1" />';
    
    frmTxt = '<div id="nxs_tmpDiv_'+nt+ii+'" style="display:none;"><form id="nxs_tmpFrm_'+nt+ii+'"><input name="action" value="nxs_snap_aj" type="hidden" /><input name="nxsact" value="setNTset" type="hidden" /><input name="nxs_mqTest" value="\'" type="hidden" /><input type="hidden" name="_wp_http_referer" value="'+jQuery("input[name='_wp_http_referer']").val()+'" /><input type="hidden" name="nxs_wp_http_referer" value="'+jQuery("input[name='_wp_http_referer']").val()+'" /><input type="hidden" name="nxsSsPageWPN_wpnonce" value="'+jQuery('#nxsSsPageWPN_wpnonce').val()+'" /><input type="hidden" name="_wpnonce" value="'+jQuery('#nxsSsPageWPN_wpnonce').val()+'" />'+isOut+'</form></div>'; //console.log(frmTxt);
    jQuery("body").append(frmTxt); jQuery("#"+divIn).clone(true).appendTo("#nxs_tmpFrm_"+nt+ii); var serTxt = jQuery("#nxs_tmpFrm_"+nt+ii).serialize(); jQuery("#nxs_tmpDiv_"+nt+ii).remove(); // console.log(serTxt); // alert(serTxt);
    jQuery.ajax({ type: "POST", url: ajaxurl, data: serTxt, 
      success: function(data){ if (isModal=='1') jQuery("#nxsAllAccntsDiv").removeClass("loading"); else {  jQuery("#"+nt+ii+"rfrshImg").show(); jQuery("#"+nt+ii+"ldImg").hide(); }
      if(typeof(divOut)!='undefined' && divOut!='' && data!='OK') jQuery('#'+divOut).html(data); 
      if (isModal=='1') {  jQuery("#nxsSaveLoadingImg"+nt+ii).hide(); jQuery("#doneMsg"+nt+ii).show(); jQuery("#doneMsg"+nt+ii).delay(600).fadeOut(3200); }
        if (loc!='') { if (loc!='r') window.location = loc; else window.location = jQuery(location).attr('href'); } 
      }
    });
    
}

function nxs_showHideFrmtInfo(hid){
  if(!jQuery('#'+hid+'Hint').is(':visible')) nxs_showFrmtInfo(hid); else {jQuery('#'+hid+'Hint').hide(); jQuery('#'+hid+'HintInfo').html('Show format info');}
}
function nxs_showFrmtInfo(hid){
  jQuery('#'+hid+'Hint').show(); jQuery('#'+hid+'HintInfo').html('Hide format info'); 
}
//## /V4 Port

//## Export File
(function(jQuery){ jQuery.generateFile = function(options){ options = options || {};
        if(!options.script || !options.filename || !options.content){
            throw new Error("Please enter all the required config options!");
        }
        var iframe = jQuery('<iframe>',{ width:1, height:1, frameborder:0, css:{ display:'none' } }).appendTo('body');
        var formHTML = '<form action="" method="post"><input type="hidden" name="filename" /><input type="hidden" name="_wpnonce" /><input type="hidden" name="action" value="nxs_getExpSettings" /></form>';
        setTimeout(function(){
            var body = (iframe.prop('contentDocument') !== undefined) ? iframe.prop('contentDocument').body : iframe.prop('document').body;    // IE
            body = jQuery(body); body.html(formHTML); var form = body.find('form');
            form.attr('action',options.script);
            form.find('input[name=filename]').val(options.filename);            
            form.find('input[name=_wpnonce]').val(options.content);
            form.submit();
        },50);
    };
})(jQuery);


function nxs_rfLog(){ var prm = new Array(0,0,0,0,0); if (jQuery('#nxs_shLogSE').prop( "checked" )) prm[0] = 1;   // console.log(jQuery('#nxs_shLogSE').prop( "checked" ));  console.log(jQuery('#nxs_shLogSE'));
  if (jQuery('#nxs_shLogSI').prop( "checked" )) prm[1] = 1;  if (jQuery('#nxs_shLogCE').prop( "checked" )) prm[2] = 1; 
  if (jQuery('#nxs_shLogCI').prop( "checked" )) prm[3] = 1; if (jQuery('#nxs_shLogSY').prop( "checked" )) prm[4] = 1;   //         console.log(prm);
  jQuery.post(ajaxurl,{action: 'nxs_rfLgo', id: 0, 'prm[]':prm, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}, function(j){ var options = '';                    
    jQuery("#nxslogDiv").html(j);
  }, "html")
}

function nxs_V4_filter_mainJS($){
    var selectized_terms;
    var selectized_metas;
    
    $.datepicker.regional['en'] = {
        closeText: 'Done',
        prevText: 'Prev',
        nextText: 'Next',
        currentText: 'Today',
        monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
        weekHeader: 'Wk',
        dateFormat: 'mm/dd/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['en']);
    
    
    // jQuery(".nxsSelIt").not(".selectized").selectize( { create: true,  persist: false, plugins:  ['remove_button'] } );
    
    //jQuery('select[class="nxsSelIt"]:not(".selectized")').selectize( { create: true,  persist: false, plugins:  ['remove_button'] } );
    
    
    jQuery('select[class="nxsSelItAjxAdd"]:not(".tokenize")').tokenize({ datas: "json.php", displayDropdownOnFocus: true });
    jQuery('select[class="nxsSelItAjx"]:not(".tokenize")').tokenize({ datas: "json.php", displayDropdownOnFocus: true, newElements:false });
    jQuery('select[class="nxsSelIt"]:not(".tokenize")').tokenize({displayDropdownOnFocus: true, newElements:false });
    /*
    jQuery('select[class="nxs_term_names"]:not(".selectized")').selectize( { create: true,  persist: false, valueField: 'value', labelField: 'title', sortField:  'title',  plugins:  ['remove_button'] } );
    jQuery('select[class="nxs_term_names"]:not(".selectized")').selectize( { create: true,  persist: false, valueField: 'value', labelField: 'title', sortField:  'title',  plugins:  ['remove_button'] } );
    jQuery('select[class="nxs_tax_names"]:not(".selectized")').selectize( { valueField: 'value', labelField: 'title', sortField:  'title', plugins:  ['remove_button'],
      onChange: function( item ) { console.log( "===" ); console.log( this.$input[0].id ); console.log( "=X="); filter_select( item, '#'+this.$input[0].id.replace("tax_names", "term_names") ); }
    });
    
    //console.log( jQuery('select[class^="nxs_tax_names"]').length );
    
    jQuery('select[class^="nxs_tax_names"]:not(".selinitialized")').each( function( i, e ) {  
        if (jQuery(e).prop('tagName')=='SELECT' && jQuery(e)[0].selectize.items.length) { jQuery(e).addClass('selinitialized'); //  console.log( "==**" ); console.log( jQuery(e).prop('id') ); console.log( "##==" ); 
           var termID = e.id.replace("tax_names", "term_names");   filter_select( jQuery(e)[0].selectize.items[0],  '#'+termID, true );
        }         
     });
    
      */
    
    //## Change values when taxonimy changed
    $( '.nxs_tax_names' ).on( 'change', function() { var tgtID =  $(this).attr('id').replace('_tax_','_term_'); $('#'+tgtID).attr('data-type', $(this).val()); $('#'+tgtID).val(''); $('#'+tgtID).tokenize().remap(); } );  
    //## Add new meta set
    $( '.nxs_add_meta_compare' ).on( 'click', function(e) { e.preventDefault(); var ii = $( this ).attr( "data-ii" ); var nt = $( this ).attr( "data-nt" );  var count_compares = +$( '#nxs_count_meta_'+nt+ii ).val( function( ii, val ) { return ( + val + 1 ); } ).val(); 
       
       var clone = jQuery('#nxs_meta_namesDiv'+nt+ii).clone();              
       var clo = jQuery('#nxs_meta_namesDiv'+nt+ii);
       
       jQuery(clone).find("#nxs"+nt+ii+"_meta_key").attr('name', nt+'['+ii+'][nxs_meta_key'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_meta_key").attr('id', 'nxs'+nt+ii+'_meta_key'+'_'+count_compares);
       jQuery(clone).find("#nxs"+nt+ii+"_meta_operator").attr('name', nt+'['+ii+'][nxs_meta_operator'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_meta_operator").attr('id', 'nxs'+nt+ii+'_meta_operator'+'_'+count_compares);
       
       jQuery(clone).find("#nxs"+nt+ii+"_meta_value").attr('name', nt+'['+ii+'][nxs_meta_value'+'_'+count_compares+'][]');     
       jQuery(clone).find("#nxs"+nt+ii+"_meta_value").attr('id', 'nxs'+nt+ii+'_meta_value'+'_'+count_compares);    // tg[0][nxs_meta_names][]
       
       jQuery(clone).find("#nxs"+nt+ii+"_meta_relation").attr('name', nt+'['+ii+'][nxs_meta_relation'+'_'+count_compares+']');
       jQuery(clone).find("#nxs"+nt+ii+"_meta_relation").attr('id', 'nxs'+nt+ii+'_meta_relation'+'_'+count_compares);       
       
       jQuery(clone).find("#nxs"+nt+ii+"_meta_names"+'_'+count_compares).find('option').remove().end();       
       clone.appendTo('#nxs_meta_namesTopDiv'+nt+ii); jQuery('#nxs_meta_namesCond'+nt+ii).show();        
       
       jQuery(clone).find(".nxs_metas_leftPanel").attr('style','display:inline-block;');       
        
       return false;
        
    } );
    
    
    $( '.nxs_remove_term_compare' ).on( 'click', function() {
        jQuery(this).parent().parent().remove();
    } );
    $( '.nxs_remove_meta_compare' ).on( 'click', function() {
        jQuery(this).parent().parent().remove();
    } );
    
    //## Filters - Taxonomies : Add More Button 
    $( '.nxs_add_term_compare' ).on( 'click', function(e) { e.preventDefault(); var ii = $( this ).attr( "data-ii" ); var nt = $( this ).attr( "data-nt" );  var count_compares = +$( '#nxs_count_term_'+nt+ii ).val( function( ii, val ) { return ( + val + 1 ); } ).val(); 
       
       var clone = jQuery('#nxs_term_namesDiv'+nt+ii).clone();              
       var clo = jQuery('#nxs_term_namesDiv'+nt+ii);
       
       jQuery(clone).find("#nxs"+nt+ii+"_tax_names").attr('name', nt+'['+ii+'][nxs_tax_names'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_tax_names").attr('id', 'nxs'+nt+ii+'_tax_names'+'_'+count_compares);
       jQuery(clone).find("#nxs"+nt+ii+"_term_operator").attr('name', nt+'['+ii+'][nxs_term_operator'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_operator").attr('id', 'nxs'+nt+ii+'_term_operator'+'_'+count_compares);
       jQuery(clone).find("#nxs"+nt+ii+"_term_children").attr('name', nt+'['+ii+'][nxs_term_children'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_children").attr('id', 'nxs'+nt+ii+'_term_children'+'_'+count_compares);       
       jQuery(clone).find(".Tokenize").remove();              
       jQuery(clone).find("#nxs"+nt+ii+"_term_names").attr('name', nt+'['+ii+'][nxs_term_names'+'_'+count_compares+'][]');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_names").attr('id', 'nxs'+nt+ii+'_term_names'+'_'+count_compares);    // tg[0][nxs_term_names][]
       
       jQuery(clone).find("#nxs"+nt+ii+"_term_relation").attr('name', nt+'['+ii+'][nxs_term_relation'+'_'+count_compares+']');     
       jQuery(clone).find("#nxs"+nt+ii+"_term_relation").attr('id', 'nxs'+nt+ii+'_term_relation'+'_'+count_compares);       
       
       jQuery(clone).find("#nxs"+nt+ii+"_term_names"+'_'+count_compares).find('option').remove().end();       
       clone.appendTo('#nxs_term_namesTopDiv'+nt+ii); jQuery('#nxs_term_namesCond'+nt+ii).show();        
       
       jQuery("#nxs"+nt+ii+"_term_names"+'_'+count_compares).tokenize({ datas: "json.php", displayDropdownOnFocus: true });
       
       jQuery(clone).find(".nxs_terms_leftPanel").attr('style','display:inline-block;');       
       $( '.nxs_tax_names' ).on( 'change', function() { var tgtID =  $(this).attr('id').replace('_tax_','_term_'); $('#'+tgtID).attr('data-type', $(this).val()); $('#'+tgtID).val(''); $('#'+tgtID).tokenize().remap(); } );  
        
       return false;
        
    } );
    
    $( '#nxs_add_date_period' ).on( 'click', function() {        
        
        var count_periods = +$( '#nxsDivWrap #nxs_count_date_periods' ).val( function( i, val ) {
            return ( + val + 1 );
        } ).val();
        var rel     = 'nxs_date_period_' + count_periods;
        
        var html = '<hr>';
        
        html += '<div class="nxs_short_field" rel="' + rel + '">';
        html += '<label class="field_title">[Timeframe] from:</label>';
        html += '<input type="text" id="nxs_starting_period_' + count_periods + '" name="nxs_starting_period_' + count_periods + '" class="selectize-input datepicker" placeholder="2012-01-01" value="">';
        html += '</div>';
        
        html += '<div class="nxs_short_field" rel="' + rel + '">';
        html += '<label class="field_title">[Timeframe] to:</label>';
        html += '<input type="text" id="nxs_end_period_' + count_periods + '" name="nxs_end_period_' + count_periods + '" class="selectize-input datepicker" placeholder="2014-01-01" value="">';
        html += '</div>';
        
        html += '<div class="nxs_small_field nxs_checkbox_field" rel="' + rel + '">';
        html += '<input type="checkbox" name="nxs_inclusive_' + count_periods + '" id="nxs_inclusive_' + count_periods + '" class="nxs-checkbox selectize-input">';
        html += 'Include';
        html += '</div>';
        
        html += '<div class="nxs_small_field" rel="' + rel + '">';
        html += '<button class="nxs_remove_date_period">Delete</button>';
        html += '</div>';
        
        $( this )
            .prev( 'hr' )
            .before( html )
            .prevAll( 'div [rel=' + rel + ']' )
            .children( '.nxs_remove_date_period' )
            .on( 'click', function() {
                remove_compare( this );
            } );
        
        $( '#nxs_starting_period_' + count_periods ).datepicker();
        
        $( '#nxs_end_period_' + count_periods ).datepicker();
        
        return false;
        
    } );
    
    $( '#nxs_add_date_abs_period' ).on( 'click', function() {        
        
        var count_periods = +$( '#nxsDivWrap #nxs_count_date_abs_periods' ).val( function( i, val ) {
            return ( + val + 1 );
        } ).val();
        var rel     = 'nxs_date_abs_period_' + count_periods;
        
        var html = '<hr>';
        
        html += '<div class="nxs_small_field" rel="' + rel + '">';
        html += '<label class="field_title">Posts after:</label>';
        html += '<input type="text" id="nxs_starting_abs_period_' + count_periods + '" name="nxs_starting_abs_period_' + count_periods + '" class="selectize-input" placeholder="Please enter the number" value="">';
        html += '<p class="description">Select Posts after...</p>';
        html += '</div>';
        
        html += '<div class="nxs_small_field" rel="' + rel + '">';
        html += '<label class="field_title">Period Type:</label>';
        html += get_select( 'nxs_types_starting_abs_period', count_periods );
        html += '<p class="description">Period Type: Day, Month, Year</p>';
        html += '</div>';
        
        html += '<div class="nxs_small_field" rel="' + rel + '">';
        html += '<label class="field_title">Posts earlier then:</label>';
        html += '<input type="text" id="nxs_end_abs_period_' + count_periods + '" name="nxs_end_abs_period_' + count_periods + '" class="selectize-input" placeholder="Please enter the number" value="">';
        html += '<p class="description">Select Posts earlier then</p>';
        html += '</div>';
        
        html += '<div class="nxs_small_field" rel="' + rel + '">';
        html += '<label class="field_title">Period Type:</label>';
        html += get_select( 'nxs_types_end_abs_period', count_periods );
        html += '<p class="description">Period Type: Day, Month, Year</p>';
        html += '</div>';
        
        html += '<div class="nxs_small_field" rel="' + rel + '">';
        html += '<button class="nxs_remove_date_abs_period">Delete</button>';
        html += '</div>';
        
        $( this )
            .prev( 'hr' )
            .before( html )
            .prevAll( 'div [rel=' + rel + ']' )
            .children( '.nxs_remove_date_abs_period' )
            .on( 'click', function() {
                remove_compare( this );
            } );
        
        $( '#nxs_types_end_abs_period_' + count_periods ).selectize( {
            valueField: 'value',
            labelField: 'title'
        } );
        
        $( '#nxs_types_starting_abs_period_' + count_periods ).selectize( {
            valueField: 'value',
            labelField: 'title'
        } );
        
        return false;        
    } )
    
    $( '.nxs_remove_meta_compare' ).on( 'click', function() {
        remove_compare( this );
    } );
    
    
    
    $( '.nxs_remove_date_period' ).on( 'click', function() {
        remove_compare( this );
    } );
    
    $( '.nxs_remove_date_abs_period' ).on( 'click', function() {
        remove_compare( this );
    } );
    
    function remove_compare( button ) {
        var selector = '';
        switch( $( button ).attr( 'class' ) ) {
            case 'nxs_remove_meta_compare': 
                selector = 'nxs_count_meta_compares';
                break;
            case 'nxs_remove_term_compare': 
                selector = 'nxs_count_term_compares';
                break;
            case 'nxs_remove_date_period': 
                selector = 'nxs_count_date_periods';
                break;
            case 'nxs_remove_date_abs_period': 
                selector = 'nxs_count_date_abs_periods';
                break;
        }
        
        $( '#' + selector ).val( function( i, val ) {
            return ( + val - 1 );
        } )
        
        var rel = $( button ).closest( 'div' ).attr( 'rel' );
        
        set_attributes_next_elements( button );    
        
        $( button ).closest( 'div' ).prevAll( 'div[rel=' + rel + ']' ).remove();
        $( button ).closest( 'div' ).prev( 'hr' ).remove();
        $( button ).closest( 'div' ).remove();
        
        return false;
    }
    
    function get_select( name, count, nt, ii ){ //alert(name); console.log( JSON.stringify( selectized_terms ) );
        if( name == 'nxs_term_names' )
            var select = selectized_terms;
        else if( name == 'nxs_meta_value' )
            var select = selectized_metas;
        // else  var select = $( 'select#' + name ).tokenize().toArray();
        
        var current_name = name + '_' + count; 
        
        if(typeof(nt)!='undefined') current_nameX = nt+'['+ii+']['+name + '_' + count+']'; else current_nameX = current_name;
         
         //console.log( JSON.stringify( jQuery(this).prop('tagName') ) );
         
        var multiple     = ( $( '#' + name ).attr( 'multiple' ) ? 'multiple="multiple"' : '' );
        
        var output = '<select name="' + ( multiple != '' ? current_nameX + '[]' : current_nameX ) + '" id="' + current_name + '" placeholder="Select from the list..." ' + multiple + '>';
        output += '<option value="">Select from the list...</option>';
        $.each( select, function( i, e ) {
            output += '<option value="' + e.value + '">' + e.title + '</option>';
        } );     
        output += '</select>';
        
        return output;
    }
    
    function set_attributes_next_elements( elem ) {
        var selector = $( elem ).hasClass( 'nxs_remove_date_period' ) ? 'div [rel^=nxs_date_period]' : 'div [rel]';
        
        $( elem ).closest( 'div' ).nextAll( selector ).each( function( i, e ) {            
            set_attrribute( $( e ), 'rel' );
            set_attrribute( $( e ).children( 'input, select' ), 'id' );
            set_attrribute( $( e ).children( 'input, select' ), 'name' );                                    
        } );    
    }
    
    function set_attrribute ( element, attribute ) {
        if( element.length > 0 ) {
            $( element ).attr( attribute, function( index, value ) {
                var multiple = ~value.indexOf( '[]' ) ? '[]' : '';
                var position = value.lastIndexOf( '_' ) + 1;
                return ( value.slice( 0, position ) + ( parseInt( value.slice( position ) ) - 1 ) + multiple );
            } );
        }
    }
    
    function filter_select ( item, selector, saveItems ) {   //console.log( '~!!-' ); console.log( selector );  console.log( $(selector)[0]  );
        var terms      = $( selector )[0].selectize;//   console.log( '~~~' ); 
        var items      = terms.items; // console.log( '~1~~' ); 
        var newOptions = []; //console.log( '~2~~' ); 
        
        if(typeof(selectized_terms)=='undefined' && typeof($( selector )[0])!='undefined') selectized_terms = $( selector )[0].selectize.options; 
        
        //console.log( '~3~~' ); 
        
         //console.log( selectized_terms );
        
        
        if( ~selector.indexOf( '_term_names' ) ) var options = selectized_terms; else if( ~selector.indexOf( '_meta_value' ) ) var options = selectized_metas; else var options = terms.options;
        
        
        $.each( options, function() { 
            var delimiter = this.value.indexOf( '||' );
            if( this.value.slice( 0, delimiter ) == item ) {
                newOptions.push( {  
                    value: this.value,
                    title: this.title
                } );
            }
        } );
        
        terms.clearOptions();
        terms.addOption( newOptions );
        if( saveItems ) {
            terms.addItems( items );
        }
    }
}

jQuery( document ).ready( function( $ ) {
    if (typeof($.datepicker)!='undefined') nxs_V4_filter_mainJS($);
} );