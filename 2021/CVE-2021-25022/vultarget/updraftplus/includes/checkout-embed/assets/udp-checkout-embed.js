(function($) {
	var checkout_embed = {
		loading: false,
		init: function() {
			var buttons = $('a[data-embed-checkout]');

			if (!buttons.length) return;

			var that = this;
			$(document).on('click', 'a[data-embed-checkout]', function(e) {
				e.preventDefault();
				that.modal.open($(this));
			});
		},
		modal: {
			open: function($target) {
				// if first time opening or different product
				this.$target = $target;

				if (!this.product_url || (this.product_url && this.product_url != $target.data('embed-checkout'))) {
					this.product_url = $target.data('embed-checkout');
					// Disabled because of SameSite cookie issues
					// this.setup();
				}

				this.show_checkout();

				if (0) {
					// Disabled because of SameSite cookie issues
					// adds / remove classes
					$('body').addClass('udp-modal-is-opened');
					this.$el.removeClass('iframe-is-opened');

					// Show it.
					this.$el.appendTo('body').show();
					window.scrollTo(0,0);
				}
			},
			setup: function() {
				if (this.$el) {
					this.$el.remove();
					this.$el = null;
				}
				var template = $('#udp-modal-template').html();
				this.$el = $(template);
				// receives events from iframe
				window.addEventListener('message', function(event) {
					var response = event.data;
					if (response && response.action) {
						switch (response.action) {
							case 'domready':
								this.$el.removeClass('loading');
								break;
							case 'closemodal':
								$(document).trigger('udp/checkout/close', response.data, this.$target);
								this.close();
								break;
							case 'ordercomplete':
								console.log('Order complete:', response.data);
								$(document).trigger('udp/checkout/done', response.data, this.$target);
								break;
						}
					}
				}.bind(this));
			},
			close: function(event) {
				if (event) event.preventDefault();
				$('body').removeClass('udp-modal-is-opened');
				if (this.$iframe) {
					this.$iframe.remove();
					this.$iframe_container.remove();
				}
				this.$el.hide();
			},
			show_checkout: function() {
				if (1) {
					window.location.assign(this.product_url);
				} else {
					// Disabled because of SameSite problems
					this.$el.addClass('loading iframe-is-opened');
					this.$iframe = $('<iframe src="' + this.product_url + '"/>');
					this.$iframe_container = $('<div class="udp-modal__iframe"/>').appendTo(this.$el.find('.udp-modal__modal')).append(this.$iframe);
				}
			}
		}
	}
	jQuery(function(e) {
		checkout_embed.init();
	});
})(jQuery);
