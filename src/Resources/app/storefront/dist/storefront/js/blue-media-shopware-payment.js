(window.webpackJsonp=window.webpackJsonp||[]).push([["blue-media-shopware-payment"],{VyeF:function(t,e,n){"use strict";n.r(e);var o=n("FGIj");function i(t){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function r(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function s(t,e){return!e||"object"!==i(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function u(t){return(u=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function l(t,e){return(l=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var c,h,y,f=function(t){function e(){return r(this,e),s(this,u(e).apply(this,arguments))}var n,o,i;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&l(t,e)}(e,t),n=e,(o=[{key:"init",value:function(){this.currentImage=0,this._getImages(),this._startRotation()}},{key:"_getImages",value:function(){this.images=this.el.querySelectorAll(this.options.imgSelector)}},{key:"_startRotation",value:function(){this.images.length<=1||this.interval||(this.interval=setInterval(this._switchImage.bind(this),this.options.interval))}},{key:"_switchImage",value:function(){this.currentImage>=this.images.length-1?this.currentImage=0:this.currentImage++,this._deactivateImages(),this._activateCurrentImage()}},{key:"_deactivateImages",value:function(){var t=this;this.images.forEach((function(e){e.classList.remove(t.options.activeClass)}))}},{key:"_activateCurrentImage",value:function(){this.images[this.currentImage].classList.add(this.options.activeClass)}}])&&a(n.prototype,o),i&&a(n,i),e}(o.a);y={imgSelector:"img",activeClass:"active",interval:3e3},(h="options")in(c=f)?Object.defineProperty(c,h,{value:y,enumerable:!0,configurable:!0,writable:!0}):c[h]=y;var p=function(){return Boolean(window.ApplePaySession&&ApplePaySession.canMakePayments())};function m(t){return(m="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function d(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function b(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function g(t,e){return!e||"object"!==m(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function v(t){return(v=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function w(t,e){return(w=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var S=function(t){function e(){return d(this,e),g(this,v(e).apply(this,arguments))}var n,o,i;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&w(t,e)}(e,t),n=e,(o=[{key:"init",value:function(){this.isSeparatedPayment=this.el.parentElement.matches(this.options.paymentMethodSelector),this.checkoutConfirmBtn=document.querySelector(this.options.checkoutConfirmBtnSelector),this.paymentMethod=this.el.closest(this.options.paymentMethodSelector),this.gatewayGroup=this.el.closest(this.options.gatewayGroupSelector),this.gateway=this.el.closest(this.options.gatewaySelector),this.gatewaysCollapse=this.gateway&&this.gateway.closest(this.options.collapseSelector),this.collapseBtn=document.querySelector("[data-collapse-checkout-confirm-methods]"),this.collapse=this.paymentMethod&&this.paymentMethod.closest(this.options.collapseSelector),this._handleGatewayVisibility(),this._handlePaymentVisibility()}},{key:"_handleGatewayVisibility",value:function(){this.isSeparatedPayment||p()||this.gateway&&(this.gateway.querySelector("input").checked&&this._disableBtn(this.checkoutConfirmBtn),this._removeNode(this.gateway),this.gatewayGroup&&this._isNodeEmpty(this.gatewaysCollapse)&&this._removeNode(this.gatewayGroup))}},{key:"_handlePaymentVisibility",value:function(){!1===this.isSeparatedPayment||p()||this.paymentMethod&&(this._removeNode(this.paymentMethod),this._handlePaymentCollapse(),0===document.querySelectorAll("".concat(this.options.paymentMethodSelector," input:checked")).length&&this._disableBtn(this.checkoutConfirmBtn))}},{key:"_handlePaymentCollapse",value:function(){this.options.handlePaymentCollapse&&this._isNodeEmpty(this.collapse)&&(this._removeNode(this.collapse),this._removeNode(this.collapseBtn))}},{key:"_removeNode",value:function(t){t.remove()}},{key:"_disableBtn",value:function(t){t.setAttribute("disabled","disabled")}},{key:"_isNodeEmpty",value:function(t){return t instanceof Node&&!t.textContent.trim()}}])&&b(n.prototype,o),i&&b(n,i),e}(o.a);function k(t){return(k="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function P(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function _(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function C(t,e){return!e||"object"!==k(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function O(t){return(O=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function j(t,e){return(j=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}!function(t,e,n){e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n}(S,"options",{paymentMethodSelector:".payment-method",gatewayGroupSelector:".blue-media-gateway-group",gatewaySelector:".blue-media-gateway",collapseSelector:".collapse",handlePaymentCollapse:!0,checkoutConfirmBtnSelector:"#confirmFormSubmit"});var E=function(t){function e(){return P(this,e),C(this,O(e).apply(this,arguments))}var n,o,i;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&j(t,e)}(e,t),n=e,(o=[{key:"init",value:function(){this._handleInput()}},{key:"_handleInput",value:function(){this.el.matches("input")&&(this.el.value=Number(p()))}}])&&_(n.prototype,o),i&&_(n,i),e}(o.a),I=n("k8s9"),B=n("477Q");function M(t){return(M="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function N(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function R(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function A(t,e){return!e||"object"!==M(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function q(t){return(q=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function D(t,e){return(D=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var T=function(t){function e(){return N(this,e),A(this,q(e).apply(this,arguments))}var n,o,i;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&D(t,e)}(e,t),n=e,(o=[{key:"init",value:function(){this._client=new I.a,this.form=this.el.closest("form"),this.button=this.el.querySelector(this.options.formSubmitButtonSelector),this.registerEvents()}},{key:"registerEvents",value:function(){var t=this;this.el.addEventListener("submit",(function(e){e.preventDefault(),t.cardOrder()}))}},{key:"cardOrder",value:function(){var t=this;new B.a(this.button).create(),this._client.post(window.router["payment.blue-payment.checkout.card"],new FormData(this.form),(function(e,n){n.status>=400&&window.location.reload();var o=JSON.parse(e);o.transactionContinueRedirect&&PayBmCheckout&&"function"==typeof PayBmCheckout.transactionStartByUrl?(t.registerPayBmEvents(o.finalRedirect,o.checkoutErrorUrl),document.body.classList.add("overflow-hidden"),PayBmCheckout.transactionStartByUrl(o.transactionContinueRedirect)):window.location.replace(o.finalRedirect)}))}},{key:"registerPayBmEvents",value:function(t,e){PayBmCheckout.transactionSuccess=function(){document.body.classList.remove("overflow-hidden"),window.location.replace(t)},PayBmCheckout.transactionDeclined=function(){document.body.classList.remove("overflow-hidden"),window.location.replace(e)},PayBmCheckout.transactionError=function(){document.body.classList.remove("overflow-hidden"),window.location.replace(e)}}}])&&R(n.prototype,o),i&&R(n,i),e}(o.a);function G(t){return(G="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function L(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function J(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function F(t,e){return!e||"object"!==G(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function V(t){return(V=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function x(t,e){return(x=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}!function(t,e,n){e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n}(T,"options",{formSubmitButtonSelector:"#confirmFormSubmit"});var H=function(t){function e(){return L(this,e),F(this,V(e).apply(this,arguments))}var n,o,i;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&x(t,e)}(e,t),n=e,(o=[{key:"init",value:function(){this.httpClient=new I.a,this.button=this.el.querySelector(this.options.formSubmitButtonSelector),this.googleClient=null,this.authJwt=null,this.merchantId=null,this.merchantOrigin=null,this.merchantName=null,this.gatewayMerchantId=null,this.allowedAuthMethods=null,this.allowedCardNetworks=null,this.setHandlers(),this.registerEvents()}},{key:"setHandlers",value:function(){this.submitHandler=this.handleSubmit.bind(this)}},{key:"registerEvents",value:function(){this.el.addEventListener("submit",this.submitHandler)}},{key:"handleSubmit",value:function(t){t.preventDefault(),this.googlePayOrder()}},{key:"googlePayOrder",value:function(){new B.a(this.button).create(),this.initGoogleClient()}},{key:"initGoogleClient",value:function(){if("undefined"!=typeof google&&void 0!==google.payments){this.googleClient=new google.payments.api.PaymentsClient({environment:this.options.environment});var t=JSON.stringify({_csrf_token:this.options.bmInitRequest.token});this.httpClient.post(this.options.bmInitRequest.path,t,this.initGoogleClientCallback.bind(this))}}},{key:"initGoogleClientCallback",value:function(t,e){var n=this;e.status>=400&&this.onErrorCallback("Can not collect GooglePay init data! Check configuration!");var o=JSON.parse(t);!0===o.error&&this.onErrorCallback("Can not collect GooglePay init data! Check configuration!"),this.authJwt=o.authJwt,this.merchantId=o.merchantId,this.merchantOrigin=o.merchantOrigin,this.merchantName=o.merchantName,this.allowedAuthMethods=o.allowedAuthMethods,this.allowedCardNetworks=o.allowedCardNetworks,this.gatewayMerchantId=o.gatewayMerchantId,this.googleClient.isReadyToPay(this.getIsReadyToPayRequestData()).then((function(t){t.result?(n.prefetchTransactionData(),n.createPayButton(),n.initiatePaymentWindow()):n.onErrorCallback(t)})).catch((function(t){n.onErrorCallback(t)}))}},{key:"prefetchTransactionData",value:function(){this.googleClient.prefetchPaymentData(this.getPaymentDataRequestData())}},{key:"createPayButton",value:function(){this.googleClient.createButton()}},{key:"initiatePaymentWindow",value:function(){var t=this;this.googleClient.loadPaymentData(this.getPaymentDataRequestData()).then((function(e){t.updatePaymentToken(e.paymentMethodData.tokenizationData.token)})).catch((function(e){t.onErrorCallback(e)})).finally((function(){t.el.removeEventListener("submit",t.submitHandler),t.el.submit()}))}},{key:"getIsReadyToPayRequestData",value:function(){var t=this.getPaymentDataRequestData();return delete t.merchantInfo,delete t.transactionInfo,delete t.shippingAddressRequired,t}},{key:"getPaymentDataRequestData",value:function(){return{apiVersion:2,apiVersionMinor:0,merchantInfo:{merchantId:this.merchantId,merchantOrigin:this.merchantOrigin,merchantName:this.merchantName,authJwt:this.authJwt},allowedPaymentMethods:[{type:"CARD",parameters:{allowedAuthMethods:this.allowedAuthMethods,allowedCardNetworks:this.allowedCardNetworks},tokenizationSpecification:{type:"PAYMENT_GATEWAY",parameters:{gateway:"bluemedia",gatewayMerchantId:this.gatewayMerchantId}}}],transactionInfo:{currencyCode:this.options.transactionCurrencyCode,countryCode:this.options.transactionCountryCode,totalPriceStatus:this.options.transactionStatus,totalPrice:String(this.options.transactionAmount)},shippingAddressRequired:this.options.requireShippingAddress}}},{key:"updatePaymentToken",value:function(t){this.el.querySelector(this.options.paymentTokenSelector).value=JSON.stringify(t)}},{key:"onErrorCallback",value:function(t){console.error(t)}}])&&J(n.prototype,o),i&&J(n,i),e}(o.a);!function(t,e,n){e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n}(H,"options",{formSubmitButtonSelector:"#confirmFormSubmit",paymentTokenSelector:"#blue-media-google-payment-token",environment:"",transactionCurrencyCode:"PLN",transactionCountryCode:"PL",transactionAmount:0,transactionStatus:"FINAL",requireShippingAddress:!1,bmInitRequest:{},blueMediaGooglePayPaymentOptions:""});var U=window.PluginManager;U.register("BlueMediaImageRotator",f,"[data-blue-media-image-rotator]"),U.register("BlueMediaApplePay",S,"[data-blue-media-apple-pay]"),U.register("BlueMediaApplePayValidationField",E,"[data-blue-media-apple-pay-validation-field]"),U.register("BlueMediaCardPayment",T,"[data-blue-media-card-payment]"),U.register("BlueMediaGooglePayPayment",H,"[data-blue-media-google-pay-payment]")}},[["VyeF","runtime","vendor-node","vendor-shared"]]]);