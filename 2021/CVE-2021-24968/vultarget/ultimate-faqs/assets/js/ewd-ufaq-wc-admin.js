jQuery(document).ready(function() {
    jQuery('.ewd-ufaq-add-faq-button').on('click', function(event) {
        var Post_ID = jQuery('#ewd-ufaq-post-id').val();

        var FAQs = [];
        jQuery('.ewd-ufaq-add-faq').each(function() {
            if (jQuery(this).is(':checked')) {FAQs.push(jQuery(this).val());}
            jQuery(this).prop('checked', false);
        });

        var data = 'FAQs=' + JSON.stringify(FAQs) + '&Post_ID=' + Post_ID + '&action=ewd_ufaq_add_wc_faqs';
        jQuery.post(ajaxurl, data, function(response) {
        	var Add_FAQs = jQuery.parseJSON(response);
        	jQuery(Add_FAQs).each(function(index, el) {
        		var HTML = "<tr class='ewd-ufaq-faq-row ewd-ufaq-delete-faq-row' data-faqid='" + el.ID + "'>";
				HTML += "<td><input type='checkbox' class='ewd-ufaq-delete-faq' name='Delete_FAQs[]' value='" + el.ID + "'/></td>";
				HTML += "<td>" + el.Name + "</td>";
				HTML += "</tr>";
                jQuery('.ewd-ufaq-delete-table tr:last').after(HTML);
        	});
        });

        event.preventDefault();
    })
});

jQuery(document).ready(function() {
    jQuery('.ewd-ufaq-delete-faq-button').on('click', function(event) {
        var Post_ID = jQuery('#ewd-ufaq-post-id').val();

        var FAQs = [];
        jQuery('.ewd-ufaq-delete-faq').each(function() {
            if (jQuery(this).is(':checked')) {FAQs.push(jQuery(this).val());}
            jQuery(this).prop('checked', false);
        });

        var data = 'FAQs=' + JSON.stringify(FAQs) + '&Post_ID=' + Post_ID + '&action=ewd_ufaq_delete_wc_faqs';
        jQuery.post(ajaxurl, data, function(response) {});

        jQuery(FAQs).each(function(index, el) {
        	jQuery(".ewd-ufaq-delete-faq-row[data-faqid='" + el + "']").fadeOut('500', function() {jQuery(this).remove();});
        });

        event.preventDefault();
    })
});

jQuery(document).ready(function() {
    jQuery('.ewd-ufaq-category-filter').on('change', function() {
        var Cat_ID = jQuery(this).val();

        var data = 'Cat_ID=' + Cat_ID + '&action=ewd_ufaq_wc_faq_category';
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('.ewd-ufaq-faq-add-table').remove();
            jQuery('.ewd-ufaq-category-filter').after(response);
        });
    })
});