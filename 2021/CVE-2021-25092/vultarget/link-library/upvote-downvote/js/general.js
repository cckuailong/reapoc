function thumbs_rating_vote(obj, ID, type, likeLabel)
{
	// For the LocalStorage

	var itemName = "thumbsrating" + ID;

	var container = '#thumbs-rating-' + ID;

	// Check if the LocalStorage value exist. If do nothing.
	 localStorage.setItem(itemName, true);

	var currentSelection = jQuery(obj);
	var selection;
	var alternate;

	if(type == 1){
		var oldTypeItemName = "thumbsrating" + ID + "-2";
		var newTypeItemName = "thumbsrating" + ID + "-1";
	} else if (type == 2){
		var oldTypeItemName = "thumbsrating" + ID + "-1";
		var  newTypeItemName = "thumbsrating" + ID + "-2";
	}

	if(currentSelection.parent().hasClass("lb-voted")){
		selection = 0;
		localStorage.removeItem(oldTypeItemName, false);
		localStorage.setItem(newTypeItemName, false);
	} else{
		selection = 1;
		localStorage.removeItem(oldTypeItemName, false);
		localStorage.setItem(newTypeItemName, true);
	}


	if(currentSelection.parent().hasClass("thumbs-rating-up")){
		if(currentSelection.parent().parent().find('.thumbs-rating-down.lb-voted').length > 0){
			alternate = 1;
		}

	} else if(currentSelection.parent().hasClass("thumbs-rating-down")){
		if(currentSelection.parent().parent().find('.thumbs-rating-up.lb-voted').length > 0){
			alternate = 1;
		}
	}

	var data = {
			action: 'thumbs_rating_add_vote',
			postid: ID,
			type: type,
			selection: selection,
			alternate : alternate,
      likelabel : likeLabel,
			nonce: thumbs_rating_ajax.nonce
		};

	jQuery.post(thumbs_rating_ajax.ajax_url, data, function(response) {

		var object = jQuery(container);

		jQuery(container).html('');

		jQuery(container).append(response);

		// Remove the class and ID so we don't have 2 DIVs with the same ID

		//jQuery(object).removeClass('thumbs-rating-container');
		//jQuery(object).attr('id', '');

		// Add the class to the clicked element

		var new_container = '#thumbs-rating-' + ID;

		// Check the type

		if( type == 1){
			thumbs_rating_class = ".thumbs-rating-up";
		}
		else{
			thumbs_rating_class = ".thumbs-rating-down";
		}

		if (selection == 1){
			jQuery(new_container + ' ' +  thumbs_rating_class ).addClass('thumbs-rating-voted lb-voted');
		}

	});
}


