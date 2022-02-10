/* =============================================================================
   DO WHEN
   ========================================================================== */

(function ($) {

	'use strict';

	var _options,
		_fields = [],
		_getActionByName,
		_getValidActions,
		_parseAndSaveData,
		_doesFieldMatch,
		_onStateMatched,
		_onStateUnmatched,
		_checkDoState,
		_checkFieldDoState;

	_getValidActions = function() {
		var validActions = [];

		for (var action in $.fn.doWhen.actions) {
			if ($.fn.doWhen.actions.hasOwnProperty(action)) {
				validActions.push(action);
			}
		}

		return validActions.join(', ');
	};

	_getActionByName = function(action) {
		var config = {},
			matchedConfig = false;

		if (action === null) {
			throw Error('$.doWhen: Action must be specified. Valid options are: [' + _getValidActions() + ']');
		}

		for (var possibleAction in $.fn.doWhen.actions) {
			if ($.fn.doWhen.actions.hasOwnProperty(possibleAction) && possibleAction === action) {
				matchedConfig = $.fn.doWhen.actions[possibleAction];
			}
		}

		if (matchedConfig === false) {
			throw Error('$.doWhen: Invalid action "' + action + '". Valid options are: [' + _getValidActions() + ']');
		}

		// fill out any unfilled options with the defaults
		$.extend(config, $.fn.doWhen.actions.blank, matchedConfig);

		return config;
	};

	_parseAndSaveData = function(el) {
		var $el = $(el),
			when = $el.attr('data-' + _options.doWhenAttr),
			action = $el.data(_options.doActionAttr),
			actions = _getActionByName(action),
			parsed = [],
			jsonObject = false,
			convertToJSON;

		convertToJSON = function(str) {
			var json = false;

			try {
				json = $.parseJSON(str);
			} catch (e) {
				return false;
			}

			return json;
		};

		if (when.indexOf('||')) {
			var arrWhen = when.split('||');
			
			for (var i = 0, len = arrWhen.length; i < len; i += 1) {
				jsonObject = convertToJSON(arrWhen[i]);
				
				if (jsonObject !== false) {
					parsed.push(jsonObject);
				}
			}
		} else {
			jsonObject = convertToJSON(when);

			if (jsonObject !== false) {
				parsed.push(jsonObject);
			}
		}

		for (var j = 0, parsedLen = parsed.length; j < parsedLen; j += 1) {
			for (var key in parsed[j]) {
				var parsedItem = parsed[j];

				if (parsedItem.hasOwnProperty(key)) {
					// if the data is an empty array it means we can ignore it
					if (parsedItem[key].length === 0) {
						delete parsedItem[key];
					} else {
						// store all the form fields that impact conditional functionality
						if (!_fields.hasOwnProperty(key)) {
							_fields[key] = [];
						}

						// store all the elements related to the specific form field
						_fields[key].push(el);
					}
				}
			}
		}

		$el.data(_options.doActionAttr + '-parsed', actions);
		$el.data(_options.doWhenAttr + '-parsed', parsed);
	};

	_doesFieldMatch = function(idOrName, value) {
		var $field = $('#' + idOrName),
			isMatched = false,
			fieldValue = [];

		// find the field based on the id or name and get the value(s)
		if ($field.length === 0) {
			$field = $('[name="' + idOrName + '"]');

			if ($field.length === 0) {
				throw Error('$.doWhen: Invalid action "' + action + '". Valid options are: [' + _getValidActions() + ']');
			}

			$field.each(function(i, el) {
				if ($(el).prop('checked')) {
					fieldValue.push($(el).val());
				}
			});
		} else {
			fieldValue.push($field.val());
		}

		for (var i = 0, len = value.length; i < len; i += 1) {
			for (var i2 = 0, len2 = fieldValue.length; i2 < len2; i2 += 1) {
				if (value[i] === fieldValue[i2]) {
					isMatched = true;
				}
			}
		}

		return isMatched;
	};

	_onStateMatched = function($el) {
		var action = $el.data(_options.doActionAttr).toLowerCase(),
			actions = $el.data(_options.doActionAttr + '-parsed');

		actions.match($el, function() {
			$el.trigger('updated.doWhen').trigger('matched.' + action + '.doWhen');
		});
	};

	_onStateUnmatched = function($el) {
		var action = $el.data(_options.doActionAttr).toLowerCase(),
			actions = $el.data(_options.doActionAttr + '-parsed');

		actions.unmatch($el, function() {
			$el.trigger('updated.doWhen').trigger('unmatched.' + action + '.doWhen');
		});
	};

	_checkDoState = function($filteredItems) {
		var $items;

		if ($filteredItems) {
			$items = $filteredItems;
		} else {
			$items = $('[data-' + _options.doWhenAttr + ']');
		}

		$items.each(function(i, el) {
			var $el = $(el),
				conditions = $el.data(_options.doWhenAttr + '-parsed'),
				toDo = false;

			for (var j = 0, len = conditions.length; j < len; j += 1) {
				var condition = conditions[j],
					conditionMet = true;

				for (var key in condition) {

					if (condition.hasOwnProperty(key) && conditionMet) {
						conditionMet = _doesFieldMatch(key, condition[key]);
					}
				}

				if (conditionMet) {
					toDo = true;
				}
			}

			if (toDo) {
				_onStateMatched($el);
			} else {
				_onStateUnmatched($el);
			}
		});
	};

	_checkFieldDoState = function() {
		var $field = $(this),
			idOrName = $field.attr('id'),
			nodeName = this.nodeName.toUpperCase();

		if (nodeName === 'INPUT' && ($field.attr('type') === 'radio' || $field.attr('type') === 'checkbox')) {
			idOrName = $field.attr('name');
		}

		if (_fields.hasOwnProperty(idOrName)) {
			// only check the items which will change
			var $filteredItems = $(_fields[idOrName]);
			_checkDoState($filteredItems);
			return;
		}

		// can't detect which items are impacted so check all
		_checkDoState();
	};

	$.fn.doWhen = function(options) {

		_options = $.extend({}, $.fn.doWhen.defaults, options);

		_fields = [];

		$(this).find('[data-' + _options.doWhenAttr + ']').each(function(i, el) {
			// format and save the data
			_parseAndSaveData(el);
		});

		for (var key in _fields) {
			if (_fields.hasOwnProperty(key)) {
				var $field = $('#' + key),
					nodeName;

				if ($field.length === 0) {
					$field = $('[name="' + key + '"]');
				}

				nodeName = $field.get(0).nodeName.toUpperCase();

				if ((nodeName === 'SELECT' || nodeName === 'INPUT') === false) {
					$field = $('[name="' + key + '"]');
				}

				$field.off('change.doWhen', _checkFieldDoState)
					.on('change.doWhen', _checkFieldDoState);
			}
		}

		// check all fields
		_checkDoState();

		return this;

	};

	$.fn.doWhen.defaults = {
		doWhenAttr: 'do-when',
		doActionAttr: 'do-action'
	};

	$.fn.doWhen.actions = {
		blank: {
			match: function($el, callback) {
				callback();
			},
			unmatch: function($el, callback) {
				callback();
			}
		},
		show: {
			match: function($el, callback) {
				$el.slideDown();
				callback();
			},
			unmatch: function($el, callback) {
				$el.slideUp();
				callback();
			}
		},
		hide: {
			match: function($el, callback) {
				$el.slideUp();
				callback();
			},
			unmatch: function($el, callback) {
				$el.slideDown();
				callback();
			}
		},
		click: {
			match: function($el, callback) {
				$el.get(0).click();
				callback();
			},
			unmatch: function($el, callback) {
				callback();
			}
		},
		disable: {
			match: function($el, callback) {
				$el.prop('disabled', true);
				callback();

				if ($el.get(0).tagName.toLowerCase() === 'option') {
					var $select = $el.parent(),
						$enabledOptions = $select.find('option:not(:disabled)');

					if ($el.prop('selected')) {
						$enabledOptions.eq(0).prop('selected', true);
					}

					if ($enabledOptions.length <= 1) {
						$select.prop('disabled', true);
					}
				}
			},
			unmatch: function($el, callback) {
				$el.prop('disabled', false);
				callback();

				if ($el.get(0).tagName.toLowerCase() === 'option') {
					var $select = $el.parent(),
						$enabledOptions = $select.find('option:not(:disabled)');

					if ($enabledOptions.length > 1) {
						$select.prop('disabled', false);
					}
				}
			}
		}
	};

	$.fn.doWhen.addAction = function(name, match, unmatch) {
		$.fn.doWhen.actions[name] = {
			match: match,
			unmatch: unmatch
		};
	};

}(jQuery));