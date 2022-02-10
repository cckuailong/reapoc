<#

var field = data.field,
    name = data.name,
    value = data.value,
    atts = '';

// Toggle data
if ( field.toggle ) {
	atts += " data-toggle='" + JSON.stringify( field.toggle ) + "'";
}

// Hide data
if ( field.hide ) {
	atts += " data-hide='" + JSON.stringify( field.hide ) + "'";
}

// Trigger data
if ( field.trigger ) {
	atts += " data-trigger='" + JSON.stringify( field.trigger ) + "'";
}

// Options Count
var optionsCount = Object.keys(field.options).length;

#>

<div class="pp-switch" data-options="{{optionsCount}}">
    <#
    // Loop through the options
	for ( var optionKey in field.options ) {
        var optionVal = field.options[ optionKey ];
        // Is selected?
		var selected = '';
        if ( optionKey == value ) {
			selected = ' pp-switch-active';
		}
        // Option label
		var label = 'object' === typeof optionVal ? optionVal.label : optionVal;
    #>
    <span class="pp-switch-button{{selected}}" data-value="{{optionKey}}">{{{label}}}</span>
    <# } #>
    <input type="hidden" class="pp-field-switch" name="{{name}}" value="{{value}}"{{{atts}}} />
</div>
