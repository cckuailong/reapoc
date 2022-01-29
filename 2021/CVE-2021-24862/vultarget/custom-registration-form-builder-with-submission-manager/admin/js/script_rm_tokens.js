
/*!
 * tokens - jQuery plugin that turns a text field into a tokenized autocomplete
 * v0.5.3
 * https://github.com/firstandthird/tokens/
 * copyright First + Third 2014
 * MIT License
*/
/*!
 * fidel - a ui view controller
 * v2.2.5
 * https://github.com/jgallen23/fidel
 * copyright Greg Allen 2014
 * MIT License
*/
(function(w, $) {
  var _id = 0;
  var Fidel = function(obj) {
    this.obj = obj;
  };

  Fidel.prototype.__init = function(options) {
    $.extend(this, this.obj);
    this.id = _id++;
    this.namespace = '.fidel' + this.id;
    this.obj.defaults = this.obj.defaults || {};
    $.extend(this, this.obj.defaults, options);
    $('body').trigger('FidelPreInit', this);
    this.setElement(this.el || $('<div/>'));
    if (this.init) {
      this.init();
    }
    $('body').trigger('FidelPostInit', this);
  };
  Fidel.prototype.eventSplitter = /^(\w+)\s*(.*)$/;

  Fidel.prototype.setElement = function(el) {
    this.el = el;
    this.getElements();
    this.dataElements();
    this.delegateEvents();
    this.delegateActions();
  };

  Fidel.prototype.find = function(selector) {
    return this.el.find(selector);
  };

  Fidel.prototype.proxy = function(func) {
    return $.proxy(func, this);
  };

  Fidel.prototype.getElements = function() {
    if (!this.elements)
      return;

    for (var selector in this.elements) {
      var elemName = this.elements[selector];
      this[elemName] = this.find(selector);
    }
  };

  Fidel.prototype.dataElements = function() {
    var self = this;
    this.find('[data-element]').each(function(index, item) {
      var el = $(item);
      var name = el.data('element');
      self[name] = el;
    });
  };

  Fidel.prototype.delegateEvents = function() {
    if (!this.events)
      return;
    for (var key in this.events) {
      var methodName = this.events[key];
      var match = key.match(this.eventSplitter);
      var eventName = match[1], selector = match[2];

      var method = this.proxy(this[methodName]);

      if (selector === '') {
        this.el.on(eventName + this.namespace, method);
      } else {
        if (this[selector] && typeof this[selector] != 'function') {
          this[selector].on(eventName + this.namespace, method);
        } else {
          this.el.on(eventName + this.namespace, selector, method);
        }
      }
    }
  };

  Fidel.prototype.delegateActions = function() {
    var self = this;
    self.el.on('click'+this.namespace, '[data-action]', function(e) {
      var el = $(this);
      var action = el.attr('data-action');
      if (self[action]) {
        self[action](e, el);
      }
    });
  };

  Fidel.prototype.on = function(eventName, cb) {
    this.el.on(eventName+this.namespace, cb);
  };

  Fidel.prototype.one = function(eventName, cb) {
    this.el.one(eventName+this.namespace, cb);
  };

  Fidel.prototype.emit = function(eventName, data, namespaced) {
    var ns = (namespaced) ? this.namespace : '';
    this.el.trigger(eventName+ns, data);
  };

  Fidel.prototype.hide = function() {
    if (this.views) {
      for (var key in this.views) {
        this.views[key].hide();
      }
    }
    this.el.hide();
  };
  Fidel.prototype.show = function() {
    if (this.views) {
      for (var key in this.views) {
        this.views[key].show();
      }
    }
    this.el.show();
  };

  Fidel.prototype.destroy = function() {
    this.el.empty();
    this.emit('destroy');
    this.el.unbind(this.namespace);
  };

  Fidel.declare = function(obj) {
    var FidelModule = function(el, options) {
      this.__init(el, options);
    };
    FidelModule.prototype = new Fidel(obj);
    return FidelModule;
  };

  //for plugins
  Fidel.onPreInit = function(fn) {
    $('body').on('FidelPreInit', function(e, obj) {
      fn.call(obj);
    });
  };
  Fidel.onPostInit = function(fn) {
    $('body').on('FidelPostInit', function(e, obj) {
      fn.call(obj);
    });
  };
  w.Fidel = Fidel;
})(window, window.jQuery || window.Zepto);

