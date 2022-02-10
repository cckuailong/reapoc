;(function($, window, document) {
	var wprss = window.wprss = window.wprss || {};
	var licenseManager = wprss.licenseManager = wprss.licenseManager || {};

	$.extend(licenseManager, {
		/**
		@class 						LicenseManager
		@description 				This class provides a way to get add-on license data or de/activate licenses.
		@... 						The class' methods return jQuery Promises since the license data will be fetched
		@...						asynchronously. The Promise will resolve if the request was handled successfully,
		@...						and will otherwise be rejected. Attach .then()/.done()/.fail() handlers as required.
		*/
		namespace: 	'wprss.licenseManager',
		licenses: 	{},

		/**
		@function 	isValid 						Returns a Promise to return whether the license for a given addon is valid.
		@param 		{String}		addon			The abbr of the addon
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{Boolean} 		isValid 		TRUE when the license is valid.
		*/
		isValid: function(addon) {
			return this.getValidity(addon).then(function(validity) {
				return validity === 'valid';
			});
		},

		/**
		@function 	isInvalid 						Returns a Promise to return whether the license for a given addon is invalid.
		@param 		{String}		addon			The abbr of the addon
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{Boolean} 		isValid 		TRUE when the license is invalid.
		*/
		isInvalid: function(addon) {
			return this.getValidity(addon).then(function(validity) {
				return validity !== 'valid';
			});
		},

		/**
		@function 	getLicense 						Returns a Promise to return an object containing the full license information as provided by EDD.
		@param 		{String}		addon			The abbr of the addon
		@param 		{Boolean}		forceFetch 		If TRUE, bypass the cache.
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{Object} 		license 		The complete EDD license object.
		*/
		getLicense: function(addon, forceFetch) {
			var me = this;

			if (this.licenses[addon] === undefined || forceFetch === true) {
				// If the license hasn't been fetched before or we're forcing a fetch,
				// return a Promise that'll be fulfilled after the license is XHR fetched.
				return this._fetchLicense(addon).then(function(license) {
					// We got the license info, save it for later use.
					me.licenses[addon] = license;

					return me.licenses[addon];
				}, function(error) {
					console.log(error);
				});
			} else {
				// The license is cached so create a Deferred and immediately resolve it,
				// returning the (fulfilled) Promise.
				// When the caller attaches callbacks via .then(), the callbacks
				// will immediately fire with the data we're passing back.
				return $.Deferred().resolve(me.licenses[addon]).promise();
			}
		},

		/**
		@function 	getExpiry 						Returns a Promise to return the expiry date for an addon license.
		@param 		{String}		addon			The abbr of the addon
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{String} 		date    		Date in "YYYY-MM-DD HH:MM:SS" format.
		*/
		getExpiry: function(addon) {
			return this._getAttribute(addon, 'expires').then(function(expiry) {
				return expiry;
			});
		},

		/**
		@function 	getName							Returns a Promise to return the name an addon license is registered to.
		@param 		{String}		addon			The abbr of the addon
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{String} 		name 	 		Customer name
		*/
		getName: function(addon) {
			return this._getAttribute(addon, 'customer_name').then(function(name) {
				return name;
			});
		},

		/**
		@function 	getEmail 						Returns a Promise to return the email an addon license is registered to.
		@param 		{String}		addon			The abbr of the addon
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{String} 		email 	 		Customer email
		*/
		getEmail: function(addon) {
			return this._getAttribute(addon, 'customer_email').then(function(email) {
				return email;
			});
		},

		/**
		@function 	getValidity 					Returns a Promise to return the validity status string of an addon license.
		@param 		{String}		addon			The abbr of the addon
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{String} 		validity 		EDD license validity status.
		*/
		getValidity: function(addon) {
			return this._getAttribute(addon, 'license').then(function(validity) {
				return validity;
			});
		},

		/**
		@function 	activateLicense 				Activates a specified license key for a given addon.
		@param 		{String}		addon			The abbr of the addon
		@param 		{String}		license			The license key to activate
		@param 		{String}		nonce 			The security nonce
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{Object} 		response
		@...		{String} 		validity 		EDD license validity status.
		@...		{String}		addon 			The addon the license was activated for.
		@...		{String}		html			The HTML markup of a deactivation button and info div.
		*/
		activateLicense: function(addon, license, nonce) {
			return this._manageLicense(addon, 'activate', license, nonce);
		},

		/**
		@function 	deactivateLicense 				Deactivates a specified license key for a given addon.
		@param 		{String}		addon			The abbr of the addon
		@param 		{String}		license			The license key to deactivate
		@param 		{String}		nonce 			The security nonce
		@returns 	{Object}		promise 		A jQuery Promise
		@promise	{Object} 		response
		@...		{String} 		validity 		EDD license validity status.
		@...		{String}		addon 			The addon the license was activated for.
		@...		{String}		html			The HTML markup of a deactivation button and info div.
		*/
		deactivateLicense: function(addon, license, nonce) {
			return this._manageLicense(addon, 'deactivate', license, nonce);
		},

		/**
		@function 	_getAttribute 					Gets a specified attribute from a specified addon's license.
		@private
		@param 		{String}		addon			The abbr of the addon
		@param 		{String}		attr			The license attribute to fetch
		@returns	{Object} 		promise 	 	A jQuery Promise
		@promise 	{String}		value 			The attr's value
		*/
		_getAttribute: function(addon, attr) {
			return this.getLicense(addon).then(function(license) {
				return license[attr];
			});
		},

		/**
		@function 	_fetchLicense 					Fetches license data via AJAX call to WordPress.
		@private
		@param 		{String}		addon			The abbr of the addon
		@returns	{Object} 		promise 	 	A jQuery Promise
		@promise 	{Object}		license			The license object, if no errors.
		*/
		_fetchLicense: function(addon) {
			return $.ajax({
				url: ajaxurl,
				dataType: 'json',
				data: {
					action: 'wprss_ajax_fetch_license',
					addon: addon
				}
			}).then(function(response, textStatus, jqXHR) {
				if (response.error !== undefined) {
					console.log('Error: ', response.error);
					return $.Deferred().reject(jqXHR, response, 'Not YES').promise();
				}

				return response;
			});
		},

		/**
		@function 	_manageLicense 					De/activates a license via AJAX call to WordPress.
		@private
		@param 		{String}		addon			The abbr of the addon
		@param 		{String}		action			'activate' or 'deactivate'
		@param 		{String}		license			The license key to deactivate
		@param 		{String}		nonce 			The security nonce
		@returns	{Object} 		promise 	 	A jQuery Promise
		@promise 	{Object}		response		The response, if no errors.
		@...		{String} 		validity 		EDD license validity status.
		@...		{String}		addon 			The addon the license was activated for.
		@...		{String}		html			The HTML markup of a deactivation button and info div.
		*/
		_manageLicense: function(addon, action, license, nonce) {
			return $.ajax({
				url: ajaxurl,
				dataType: 'json',
				data: {
					action: 'wprss_ajax_manage_license',
					addon: addon,
					event: action,
					license: license,
					nonce: nonce
				}
			}).then(function(response, textStatus, jqXHR) {
				if (response.error !== undefined) {
					// If there was an error on the backend, we want to break the chain
					// of resolves. We do this by creating a new Promise and rejecting it with
					// the data indicating the error.
					console.log('Error: ', response.error);
					return $.Deferred().reject(jqXHR, response, 'Not YES').promise();
				}

				return response;
			});
		}

	});

})(jQuery, top, document);