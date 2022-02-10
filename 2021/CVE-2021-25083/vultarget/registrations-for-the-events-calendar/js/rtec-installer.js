(function($) {

    /**
     * RtecAddOn
     * Handle events and HTML updates for plugin managing
     *
     * @param plugin
     * @constructor
     *
     * @since 2.20
     */
    function RtecAddOn(plugin) {
        this.plugin = 'tec-main';

        this.$wrapper = $('.rtec-license-container').length ? $('.rtec-license-container') : $('#rtec-admin-tec-welcome');
    }

    RtecAddOn.prototype = {

        /**
         * Perform the action on the plugin. Usually goes with an
         * AJAX call
         *
         * @param action
         *
         * @since 2.20
         */
        initAction: function(action) {
            this.$wrapper.find('.rtec-addon-license-input').removeClass('rtec-danger-outline');
            this.$wrapper.find('.rtec-add-on-status-message').remove();
            if (action === 'enter-license') {
                this.toggleLicense();
                return;
            }
            if (action === 'clear-license') {
                this.$wrapper.find('.rtec-addon-license-input').val('');
            }
            var acceptedActions = ['activate','deactivate','install','recheck-license','deactivate-license','activate-license','clear-license'];
            if (acceptedActions.indexOf(action) === -1) {
                return;
            }
            if (action === 'install') {
                this.$wrapper.find('.rtec-addon-container').after('<div class="rtec-add-on-status-message" style="display: inline-block;">' + rtecAdminAddOns.thanks_patience + '</div>');
            }
            var data = {
                action : 'rtec_addon_' + action.replace('-','_'),
                license : this.$wrapper.find('input[name=rtec_license_input]').val()
            };
            this.ajax(data,action);
        },

        /**
         * Perform plugin related AJAX calls.
         *
         * @param data
         * @param action
         *
         * @since 2.20
         */
        ajax: function(data,action) {
            data.rtec_nonce = rtecAdminAddOns.rtec_nonce;
            data.plugin = this.plugin;

            var self = this,
                $addOnWrapper = self.$wrapper,
                $actionButton = $addOnWrapper.find('button[data-action='+ action+']').length ? $addOnWrapper.find('button[data-action='+ action+']') : $addOnWrapper.find('a[data-action='+ action+']'),
                isLicenseOption = $addOnWrapper.find('a[data-action='+ action+']').length,
                currentHTML = $actionButton.html();

            if (isLicenseOption) {
                $actionButton.closest('.rtec-license-options').append(window.RtecAddOnsManager.spinnerHTML()).addClass('rtec-action-disabled rtec-spinner-active');
            } else {
                $actionButton.prepend(window.RtecAddOnsManager.spinnerHTML()).addClass('rtec-action-disabled rtec-spinner-active');
            }
            self.$wrapper.addClass('rtec-doing-ajax');

            $.post(rtecAdminAddOns.ajax_url, data, function(res) {
                self.$wrapper.removeClass('rtec-doing-ajax');
                $actionButton.removeClass('rtec-action-disabled rtec-spinner-active').find('.rtec-spinner-circle').remove();
                $actionButton.closest('.rtec-license-options').removeClass('rtec-action-disabled rtec-spinner-active');
                if (res.success) {
                    self.afterActionSuccess(res.data);
                } else {
                    $actionButton.html(currentHTML);
                    self.afterActionError(res.data,action);
                }
            }).fail(function(xhr) {
                self.$wrapper.removeClass('rtec-doing-ajax');
                console.log( xhr.responseText );
            });
        },

        /**
         * HTML changes when the AJAX response is a success.
         *
         * @param res
         *
         * @since 2.20
         */
        afterActionSuccess: function(res) {
            if (this.plugin === 'tribe-tec') {
                this.toggleTECButtons(res);
            }
            if (typeof res.messageHTML !== 'undefined') {
                this.$wrapper.find('.rtec-addon-buttons').empty().append('<div class="rtec-add-on-status-message rtec-flex-center-space" style="display: flex;">' + window.RtecAddOnsManager.spinnerHTML() + res.messageHTML + '</div>');
                window.location.reload();
            }
        },

        /**
         * HTML changes when the AJAX response is an error.
         *
         * @param res
         *
         * @since 2.20
         */
        afterActionError: function(res,action) {
            if (typeof res.messageHTML !== 'undefined') {
                if (this.plugin === 'rtec-main') {
                    this.$wrapper.find('.rtec-license-field').append('<div class="rtec-add-on-status-message">' + res.messageHTML + '</div>');
                } else {
                    this.$wrapper.append('<div class="rtec-add-on-status-message">' + res.messageHTML + '</div>');
                }
            }
            if (typeof res.licenseHTML !== 'undefined') {
                this.$wrapper.find('.rtec-license-options').html(res.licenseHTML);
                window.RtecAddOnsManager.initActionButtons(this.$wrapper.find('.rtec-license-options a'));
            }
            if (action === 'install') {
                this.$wrapper.find('.rtec-addon-license-input').addClass('rtec-danger-outline');
            }
        },

        /**
         * Used to toggle what buttons show when installing TEC. (Welcome page)
         *
         * @since 2.20
         */
        toggleTECButtons: function() {
            this.$wrapper.find('.rtec-tec-install').addClass('rtec-tec-success');
            this.$wrapper.find('.rtec-tec-activate').addClass('rtec-tec-success');
            this.$wrapper.find('.rtec-tec-activate').removeClass('rtec-tec-activate');
            this.$wrapper.find('.rtec-tec-install').removeClass('rtec-tec-install');

        },
    };

    var RtecAddOnsManager = {

        /**
         * Start.
         *
         * @since 2.20
         */
        init: function() {
            // Document ready.
            $(document).ready(RtecAddOnsManager.ready);

            RtecAddOnsManager.initTEC();
        },

        /**
         * Document ready.
         *
         * @since 2.20
         */
        ready: function() {
            // Action available for each binding.
            $(document).trigger( 'rtecReady' );
        },

        /**
         * Used to re-init buttons and links on the fly.
         *
         * @since 2.20
         */
        initActionButtons: function($selector) {
            $selector.on('click',function(event) {
                event.preventDefault();
                var action = typeof $(this).attr('data-action') !== 'undefined' ? $(this).attr('data-action') : false;

                if (action) {
                    var pluginSlug = $(this).closest('.rtec-addon-container').length ? $(this).closest('.rtec-addon-container').attr('data-add-on') : 'rtec-main',
                        addOn = new RtecAddOn(pluginSlug);

                    addOn.initAction(action);
                }
            });
        },

        /**
         * For TEC (Welcome Page)
         *
         * @since 2.20
         */
        initTEC: function() {
            $(document).on( 'rtecReady', function() {
                RtecAddOnsManager.initActionButtons($('#rtec-admin-tec-welcome button'));
            });
        },

        /**
         * Adding spinner after actions
         *
         * @returns {string}
         *
         * @since 2.20
         */
        spinnerHTML() {
            return '<div class="rtec-spinner-container"><div class="rtec-spinner-circle"></div></div>';
        }
    };

    RtecAddOnsManager.init();

    window.RtecAddOnsManager = RtecAddOnsManager;

})( jQuery );