(function($) {
  $.declare = function(name, obj) {

    $.fn[name] = function() {
      var args = Array.prototype.slice.call(arguments);
      var options = args.shift();
      var methodValue;
      var els;

      els = this.each(function() {
        var $this = $(this);

        var data = $this.data(name);

        if (!data) {
          var View = Fidel.declare(obj);
          var opts = $.extend({}, options, { el: $this });
          data = new View(opts);
          $this.data(name, data); 
        }
        if (typeof options === 'string') {
          methodValue = data[options].apply(data, args);
        }
      });

      return (typeof methodValue !== 'undefined') ? methodValue : els;
    };

    $.fn[name].defaults = obj.defaults || {};

  };

  $.Fidel = window.Fidel;

})(jQuery);

(function($){
  function escapeString (value) {
    return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
  }

  $.declare('tokens',{
    defaults : {
      formatSuggestion : function(suggestion, value){
        var pattern = '(' + escapeString(value) + ')';
        return suggestion.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
      },
      search : function(suggestion, queryOriginal, queryLowerCase){
        return suggestion.toLowerCase().indexOf(queryLowerCase.toLowerCase()) !== -1;
      },
      query : function(query, callback){
        var queryLower = query.toLowerCase(), self = this;
          
        if(query==" "){
             var suggestions = $.grep(this.source, function(suggestion){
             return true;
            });
        }

        else
            var suggestions = $.grep(this.source, function(suggestion){
                return self.search(suggestion, query, queryLower);
            });

        callback.apply(this,[suggestions]);
      },
      validate : function(query) {
        return true;
      },
      keyCode : {
        UP : 38,
        DOWN : 40,
        BACKSPACE : 8,
        TAB: 9,
        ENTER : 13,
        ESC : 27,
        COMMA : 188,
        SPACE: 32
      },
      texts : {
        'close-text' : '×',
        'type-suggestions' : 'Type to search values',
        'no-results' : 'There are no results matching',
        'add-result' : 'Add "%s" to the list',
        'invalid-format' : '%s is not the correct format',
        'existing-item' : '%s is already selected'
      },
      cssClasses : {
        'token-list' : 'tokens-token-list',
        'list-input-holder' : 'tokens-list-input-holder',
        'list-token-holder' : 'tokens-list-token-holder',
        'input-text' : 'tokens-input-text',
        'delete-anchor' : 'tokens-delete-token',
        'suggestion-selector' : 'tokens-suggestion-selector',
        'suggestions-list-element' : 'tokens-suggestions-list-element',
        'highlighted-suggestion' : 'tokens-highlighted-suggestion',
        'add-new-result' : 'tokens-add-new-result',
        'existing-item' : 'tokens-existing-item'
      },
      maxSelected : 0,
      onSelect : false,
      showSuggestionOnFocus : true,
      showMessageOnNoResults : true,
      allowAddingNoSuggestion : true,
      cleanInputOnHide : true,
      suggestionsZindex : 999,
      source : [],
      initValue : [],
      minChars : 0,
      allowMultiplePaste: false
    },
    _getTarget : function(e){
      return $(e.currentTarget || e.toElement);
    },
    _setAttributes: function () {
      this.el.hide();
    },
    _createStructure: function () {
      this.list = $('<ul>').addClass(this.cssClasses['token-list']);

      this.listInputHolder = $('<li>')
          .addClass(this.cssClasses['list-input-holder'])
          .appendTo(this.list);

      this.inputText = $('<input type="text">')
          .attr('autocomplete','off')
          .attr('autocapitalize', 'off')
          .addClass(this.cssClasses['input-text'])
          .appendTo(this.listInputHolder);

      this.list.insertBefore(this.el);
      this._createTester();
      this._createSuggestionsStructure();
    },
    _createSuggestionsStructure : function(){
      this.suggestionsHolder = $('<div>')
          .addClass(this.cssClasses['suggestion-selector'])
          .css({
            position : 'absolute',
            'z-index' : this.suggestionsZindex
          })
          .hide()
          .appendTo($('body'));
    },
    _createTester : function(){
      this.inputResizer = $('<tester>').css({
        position: 'absolute',
        top : -9999,
        left : -9999,
        width : 'auto',
        'font-size' : this.inputText.css('font-size'),
        'font-family' : this.inputText.css('font-family'),
        'font-weight' : this.inputText.css('font-weight'),
        'letter-spacing' : this.inputText.css('letter-spacing'),
        whitespace : 'nowrap'
      }).insertAfter(this.inputText);
    },
    _focusInput: function () {
      var self = this;
      setTimeout(function(){
        self.inputText.focus();
      },10);
    },
    _onDeleteClick: function (e) {
      e.stopImmediatePropagation();
      this._removeNode(this._getTarget(e).parent());
    },
    _onListClick : function(){
      this._focusInput();
      this._showTypeSuggestion();
      this._suggestionChanged();
    },
    _onKeyDown : function(event){
      switch(event.keyCode){
        case this.keyCode.UP :
          this._prevSuggestion();
          break;
        case this.keyCode.DOWN :
          this._nextSuggestion();
          break;
        case this.keyCode.ESC :
          this.el.val(this.currentValue);
          this._hideSuggestions();
          break;
        case this.keyCode.TAB:
        case this.keyCode.ENTER:
        case this.keyCode.COMMA:
        case this.keyCode.SPACE:
          this._selectSuggestion();
          break;
        case this.keyCode.BACKSPACE:
          this._deleteLastIfEmpty();
          return;
        default:
          return;
      }

      event.stopImmediatePropagation();
      event.preventDefault();
    },
    _onKeyUp : function(event){
      switch(event.keyCode){
        case this.keyCode.UP :
        case this.keyCode.DOWN :
          return;
      }
      this._suggestionChanged();
    },
    _onMouseOver : function (event) {
      var target = this._getTarget(event);
      this._activateSuggestion(target.data('index'));
    },
    _suggestionChanged : function(){
      var value = this.inputText.val();
        // To load all the options on focus
        if(value=="")
            value=' ';

      if (value !== this.suggestionValue){
        this.inputText.val(value);
        this.suggestionValue = value;
        this.selectedSuggestion = -1;

        if (value.length > this.minChars){
          this._updateSuggestions();
        }
      }
    },
    _deleteLastIfEmpty : function(){
      if (this.inputText.val() === ''){
        this._deleteLastToken();
      }
    },
    _deleteLastToken : function(){
      var tokens = this.currentValue.slice(0),
          lastToken = tokens.pop();

      this.removeValue(lastToken);
    },
    _nextSuggestion : function(){
      if (this.selectedSuggestion !== (this.suggestions.length -1)){
        this._adjustPosition(this.selectedSuggestion + 1);
      }
    },
    _prevSuggestion : function(){
      if (this.selectedSuggestion !== -1){
        this._adjustPosition(this.selectedSuggestion -1);
      }
    },
    _updateSuggestions : function(){
      var self = this;

      this.query.call(self, self.suggestionValue, function(suggestions){
        var len = suggestions.length;

        if (suggestions && $.isArray(suggestions) && len){
          var html = $('<ul>');
          this.suggestions = suggestions;

          for (var i = 0; i < len; i++){
            html.append(
              $('<li>').
              addClass(this.cssClasses['suggestions-list-element']).
              data('index',i).
              html(this.formatSuggestion(suggestions[i], this.suggestionValue))
            );
          }

          this.suggestionsHolder.empty().append(html);
          this._showSuggestions();
        }
        else if (this.showMessageOnNoResults) {
          if (!this.allowAddingNoSuggestion){
            if (this.source.length){
              this._addTextToSuggestions(this.texts['no-results']);
            }
          }
          else {
            this._addTextToSuggestions(this.texts['add-result'].replace('%s',this.suggestionValue), this.cssClasses['add-new-result']);
          }
        }
      });
    },
    _adjustPosition : function(index){
      var selectedSuggestion = this._activateSuggestion(index),
          selTop, upperLimit, lowerLimit, elementHeight;

      if (selectedSuggestion){
        selTop = selectedSuggestion.offset().top;
        upperLimit = this.suggestionsHolder.scrollTop();
        elementHeight = selectedSuggestion.outerHeight();
        lowerLimit = upperLimit - elementHeight;

        if (selTop < upperLimit){
          this.suggestionsHolder.scrollTop(selTop);
        }
        else if (selTop > lowerLimit) {
          this.suggestionsHolder.scrollTop(selTop - elementHeight);
        }

        this.inputText.val(this.suggestions[index]);
      }
    },
    _deactivateSuggestion : function(event){
      this._getTarget(event).removeClass(this.cssClasses['']);
      this.selectedSuggestion = -1;
    },
    _selectSuggestion : function(){
      if (this.suggestions.length && this.selectedSuggestion !== -1){
        if(this.addValue(this.suggestions[this.selectedSuggestion])) {
         /*
         * Calling a function after suggestion selected
         */
         if(this.onSelect!==false)
            this.onSelect(this.currentValue);
          this.cancelBlur = false;
          this._hideSuggestions();
          this.inputText.val('');
        } else {
          this._addTextToSuggestions(this.texts['existing-item'].replace('%s',this.suggestions[this.selectedSuggestion]), this.cssClasses['existing-item']);
          this.cancelBlur = true;
        }
      }
      else if (this.allowAddingNoSuggestion){
        var val = $.trim(this.inputText.val());
        var isValid = this.validate(val);
        
        if(typeof isValid === 'string') {
          val = isValid;
          isValid = true;
        }

        if (val.length >= this.minChars && isValid){
            console.log('papa');
          if(this.addValue(val)) {
            this.cancelBlur = false;
            this._hideSuggestions();
            this.inputText.val('');
          } else {
            this._addTextToSuggestions(this.texts['existing-item'].replace('%s',this.suggestionValue), this.cssClasses['existing-item']);
            this.cancelBlur = true;
          }
        } else {
          if(val.length !== 0) {
            this._addTextToSuggestions(this.texts['invalid-format'].replace('%s',this.suggestionValue), this.cssClasses['invalid-format']);
            this.cancelBlur = true;
          }
        }
      }
      
       
    },
    _activateSuggestion : function(index){
      var cssClass = this.cssClasses['highlighted-suggestion'],
          list = this.suggestionsHolder.find('ul'),
          element = null;

      list.children('.' + cssClass).removeClass(cssClass);
      this.selectedSuggestion = index;

      if (index !== -1 && list.children().length > index){
        element =  $(list.children().get(index)).addClass(cssClass);
      }

      return element;
    },
    _resizeInput : function() {
      if (this.inputResizer.text() !== this.inputText.val()){
        this.inputResizer.html($('<div/>').text(this.inputText.val()).html());
        this.inputText.width(this.inputResizer.width() + 30);
      }
    },
    _bindEvents: function () {
      this.list.on('click',this.proxy(this._onListClick,this));
      this.list.on('click', '.' + this.cssClasses['delete-anchor'], this.proxy(this._onDeleteClick,this));

      this.inputText.on('paste', this.proxy(this._handlePaste,this));
      this.inputText.on('blur', this.proxy(this._hideSuggestions,this));
      this.inputText.on('keydown', this.proxy(this._onKeyDown,this));
      this.inputText.on('keyup', this.proxy(this._onKeyUp,this));
      this.inputText.on('blur keyup keydown', this.proxy(this._resizeInput,this));

      var listClass = '.' + this.cssClasses['suggestions-list-element'] + ', .' + this.cssClasses['add-new-result'];

      this.suggestionsHolder.on('mouseover', listClass, this.proxy(this._onMouseOver,this));
      this.suggestionsHolder.on('mouseout', listClass, this.proxy(this._deactivateSuggestion,this));
      this.suggestionsHolder.on('mousedown', listClass, this.proxy(this._selectSuggestion,this));
    },
    _getCloseAnchor: function () {
      return $('<span>').text(this.texts['close-text']).addClass(this.cssClasses['delete-anchor']);
    },
    _updateValue : function() {
      this.el.val(this.currentValue.join(', '));
    },
    _getTextFromNode : function($node){
      return $node.find('p').text();
    },
    _getNodeFromText : function(text){
      var $node = null,
          self = this;

      this.list.find('.' + this.cssClasses['list-token-holder']).each(function(){
        if (self._getTextFromNode($(this)) === text){
          $node = $(this);
        }

        return $node === null;
      });

      return $node;
    },
    _removeNode : function($node, text){
      text = text || this._getTextFromNode($node);
      var index = this.currentValue.indexOf(text);
      if (index !== -1){
        $node.remove();
        this.currentValue.splice(index,1);
        this._updateValue();

        this.emit('remove', text);
          
        if(this.onSelect!==false)
            this.onSelect(this.currentValue)
      }
    },
    _addInitialValues : function(){
      for (var i = 0, len = this.initValue.length; i < len; i++) {
        this.addValue(this.initValue[i]);
      }
    },
    _isWithinMax : function(){
      return this.maxSelected === 0 || (this.currentValue.length < this.maxSelected);
    },
    _hasReachedMax : function() {
      return this.maxSelected !== 0 || (this.currentValue.length === this.maxSelected);
    },
    _getSuggestionPosition : function(){
      return {
        top : this.list.offset().top + this.list.outerHeight(),
        left : this.list.offset().left,
        width : this.list.width()
      };
    },
    _showTypeSuggestion : function(){
      if (this.showSuggestionOnFocus && !this.suggestions.length && this.source.length){
        this._addTextToSuggestions(this.texts['type-suggestions']);
      }
    },
    _addTextToSuggestions : function(text, classes){
      classes = classes || "";
      this.suggestionsHolder.html('<p class="' + classes +'">' + $('<div/>').text(text).html() + '</p>');
      this._showSuggestions();
    },
    _showSuggestions : function(){
      this.suggestionsHolder.css(this._getSuggestionPosition()).show();
    },
    _hideSuggestions : function() {
      if(this.cancelBlur) {
        this.cancelBlur = false;
        return false;
      }

      this.suggestionValue = '';
      this.suggestions = [];
      this.suggestionsHolder.hide();

      if (this.cleanInputOnHide){
        this.inputText.val('');
      }
    },
    _handlePaste: function(e) {
      if(!this.allowMultiplePaste) return;

      setTimeout(this.proxy(function() {
        var text = this.inputText.val();
        var items = text.split(',');

        for(var item in items) {
          item = items[item];

          this.inputText.val(item);
          this._selectSuggestion();
        }
      }, this),0);
    },
    getValue : function() {
      return this.currentValue;
    },
    addValue: function (value) {
      var tmp = this.currentValue.join(',').toLowerCase().split(',');

      if (tmp.indexOf(value.toLowerCase()) === -1 && this._isWithinMax()){
        this.currentValue.push(value);
        var list = $('<li>').addClass(this.cssClasses['list-token-holder']),
            paragraph = $('<p>').text(value);

        paragraph.appendTo(list);
        this._getCloseAnchor().appendTo(list);
        list.insertBefore(this.listInputHolder);
        this._updateValue();

        this.emit('add', value);
        
        if (this._hasReachedMax()){
          this.emit('max', value);
        }
        
       
         
        return true;
      } else {
        return false;
      }
     },
    removeValue: function (value) {
      this._removeNode(this._getNodeFromText(value),value);

      return this.el;
    },
    init : function(){
      this.currentValue = [];
      this.suggestions = [];
      this.suggestionValue = '';
      this.selectedSuggestion = -1;

      this._setAttributes();
      this._createStructure();
      this._bindEvents();
      this._addInitialValues();
    }
  });
})(jQuery);
