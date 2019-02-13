/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

var OneStepCheckout = Class.create();
OneStepCheckout.prototype = {
    initialize: function (urls, settings) {
        this.showLoginUrl = urls.showLoginUrl;
        this.showPasswordUrl = urls.showPasswordUrl;
        this.loginPostUrl = urls.loginPostUrl;
        this.retrievePasswordUrl = urls.retrievePasswordUrl;
        this.save_address_url = urls.save_address_url;
        this.update_address_shipping = urls.update_address_shipping;
        this.update_address_payment = urls.update_address_payment;
        this.update_address_review = urls.update_address_review;
        this.update_shipping_payment = urls.update_shipping_payment
        this.update_shipping_review = urls.update_shipping_review;
        this.update_payment_review = urls.update_payment_review;
        this.savePaymentRedirectUrl = urls.savePaymentRedirectUrl;
        this.saveOrderPro = urls.saveOrderPro;
        this.fillAddressUrl = urls.fillAddressUrl;
        this.tcoUrl = urls.tcoUrl;
        this.wirecardUrl = urls.wirecardUrl;
        this.wirecard_checkout_pagecheckout = urls.wirecard_checkout_pagecheckout;
        this.isEnableAmazon = urls.isEnableAmazon;
        this.amazonCheckoutUrl = urls.amazonCheckoutUrl;
        this.sellerdeckPaymentUrl = urls.sellerdeckPaymentUrl;
        this.sellerdeckText = urls.sellerdeckText;
        this.preloadImage = urls.preloadImage;

        this.shipping_method_url = urls.shipping_method_url;
        this.enable_update_payment = urls.enable_update_payment;
        this.login_url = urls.login_url;
        this.show_login_link = urls.show_login_link;
        this.show_term_condition_url = urls.show_term_condition_url;




        this.wirecard_checkout_pagecheckout = urls.wirecard_checkout_pagecheckout;
        this.savePaymentRedirectUrl = urls.savePaymentRedirectUrl;
        this.fillAddressUrl = urls.fillAddressUrl;

        /* Settings*/
        this.isAjaxBillingFieldCountry = settings.isAjaxBillingFieldCountry;
        this.isShowShippingAddress = settings.isShowShippingAddress;
        this.isAjaxBillingFieldRegion = settings.isAjaxBillingFieldRegion;
        this.isAjaxBillingFieldPostCode = settings.isAjaxBillingFieldPostCode;
        this.isAjaxBillingFieldCity = settings.isAjaxBillingFieldCity;

        this.isFieldRequireManagement = settings.isFieldRequireManagement;

        this.reload_payment = settings.reload_payment;
    },

    form : $('one-step-checkout-form'),

    showloginbox: function () {
        this.showLogin(this.showLoginUrl);
    },

    showforgotpwd: function () {
        this.showpwdbox(this.showPasswordUrl);
    },

    showPasswordForm: function () {
        $('onestepcheckout-login').hide();
        $('onestepcheckout-forgot-password').show();
    },

    showloginform: function () {
        $('onestepcheckout-forgot-password').hide();
        $('onestepcheckout-login').show();
    },

    submitLoginForm: function () {
        var login_validator = new Validation('onestepcheckout-login-form');
        if (login_validator.validate()) {
            this.showLoginLoading();
            var url = this.loginPostUrl;
            var email = $('osclogin:email_address').value;
            var password = $('osclogin:password').value;
            var parameters = {email: email, password: password};
            var loginRequest = new Ajax.Request(url, {
                parameters: parameters,
                onComplete: loginProcess.bindAsEventListener(this),
                onFailure: ""
            });
        }
    },

    retrievePassword: function () {
        var passwordValidator = new Validation('osc-forgotpassword-form');
        if (passwordValidator.validate()) {
            this.showPassLoading();
            var url = this.retrievePasswordUrl;
            var email = $('forgotpassword_email_address').value;
            var parameters = {email: email};
            var loginRequest = new Ajax.Request(url, {
                parameters: parameters,
                onComplete: passwordProcess.bindAsEventListener(this),
                onFailure: ""
            });
        }
    },

    getResponseText: function (transport) {
        if (transport && transport.responseText) {
            try {
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        return response;
    },

    loginProcess: function (transport) {
        var response = this.getResponseText(transport);
        if (response.error && response.error != '') {
            $('onestepcheckout-login-error-message').update(response.error);
            $('onestepcheckout-login-error-message').show();
            this.disableLoginLoading();
        }
        else {
            $('onestepcheckout-login-error-message').hide();
            window.location = window.location;
        }
    },

    passwordProcess: function (transport) {
        var response = this.getResponseText(transport);
        if (response.success) {
            $('onestepcheckout-password-error-message').hide();
            $('onestepcheckout-password-loading').hide();
            $('onestepcheckout-password-success-message').show();
        }
        else {
            if (response.error && response.error != '') {
                $('onestepcheckout-password-error-message').update(response.error);
                $('onestepcheckout-password-error-message').show();
                this.disablePassLoading();
            }
        }
    },

    //Validate Radio
    $RF: function (el, radioGroup) {
        if ($(el).type && $(el).type.toLowerCase() == 'radio') {
            var radioGroup = $(el).name;
            var el = $(el).form;
        } else if ($(el).tagName.toLowerCase() != 'form') {
            return false;
        }

        var checked = $(el).getInputs('radio', radioGroup).find(
            function (re) {
                return re.checked;
            }
        );
        return (checked) ? $F(checked) : null;
    },

    enablePlaceOrderButton: function () {
        var validatorForm = new Validation('one-step-checkout-form');
        if (validatorForm.validate()) {
            $('onestepcheckout-button-place-order').disabled = false;
            $('onestepcheckout-button-place-order').addClassName('onestepcheckout-btn-checkout');
            $('onestepcheckout-button-place-order').removeClassName('place-order-loader');
        }
    },

    disablePlaceOrderButton: function () {
        $('onestepcheckout-button-place-order').disabled = true;
        $('onestepcheckout-button-place-order').removeClassName('onestepcheckout-btn-checkout');
        $('onestepcheckout-button-place-order').addClassName('place-order-loader');
    },

    save_address_information: function (save_address_url, update_address_shipping, update_address_payment, update_address_review) {
        var form = $('one-step-checkout-form');
        var shipping_method = this.$RF(form, 'shipping_method');
        var self = this;


        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        var parameters = {shipping_method: shipping_method, form_key: form_key};

        this.get_billing_data(parameters);
        this.get_shipping_data(parameters);

        if (typeof update_address_shipping == 'undefined') {
            var update_address_shipping = false;
        }
        if (typeof update_address_payment == 'undefined') {
            var update_address_payment = false;
        }
        if (typeof update_address_review == 'undefined') {
            var update_address_review = false;
        }
        if (update_address_shipping == 1) {
            var shipping_method_section = $$('div.onestepcheckout-shipping-method-section')[0];
            if (typeof shipping_method_section != 'undefined') {
                this.shippingLoad();
            }
        }

        if (update_address_payment == 1) {
            var payment_method_section = $$('div.onestepcheckout-payment-methods')[0];
            this.paymentLoad();
        }

        if (update_address_review == 1) {
            var review = $('checkout-review-load');
            this.reviewLoad();
        }
        count_loading = count_loading + 1;
        if ((update_address_shipping == 1) || (update_address_payment == 1) || (update_address_review == 1)) {
            this.disablePlaceOrderButton();
        }


        var request = new Ajax.Request(save_address_url, {
            parameters: parameters,
            onSuccess: function (transport) {
                if (transport.status == 200) {
                    var response = self.getResponseText(transport);
                    //leonard  show detail error
                    if (response.form_key_status == 1) {
                        alert(response.form_key_details);
                    }
                    //leonard  show detail error
                    count_loading = count_loading - 1;
                    if (count_loading == 0) {
                        if (update_address_shipping == 1) {
                            if (typeof shipping_method_section != 'undefined') {
                                shipping_method_section.update(response.shipping_method);
                                self.shippingShow();
                            }
                        }
                        if (update_address_payment == 1) {
                            payment_method_section.update(response.payment_method);
                            self.paymentShow();
                            // show payment form if available
                            if (self.$RF(form, 'payment[method]') != null) {
                                try {
                                    var payment_method = self.$RF(form, 'payment[method]');
                                    $('container_payment_method_' + payment_method).show();
                                    $('payment_form_' + payment_method).show();
                                } catch (err) {
                                }
                            }
                        }

                        if (update_address_review == 1) {
                            review.update(response.review);
                            self.reviewShow();
                        }
                        if (update_address_shipping == 1)
                            self.save_shipping_method(shipping_method_url, update_address_payment, update_address_review);
                        else
                            self.checkvalidEmail();

                        if ((update_address_shipping == 1) || (update_address_payment == 1) || (update_address_review == 1)) {
                            if (update_address_shipping == 1) {
                                if ((update_address_payment == 1) || (update_address_review == 1)) {

                                } else {
                                    self.enablePlaceOrderButton();
                                }
                            } else {
                                self.enablePlaceOrderButton();
                            }
                        }

                    }
                }
            },
            onFailure: ''
        });
    },

    save_shipping_method: function (shipping_method_url, update_shipping_payment, update_shipping_review) {
        var self = this;
        if (typeof update_shipping_payment == 'undefined') {
            var update_shipping_payment = false;
        }
        if (typeof update_shipping_review == 'undefined') {
            var update_shipping_review = false;
        }

        var form = $('one-step-checkout-form');
        var shipping_method = self.$RF(form, 'shipping_method');
        var payment_method = self.$RF(form, 'payment[method]');

        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        //reload payment only if this feature is enabled in admin - show image loading
        if (update_shipping_payment == 1) {
            var payment_method_section = $$('div.onestepcheckout-payment-methods')[0];
            self.paymentLoad();
        }
        //show image loading for review total
        if (update_shipping_review == 1) {
            var review = $('checkout-review-load');
            self.reviewLoad();
        }
        var parameters = {
            shipping_method: shipping_method,
            payment_method: payment_method,
            form_key: form_key
        };

        //Find payment parameters and include
        var items = $$('input[name^=payment]', 'select[name^=payment]');
        var names = items.pluck('name');
        var values = items.pluck('value');

        for (var x = 0; x < names.length; x++) {
            if (names[x] != 'payment[method]') {
                parameters[names[x]] = values[x];
            }
        }
        if ((update_shipping_payment == 1) || (update_shipping_review == 1)) {
            self.disablePlaceOrderButton();
        }
        var request = new Ajax.Request(shipping_method_url, {
            method: 'post',
            parameters: parameters,
            onFailure: '',
            onSuccess: function (transport) {
                if (transport.status == 200) {
                    var response = self.getResponseText(transport);
                    //leonard  show detail error
                    if (response.form_key_status == 1) {
                        alert(response.form_key_details);
                    }
                    //leonard  show detail error
                    if (enable_update_payment) {
                        if (update_shipping_payment == 1) {
                            payment_method_section.update(response.payment_method);
                            self.paymentShow();
                            // show payment form if available
                            if (self.$RF(form, 'payment[method]') != null) {
                                try {
                                    var payment_method = self.$RF(form, 'payment[method]');
                                    $('container_payment_method_' + payment_method).show();
                                    $('payment_form_' + payment_method).show();
                                } catch (err) {
                                }
                            }
                        }
                    }
                    if (update_shipping_review == 1) {
                        review.update(response.review);
                        self.reviewShow();
                    }
                    self.checkvalidEmail();
                }
            }
        });
    },


    checkvalidEmail: function () {
        if (($('billing:email') && $('billing:email').value != "") || ($('islogin2') && $('islogin2').value != '1')) {
            if (($('emailvalid') && $('emailvalid').value == "valid") || ($('islogin') && $('islogin').value == "1")
                || ($('islogin2') && $('islogin2').value == '1')) {
                invalidEmailPopup.close();

            } else if (($('emailvalid') && $('emailvalid').value == "invalid")) {
                invalidEmailPopup.open();
            }
        } else {
            if ($('emailvalid') && $('emailvalid').value == "invalid")
                invalidEmailPopup.open();
            else
                invalidEmailPopup.close();

        }
        this.enablePlaceOrderButton();
    },

    get_billing_data: function (parameters) {
        var input_billing_array = $$('input[name^=billing]');
        var select_billing_array = $$('select[name^=billing]');
        var textarea_billing_array = $$('textarea[name^=billing]');
        var street_count = 0;

        for (var i = 0; i < textarea_billing_array.length; i++) {
            var item = textarea_billing_array[i];
            parameters[item.name] = item.value;
        }

        for (var i = 0; i < input_billing_array.length; i++) {
            var item = input_billing_array[i];
            if (item.type == 'checkbox') {
                if (item.checked) {
                    parameters[item.name] = item.value;
                }
            }
            else {
                if (item.name == 'billing[street][]') {
                    var name = 'billing[street][' + street_count + ']';
                    parameters[name] = item.value;
                    street_count = street_count + 1;
                }
                else {
                    parameters[item.name] = item.value;
                }
            }
        }

        var street_count = 0;
        for (var i = 0; i < select_billing_array.length; i++) {
            var item = select_billing_array[i];
            //data[item.name] = item.value;
            if (item.type == 'checkbox') {
                if (item.checked) {
                    parameters[item.name] = item.value;
                }
            }
            else {
                if (item.name == 'billing[street][]') {
                    var name = 'billing[street][' + street_count + ']';
                    parameters[name] = item.value;
                    street_count = street_count + 1;
                }
                else {
                    parameters[item.name] = item.value;
                }
            }
        }
    },

    get_shipping_data: function (parameters) {
        var input_shipping_fields = $$('input[name^=shipping]');
        var select_shipping_fields = $$('select[name^=shipping]');
        var street_count = 0;
        for (var i = 0; i < input_shipping_fields.length; i++) {
            var item = input_shipping_fields[i];
            if (item.type == 'checkbox') {
                if (item.checked) {
                    parameters[item.name] = item.value;
                }
            }
            else {
                if (item.name != 'shipping_method') {
                    if (item.name == 'shipping[street][]') {
                        var name = 'shipping[street][' + street_count + ']';
                        parameters[name] = item.value;
                        street_count = street_count + 1;
                    }
                    else {
                        parameters[item.name] = item.value;
                    }
                }
            }
        }

        var street_count = 0;
        for (var i = 0; i < select_shipping_fields.length; i++) {
            var item = select_shipping_fields[i];
            //data[item.name] = item.value;
            if (item.type == 'checkbox') {
                if (item.checked) {
                    parameters[item.name] = item.value;
                }
            }
            else {
                if (item.name != 'shipping_method') {
                    if (item.name == 'shipping[street][]') {
                        var name = 'shipping[street][' + street_count + ']';
                        parameters[name] = item.value;
                        street_count = street_count + 1;
                    }
                    else {
                        parameters[item.name] = item.value;
                    }
                }
            }
        }
    },


    /* LOAD */

    reviewShow: function () {
        $('ajax-review').hide();
        $('control_overlay_review').hide();
        $('checkout-review-table-wrapper').setStyle({
            'opacity': '1',
            'filter': 'alpha(opacity=100)'
        });
    },

    reviewLoad: function () {
        $('ajax-review').show();
        $('control_overlay_review').show();
        $('checkout-review-table-wrapper').setStyle({
            'opacity': '0.3',
            'filter': 'alpha(opacity=30)'
        });
    },

    shippingShow: function () {
        if ($('ajax-shipping'))
            $('ajax-shipping').hide();
        if ($('control_overlay_shipping'))
            $('control_overlay_shipping').hide();
        if ($('onestepcheckout-shipping-method-section'))
            $('onestepcheckout-shipping-method-section').setStyle({
                'opacity': '1',
                'filter': 'alpha(opacity=100)'
            });
    },

    shippingLoad: function () {
        if ($('ajax-shipping'))
            $('ajax-shipping').show();
        if ($('control_overlay_shipping'))
            $('control_overlay_shipping').show();
        if ($('onestepcheckout-shipping-method-section'))
            $('onestepcheckout-shipping-method-section').setStyle({
                'opacity': '0.3',
                'filter': 'alpha(opacity=30)'
            });
    },

    paymentShow: function () {
        $('ajax-payment').hide();
        $('control_overlay_payment').hide();
        $('onestepcheckout-payment-methods').setStyle({
            'opacity': '1',
            'filter': 'alpha(opacity=100)'
        });
    },

    paymentLoad: function () {
        $('ajax-payment').show();
        $('control_overlay_payment').show();
        $('onestepcheckout-payment-methods').setStyle({
            'opacity': '0.3',
            'filter': 'alpha(opacity=30)'
        });
    },
    /* END */

    showPassLoading: function () {
        $('onestepcheckout-password-error-message').hide();
        $('osc-forgotpassword-form').hide();
        $('onestepcheckout-password-loading').show();
    },

    disablePassLoading: function () {
        $('osc-forgotpassword-form').show();
        $('onestepcheckout-password-loading').hide();
    },


    showLoginLoading: function () {
        $('onestepcheckout-login-error-message').hide();
        $('onestepcheckout-login-form').hide();
        $('onestepcheckout-login-loading').show();
    },


    disableLoginLoading: function () {
        $('onestepcheckout-login-form').show();
        $('onestepcheckout-login-loading').hide();
    },


    showLogin: function (url) {
        TINY.box.show(url, 1, 400, 250, 150);
        return false;
    },

    showpwdbox: function (url) {
        TINY.box.show(url, 1, 400, 250, 150);
        return false;
    },

    oscPlaceOrder: function (element) {
        var validator = new Validation('one-step-checkout-form');
        var self = this;
        var form = $('one-step-checkout-form');
        if (validator.validate()) {
            if (($('p_method_hosted_pro') && $('p_method_hosted_pro').checked) || ($('p_method_payflow_advanced') && $('p_method_payflow_advanced').checked)) {
                $('onestepcheckout-place-order-loading').show();
                $('onestepcheckout-button-place-order').removeClassName('onestepcheckout-btn-checkout');
                $('onestepcheckout-button-place-order').addClassName('place-order-loader');
                $('ajaxcart-load-ajax').show();
                self.checkAjax(this.saveOrderPro);
            } else {
                if (self.checkpayment()) {
                    var options = document.getElementsByName('payment[method]');
                    var paymentChecked;
                    for (var i = 0; i < options.length; i++) {
                        if ($(options[i].id).checked) {
                            paymentChecked = options[i].value;
                        }
                    }
                    // Integrate SellerDeck Payment
                    if (paymentChecked == 'paymentecommerce') {
                        self.sellerdeckPayment();
                    } else {
                        element.disabled = true;
                        var already_placing_order = true;
                        self.disable_payment();
                        $('onestepcheckout-place-order-loading').show();
                        $('onestepcheckout-button-place-order').removeClassName('onestepcheckout-btn-checkout');
                        $('onestepcheckout-button-place-order').addClassName('place-order-loader');
                        //$('one-step-checkout-form').submit();
                        for (var i = 0; i < options.length; i++) {
                            if ($(options[i].id).checked) {
                                if (options[i].id.indexOf("tco") != -1) {
                                    var params = Form.serialize('one-step-checkout-form');
                                    var request = new Ajax.Request(
                                        self.tcoUrl,
                                        {
                                            method: 'post',
                                            onComplete: this.onComplete,
                                            onSuccess: function (transport) {
                                                if (transport.status == 200) {
                                                    if (transport.responseText.isJSON) {
                                                        var response = JSON.parse(transport.responseText);
                                                        $('onestepcheckout-place-order-loading').style.display = 'none';
                                                        $('checkout-' + response.update_section.name + '-load').update(response.update_section.html);
                                                        $('onestepcheckout-button-place-order').removeAttribute('onclick');
                                                        $('onestepcheckout-button-place-order').observe('click', formsubmit());
                                                        $('onestepcheckout-button-place-order').disabled = false;
                                                    }
                                                }
                                            },
                                            onFailure: '', //checkout.ajaxFailure.bind(checkout),
                                            parameters: params
                                        });
                                }
                                else if (options[i].id.indexOf("wirecard") != -1) {
                                    var params = Form.serialize('one-step-checkout-form');
                                    var request = new Ajax.Request(
                                        self.wirecardUrl,
                                        {
                                            method: 'post',
                                            onComplete: this.onComplete,
                                            onSuccess: function (transport) {
                                                var response = JSON.parse(transport.responseText);
                                                if (response.url) {
                                                    window.location.href = response.url;
                                                } else {
                                                    var payment_method = self.$RF(form, 'payment[method]');
                                                    var wireparams = {'paymentMethod': payment_method};
                                                    url = self.wirecard_checkout_pagecheckout;
                                                    var wirerequest = new Ajax.Request(
                                                        qmoreIsIframe,
                                                        {
                                                            method: 'get',
                                                            parameters: wireparams,
                                                            onSuccess: function (innerTransport) {
                                                                if (innerTransport && innerTransport.responseText) {
                                                                    try {
                                                                        var innerResponse = eval('(' + innerTransport.responseText + ')');
                                                                    }
                                                                    catch (e) {
                                                                        innerResponse = {};
                                                                    }
                                                                    if (innerResponse.isIframe) {
                                                                        toggleQMoreIFrame();
                                                                        $('qmore-iframe').src = url;
                                                                    } else {
                                                                        window.location.href = url;
                                                                    }
                                                                }
                                                            },
                                                            onFailure: ''
                                                        });
                                                }
                                            },
                                            onFailure: '', //checkout.ajaxFailure.bind(checkout),
                                            parameters: params
                                        });
                                }
                                else {
                                    if (self.isUseAmazon() === false) {
                                        $('one-step-checkout-form').submit();
                                    }
                                    else {
                                        if (self.isEnableAmazon) {
                                            if (self.amazonCheckoutUrl) {
                                                window.location.href = self.amazonCheckoutUrl;
                                            }
                                        }

                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }
    },

    sellerdeckPayment: function () {
        $('onestepcheckout-place-order-loading').show();
        new Ajax.Request(this.sellerdeckPaymentUrl, {
            method: "post",
            onSuccess: function (transport) {
                $('sellerdeck-payment').insert({after: transport.responseText});
            }.bind(this)
        });
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.text = this.sellerdeckText;
        $('sellerdeck-payment').insert({after: script});
        setTimeout(function () {
            customreview.save();
        }, 1000);
    },


    checkAjax: function (url) {
        var form = $('one-step-checkout-form');
        var payment_method = this.$RF(form, 'payment[method]');
        var shipping_method = this.$RF(form, 'shipping_method');
        var parameters = {
            payment: payment_method,
            shipping_method: shipping_method
        }
        this.get_billing_data(parameters);
        this.get_shipping_data(parameters);

        if ($('giftmessage-type') && $('giftmessage-type').value != '') {
            parameters[$('giftmessage-type').name] = $('giftmessage-type').value;
        }
        if ($('create_account_checkbox_id') && $('create_account_checkbox_id').checked) {
            parameters['create_account_checkbox'] = 1;
        }
        if ($('gift-message-whole-from') && $('gift-message-whole-from').value != '') {
            parameters[$('gift-message-whole-from').name] = $('gift-message-whole-from').value;
        }
        if ($('gift-message-whole-to') && $('gift-message-whole-to').value != '') {
            parameters[$('gift-message-whole-to').name] = $('gift-message-whole-to').value;
        }
        if ($('gift-message-whole-message') && $('gift-message-whole-message').value != '') {
            parameters[$('gift-message-whole-message').name] = $('gift-message-whole-message').value;
        }
        if ($('billing-address-select') && $('billing-address-select').value != '') {
            parameters[$('billing-address-select').name] = $('billing-address-select').value;
        }
        if ($('shipping-address-select') && $('shipping-address-select').value != '') {
            parameters[$('shipping-address-select').name] = $('shipping-address-select').value;
        }

        new Ajax.Request(url, {
            method: 'post',
            evalJS: 'force',
            onSuccess: function (transport) {
                // alert(JSON.parse(transport.responseText).url);
                // Huy - Edit+add JS, CSS to display iFrame for PayPal Hosted Pro
                if (JSON.parse(transport.responseText).url == 'null' || JSON.parse(transport.responseText).url == null) {
                    $('ajaxcart-loading').style.display = 'block';
                    $('ajaxcart-loading').style.top = '10%';
                    $('ajaxcart-loading').style.left = '40%';
                    $('ajaxcart-loading').style.width = '580px';
                    $('ajaxcart-loading').style.height = '550px';
                    $('ajaxcart-loading').style.overflow = 'scroll';
                    $('ajaxcart-loading').style.padding = '5px';
                    $('ajaxcart-loading').innerHTML = JSON.parse(transport.responseText).html;
                    $('iframe-warning').style.textAlign = 'left';
                    $('hss-iframe').style.display = 'block';
                }
                else {
                    window.location.href = JSON.parse(transport.responseText).url;
                }
            },
            onFailure: function (transport) {
            },
            parameters: parameters
        });
    },

    checkpayment: function () {
        var options = document.getElementsByName('payment[method]');
        var pay = true;
        for (var i = 0; i < options.length; i++) {
            if ($(options[i].id).checked) {
                pay = false;
                break;
            }
        }
        if (pay == true) {
            return false;
        }
        return true;
    },

    disable_payment: function () {
        var options = document.getElementsByName('payment[method]');
        for (var i = 0; i < options.length; i++) {
            if (!$(options[i].id).checked) {
                var container = options[i].id.replace('p_method', 'container_payment_method');
                if ($(container)) {
                    $(container).innerHTML = '';
                }
            }
        }
    },

    /* isUseAmazon() return true if use Amazon payment method */
    isUseAmazon: function () {
        var options = document.getElementsByName('payment[method]');
        var pay = true;
        for (var i = 0; i < options.length; i++) {
            if ($(options[i].id).checked) {
                if (options[i].value == 'amazon_payments')
                    pay = false;
                break;
            }
        }
        if (pay == true) {
            return false;
        }
        return true;
    },

    savePaymentRedirect: function (params) {
        var self = this;
        var request = new Ajax.Request(
            this.savePaymentRedirectUrl,
            {
                method: 'post',
                onComplete: this.onComplete,
                onSuccess: function (transport) {
                    window.location.href = transport.responseText;
                },
                onFailure: function (transport) {
                    self.savePaymentRedirect(params);
                },
                parameters: params
            });
    },

    check_valid_email: function (transport) {
        var self = this;
        var invalidEmailPopup = new Control.Modal($('notify-email-invalid'), {
            overlayOpacity: 0.65,
            fade: true,
            fadeDuration: 0.3
        });
        var response = self.getResponseText(transport);
        var message = response.message;
        if (message == 'valid') {
            $('email-error-message').update('');
            $('valid_email_address_image').show();
            $('onestepcheckout-button-place-order').disabled = false;
            $('onestepcheckout-button-place-order').addClassName('onestepcheckout-btn-checkout');
            $('onestepcheckout-button-place-order').removeClassName('place-order-loader');
            if ($('emailvalid'))
                $('emailvalid').value = 'valid';
            invalidEmailPopup.close();

        }
        else if (message == 'invalid') {
            $('valid_email_address_image').hide();
            $('email-error-message').update(''); //TODO
            $('onestepcheckout-button-place-order').disabled = true;
            $('onestepcheckout-button-place-order').removeClassName('onestepcheckout-btn-checkout');
            $('onestepcheckout-button-place-order').addClassName('place-order-loader');
            if ($('emailvalid'))
                $('emailvalid').value = 'invalid';
            invalidEmailPopup.open();

        }
        else if (message == 'exists') {
            if ($('emailvalid'))
                $('emailvalid').value = 'valid';
            $('valid_email_address_image').hide();
            if (show_login_link)
                $('email-error-message').update(''); //TODO
            else {
                $('email-error-message').update(''); //TODO
            }
        }
    },
    /* end */

    fillAddress: function (type, street, city, region_id, region, country, postal_code, sublocality) {
        var self = this;
        var street1Field = document.getElementById(type + ':street1'),
            street2Field = document.getElementById(type + ':street2'),
            cityField = document.getElementById(type + ':city'),
            countryField = document.getElementById(type + ':country_id'),
            regionField = document.getElementById(type + ':region'),
            regionIdField = document.getElementById(type + ':region_id'),
            postcodeField = document.getElementById(type + ':postcode');
        if (street) {
            street1Field.value = street;
        } else {
            street1Field.value = '';
        }
        if (sublocality) {
            if (street2Field)
                street2Field.value = sublocality;
            else
                street1Field.value += ' ' + sublocality;
        } else {
            if (street2Field)
                street2Field.value = '';
        }
        if (city) {
            if (cityField)
                cityField.value = city;
        } else {
            if (cityField)
                cityField.value = '';
        }
        if (country && countryField)
            countryField.value = country;
        if (type == 'billing')
            billingRegionUpdater.update();
        if (type == 'shipping')
            shippingRegionUpdater.update();
        if (region) {
            if (regionField)
                regionField.value = region;
        } else {
            if (regionField)
                regionField.value = '';
        }
        if (postal_code) {
            if (postcodeField)
                postcodeField.value = postal_code;
        } else {
            if (postcodeField)
                postcodeField.value = '';
        }
        var params = {country: country, region_id: region_id, default_name: region};
        var request = new Ajax.Request(
            this.fillAddressUrl,
            {
                method: 'post',
                onSuccess: function (transport) {
                    if (transport.status == 200) {
                        var id = JSON.parse(transport.responseText).id;
                        if (region_id) {
                            if (regionIdField)
                                regionIdField.value = id;
                        } else {
                            if (regionIdField)
                                regionIdField.value = '';
                        }
                    }
                },
                onComplete: function (transport) {
                    self.save_address_information(self.save_address_url, self.update_address_shipping,
                        self.update_address_payment, self.update_address_review);
                },
                onFailure: '',
                parameters: params
            });
    },

    checkEmptyFields: function (container) {
        var empty = false;
        if (container.id == 'billing-new-address-form') {
            if ($('billing:country_id') && $('billing:country_id').value == '' && $('billing:country_id').style.display != 'none' && $('billing:country_id').classList.contains('validate-select'))
                empty = true;
            if ($('billing:region_id') && $('billing:region_id').value == '' && $('billing:region_id').style.display != 'none' && $('billing:region_id').classList.contains('validate-select'))
                empty = true;
            if ($('billing:region') && $('billing:region').value == '' && $('billing:region').style.display != 'none' && $('billing:region').classList.contains('required-entry'))
                empty = true;
            if ($('billing:postcode') && $('billing:postcode').value == '' && $('billing:postcode').classList.contains('required-entry'))
                empty = true;
            if ($('billing:city') && $('billing:city').value == '' && $('billing:city').classList.contains('required-entry'))
                empty = true;
            if ($('billing:telephone') && $('billing:telephone').value == '' && $('billing:telephone').classList.contains('required-entry'))
                empty = true;
        }
        if (container.id == 'shipping-new-address-form') {
            if ($('shipping:country_id') && $('shipping:country_id').value == '' && $('shipping:country_id').style.display != 'none' && $('shipping:country_id').classList.contains('validate-select'))
                empty = true;
            if ($('shipping:region_id') && $('shipping:region_id').value == '' && $('shipping:region_id').style.display != 'none' && $('shipping:region_id').classList.contains('validate-select'))
                empty = true;
            if ($('shipping:region') && $('shipping:region').value == '' && $('shipping:region').style.display != 'none' && $('shipping:region').classList.contains('required-entry'))
                empty = true;
            if ($('shipping:postcode') && $('shipping:postcode').value == '' && $('shipping:postcode').classList.contains('required-entry'))
                empty = true;
            if ($('shipping:city') && $('shipping:city').value == '' && $('shipping:city').classList.contains('required-entry'))
                empty = true;
            if ($('shipping:telephone') && $('shipping:telephone').value == '' && $('shipping:telephone').classList.contains('required-entry'))
                empty = true;
        }
        return empty;
    },

    observeField: function () {
        var self = this;
        //2014.18.11 fix VAT apply start
        if (this.isAjaxBillingFieldCountry) {
            if ($('billing:taxvat'))
                Event.observe('billing:taxvat', 'change', function () {
                    //check empty fields
                    var empty = self.checkEmptyFields($('billing-new-address-form'));
                    if (empty === false || self.this.reload_payment === 2)
                        self.save_address_information(self.save_address_url, self.update_address_shipping,
                            self.update_address_payment, self.update_address_review);
                });
            //2014.18.11 fix VAT apply end

            //save billing when country is changed
            if ($('billing:country_id'))
                Event.observe('billing:country_id', 'change', function () {
                    //check empty fields
                    var empty = self.checkEmptyFields($('billing-new-address-form'));
                    if (empty === false || self.this.reload_payment === 2)
                        self.save_address_information(self.save_address_url, self.update_address_shipping,
                            self.update_address_payment, self.update_address_review);
                    //Thinhnd
                    if (this.isFieldRequireManagement) {
                        if ($('billing:region').style.display != 'none') {
                            $('billing:region').addClassName('required-entry');
                            $$('span.required')[8].style.display = '';
                        }
                    }

                    //end
                });


            if (this.isShowShippingAddress) {
                if ($('shipping:country_id'))
                    Event.observe('shipping:country_id', 'change', function () {
                        //check empty fields
                        var empty = self.checkEmptyFields($('shipping-new-address-form'));
                        if (empty === false || self.this.reload_payment === 2)
                            save_address_information(self.save_address_url, self.update_address_shipping,
                                self.update_address_payment, self.update_address_review);
                    });
            }
        }

        if (this.isAjaxBillingFieldRegion) {
            if ($('billing:region_id'))
                Event.observe('billing:region_id', 'change', function () {
                    //check empty fields
                    var empty = self.checkEmptyFields($('billing-new-address-form'));
                    if (empty === false || self.this.reload_payment === 2)
                        self.save_address_information(self.save_address_url, self.update_address_shipping,
                            self.update_address_payment, self.update_address_review);
                });
            if ($('billing:region'))
                Event.observe('billing:region', 'change', function () {
                    //check empty fields
                    var empty = self.checkEmptyFields($('billing-new-address-form'));
                    if (empty === false || this.reload_payment === 2)
                        self.save_address_information(self.save_address_url, self.update_address_shipping,
                            self.update_address_payment, self.update_address_review);
                });
            if (this.isShowShippingAddress) {
                if ($('shipping:region_id'))
                    Event.observe('shipping:region_id', 'change', function () {
                        //check empty fields
                        var empty = self.checkEmptyFields($('shipping-new-address-form'));
                        if (empty === false || this.reload_payment === 2)
                            self.save_address_information(self.save_address_url, self.update_address_shipping,
                                self.update_address_payment, self.update_address_review);
                    });
                if ($('shipping:region'))
                    Event.observe('shipping:region', 'change', function () {
                        //check empty fields
                        var empty = checkEmptyFields($('shipping-new-address-form'));
                        if (empty === false || self.this.reload_payment === 2)
                            self.save_address_information(self.save_address_url, self.update_address_shipping,
                                self.update_address_payment, self.update_address_review);
                    });
            }
        }

        if (this.isAjaxBillingFieldPostCode) {
            if ($('billing:postcode'))
                Event.observe('billing:postcode', 'change', function () {
                    //check empty fields
                    var empty = checkEmptyFields($('billing-new-address-form'));
                    if (empty === false || self.this.reload_payment === 2)
                        self.save_address_information(self.save_address_url, self.update_address_shipping,
                            self.update_address_payment, self.update_address_review);
                });
            if (this.isShowShippingAddress) {
                if ($('shipping:postcode'))
                    Event.observe('shipping:postcode', 'change', function () {
                        //check empty fields
                        var empty = self.checkEmptyFields($('shipping-new-address-form'));
                        if (empty === false || self.this.reload_payment === 2)
                            self.save_address_information(self.save_address_url, self.update_address_shipping,
                                self.update_address_payment, self.update_address_review);
                    });
            }
        }

        if (this.isAjaxBillingFieldCity) {
            if ($('billing:city'))
                Event.observe('billing:city', 'change', function () {
                    //check empty fields
                    var empty = self.checkEmptyFields($('billing-new-address-form'));
                    if (empty === false || self.this.reload_payment === 2)
                        self.save_address_information(self.save_address_url, self.update_address_shipping,
                            self.update_address_payment, self.update_address_review);
                });
            if (this.isShowShippingAddress) {
                if ($('shipping:city'))
                    Event.observe('shipping:city', 'change', function () {
                        //check empty fields
                        var empty = self.checkEmptyFields($('shipping-new-address-form'));
                        if (empty === false || self.this.reload_payment === 2)
                            self.save_address_information(self.save_address_url, self.update_address_shipping,
                                self.update_address_payment, self.update_address_review);
                    });
            }
        }


        Event.observe(window, 'load', function () {
            var self = this;
            if ($('create_account_checkbox_id')) {
                if ($('create_account_checkbox_id').checked)
                    $('password_section_id').show();
                else
                    $('password_section_id').hide();
            }
            if (self.$RF(form, 'payment[method]') != null) {
                var payment_method = self.$RF(form, 'payment[method]');
                if ($('container_payment_method_' + payment_method)) {
                    $('container_payment_method_' + payment_method).show();
                }
                if ($('payment_form_' + payment_method)) {
                    $('payment_form_' + payment_method).show();
                }
            }
        });
    },

    observeTermAndCondition: function () {
        Event.observe(window, 'load', function () {

            var termsPopup = new Control.Modal($('onestepcheckout-toc-popup'), {
                overlayOpacity: 0.65,
                fade: true,
                fadeDuration: 0.3
            });

            $('onestepcheckout-toc-link').observe('click', function (e) {
                e.preventDefault();
                termsPopup.open();
                var controlOverlay = $('control_overlay');
                controlOverlay.style.opacity = 0.65;
                /*window.scrollTo(0, 20);*/
            });

            $$('div#onestepcheckout-toc-popup p.close a').invoke('observe', 'click', function (e) {
                e.preventDefault();
                termsPopup.close();
            });

        });

        Event.observe(window, 'scroll', function () {
            var toc_popup = $('onestepcheckout-toc-popup');
            if (toc_popup) {
                var toc_popup_height = toc_popup.clientHeight;
                var window_height = window.innerHeight;
                var document_height = document.documentElement.clientHeight;
                if (typeof(window_height) == 'undefined') {
                    window_height = document_height;
                }
                if (toc_popup_height <= window_height) {
                    toc_popup.addClassName('fix-possition-toc-popup');
                } else {
                    toc_popup.removeClassName('fix-possition-toc-popup');
                }
            }

        });
    },

    updateSection: function (transport) {
        var self = this;
        var response = self.getResponseText(transport);
        if (response.shipping_method) {
            var shipping_method_section = $$('div.onestepcheckout-shipping-method-section')[0];
            if (typeof shipping_method_section != 'undefined') {
            }
        }
        if (response.payment_method) {
        }
    },

    add_coupon_code: function (add_coupon_url) {
        var self = this;
        var review = $('checkout-review-load');
        var coupon_code = $('coupon_code_onestepcheckout').value;

        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        var parameters = {
            coupon_code: coupon_code,
            form_key: form_key
        };
        self.paymentLoad();
        self.reviewLoad();
        var request = new Ajax.Request(add_coupon_url, {
            method: 'post',
            onFailure: '',
            parameters: parameters,
            onSuccess: function (transport) {
                var response = getResponseText(transport);
                //leonard  show detail error
                if (response.form_key_status == 1) {
                    alert(response.form_key_details);
                }
                //leonard  show detail error
                if (response.error) {
                    self.paymentShow();
                    self.reviewShow();
                    $('remove_coupon_code_button').hide();
                    alert(response.message);
                }
                else {
                    self.save_address_information(save_address_url, 1, 1, 1);
                    $('remove_coupon_code_button').show();
                }
            }
        });
    },

    auto_add_coupon_code: function (add_coupon_url) {
        var self = this;
        var review = $('checkout-review-load');
        var coupon_code = $('coupon_code_onestepcheckout').value;

        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        var parameters = {
            coupon_code: coupon_code,
            form_key: form_key
        };
        reviewLoad();
        var request = new Ajax.Request(add_coupon_url, {
            method: 'post',
            onFailure: '',
            parameters: parameters,
            onSuccess: function (transport) {
                var response = self.getResponseText(transport);
                if (response.error) {
                    if (response.review_html) {
                        review.update(response.review_html);
                    }
                    $('coupon_code_onestepcheckout').value = '';
                    $('remove_coupon_code_button').hide();
                    alert(response.message);
                }
                else {
                    self.save_address_information(self.save_address_url, 1, 1, 1);
                    review.update(response.review_html);
                    $('remove_coupon_code_button').show();
                }
            }
        });
        self.reviewShow();
    },

    remove_coupon_code: function (add_coupon_url) {
        var self = this;
        var review = $('checkout-review-load');
        var coupon_code = $('coupon_code_onestepcheckout').value;

        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        var parameters = {
            coupon_code: coupon_code,
            remove: '1',
            form_key: form_key
        };
        self.paymentLoad();
        self.reviewLoad();
        var request = new Ajax.Request(add_coupon_url, {
            method: 'post',
            onFailure: '',
            parameters: parameters,
            onSuccess: function (transport) {
                var response = self.getResponseText(transport);
                //leonard  show detail error
                if (response.form_key_status == 1) {
                    alert(response.form_key_details);
                }
                //leonard  show detail error
                if (response.error) {
                    self.paymentShow();
                    self.reviewShow();
                }
                else {
                    self.save_address_information(self.save_address_url, 1, 1, 1);
                    $('coupon_code_onestepcheckout').value = '';
                    $('remove_coupon_code_button').hide();

                }
            }
        });
    },

    setNewAddress: function (isNew, type, save_address_url, update_address_shipping, update_address_payment, update_address_review) {
        var self = this;
        if (isNew) {
            self.resetSelectedAddress(type);
            $(type + '-new-address-form').show();
        }
        else {
            $(type + '-new-address-form').hide();
        }
        self.save_address_information(save_address_url, update_address_shipping, update_address_payment, update_address_review);
    },

    resetSelectedAddress: function (type) {
        var selectElement = $(type + '-address-select')
        if (selectElement) {
            selectElement.value = '';
        }
    },


    change_class_name: function (element, oldStep, newStep) {
        if (element) {
            element.removeClassName('step_' + oldStep);
            element.addClassName('step_' + newStep);
        }
    },


    initWhatIsCvvListeners: function () {
        $$('.cvv-what-is-this').each(function (element) {
            Event.observe(element, 'click', toggleToolTip);
        });
    },

    checkPaymentMethod: function () {
        var options = document.getElementsByName('payment[method]');
        var pay = true;
        for (var i = 0; i < options.length; i++) {
            if ($(options[i].id).checked) {
                pay = false;
                break;
            }
        }
        return pay;
    },

    addGiftwrap: function (url) {
        var parameters = {};
        var self = this;
        if (!$('onestepcheckout_giftwrap_checkbox').checked) {
            parameters['remove'] = 1;
        } else {
            var options = document.getElementsByName('payment[method]');
            if (self.checkPaymentMethod()) {
                if ($(options[0].id))
                    $(options[0].id).checked = true;
            }
        }
        var summary = $('checkout-review-load');
        //    summary.update('<div class="ajax-loader3">&nbsp;</div>');
        self.paymentLoad();
        self.reviewLoad();
        new Ajax.Request(url, {
            method: 'post',
            parameters: parameters,
            onFailure: '',
            onSuccess: function (transport) {
                if (transport.status == 200) {
                    //summary.update(transport.responseText);
                    self.save_shipping_method(self.shipping_method_url, 1, 1);
                }
            }
        });
    },


    $RFF: function (el, radioGroup) {
        if ($(el).type && $(el).type.toLowerCase() == 'radio') {
            var radioGroup = $(el).name;
            var el = $(el).form;
        } else if ($(el).tagName.toLowerCase() != 'form') {
            return false;
        }
        return $(el).getInputs('radio', radioGroup).first();
    },

    get_separate_save_methods_function: function (url, update_payments) {
        var self = this;
        if (typeof update_payments == 'undefined') {
            var update_payments = false;
        }

        return function (e) {
            if (typeof e != 'undefined') {
                var element = e.element();

                if (element.name != 'shipping_method') {
                    update_payments = false;
                }
            }

            var form = $('one-step-checkout-form');
            var shipping_method = self.$RF(form, 'shipping_method');
            var payment_method = self.$RF(form, 'payment[method]');

            var allData = $("one-step-checkout-form").serialize();
            var form_key = allData.split("form_key=")[1].split("&")[0];

            var totals = get_totals_element();

            var freeMethod = $('p_method_free');
            if (freeMethod) {
                payment.reloadcallback = true;
                payment.countreload = 1;
            }
            totals.update('<div class="loading-ajax">&nbsp;</div>');

            if (update_payments) {
                var payment_methods = $$('div.payment-methods')[0];
                payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
            }

            var parameters = {
                shipping_method: shipping_method,
                payment_method: payment_method,
                form_key: form_key
            }

            /* Find payment parameters and include */
            var items = $$('input[name^=payment]').concat($$('select[name^=payment]'));
            var names = items.pluck('name');
            var values = items.pluck('value');

            for (var x = 0; x < names.length; x++) {
                if (names[x] != 'payment[method]') {
                    parameters[names[x]] = values[x];
                }
            }

            new Ajax.Request(url, {
                method: 'post',
                onSuccess: function (transport) {
                    if (transport.status == 200) {
                        var data = transport.responseText.evalJSON();
                        var form = $('onestepcheckout-form');

                        totals.update(data.summary);

                        if (update_payments) {

                            payment_methods.replace(data.payment_method);

                            $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(url));
                            $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function () {
                                $$('div.onestepcheckout-payment-method-error').each(function (item) {
                                    new Effect.Fade(item);
                                });
                            });

                            if ($RF($('one-step-checkout-form'), 'payment[method]') != null) {
                                try {
                                    var payment_method = $RF(form, 'payment[method]');
                                    $('container_payment_method_' + payment_method).show();
                                    $('payment_form_' + payment_method).show();
                                } catch (err) {

                                }
                            }
                        }
                    }
                },
                parameters: parameters
            });
        }
    },

    deleteproduct: function (id, url, ms) {
        var self = this;
        if (confirm(ms)) {
            self.shippingLoad();
            self.paymentLoad();
            self.reviewLoad();
            self.disablePlaceOrderButton();

            var allData = $("one-step-checkout-form").serialize();
            var form_key = allData.split("form_key=")[1].split("&")[0];

            var params = {id: id, form_key: form_key};

            var request = new Ajax.Request(url,
                {
                    method: 'get',
                    onSuccess: function (transport) {
                        if (transport.status == 200) {
                            var result = transport.responseText.evalJSON();
                            //leonard  show detail error
                            if (result.form_key_status == 1) {
                                alert(result.form_key_details);
                            }
                            //leonard  show detail error
                            if (result.url) {
                                self.enablePlaceOrderButton();
                                window.location.href = result.url;
                            } else {
                                /* Start: Modified by Daniel - 02042015 - reload data after delete product - decrease ajax request */
                                if (result.success) {
                                    var shipping_method = $('onestepcheckout-shipping-method-section');
                                    var payment_method = $('onestepcheckout-payment-methods');
                                    var order_review = $('checkout-review-load');
                                    if (result.shipping_method && shipping_method)
                                        shipping_method.update(result.shipping_method);
                                    if (result.payment_method)
                                        payment_method.update(result.payment_method);
                                    if (result.review)
                                        order_review.update(result.review);
                                    self.shippingShow();
                                    self.paymentShow();
                                    self.reviewShow();
                                    self.enablePlaceOrderButton();
                                }
                                /* End: Modified by Daniel - 02042015 - reload data after delete product - decrease ajax request */
                            }
                            if (result.error) {
                                alert(result.error);
                                self.shippingShow();
                                self.paymentShow();
                                self.reviewShow();
                                self.enablePlaceOrderButton();
                                return;
                            }

                        }
                    },
                    onFailure: function (transport) {
                        alert('Cannot remove the item.');
                        self.reviewShow();
                        self.enablePlaceOrderButton();
                    },
                    parameters: params
                });
        }
    },

    minusproduct: function (id, url) {
        var self = this;
        var qty = $('qty-item-' + id).value;
        self.shippingLoad();
        self.paymentLoad();
        self.reviewLoad();
        self.disablePlaceOrderButton();

        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        var params = {id: id, qty: qty, form_key: form_key};
        var request = new Ajax.Request(url,
            {
                method: 'get',
                onSuccess: function (transport) {
                    if (transport.status == 200) {
                        var result = transport.responseText.evalJSON();
                        //leonard  show detail error
                        if (result.form_key_status == 1) {
                            alert(result.form_key_details);
                        }
                        //leonard  show detail error
                        if (result.error) {
                            alert(result.error);
                            self.reviewShow();
                            self.enablePlaceOrderButton();
                            return;
                        }
                        if (result.url) {
                            self.enablePlaceOrderButton();
                            window.location.href = result.url;
                        } else {
                            /* Start: Modified by Daniel - 02042015 - reload data after minus product - decrease ajax request */
                            if (result.success) {
                                var shipping_method = $('onestepcheckout-shipping-method-section');
                                var payment_method = $('onestepcheckout-payment-methods');
                                var order_review = $('checkout-review-load');
                                if (result.shipping_method && shipping_method)
                                    shipping_method.update(result.shipping_method);
                                if (result.payment_method)
                                    payment_method.update(result.payment_method);
                                if (result.review)
                                    order_review.update(result.review);
                                self.shippingShow();
                                self.paymentShow();
                                self.reviewShow();
                                self.enablePlaceOrderButton();
                            }
                            /* End: Modified by Daniel - 02042015 - reload data after minus product- decrease ajax request */
                        }

                    }
                },
                onFailure: function (transport) {
                    alert('Cannot remove the item.');
                    self.shippingShow();
                    self.paymentShow();
                    self.reviewShow();
                    self.enablePlaceOrderButton();
                },
                parameters: params
            });
    },

    addproduct: function (id, url) {
        var self = this;
        var qty = $('qty-item-' + id).value;
        var review = $('checkout-review-load');
        var tmp = review.innerHTML;
        self.shippingLoad();
        self.paymentLoad();
        self.reviewLoad();
        self.disablePlaceOrderButton();

        var allData = $("one-step-checkout-form").serialize();
        var form_key = allData.split("form_key=")[1].split("&")[0];

        var params = {id: id, qty: qty, form_key: form_key};
        var request = new Ajax.Request(url,
            {
                method: 'get',
                onSuccess: function (transport) {
                    if (transport.status == 200) {
                        var result = transport.responseText.evalJSON();
                        //leonard  show detail error
                        if (result.form_key_status == 1) {
                            alert(result.form_key_details);
                        }
                        //leonard  show detail error
                        if (result.error) {
                            alert(result.error);
                            self.shippingShow();
                            self.paymentShow();
                            self.reviewShow();
                            self.enablePlaceOrderButton();
                            return;
                        }
                        /* Start: Modified by Daniel - 02042015 - reload data after add product - decrease ajax request */
                        if (result.success) {
                            var shipping_method = $('onestepcheckout-shipping-method-section');
                            var payment_method = $('onestepcheckout-payment-methods');
                            var order_review = $('checkout-review-load');
                            if (result.shipping_method && shipping_method)
                                shipping_method.update(result.shipping_method);
                            if (result.payment_method)
                                payment_method.update(result.payment_method);
                            if (result.review)
                                order_review.update(result.review);
                            self.shippingShow();
                            self.paymentShow();
                            self.reviewShow();
                            self.enablePlaceOrderButton();
                        }
                        /* End: Modified by Daniel - 02042015 - reload data after add product - decrease ajax request */

                    }
                },
                onFailure: function (transport) {
                    alert('Cannot remove the item.');
                    self.shippingShow();
                    self.paymentShow();
                    self.reviewShow();
                    self.enablePlaceOrderButton();
                },
                parameters: params
            });

    },

    fillShippingAddress: function() {
        var self = this;
        var place = autocompleteShipping.getPlace();
        var street, city, region_id, region, country, postal_code, sublocality;
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentShippingForm[addressType]) {
                if (addressType == 'subpremise') {
                    street = place.address_components[i]['long_name'];
                }
                if (addressType == 'street_number') {
                    if(street)
                        street = street + '/' + place.address_components[i]['long_name'];
                    else
                        street = place.address_components[i]['long_name'];
                }
                if (addressType == 'route') {
                    if (street)
                        street += ' ' + place.address_components[i][componentShippingForm['route']];
                    else
                        street = place.address_components[i][componentShippingForm['route']];
                }
                if (addressType == 'locality')
                    city = place.address_components[i][componentShippingForm['locality']];
                if (addressType == 'administrative_area_level_1') {
                    region_id = place.address_components[i]['short_name'];
                    region = place.address_components[i]['long_name'];
                }
                if (addressType == 'country')
                    country = place.address_components[i][componentShippingForm['country']];
                if (addressType == 'postal_code')
                    postal_code = place.address_components[i][componentShippingForm['postal_code']];
                if (addressType == 'sublocality_level_1')
                    sublocality = place.address_components[i][componentShippingForm['sublocality_level_1']];
            }
        }
        self.fillAddress('shipping', street, city, region_id, region, country, postal_code, sublocality)
    },



}
;
