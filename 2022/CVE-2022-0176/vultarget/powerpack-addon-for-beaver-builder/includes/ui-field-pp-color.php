<#

var field   = data.field,
    name    = data.name,
    value   = data.value,
    atts    = '',
    primary = '',
    secondary = '';

if ( '' === value.primary || 'undefined' === typeof value.primary ) {
    primary = ' fl-color-picker-empty';
}

if ( '' === value.secondary || 'undefined' === typeof value.secondary ) {
    secondary = ' fl-color-picker-empty';
}

#>

<div class="pp-color-picker fl-color-picker<# if ( field.show_reset ) { #> fl-color-picker-has-reset<# } #><# if ( field.className ) { #> {{field.className}}<# } #>">
    <button class="fl-color-picker-color{{primary}}">
        <svg class="fl-color-picker-icon" width="18px" height="18px" viewBox="0 0 18 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g fill-rule="evenodd">
                <path d="M17.7037706,2.62786498 L15.3689327,0.292540631 C14.9789598,-0.0975135435 14.3440039,-0.0975135435 13.954031,0.292540631 L10.829248,3.41797472 L8.91438095,1.49770802 L7.4994792,2.91290457 L8.9193806,4.33310182 L0,13.2493402 L0,18 L4.74967016,18 L13.6690508,9.07876094 L15.0839525,10.4989582 L16.4988542,9.08376163 L14.5789876,7.16349493 L17.7037706,4.03806084 C18.0987431,3.64800667 18.0987431,3.01791916 17.7037706,2.62786498 Z M3.92288433,16 L2,14.0771157 L10.0771157,6 L12,7.92288433 L3.92288433,16 Z"></path>
            </g>
        </svg>
    </button>
    <# if ( field.show_reset ) { #>
        <button class="fl-color-picker-clear"><div class="fl-color-picker-icon-remove"></div></button>
    <# } #>
    <div class="pp-color-text">{{{field.options.primary}}}</div>
    <input name="{{name}}[][primary]" type="hidden" value="<# if ('undefined' !== typeof value.primary) { #>{{value.primary}}<# } #>" class="fl-color-picker-value pp-field-color pp-color-primary" />
</div>
<div class="pp-color-picker fl-color-picker<# if ( field.show_reset ) { #> fl-color-picker-has-reset<# } #><# if ( field.className ) { #> {{field.className}}<# } #>">
    <button class="fl-color-picker-color{{secondary}}">
        <svg class="fl-color-picker-icon" width="18px" height="18px" viewBox="0 0 18 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g fill-rule="evenodd">
                <path d="M17.7037706,2.62786498 L15.3689327,0.292540631 C14.9789598,-0.0975135435 14.3440039,-0.0975135435 13.954031,0.292540631 L10.829248,3.41797472 L8.91438095,1.49770802 L7.4994792,2.91290457 L8.9193806,4.33310182 L0,13.2493402 L0,18 L4.74967016,18 L13.6690508,9.07876094 L15.0839525,10.4989582 L16.4988542,9.08376163 L14.5789876,7.16349493 L17.7037706,4.03806084 C18.0987431,3.64800667 18.0987431,3.01791916 17.7037706,2.62786498 Z M3.92288433,16 L2,14.0771157 L10.0771157,6 L12,7.92288433 L3.92288433,16 Z"></path>
            </g>
        </svg>
    </button>
    <# if ( field.show_reset ) { #>
        <button class="fl-color-picker-clear"><div class="fl-color-picker-icon-remove"></div></button>
    <# } #>
    <div class="pp-color-text">{{{field.options.secondary}}}</div>
    <input name="{{name}}[][secondary]" type="hidden" value="<# if ('undefined' !== typeof value.secondary) { #>{{value.secondary}}<# } #>" class="fl-color-picker-value pp-field-color pp-color-secondary" />
</div>
