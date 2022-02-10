var $j = jQuery.noConflict();
$j(function() {

	// This controls the Skip Overview link on the Overview screen
	$j('#skip_overview').click(function(){
		$j('#skip_overview_form').submit();
	});

	// Controls the Quick Export button on the Export screen
	$j('#quick_export').click(function(){
		$j('#postform').submit();
	});

	// Date Picker
	if( $j.isFunction($j.fn.datepicker) ) {
		$j('.datepicker').datepicker({
			dateFormat: 'dd/mm/yy'
		}).on('change', function() {
			$j('input:radio[name="order_dates_filter"][value="manual"]').prop( 'checked', true );
		});
	}

	// Time Picker element
	if( $j.isFunction($j.fn.datetimepicker) ) {
		var timezone = new Date(new Date().getTime());
		$j('.datetimepicker').datetimepicker({
			dateFormat: 'dd/mm/yy',
			timeFormat: 'HH:mm',
			controlType: 'select',
			minDate: timezone,
			showTimezone: false,
			showSecond: false
		});
	}

	// Chosen dropdown element
	if( $j.isFunction($j.fn.chosen) ) {
		$j(".chzn-select").chosen({
			search_contains: true,
			width: "95%"
		});
	}

	// Sortable export columns
	if( $j.isFunction($j.fn.sortable) ) {
		$j('table.ui-sortable').sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
			handle: 'td',
			scrollSensitivity:40,
			helper:function(e,ui){
				ui.children().each(function(){
					$j(this).width($j(this).width());
				});
				ui.css('left', '0');
				return ui;
			},
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				field_row_indexes(this);
			}
		});
	
		function field_row_indexes(obj) {
			rows = $j(obj).find('tr');
			$j(rows).each(function(index, el){
				$j('input.field_order', el).val( parseInt( $j(el).index() ) );
			});
		};
	}

	// Select all field options for this export type
	$j('.checkall').click(function () {
		$j(this).closest('.postbox').find('input[type="checkbox"]:not(:disabled)').prop('checked', true);
	});

	// Unselect all field options for this export type
	$j('.uncheckall').click(function () {
		$j(this).closest('.postbox').find('input[type="checkbox"]:not(:disabled)').prop('checked', false);
	});

	// Reset sorting of fields for this export type
	$j('.resetsorting').click(function () {
		var type = $j(this).prop('id');
		var type = type.replace('-resetsorting','');
		for(i=0; i<$j('#' + type + '-fields tr').length; i++){
			$j('#' + type + '-' + i).appendTo('#' + type + '-fields');
		}
		field_row_indexes($j('#' + type + '-fields'));
	});

	$j('.export-types').hide();
	$j('.export-options').hide();

	// Categories
	$j('#export-products-filters-categories').hide();
	if( $j('#products-filters-categories').prop('checked') ) {
		$j('#export-products-filters-categories').show();
	}
	// Tags
	$j('#export-products-filters-tags').hide();
	if( $j('#products-filters-tags').prop('checked') ) {
		$j('#export-products-filters-tags').show();
	}
	// Brands
	$j('#export-products-filters-brands').hide();
	if( $j('#products-filters-brands').prop('checked') ) {
		$j('#export-products-filters-brands').show();
	}
	// Product Vendors
	$j('#export-products-filters-vendors').hide();
	if( $j('#products-filters-vendors').prop('checked') ) {
		$j('#export-products-filters-vendors').show();
	}
	// Product Status
	$j('#export-products-filters-status').hide();
	if( $j('#products-filters-status').prop('checked') ) {
		$j('#export-products-filters-status').show();
	}
	// Type
	$j('#export-products-filters-type').hide();
	if( $j('#products-filters-type').prop('checked') ) {
		$j('#export-products-filters-type').show();
	}
	// Stock
	$j('#export-products-filters-stock').hide();
	if( $j('#products-filters-stock').prop('checked') ) {
		$j('#export-products-filters-stock').show();
	}

	$j('#export-category').hide();

	$j('#export-tag').hide();

	$j('#export-brand').hide();

	$j('#export-order').hide();
	// Order Status
	$j('#export-orders-filters-status').hide();
	if( $j('#orders-filters-status').prop('checked') ) {
		$j('#export-orders-filters-status').show();
	}
	// Order Date
	$j('#export-orders-filters-date').hide();
	if( $j('#orders-filters-date').prop('checked') ) {
		$j('#export-orders-filters-date').show();
	}
	// Customer
	$j('#export-orders-filters-customer').hide();
	if( $j('#orders-filters-customer').prop('checked') ) {
		$j('#export-orders-filters-customer').show();
	}
	// Billing Country
	$j('#export-orders-filters-billing_country').hide();
	if( $j('#orders-filters-billing_country').prop('checked') ) {
		$j('#export-orders-filters-billing_country').show();
	}
	// Shipping Country
	$j('#export-orders-filters-shipping_country').hide();
	if( $j('#orders-filters-shipping_country').prop('checked') ) {
		$j('#export-orders-filters-shipping_country').show();
	}
	// User Role
	$j('#export-orders-filters-user_role').hide();
	if( $j('#orders-filters-user_role').prop('checked') ) {
		$j('#export-orders-filters-user_role').show();
	}
	// Coupon Code
	$j('#export-orders-filters-coupon').hide();
	if( $j('#orders-filters-coupon').prop('checked') ) {
		$j('#export-orders-filters-coupon').show();
	}
	// Products
	$j('#export-orders-filters-product').hide();
	if( $j('#orders-filters-product').prop('checked') ) {
		$j('#export-orders-filters-product').show();
	}
	// Categories
	$j('#export-orders-filters-category').hide();
	if( $j('#orders-filters-category').prop('checked') ) {
		$j('#export-orders-filters-category').show();
	}
	// Tags
	$j('#export-orders-filters-tag').hide();
	if( $j('#orders-filters-tag').prop('checked') ) {
		$j('#export-orders-filters-tag').show();
	}
	// Brands
	$j('#export-orders-filters-brand').hide();
	if( $j('#orders-filters-brand').prop('checked') ) {
		$j('#export-orders-filters-brand').show();
	}
	// Order ID
	$j('#export-orders-filters-id').hide();
	if( $j('#orders-filters-id').prop('checked') ) {
		$j('#export-orders-filters-id').show();
	}
	// Payment Gateway
	$j('#export-orders-filters-payment_gateway').hide();
	if( $j('#orders-filters-payment_gateway').prop('checked') ) {
		$j('#export-orders-filters-payment_gateway').show();
	}
	// Payment Gateway
	$j('#export-orders-filters-shipping_method').hide();
	if( $j('#orders-filters-shipping_method').prop('checked') ) {
		$j('#export-orders-filters-shipping_method').show();
	}

	// Order Status
	$j('#export-customers-filters-status').hide();
	if( $j('#customers-filters-status').prop('checked') ) {
		$j('#export-customers-filters-status').show();
	}
	// User Role
	$j('#export-customers-filters-user_role').hide();
	if( $j('#customers-filters-user_role').prop('checked') ) {
		$j('#export-customers-filters-user_role').show();
	}

	// Subscription Status
	$j('#export-subscriptions-filters-status').hide();
	if( $j('#subscriptions-filters-status').prop('checked') ) {
		$j('#export-subscriptions-filters-status').show();
	}
	// Subscription Product
	$j('#export-subscriptions-filters-product').hide();
	if( $j('#subscriptions-filters-product').prop('checked') ) {
		$j('#export-subscriptions-filters-product').show();
	}

	// Order Date
	$j('#export-commissions-filters-date').hide();
	if( $j('#commissions-filters-date').prop('checked') ) {
		$j('#export-commissions-filters-date').show();
	}
	// Product Vendor
	$j('#export-commissions-filters-product_vendor').hide();
	if( $j('#commissions-filters-product_vendor').prop('checked') ) {
		$j('#export-commissions-filters-product_vendor').show();
	}
	// Commission Status
	$j('#export-commissions-filters-commission_status').hide();
	if( $j('#commissions-filters-commission_status').prop('checked') ) {
		$j('#export-commissions-filters-commission_status').show();
	}

	$j('#export-customer').hide();
	$j('#export-user').hide();
	$j('#export-review').hide();
	$j('#export-coupon').hide();
	$j('#export-subscription').hide();
	$j('#export-product_vendor').hide();
	$j('#export-commission').hide();
	$j('#export-shipping_class').hide();
	$j('#export-ticket').hide();
	$j('#export-attribute').hide();

	$j('#products-filters-categories').click(function(){
		$j('#export-products-filters-categories').toggle();
	});
	$j('#products-filters-tags').click(function(){
		$j('#export-products-filters-tags').toggle();
	});
	$j('#products-filters-brands').click(function(){
		$j('#export-products-filters-brands').toggle();
	});
	$j('#products-filters-vendors').click(function(){
		$j('#export-products-filters-vendors').toggle();
	});
	$j('#products-filters-status').click(function(){
		$j('#export-products-filters-status').toggle();
	});
	$j('#products-filters-type').click(function(){
		$j('#export-products-filters-type').toggle();
	});
	$j('#products-filters-stock').click(function(){
		$j('#export-products-filters-stock').toggle();
	});

	$j('#orders-filters-date').click(function(){
		$j('#export-orders-filters-date').toggle();
	});
	$j('#orders-filters-status').click(function(){
		$j('#export-orders-filters-status').toggle();
	});
	$j('#orders-filters-customer').click(function(){
		$j('#export-orders-filters-customer').toggle();
	});
	$j('#orders-filters-billing_country').click(function(){
		$j('#export-orders-filters-billing_country').toggle();
	});
	$j('#orders-filters-shipping_country').click(function(){
		$j('#export-orders-filters-shipping_country').toggle();
	});
	$j('#orders-filters-user_role').click(function(){
		$j('#export-orders-filters-user_role').toggle();
	});
	$j('#orders-filters-coupon').click(function(){
		$j('#export-orders-filters-coupon').toggle();
	});
	$j('#orders-filters-product').click(function(){
		$j('#export-orders-filters-product').toggle();
	});
	$j('#orders-filters-category').click(function(){
		$j('#export-orders-filters-category').toggle();
	});
	$j('#orders-filters-tag').click(function(){
		$j('#export-orders-filters-tag').toggle();
	});
	$j('#orders-filters-brand').click(function(){
		$j('#export-orders-filters-brand').toggle();
	});
	$j('#orders-filters-id').click(function(){
		$j('#export-orders-filters-id').toggle();
	});
	$j('#orders-filters-payment_gateway').click(function(){
		$j('#export-orders-filters-payment_gateway').toggle();
	});
	$j('#orders-filters-shipping_method').click(function(){
		$j('#export-orders-filters-shipping_method').toggle();
	});

	$j('#customers-filters-status').click(function(){
		$j('#export-customers-filters-status').toggle();
	});
	$j('#customers-filters-user_role').click(function(){
		$j('#export-customers-filters-user_role').toggle();
	});

	$j('#subscriptions-filters-status').click(function(){
		$j('#export-subscriptions-filters-status').toggle();
	});
	$j('#subscriptions-filters-product').click(function(){
		$j('#export-subscriptions-filters-product').toggle();
	});

	$j('#commissions-filters-date').click(function(){
		$j('#export-commissions-filters-date').toggle();
	});
	$j('#commissions-filters-product_vendor').click(function(){
		$j('#export-commissions-filters-product_vendor').toggle();
	});
	$j('#commissions-filters-commission_status').click(function(){
		$j('#export-commissions-filters-commission_status').toggle();
	});

	// Export types
	$j('#product').click(function(){
		$j('.export-types').hide();
		$j('#export-product').show();

		$j('.export-options').hide();
		$j('.product-options').show();
	});
	$j('#category').click(function(){
		$j('.export-types').hide();
		$j('#export-category').show();

		$j('.export-options').hide();
		$j('.category-options').show();
	});
	$j('#tag').click(function(){
		$j('.export-types').hide();
		$j('#export-tag').show();

		$j('.export-options').hide();
		$j('.tag-options').show();
	});
	$j('#brand').click(function(){
		$j('.export-types').hide();
		$j('#export-brand').show();

		$j('.export-options').hide();
		$j('.brand-options').show();
	});
	$j('#order').click(function(){
		$j('.export-types').hide();
		$j('#export-order').show();

		$j('.export-options').hide();
		$j('.order-options').show();
	});
	$j('#customer').click(function(){
		$j('.export-types').hide();
		$j('#export-customer').show();

		$j('.export-options').hide();
		$j('.customer-options').show();
	});
	$j('#user').click(function(){
		$j('.export-types').hide();
		$j('#export-user').show();

		$j('.export-options').hide();
		$j('.user-options').show();
	});
	$j('#review').click(function(){
		$j('.export-types').hide();
		$j('#export-review').show();

		$j('.export-options').hide();
		$j('.review-options').show();
	});
	$j('#coupon').click(function(){
		$j('.export-types').hide();
		$j('#export-coupon').show();

		$j('.export-options').hide();
		$j('.coupon-options').show();
	});
	$j('#subscription').click(function(){
		$j('.export-types').hide();
		$j('#export-subscription').show();

		$j('.export-options').hide();
		$j('.subscription-options').show();
	});
	$j('#product_vendor').click(function(){
		$j('.export-types').hide();
		$j('#export-product_vendor').show();

		$j('.export-options').hide();
		$j('.product_vendor-options').show();
	});
	$j('#commission').click(function(){
		$j('.export-types').hide();
		$j('#export-commission').show();

		$j('.export-options').hide();
		$j('.commission-options').show();
	});
	$j('#shipping_class').click(function(){
		$j('.export-types').hide();
		$j('#export-shipping_class').show();

		$j('.export-options').hide();
		$j('.shipping_class-options').show();
	});
	$j('#attribute').click(function(){
		$j('.export-types').hide();
		$j('#export-attribute').show();

		$j('.export-options').hide();
		$j('.attribute-options').show();
	});

	// Export button
	$j('#export_product').click(function(){
		$j('input:radio[name=dataset][value="product"]').prop('checked',true);
	});
	$j('#export_category').click(function(){
		$j('input:radio[name=dataset][value="category"]').prop('checked',true);
	});
	$j('#export_tag').click(function(){
		$j('input:radio[name=dataset][value="tag"]').prop('checked',true);
	});
	$j('#export_brand').click(function(){
		$j('input:radio[name=dataset][value="brand"]').prop('checked',true);
	});
	$j('#export_order').click(function(){
		$j('input:radio[name=dataset][value="order"]').prop('checked',true);
	});
	$j('#export_customer').click(function(){
		$j('input:radio[name=dataset][value="customer"]').prop('checked',true);
	});
	$j('#export_user').click(function(){
		$j('input:radio[name=dataset][value="user"]').prop('checked',true);
	});
	$j('#export_review').click(function(){
		$j('input:radio[name=dataset][value="review"]').prop('checked',true);
	});
	$j('#export_coupon').click(function(){
		$j('input:radio[name=dataset][value="coupon"]').prop('checked',true);
	});
	$j('#export_subscription').click(function(){
		$j('input:radio[name=dataset][value="subscription"]').prop('checked',true);
	});
	$j('#export_product_vendor').click(function(){
		$j('input:radio[name=dataset][value="product_vendor"]').prop('checked',true);
	});
	$j('#export_commission').click(function(){
		$j('input:radio[name=dataset][value="commission"]').prop('checked',true);
	});
	$j('#export_shipping_class').click(function(){
		$j('input:radio[name=dataset][value="shipping_class"]').prop('checked',true);
	});
	$j('#export_ticket').click(function(){
		$j('input:radio[name=dataset][value="ticket"]').prop('checked',true);
	});
	$j('#export_booking').click(function(){
		$j('input:radio[name=dataset][value="booking"]').prop('checked',true);
	});
	$j('#export_attribute').click(function(){
		$j('input:radio[name=dataset][value="attribute"]').prop('checked',true);
	});

	// Changing the Export Type will show/hide other options
	$j("#export_type").change(function () {
		var type = $j('select[name=export_type]').val();
		$j('.export_type_options .export-options').hide();
		if( type == null )
			var type = 'product';
		$j('.export_type_options .'+type+'-options').show();
	});

	// Changing the Export Method will show/hide other options
	$j("#auto_method").change(function () {
		var type = $j('select[name=auto_method]').val();
		$j('.auto_method_options .export-options').hide();
		$j('.auto_method_options .'+type+'-options').show();
	});

	// Display a list of advanced options on the Settings screen
	$j('#advanced-settings').click(function(){
		$j('.advanced-settings').toggle();
		return false;
	});

	// Confirmation prompt on button actions
	$j('.advanced-settings a.delete, .post-type-scheduled_export a.confirm-button').click(function(e){
		e.preventDefault();
		var choice = confirm($j(this).attr('data-confirm'));
		if( choice ) {
			window.location.href = $j(this).attr('href');
		}
	});

	$j(document).ready(function() {

		// This auto-selects the export type based on the link from the Overview screen
		var href = $j(location).attr('href');
		// If this is the Export tab
		if (href.toLowerCase().indexOf('tab=export') >= 0) {
			// If the URL includes an in-line link
			if (href.toLowerCase().indexOf('#') >= 0 ) {
				var type = href.substr(href.indexOf("#") + 1);
				var type = type.replace('export-','');
				$j('#'+type).trigger('click');
			} else {
				// This auto-selects the last known export type based on stored WordPress option, defaults to Products
				var type = $j('input:radio[name=dataset]:checked').val();
				$j('#'+type).trigger('click');
			}
		} else if ( href.toLowerCase().indexOf('tab=settings') >= 0 ) {
			$j("#export_type").trigger("change");
			$j("#auto_method").trigger("change");
		} else {
			// This auto-selects the last known export type based on stored WordPress option, defaults to Products
			var type = $j('input:radio[name=dataset]:checked').val();
			$j('#'+type).trigger('click');
		}

		// Adds the Export button to WooCommerce screens within the WordPress Administration
		var export_url = 'admin.php?page=woo_ce';
		var export_text = 'Export';
		var export_text_override = 'Export with <attr value="Store Exporter">SE</attr>';
		var export_html = '<a href="' + export_url + '" class="page-title-action">' + export_text + '</a>';

		// Adds the Export button to the Products screen
		var product_screen = $j( '.edit-php.post-type-product' );
		var title_action = product_screen.find( '.page-title-action:last' );
		export_html = '<a href="' + export_url + '#export-product" class="page-title-action" title="Export Products with Store Exporter">' + export_text_override + '</a>';
		title_action.after( export_html );

		// Adds the Export button to the Category screen
		var category_screen = $j( '.edit-tags-php.post-type-product.taxonomy-product_cat' );
		var title_action = category_screen.find( '.wp-heading-inline' );
		export_html = '<a href="' + export_url + '#export-category" class="page-title-action" title="Export Categories with Store Exporter">' + export_text + '</a>';
		title_action.after( export_html );

		// Adds the Export button to the Product Tag screen
		var tag_screen = $j( '.edit-tags-php.post-type-product.taxonomy-product_tag' );
		var title_action = tag_screen.find( '.wp-heading-inline' );
		export_html = '<a href="' + export_url + '#export-tag" class="page-title-action" title="Export Product Tags with Store Exporter">' + export_text + '</a>';
		title_action.after( export_html );

		// Adds the Export button to the Orders screen
		var order_screen = $j( '.edit-php.post-type-shop_order' );
		var title_action = order_screen.find( '.page-title-action:last' );
		export_html = '<a href="' + export_url + '#export-order" class="page-title-action" title="Export Orders with Store Exporter">' + export_text + '</a>';
		title_action.after( export_html );

		// Adds the Export button to the Users screen
		var user_screen = $j( '.users-php' );
		var title_action = user_screen.find( '.page-title-action:last' );
		export_html = '<a href="' + export_url + '#export-user" class="page-title-action" title="Export Users with Store Exporter">' + export_text + '</a>';
		title_action.after( export_html );

	});

});