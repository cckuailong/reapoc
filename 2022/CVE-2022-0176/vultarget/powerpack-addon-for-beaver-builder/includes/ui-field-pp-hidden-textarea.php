<#

var field = data.field,
    name = data.name,
    value = data.value,
    atts = '';

if ( field.className ) {
    atts += ' class="' + field.className + '"';
}

if ( field.placeholder ) {
    atts += ' placeholder="' + field.placeholder + '"';
}

if ( field.rows ) {
    atts += ' rows="' + field.rows + '"';
}

#>

<textarea name="{{name}}"{{{atts}}} style="display: none;">{{{value}}}</textarea>
