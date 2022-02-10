<#

var field = data.field,
    color = '#ddd';

if ( field.color ) {
    color = '#' + field.color;
}

#>
<div class="pp-field-separator" style="height: 1px; background: {{color}};"></div>
