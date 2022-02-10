(function($) {
  $.widget( "custom.durationPicker", {
    options: {
      seconds: 0
    },
    seconds: function(newSeconds) {
      if(arguments.length) {
        this._setSeconds(newSeconds);
        this._refresh();
      }

      return this.options.seconds;
    },
    setUnitQty: function(unit, quantity) {
      var index = this._subUnitsIndexByUnit[unit];
      var qtyInNextBiggestUnit = this._subUnits[index].qtyInNextBiggestUnit;
      if(quantity < 0 || quantity >= qtyInNextBiggestUnit) {
        quantity = this._subUnits[index].quantity;
      }
      this._subUnits[index].quantity = quantity;
      this._reCalculateSeconds();

      $("."+unit, this.element).val(quantity);
    },
    getUnitQty: function(unit) {
      var index = this._subUnitsIndexByUnit[unit];
      var qty = this._subUnits[index].quantity;
      return qty;
    },
    _create: function() {
      this.element.addClass("durationPicker");
      this._initSubUnits();
      this._initUI();
      this._setSeconds(this.options.seconds);
    },
    _initSubUnits: function() {
      var self = this;

      this._subUnits = [
          {unit: "seconds", qtyInNextBiggestUnit: 60},
          {unit: "minutes", qtyInNextBiggestUnit: 60},
          {unit: "hours", qtyInNextBiggestUnit: 24},
          {unit: "days", qtyInNextBiggestUnit: 30},
          {unit: "months", qtyInNextBiggestUnit: 12},
          {unit: "years"}
        ];

      // Set number of seconds that correspond with each unit.
      //
      // This could have been hardcoded into this._subUnits, but I wanted to
      // avoid duplication.
      //
      var secondsPerUnit = 1;
      $.each(this._subUnits, function(ii, subUnit) {
        self._subUnitsIndexByUnit[subUnit.unit] = ii;
        self._subUnits[ii].secondsPerUnit = secondsPerUnit;
        self._subUnits[ii].quantity = 0;
        if(subUnit.qtyInNextBiggestUnit) {
          secondsPerUnit *= subUnit.qtyInNextBiggestUnit;
        }
      });
    },
    _initUI: function() {
      var self = this;

      var groupClass = "durationPickerGroup";
      var unitClass = "unit";
      var unitControlClass = "subunit subunit-control";
      var unitLabelClass = "subunit subunit-label";

      $("<ul class='" + groupClass + "'></ul>")
        .appendTo(self.element);

      $.each(this._getSubUnits().reverse(),
        function(ii, subUnit) {
          var max = 99;
          if(subUnit.qtyInNextBiggestUnit) {
            max = subUnit.qtyInNextBiggestUnit-1;
          }

          var unitRow =
            "<li>" +
            "<div class='"+unitClass+"'>" +
              "<div class='"+unitControlClass+"'>" +
                "<input type='number' class='" + subUnit.unit + "' " +
                "value=0 step=1 min=0 max=" + max + " />" +
              "</div>" +
              "<div class='"+unitLabelClass+"'>" + subUnit.unit + "</div>" +
            "</div></li>";

          var sel = "ul." + groupClass;
          $(unitRow).appendTo($(sel, self.element));

          $(sel+" input:last", self.element)
            .on("keyup change", function() {
              var value = $(this).val();
              self.setUnitQty(subUnit.unit, value);
              self.element.change();
            });
        });
    },
    _reCalculateSeconds: function() {
      var seconds = 0;
      $.each(this._getSubUnits(), function(ii, subUnit) {
        seconds += subUnit.quantity * subUnit.secondsPerUnit;
      });
      this.options.seconds = seconds;
    },
    _setSeconds: function(value) {
      this.options.seconds = this._constrain(value);
      this._refresh();
    },
    _refresh: function() {
      var self = this;
      var secondsInNextBiggestUnit = 1;
      var totalSeconds = this.options.seconds;

      $.each(this._getSubUnits(), function(ii, subUnit) {
        var isBiggerUnit = typeof subUnit.qtyInNextBiggestUnit === "number";
        var qtyInNextBiggestUnit = isBiggerUnit ?
          subUnit.qtyInNextBiggestUnit : 1;

        secondsPerUnit = self._getSecondsPerUnit(subUnit.unit);
        secondsInNextBiggestUnit =
          secondsPerUnit * subUnit.qtyInNextBiggestUnit;

        var valueInSeconds = totalSeconds;
        if(isBiggerUnit) { valueInSeconds %= secondsInNextBiggestUnit; }
        var valueInCurrentUnit = valueInSeconds / secondsPerUnit;
        totalSeconds -= valueInSeconds;

        self.setUnitQty(subUnit.unit, valueInCurrentUnit);
      });

      this.element.change();
    },
    _getSubUnits: function() {
      return this._subUnits.slice(0);
    },
    _getSecondsPerUnit: function(unit) {
      if(typeof this._subUnitsIndexByUnit[unit] === "undefined") {
        throw new Error("Unit does not exist!");
      }
      var index = this._subUnitsIndexByUnit[unit];
      return this._subUnits[index].secondsPerUnit;
    },
    _constrain: function( value ) {
      value = parseInt(value, 10);
      if (isNaN(value) || value < 0) { value = 0; }
      return value;
    },
    _destroy: function() {
      this.element
        .removeClass("durationPicker")
        .html("");
    },
    _subUnitsIndexByUnit: {}
  });
})(jQuery);
