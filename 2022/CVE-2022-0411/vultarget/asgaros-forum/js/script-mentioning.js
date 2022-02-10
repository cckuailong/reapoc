window.asgaros = window.asgaros || {};

(function(asgaros, $) {
	var mentionsQueryCache = [];

	// Adds @mentions to form inputs.
	$.fn.suggestions = function() {
		var opts = {
			at:					'@',
			delay:				200,
			displayTpl:			'<li data-value="@${ID}"><img src="${image}"><span class="username">@${ID}</span><small>${name}</small></li>',
			hideWithoutSuffix:	true,
			insertTpl:			'@${ID}',
			limit:				10,
			searchKey:			'ID',
			startWithSpace:		false,
			suffix:				'',
			callbacks: {
				remoteFilter: function(query, render_view) {
					// Cancel if query-string is empty.
					if (!query) {
						return;
					}

					// Try to get matches from cache first.
					var mentionsItem = mentionsQueryCache[query];

					if (typeof mentionsItem === 'object') {
						render_view(mentionsItem);
						return;
					}

					// If no matches available, do a request.
					$.ajax({
		                url: wpApiSettings.root+'asgaros-forum/v1/suggestions/mentioning/'+query,
		                method: 'POST'
		            })
					.done(function(response) {
		                if (response.status === true) {
							mentionsQueryCache[query] = response.data;
							render_view(response.data);
						}
		            });
				},
				beforeReposition: function(offset) {
					var caret;
					var line;
					var iframeOffset;
					var move;
					var $view = $('#atwho-ground-'+this.id+' .atwho-view');
					var $body = $('body');
					var atwhoDataValue = this.$inputor.data('atwho');

					if ('undefined' !== atwhoDataValue && 'undefined' !== atwhoDataValue.iframe && null !== atwhoDataValue.iframe) {
						caret = this.$inputor.caret('offset', { iframe: atwhoDataValue.iframe });
						// Caret.js no longer calculates iframe caret position from the window (it's now just within the iframe).
						// We need to get the iframe offset from the window and merge that into our object.
						iframeOffset = $(atwhoDataValue.iframe).offset();
						if ('undefined' !== iframeOffset) {
							caret.left += iframeOffset.left;
							caret.top += iframeOffset.top;
						}
					} else {
						caret = this.$inputor.caret('offset');
					}

					// If the caret is past horizontal half, then flip it, yo
					if (caret.left > ($body.width() / 2)) {
						$view.addClass('right');
						move = caret.left - offset.left - this.view.$el.width();
					} else {
						$view.removeClass('right');
						move = caret.left - offset.left + 1;
					}

					// If we're on a small screen, scroll to caret
					if ($body.width() <= 400) {
						$(document).scrollTop(caret.top - 6);
					}

					// New position is under the caret (never above) and positioned to follow
					// Dynamic sizing based on the input area (remove 'px' from end)
					line = parseInt(this.$inputor.css('line-height').substr(0, this.$inputor.css('line-height').length - 2), 10);
					if (!line || line < 5) { // sanity check, and catch no line-height
						line = 19;
					}

					offset.top = caret.top + line;
					offset.left += move;
				}
			},
			functionOverrides: {
				// Override default behaviour which inserts junk tags in the WordPress Visual editor.
				insert: function(content, $li) {
					data = $li.data('item-data');
					this.query.el.removeAttr('class').html(content).attr('contenteditable', "false");

					if (!this.$inputor.is(':focus')) {
						this.$inputor.focus();
					}

					return this.$inputor.change();
				}
			}
		};

		return $.fn.atwho.call(this, opts);
	};

	asgaros.suggestions_initialize = function() {
		if (typeof window.tinyMCE === 'undefined' || window.tinyMCE.activeEditor === null || typeof window.tinyMCE.activeEditor === 'undefined') {
			return;
		} else {
			$(window.tinyMCE.activeEditor.contentDocument.activeElement).atwho('setIframe', $('.wp-editor-wrap iframe')[0]).suggestions();
		}
	};
})(asgaros, jQuery);
