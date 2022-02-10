( function( $ ) {

	jQuery(document).ready(function($) {

		var adminAjaxUrl = popb_admin_url_data.page_ajax_url;
		var POPB_data_nonce = popb_admin_url_data.pluginops_nonce;
		var P_ID = popb_admin_url_data.post_id;



		$('.edit-slug').on('click',function(){
	          var prevTxt = $('#editable-post-name').text();
	          $('.editable-post-name-field').val(prevTxt);
	          $('#editable-post-name').css('display','none');
	          $('.edit-slug').css('display','none');
	          $('.editable-post-name-field').css('display','inline-block');
	          $('.savePermalink').css('display','inline-block');
	    });
	    

	    $('.savePermalink').on('click',function(){

	        var postId = "<?php echo $postId; ?>";
	        var new_slug = $( '.editable-post-name-field' ).val();

	        $.ajax({
	          url: adminAjaxUrl + "?action=ulpb_get_sample_permalink_for_landingpages&POPB_nonce="+POPB_data_nonce,
	          method: 'post',
	          data: {
	            post_id: postId,
	            new_slug: new_slug,
	          },
	          success: function(result){
	            $('#editable-post-name').html( result );
	            $('#editable-post-name-full').html( result );
	            $('#editable-post-name').css('display','inline-block');
	            $('.edit-slug').css('display','inline-block');
	            $('.editable-post-name-field').css('display','none');
	            $('.savePermalink').css('display','none');
	            $( '.edit-slug' ).focus();
	          }
	        });

	    });



	    $('#updateFonts').on('click',function(){

	    	var allCustomFonts = [];
	    	var localFonts = [];
	    	$('.customFontsStylesContainer').html('');
	    	$('.customFontsItemsContainer li').each(function(index, el) {

	    		var thisFontItem = {
	    			fontTitle: $( this ).children('.accordContentHolder').children('.fontTitle').val(),
	    			fontwoff: $( this ).children('.accordContentHolder').children('.image_location_woff_'+index).val(),
	    			fontwoff2: $( this ).children('.accordContentHolder').children('.image_location_woff2_'+index).val(),
	    			fontttf: $( this ).children('.accordContentHolder').children('.image_location_ttf_'+index).val(),
	    			fontsvg: $( this ).children('.accordContentHolder').children('.image_location_svg_'+index).val(),
	    			fonteot: $( this ).children('.accordContentHolder').children('.image_location_eot_'+index).val(),
	    		};

	    		allCustomFonts.push(thisFontItem);

	    		localFonts.push(thisFontItem['fontTitle']);

	    		addThisFontToCustomFontStyles = '@font-face { font-family: "'+thisFontItem['fontTitle']+'" ; src:';


			    fontwoff =  'url("'+thisFontItem['fontwoff']+'") format("woff")';

			    fontwoff2 = ', url("'+thisFontItem['fontwoff2']+'") format("woff2")';

			    fontttf = ', url("'+thisFontItem['fontttf']+'") format("truetype")';

			    fonteot = ', url("'+thisFontItem['fonteot']+'") format("embedded-opentype")';

			    fontsvg = ', url("'+thisFontItem['fontsvg']+'") format("svg")';


			    addThisFontToCustomFontStyles =  addThisFontToCustomFontStyles + fontwoff + fontwoff2 + fontttf +  fonteot + fontsvg + '; }';

			    addThisFontToCustomFontStyles = '<style> '+addThisFontToCustomFontStyles+' </style>';

			    $('.customFontsStylesContainer').append(addThisFontToCustomFontStyles);

	    	});


	    	
	    	jQuery('.font-select').remove();
	    	jQuery('.gFontSelectorulpb').fontselect({
		      style: 'font-select',
		      placeholder: 'Select a font',
		      placeholderSearch: 'Search...',
		      lookahead: 1,
		      searchable: true,
		      localFonts: localFonts,
		      localFontsUrl: '/fonts/' // End with a slash!
		    });

	    	allCustomFontsStringify = JSON.stringify(allCustomFonts);

	    	$.ajax({
	    		url: adminAjaxUrl + "?action=popb_update_custom_fonts&POPB_nonce="+POPB_data_nonce,
	    		type: 'post',
	    		dataType: 'json',
	    		data: {updatedcustomfonts : allCustomFonts,},
	    	})
	    	.done(function(result) {
	    		if (result == 'Data Saved Successfully') {
	    			console.log('success');
	    		}

	    		if (result == 'Please make some changes first.') {
	    			console.log('Please make some changes first.');
	    		}
	    	})
	    	.fail(function(error) {
	    		console.log(error);
	    	})
	    	.always(function() {
	    		console.log("complete");
	    	});
	    	

	    });


		jQuery('.insertTemplateFormSubmit').on('click', function(e)  {

		    var confirmIt =  confirm('Are you sure ? It will insert the temlate below your existing content.');
		    if (confirmIt == true) {

		        var result = " ";
		        var form = jQuery('.insertTemplateForm');

		        jQuery.ajax({
		            url: adminAjaxUrl + "?action=ulpb_insert_template&insertTemplateNonce="+POPB_data_nonce,
		            method: 'post',
		            data: form.serialize(),
		            success: function(result){
		                resonse = JSON.parse(result);
		                if (resonse['Message'] == 'Success'){
		                  jQuery.each(resonse['Rows'], function(index,val){
		                    val['rowID'] = 'ulpb_Row'+Math.floor((Math.random() * 200000) + 100);
		                    collectionSize = pageBuilderApp.rowList.length;
		                    pageBuilderApp.rowList.add(val, {at: collectionSize+1} );
		                  });
		                  alert('Selected Template Added Successfully.');
		                }else{
		                  jQuery('.upt_response').html('There is some bug which is preventing this page to be updated, Contact the <a href="https://wordpress.org/support/plugin/page-builder-add" target="_blank" > Bug Killers </a>');
		                }
		            }
		        });
		         
		    }

		    return false;
		});


		$('.empty_button_form').on('submit',function(){
         
	        $('#response').text('Processing'); 
	         
	        var form = $(this);
	        $.ajax({
	            url: form.attr('action')+'&subsListEmpty='+POPB_data_nonce,
	            method: form.attr('method'),
	            data: form.serialize(),
	            success: function(result){
	                $('.download_file_link').css('display','none');
	                if (result == 'Success'){
	                    $('#response').text(result);  
	                }else {
	                    $('#response').text(result);
	                }
	            }
	        });
	         
	        
	        return false;   
	    });



	    $('.download_button_form').on('submit',function(){
         
	        $('#response').text('Processing'); 
	        var form = $(this);
	        $.ajax({
	            url: form.attr('action'),
	            method: form.attr('method'),
	            data: form.serialize(),
	            success: function(result){
	                if (result == 'success'){
	                    $('#response').text(result);  
	                }else {
	                    $('#response').text(result);
	                }
	            }
	        });
	        
	        return false;   
	    });


	    jQuery('.emptyFormDataBtn').on('click', function(e)  {

	      var confirmIt =  confirm('Are you sure ? It will delete all your form data for eternity.');
	      if (confirmIt == true) {

	          var result = " ";
	          var form = jQuery('#formBuilderDataListEmpty');

	          jQuery.ajax({
	              url: adminAjaxUrl + '?action=ulpb_empty_form_builder_data&submitNonce='+POPB_data_nonce,
	              method: 'post',
	              data: form.serialize(),
	              success: function(result){
	                  if (result == 'Success'){

	                    $('.emptyFormDataBtn').hide();
	                    $('#formBuilderDataListEmpty p ').text('All data has been dumped successfully.');
	                  }else{
	                    $('#formBuilderDataListEmpty p ').text('Already empty.');
	                  }
	              }
	          });
	 
	      }
	      return false;
	    });


	    jQuery('.entryDeleteBtn').on('click', function(e)  {

	      var entryIndex = $(this).attr('data-entryIndex');
	      var confirmIt =  confirm('Are you sure ? It will delete this data entry for eternity.');
	      if (confirmIt == true) {

	        var result = " ";
	        var form = jQuery('#formBuilderDataListEmpty');
	        jQuery.ajax({
	            url: adminAjaxUrl + '?action=ulpb_delete_form_builder_entry&postID='+P_ID+"&dataEntryIndex="+entryIndex+"&submitNonce="+POPB_data_nonce,
	            method: 'post',
	            data: form.serialize(),
	            success: function(result){
	                if (result == 'success'){
	                  $('.edb-'+entryIndex).parent().parent().hide();
	                }else{
	                  $('#formBuilderDataListEmpty p ').text('Already empty.');
	                }
	            }
	        });
	         
	       
	      }

	        return false;
	    });


	    jQuery('#resetAnalyticsBtn').on('click', function(e)  {

	      var confirmIt =  confirm('Are you sure ? It will delete this data for eternity.');
	      if (confirmIt == true) {

	        var result = " ";
	        var form = jQuery('#formBuilderDataListEmpty');
	        jQuery.ajax({
	            url: adminAjaxUrl + "?action=ulpb_delete_optin_analytics&postID="+P_ID+"&actionConfirmed="+confirmIt+"&submitNonce="+POPB_data_nonce,
	            method: 'post',
	            data: form.serialize(),
	            success: function(result){
	                if (result == 'success'){
	                  $('#resetAnalyticsBtn').text('Analytics reset completed.');
	                }else{
	                  $('#resetAnalyticsBtn').text('Some error occurred!');
	                }
	            }
	        });
	         
	      }

	        return false;
	    });


	    jQuery('.analyticsDateRange').on('change', function(e)  {

	      var confirmIt =  true;
	      if (confirmIt == true) {

	        var dateRange = jQuery('.analyticsDateRange').val();
	        var result = " ";
	        var form = jQuery('#formBuilderDataListEmpty');

	        jQuery.ajax({
	            url: adminAjaxUrl + "?action=ulpb_get_new_analytics&postID="+P_ID+"&actionConfirmed="+confirmIt+"&dateRange="+dateRange+"&submitNonce="+POPB_data_nonce,
	            method: 'post',
	            data: form.serialize(),
	            success: function(result){
	              result = JSON.parse(result);
	                if (result['message'] == 'success'){
	                  $('#mainAnalyticsContainer').html(' ');
	                  $('#mainAnalyticsContainer').html(result['analytics']);
	                }else{
	                  $('#resetAnalyticsBtn').text('Some error occurred!');
	                }
	            }
	        });
	         
	      }

	        return false;
	    });

	    jQuery('.analyticsDateRange').trigger('change');


	  	$('#aweberConnectButton').on('click',function(){

	        $('.aweberLoader').text('Connecting...');
	        $('.aweberLoader').show();
	        var authCode = $('.aweberAuthCode').val();
	        $.ajax({
	            url: adminAjaxUrl + "?action=ulpb_aweber_connect&POPB_nonce="+POPB_data_nonce,
	            method: 'post',
	            data: {aweberAuthCode:authCode},
	            success: function(result){
	                var parsedResult= JSON.parse(result);
	                if (parsedResult['queryMessage'] == 'success' ) {
	                  $('.aweberLoader').hide();
	                  $('.aweberConnectionSetupOne').hide('slow');
	                  $('.aweberConnectionSetupTwo').show('slow');
	                  $('#formBuilderAweberList').html(parsedResult['allLists']);
	                }else{
	                  $('.aweberLoader').text('Connection unsuccesful, Please try getting your authorization code again.');
	                }
	                
	            }
	        });

  		});

  		$.ajax({

            url: adminAjaxUrl + "?action=ulpb_aweber_connection_check&POPB_nonce="+POPB_data_nonce,
            method: 'post',
            data: '',
            success: function(result){
                var parsedResult= JSON.parse(result);
                if (parsedResult['queryMessage'] == 'success' ) {

                  $('.aweberConnectionSetupOne').hide('slow');
                  $('.aweberConnectionSetupTwo').show('slow');
                  $('#formBuilderAweberList').html(parsedResult['allLists']);
                }else{
                  $('.aweberLoader').text(' ');
                  $('.aweberConnectionSetupOne').show('slow');
                  $('.aweberConnectionSetupTwo').hide('slow');
                }
                
            }

    	});


    	$('#mcGetGrpsBtn').on('click',function(){

	        $('.mcGroupsLoader').text('Connecting...');
	        $('.mcGroupsLoader').show();
	        var form = $('#aweberConnectForm');

	        apiKey = $('.formBuilderMCApiKey').val();
	        listID = $('.formBuilderMCAccountName').val();

	        $('.mcgrpsContainer').css('display','none');

	        if (apiKey == '' || listID == '') {
	          $('.mcGroupsLoader').text("Api key or List ID can't be empty");
	        }else{

	          $.ajax({
	              url: adminAjaxUrl + "?action=ulpb_getMCGroupIds&POPB_nonce="+POPB_data_nonce+"&apiKey="+apiKey+"&listID="+listID,
	              method: form.attr('method'),
	              data: form.serialize(),
	              async:true,
	              success: function(result){
	                var parsedResult= JSON.parse(result);
	                thisMcAllGroups = '';
	                if (typeof( parsedResult['success']) != 'undefined' ) {
	                  
	                  groups = parsedResult['success'];
	                  $.each(groups, function(title,arrayVal){
	                    thisOptGroup = '<optgroup label="'+title+'">';

	                    thisgrpFields = '';
	                    $.each(arrayVal, function(index, val){
	                      $.each(val,function(grpId, grpName){
	                        thisField = '<option value="'+grpName+'"> '+grpId +' </option>';
	                        thisgrpFields =  thisgrpFields + thisField;
	                      });
	                    });

	                    thisCompleteOptGroup = thisOptGroup + thisgrpFields + "</optgroup>";

	                    thisMcAllGroups = thisMcAllGroups + thisCompleteOptGroup;

	                  });

	                  $('.formBuilderMCGroups').html( "<option value='false'>None</option>" + thisMcAllGroups);
	                  $('.mcgrpsContainer').css('display','block');
	                  $('.mcGroupsLoader').text('');

	                  $('.formBuilderMCGroups').val(pageBuilderApp.thisMCSelectedGroup);


	                  pageBuilderApp.changedOpType = 'specific';
	                  pageBuilderApp.changedOpName =  'widgetPbFbFormMailChimp.formBuilderMCGroupsList';
	                  var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
	                  
	                  jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

	                  ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
	                  currentEditableColId = jQuery('.currentEditableColId').val();
	                  jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');


	                }else{
	                  $('.mcGroupsLoader').text(parsedResult['error']);
	                }
	                
	              }
	          });

	        }

	  	});


	  	$(document).on('click','.mcGetGrpsFieldBtn',function(){
	        $('.mcGroupsFieldLoader').text('Connecting...');
	        $('.mcGroupsFieldLoader').show();
	        var form = $('#aweberConnectForm');

	        apiKey = $('.formBuilderMCApiKey').val();
	        listID = $('.formBuilderMCAccountName').val();
	        $('.formBuilderMCFieldGroupsDiv').css('display','none');

	        if (apiKey == '' || listID == '') {
	          $('.mcGroupsFieldLoader').text("Api key or List ID can't be empty");
	        }else{

	          $.ajax({
	              url: adminAjaxUrl + "?action=ulpb_getMCGroupIds&POPB_nonce="+POPB_data_nonce+"&apiKey="+apiKey+"&listID="+listID,
	              method: form.attr('method'),
	              data: form.serialize(),
	              async:true,
	              success: function(result){
	                var parsedResult= JSON.parse(result);
	                thisMcAllGroupsFields = '';
	                if (typeof( parsedResult['success']) != 'undefined' ) {
	                  
	                  groups = parsedResult['success'];

	                  $.each(groups, function(title,arrayVal){
	                    
	                    thisMcGroup = '<option value="'+title+'"> '+title+' </option>';
	                    thisMcAllGroupsFields = thisMcAllGroupsFields + thisMcGroup;

	                  });

	                  $('.formBuilderMCFieldGroups').html( "<option value='false'>None</option>" + thisMcAllGroupsFields);
	                  $('.mcGroupsFieldLoader').text('');
	                  $('.formBuilderMCFieldGroupsDiv').css('display','block');
	                  $('.formBuilderMCFieldGroups').val(pageBuilderApp.thisMCSelectedGroupField);


	                  $('.formBuilderMCGroupsList').val(result);
	                  pageBuilderApp.changedOpType = 'specific';
	                  pageBuilderApp.changedOpName =  'widgetPbFbFormMailChimp.formBuilderMCGroupsList';
	                  var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
	                  
	                  jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

	                  ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
	                  currentEditableColId = jQuery('.currentEditableColId').val();
	                  jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');


	                }else{
	                  $('.mcGroupsFieldLoader').text(parsedResult['error']);
	                }
	                
	              }
	          });

	        }

	  	});


	  	$(document).on('click','.mailRelayGetGrpsFieldBtn',function(){
	        $('.mrGroupsFieldLoader').text('Connecting...');
	        $('.mrGroupsFieldLoader').show();
	        var form = $('#aweberConnectForm');

	        apiKey = $('.wfbMRApiKey').val();
	        accountUrl = $('.wfbMRAccountUrl').val();
	        $('.formBuilderMCFieldGroupsDiv').css('display','none');

	        if (apiKey == '' || accountUrl == '') {
	          $('.mrGroupsFieldLoader').text("Api key or List ID can't be empty");
	        }else{

	          $.ajax({
	              url: adminAjaxUrl + "?action=ulpb_getMailRelayGroupIds&POPB_nonce="+POPB_data_nonce+"&apiKey="+apiKey+"&accountUrl="+accountUrl,
	              method: 'get',
	              data: {},
	              async:true,
	              success: function(result){
	                var parsedResult= JSON.parse(result);


	                if ( typeof(parsedResult['error']) !== 'undefined') {
	                	$('.mrGroupsFieldLoader').text(parsedResult['error']);
	                	return false;
	                }

	                thisMRAllGroupsFields = '';
	                $.each(parsedResult, function(title,arrayVal){
	                    thisMrGroup = '<option value="'+arrayVal['id']+'"> '+arrayVal['name']+' </option>';
	                    thisMRAllGroupsFields = thisMRAllGroupsFields + thisMrGroup;

	                  });

	                  //$('.formBuilderMRFieldGroups').html( "<option value='false'>None</option>" + thisMRAllGroupsFields);
	                  $('.mrGroupsFieldLoader').text('');
	                  $('.formBuilderMRFieldGroupsDiv').css('display','block');
	                  //$('.formBuilderMRFieldGroups').val(pageBuilderApp.thisMRSelectedGroupField);


	                  $('.formBuilderMRGroupsList').val(result);
	                  pageBuilderApp.changedOpType = 'specific';
	                  pageBuilderApp.changedOpName =  'widgetPbFbFormMailChimp.formBuilderMRGroupsList';
	                  var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
	                  
	                  jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

	                  ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
	                  currentEditableColId = jQuery('.currentEditableColId').val();
	                  jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');
	                
	              }

	          });

	        }

	  	});


	  	$('#ckGetseqsBtn').on('click',function(){

	        $('.ckSeqsLoader').text('Connecting...');
	        $('.ckSeqsLoader').show();
	        var form = $('#aweberConnectForm');

	        apiKey = $('.formBuilderConvertKitApiKey').val();

	        $('.ckSeqsContainer').css('display','none');

	        if (apiKey == '') {
	          $('.ckSeqsLoader').text("Api key or List ID can't be empty");
	        }else{

	          $.ajax({
	              url: adminAjaxUrl + "?action=ulpb_getCkSequenceIds&POPB_nonce="+POPB_data_nonce+"&apiKey="+apiKey,
	              method: form.attr('method'),
	              data: form.serialize(),
	              async:true,
	              success: function(result){
	                var parsedResult= JSON.parse(result);
	                thisMcAllGroups = '';
	                if (typeof( parsedResult['success']) != 'undefined' ) {
	                  
	                  ck_sequences = parsedResult['success'];

	                  thisgrpFields = '';
	                  $.each(ck_sequences, function(index,val){
	                    
	                    thisField = '<option value="'+val['id']+'"> '+val['name'] +' </option>';
	                    thisgrpFields =  thisgrpFields + thisField;

	                  });

	                  $('.formBuilderConvertKitAccountName').html( "<option value='false'>None</option>" + thisgrpFields);
	                  $('.ckSeqsContainer').css('display','block');
	                  $('.ckSeqsLoader').text('');

	                  $('.formBuilderConvertKitAccountName').val(pageBuilderApp.thisCkSelectedSeq);

	                }else{
	                  $('.ckSeqsLoader').text(parsedResult['error']);
	                }
	                
	              }
	          });

	        }

	  	});


	  	$('#CC_ConnectButton').on('click',function(){

	        $('.CC_loader').text('Connecting...');
	        $('.CC_loader').show();
	        var form = $('#aweberConnectForm');

	        ccToken = $('.wfbCCAccessKey').val();

	        $.ajax({
	              url: adminAjaxUrl + "?action=ulpb_getConstantContactLists&POPB_nonce="+POPB_data_nonce+"&ccToken="+ccToken,
	              method: 'post',
	              data: form.serialize(),
	              async:true,
	              success: function(result){
	                var parsedResult = JSON.parse(result);
	                thisMcAllGroups = '';
	                if (typeof( parsedResult['success']) != 'undefined' ) {
	                  
	                  CClists = parsedResult['success'];
	                
	                  thisCCLists = '<option value="select"> Select </option>';
	                  $.each(CClists, function(index,val){

	                    thisField = '<option value="'+index+'"> '+val +' </option>';
	                    thisCCLists =  thisCCLists + thisField;

	                  });

	                  $('.wfbCCLists').html( thisCCLists);
	                  $('.cc_token_container').css('display','none');
	                  $('.cc_lists_container').css('display','block');
	                  $('.CC_loader').text('connected');

	                }else{

	                  if (parsedResult['error'] == 'Unauthorized') {
	                    $('.CC_loader').text( 'Please get a new access token and reconncet.');
	                  }else{
	                    $('.CC_loader').text(parsedResult['error']);
	                  }

	                }
	                
	              }
	        });

	  	});


	  	jQuery('.tab-editor-deactivate').on('click', function(e)  {

	        $('#SavePageOther').trigger('click');
	        var result = " ";

	        $.ajax({
	            url: adminAjaxUrl + "?action=ulpb_activate_pb_request&page_id="+P_ID+"ulpbActivate=DeactivatePB&POPB_data_nonce="+POPB_data_nonce,
	            method: 'get',
	            data: '',
	            success: function(result){
	                setTimeout(function(){
	                   window.location.href = admURL+'post.php?post='+P_ID+'&action=edit';
	                }, 1600);
	            }
	        });
	           
	        return false;   
	     });

	  	$('#CC_ConnectButton').trigger('click');



		var adminAjaxUrl = popb_admin_url_data.page_ajax_url;
	  	var POPB_data_nonce = popb_admin_url_data.pluginops_nonce;
		var P_ID = popb_admin_url_data.post_id;

		try {
			
			jQuery('.customFontsItemsContainer').html('');	
			$.each(popb_admin_url_data.customFonts,function(index, val) {

				accordionItemsCount = index;
				jQuery('.customFontsItemsContainer').append(
			        '<li>'+
			          '<h3 class="handleHeader">Font - '+val['fontTitle']+
			            '<span class="dashicons dashicons-trash customfontRemoveBtn"></span>'+
			          '</h3>'+
			          '<div class="accordContentHolder">'+

			              '<label> Font Name  </label>'+
			              '<input style="width:90%;" type="text" class="fontTitle" value="'+val['fontTitle']+'">'+

			              '<br><br><br><br><hr><br>'+
			              
			              "<label>Font WOFF :</label>"+
			              '<input id="image_location_woff_'+accordionItemsCount+'" type="text" class="fontItemUrl image_location_woff_'+accordionItemsCount+' "  name="lpp_add_img_'+accordionItemsCount+'" value="'+val['fontwoff']+'"  placeholder="Upload or Select a font" style="width:90%;" />'+
			              "<label></label>"+ 
			              '<input id="image_location_woff_'+accordionItemsCount+'" type="button" class="fontUploadBtn" data-id="'+accordionItemsCount+'" data-fonttype="woff" value="Select Font File" style="width:90%;" />'+

			              "<br><br><br><br><br><br><br><hr><br>"+

			              "<label>Font WOFF2 :</label>"+
			              '<input id="image_location_woff2_'+accordionItemsCount+'" type="text" class="fontItemUrl image_location_woff2_'+accordionItemsCount+' "  name="lpp_add_img_'+accordionItemsCount+'" value="'+val['fontwoff2']+'"  placeholder="Upload or Select a font" style="width:90%;" />'+
			              "<label></label>"+ 
			              '<input id="image_location_woff2_'+accordionItemsCount+'" type="button" class="fontUploadBtn" data-id="'+accordionItemsCount+'" data-fonttype="woff2" value="Select Font File" style="width:90%;" />'+

			              "<br><br><br><br><br><br><br><hr><br>"+

			              "<label>Font TTF :</label>"+
			              '<input id="image_location_ttf_'+accordionItemsCount+'" type="text" class="fontItemUrl image_location_ttf_'+accordionItemsCount+' "  name="lpp_add_img_'+accordionItemsCount+'" value="'+val['fontttf']+'"  placeholder="Upload or Select a font" style="width:90%;" />'+
			              "<label></label>"+ 
			              '<input id="image_location_ttf_'+accordionItemsCount+'" type="button" class="fontUploadBtn" data-id="'+accordionItemsCount+'" data-fonttype="ttf" value="Select Font File" style="width:90%;" />'+

			              "<br><br><br><br><br><br><br><hr><br>"+

			              "<label>Font SVG :</label>"+
			              '<input id="image_location_svg_'+accordionItemsCount+'" type="text" class="fontItemUrl image_location_svg_'+accordionItemsCount+' "  name="lpp_add_img_'+accordionItemsCount+'" value="'+val['fontsvg']+'"  placeholder="Upload or Select a font" style="width:90%;" />'+
			              "<label></label>"+ 
			              '<input id="image_location_svg_'+accordionItemsCount+'" type="button" class="fontUploadBtn" data-id="'+accordionItemsCount+'" data-fonttype="svg" value="Select Font File" style="width:90%;" />'+

			              "<br><br><br><br><br><br><br><hr><br>"+

			              "<label>Font EOT :</label>"+
			              '<input id="image_location_eot_'+accordionItemsCount+'" type="text" class="fontItemUrl image_location_eot_'+accordionItemsCount+' "  name="lpp_add_img_'+accordionItemsCount+'" value="'+val['fonteot']+'"  placeholder="Upload or Select a font" style="width:90%;" />'+
			              "<label></label>"+ 
			              '<input id="image_location_eot_'+accordionItemsCount+'" type="button" class="fontUploadBtn" data-id="'+accordionItemsCount+'" data-fonttype="eot" value="Select Font File" style="width:90%;" />'+

			              "<br><br><br><br><br><br><br><hr><br>"+

			          '</div>'+
			        '</li>'

			    );

				addThisFontToCustomFontStyles = '@font-face { font-family: "'+val['fontTitle']+'" ; src:';

			   	fontwoff =  'url("'+val['fontwoff']+'") format("woff")';

			    fontwoff2 = ', url("'+val['fontwoff2']+'") format("woff2")';

			    fontttf = ', url("'+val['fontttf']+'") format("truetype")';

			    fonteot = ', url("'+val['fonteot']+'") format("embedded-opentype")';

			    fontsvg = ', url("'+val['fontsvg']+'") format("svg")';


			    addThisFontToCustomFontStyles =  addThisFontToCustomFontStyles + fontwoff + fontwoff2 + fontttf +  fonteot + fontsvg + '; }';

			    addThisFontToCustomFontStyles = '<style> '+addThisFontToCustomFontStyles+' </style>';

			    $('.customFontsStylesContainer').append(addThisFontToCustomFontStyles);
			});						

		} catch(e) {
			// statements
			console.log(e);
		}


	// document.ready	
	});

})(jQuery);