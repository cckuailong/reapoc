( function( $ ) {

	jQuery(document).ready(function($) {

		function findGetParameter(parameterName) {
		    var result = null,
		        tmp = [];
		    location.search
		        .substr(1)
		        .split("&")
		        .forEach(function (item) {
		          tmp = item.split("=");
		          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
		        });
		    return result;
		}

		$('#selectPostTypeFormSubmissions').on('change',function(){
			
			var changedVal = $(this).val();

			if (changedVal != '' && changedVal != 'none') {

				window.location.href = popb_admin_url_data.form_subbmissions_page+'&selectedPostID='+changedVal;

			}

		});


		jQuery('.entryDeleteBtn').on('click', function(e)  {

		  var P_ID = findGetParameter('selectedPostID');
		  var shortCodeRenderWidgetNO = popb_admin_url_data.pluginops_nonce;
	      var entryIndex = $(this).attr('data-entryIndex');
	      var confirmIt =  confirm('Are you sure ? It will delete this data entry for eternity.');
	      if (confirmIt == true) {

	        var result = " ";
	        var form = jQuery('#formBuilderDataListEmpty');
	        jQuery.ajax({
	            url: popb_admin_url_data.form_submissions_ajax_url + "?action=ulpb_delete_form_builder_entry&postID="+P_ID+"&dataEntryIndex="+entryIndex+"&submitNonce="+shortCodeRenderWidgetNO,
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

	    jQuery('.emptyFormDataBtn').on('click', function(e)  {


	      var P_ID = findGetParameter('selectedPostID');
		  var shortCodeRenderWidgetNO = popb_admin_url_data.pluginops_nonce;

	      var confirmIt =  confirm('Are you sure ? It will delete all your form data for eternity.');
	      if (confirmIt == true) {

	          var result = " ";
	          var form = jQuery('#formBuilderDataListEmpty');

	          jQuery.ajax({
	              url: popb_admin_url_data.form_submissions_ajax_url + "?action=ulpb_empty_form_builder_data&postID="+P_ID+"&submitNonce="+shortCodeRenderWidgetNO,
	              method: 'post',
	              data: {ps_ID:P_ID},
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


		jQuery( function() {
	      jQuery( "#PB_accordion_forms, .PB_accordion_forms" ).accordion({
	        collapsible: true,
	        heightStyle: "content"
	      });
	    });


	   // $('.emptyFormDataBtn').css('display','none');
		
	});

})(jQuery);