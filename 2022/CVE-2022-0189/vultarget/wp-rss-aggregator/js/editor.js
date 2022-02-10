var WPRSS_TMCE_PLUGIN_ID = 'wprss';
var WPRSS_ED = null;
var wprss_dialog_submit = null;

(function($) {
	wprss_dialog_submit = function() {
		this.focus();

		var shortcode = '[wp-rss-aggregator';

		var all = $('#wprss-dialog-all-sources').is(':checked');

		var selected_template = $('#wprss-dialog-templates').val();
		if (selected_template.length > 0) {
			shortcode += ' template="' + selected_template + '"';
		}

		sources = [];
		$('#wprss-dialog-feed-source-list :selected').each( function( i, selected ){
			sources[i] = $(selected).val();
		});
		sources = sources.join(',');

		excludes = [];
		$('#wprss-dialog-exclude-list :selected').each( function( i, selected ){
			excludes[i] = $(selected).val();
		});
		excludes = excludes.join(',');

		limit = $('#wprss-dialog-feed-limit').val();

		pagination = $('#wprss-dialog-pagination').val();

		page = $('#wprss-dialog-start-page').val();

		if ( all ) {
			if ( excludes.length > 0 ) {
				shortcode += ' exclude="' + excludes + '"';
			}
		} else {
			if ( sources.length > 0 ) {
				shortcode += ' source="' + sources + '"';
			}
		}

		if ( limit !== '' && limit !== '0' ) {
			shortcode += ' limit="' + limit + '"';
		}

		if (pagination.length > 0) {
			shortcode += ' pagination="' + pagination + '"';
		}

		if ( page !== '' && parseInt(page) > 1 ) {
			shortcode += ' page="' + page + '"';
		}

		shortcode += ']';

		WPRSS_ED.execCommand('mceInsertContent', false, shortcode);
		WPRSS_Dialog.close();
	}

	window.WPRSS_Dialog = new function() {
		// Keep a reference to the current object
		var base = this;
		var dialog = null;
		var dialog_head = null;
		var dialog_head_close = null;
		var dialog_inside = null;

		var close = function( e ) {
			overlay.fadeOut();
			dialog_inside.empty();
		};

		base.close = close;

		base.init = function() {
			overlay = $('<div id="wprss-overlay"></div>');
			dialog = $('<div id="wprss-editor-dialog" class="postbox"></div>');

			dialog_head = $('<div class="wprss-dialog-header"> <h1>WP RSS Aggregator Shortcode</h1> </div>');
			dialog_head_close = $('<span class="close-btn">Close</span>').appendTo( dialog_head );
			dialog_inside = $('<div class="wprss-dialog-inside"></div>');
			dialog.append( dialog_head );
			dialog.append( dialog_inside );

			overlay.hide().appendTo('body');
			dialog.appendTo(overlay);

			overlay.click( close );
			dialog_head_close.click( close );

			dialog.on( 'click', function( e ) {
				e.stopPropagation();
			});
		};


		base.getDialog = function() {
			overlay.show();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wprss_editor_dialog'
				},
				success: function( data, status, jqXHR) {
					if ( data.length > 0 ) {
						dialog_inside.html( data );
					}
				}
			});

			
		};
	}


	WPRSS_Dialog.init();




	tinymce.create( 'tinymce.plugins.' + WPRSS_TMCE_PLUGIN_ID, {
		// INITIALIZE THE BUTTON
		init : function( ed, url ) {
			// Add the button
			ed.addButton( WPRSS_TMCE_PLUGIN_ID, {
				title : 'WP RSS Aggregator shortcode',
				image : url + '/../images/wpra-icon-32.png',
				onclick : function() {
					idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
					WPRSS_Dialog.getDialog();
					WPRSS_ED = ed;
					/*
					var vidId = prompt("WP RSS Aggregator", "Choose feed source");
					var m = idPattern.exec(vidId);
					if (m != null && m != 'undefined')
						ed.execCommand('mceInsertContent', false, '[wprss source="'+m[1]+'"]');
					*/
				}
			});
		},
		createControl : function( n, cm ) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "WP RSS Aggregator Shortcode",
				author : 'RebelCode',
				authorurl : 'http://www.wprssaggregator.com/',
				infourl : 'http://www.wprssaggregator.com/',
				version : "1.1"
			};
		}
	});
	tinymce.PluginManager.add( WPRSS_TMCE_PLUGIN_ID, tinymce.plugins.wprss );
})(jQuery);
