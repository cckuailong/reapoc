(function ($) {
    $(document).ready(function () {
        var setUpStripe = function (stripe_keys) {
            var stripe = Stripe(stripe_keys.public);
            var elements = stripe.elements();
            var style = {
                base: {
                    color: '#32325d',
                    lineHeight: '18px',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '14px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            var card = elements.create('card', {style: style}); // We can place style here
            card.mount('#rm-stripe-card-element');
            var payment_error = null;
            var pay_btn = $('.rm_stripe_pay_btn');
            var clientSecret = null;
            var sub_id,log_id,total_price,description;
            pay_btn.click(function () {
                var container = $(this).closest('.rm_stripe_fields');
                payment_error = container.find("#rm_stripe_payment_errors");
                sub_id = $(this).data('submission-id');
                if (!sub_id) {
                    return;
                }
                total_price = $(this).data('total-price');
                if (!total_price) {
                    return;
                }
                log_id = $(this).data('log-id');
                if (!log_id) {
                    return;
                }
                description = $(this).data('description');
                if (!description) {
                    return;
                }
                pay_btn.addClass('rm_req_in_progress');
                var data = {action: 'rm_get_intent_from_stripe', sub_id: sub_id, total_price: total_price};
                $.ajax({
                    url: rm_ajax.url,
                    type: 'POST',
                    data: data,
                    async: true,
                    success: function(response) {
                        stripe.confirmCardPayment(response.data.client_secret, {
                            payment_method: {
                                card: card
                            }
                        }).then(function(result) {
                            if (result.error) {
                                payment_error.html(result.error.message);
                                pay_btn.removeClass('rm_req_in_progress');
                            } else {
                                var data = {action: 'rm_stripe_after_intent', intent_status: result.paymentIntent.status, intent: response.data.intent_json, total_price: total_price, sub_id: sub_id, current_url: get_current_url(), log_id: log_id, description: description};
                                $.ajax({
                                    url: rm_ajax.url,
                                    type: 'POST',
                                    data: data,
                                    async: true,
                                    success: function (success_response) {
                                        pay_btn.removeClass('rm_req_in_progress');
                                        container.html(success_response.data.msg);
                                        if (success_response.data.redirect) {
                                            location.href = success_response.data.redirect;
                                        }
                                        if (success_response.data.hasOwnProperty('reload_params')) {
                                            var url = [location.protocol, '//', location.host, location.pathname].join('');
                                            if(url.indexOf('admin-ajax.php')>=0){
                                                return;
                                            }
                                            url += success_response.data.reload_params;
                                            location.href = url;
                                        }
                                    }
                                });
                            }
                        });
                    },
                    error: function(response) {
                        payment_error.html(response.data.msg);
                        pay_btn.removeClass('rm_req_in_progress');
                    }
                });
                pay_btn.addClass('rm_req_in_progress');
                return false;
            });

            var charge_op_response = function (response, container) {
                if (!response.success) {
                    payment_error.html(response.data.msg);
                    pay_btn.removeClass('rm_req_in_progress');
                } else if (response.data.requires_action) {
                    handle_card_action(response, container);
                } else {
                    pay_btn.removeClass('rm_req_in_progress');
                    container.html(response.data.msg);
                    if (response.data.redirect) {
                        location.href = response.data.redirect;
                    }
                    if (response.data.hasOwnProperty('reload_params')) {
                        var url = [location.protocol, '//', location.host, location.pathname].join('');
                        if(url.indexOf('admin-ajax.php')>=0){
                            return;
                        }
                        url += response.data.reload_params;
                        location.href = url;
                    }
                }
            }

            var handle_card_action = function (response, container) {
                stripe.handleCardAction(response.data.payment_intent_client_secret)
                        .then(function (result) {
                            if (result.error) {
                                payment_error.html(result.error);
                                pay_btn.removeClass('rm_req_in_progress');
                            } else {
                                $.ajax({
                                    url: rm_ajax.url,
                                    type: 'POST',
                                    data: {payment_method_id: result.paymentMethod.id, action: 'rm_charge_amount_from_stripe', sub_id: sub_id, current_url: get_current_url(), log_id: log_id,total_price: total_price},
                                    async: true,
                                    success: function (result) {
                                        charge_op_response(result, container);
                                    }
                                });
                            }
                        });
            }

            var get_current_url = function () {
                return location.protocol + '//' + location.host + location.pathname;
            }
        }
        if (typeof stripe_keys === 'undefined') { // Handling embed form case
            $.ajax({
                url: rm_ajax.url,
                type: 'POST',
                data: {action: 'rm_stripe_localize_data'},
                async: true,
                success: function (result) {
                    var stripe_keys = result;
                    setUpStripe(stripe_keys);
                }
            });
        }
    });
})(jQuery);