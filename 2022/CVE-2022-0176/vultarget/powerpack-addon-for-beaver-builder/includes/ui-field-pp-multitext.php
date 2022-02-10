<#

var field   = data.field,
    name    = data.name,
    value   = data.value,
    atts    = '',
    count   = 0,
    defaultSettings = {},
    responsive = {};

if ( field.default ) {
    defaultSettings = field.default;
}

if ( field.responsive ) {
    responsive = field.responsive;
}

#>
<div class="pp-multitext-wrap">
    <# if ( responsive.length > 0 ) { #>
        <div class="pp-multitext-responsive-toggle">
            <span class="fa fa-desktop pp-multitext-default" data-field-target="medium" title="<?php esc_html_e('Default', 'bb-powerpack-lite'); ?>"></span>
            <span class="fa fa-tablet pp-multitext-medium" data-field-target="small" title="<?php esc_html_e('Medium Devices', 'bb-powerpack-lite'); ?>"></span>
            <span class="fa fa-mobile pp-multitext-small" data-field-target="default" title="<?php esc_html_e('Responsive Devices', 'bb-powerpack-lite'); ?>"></span>
        </div>
    <# } #>
    <#
    if ( 'undefined' === typeof responsive.medium ) {
        responsive.medium = {};
    }
    if ( 'undefined' === typeof responsive.small ) {
        responsive.small = {};
    }
    for ( var optionKey in field.options ) {
        var optionVal   = field.options[ optionKey ],
            label       = optionVal.label,
            placeholder = ( optionVal.placeholder ) ? optionVal.placeholder : '',
            icon        = ( optionVal.icon ) ? 'fa ' + optionVal.icon : '',
            preview     = ( optionVal.preview ) ? optionVal.preview : {},
            tooltip     = ( optionVal.tooltip ) ? optionVal.tooltip : '';

        if ( 'undefined' === typeof responsive.medium[optionKey] || '' === responsive.medium[optionKey] ) {
            responsive.medium[optionKey] = value[optionKey];
        }
        if ( 'undefined' === typeof responsive.small[optionKey] || '' === responsive.small[optionKey] ) {
            responsive.small[optionKey] = value[optionKey];
        }
        if ( 'undefined' === typeof value.responsive_medium && 'undefined' === typeof value.responsive_small ) {
            value.responsive_medium = defaultSettings;
            value.responsive_small = defaultSettings;
        }
        if ( 'undefined' === typeof value.responsive_medium[optionKey] || '' === value.responsive_medium[optionKey] ) {
            value.responsive_medium[optionKey] = responsive.medium[optionKey];
        }
        if ( 'undefined' === typeof value.responsive_small[optionKey] || '' === value.responsive_small[optionKey] ) {
            value.responsive_small[optionKey] = responsive.small[optionKey];
        }
    #>
    <span class="pp-multitext {{icon}}<# if ( '' !== tooltip ) { #> pp-tip<# } #> pp-field<# if ( responsive.length > 0 ) { #> pp-responsive-enabled<# } #>" <# if ( preview.length > 0 ) { #>data-preview="{{preview}}"<# } #> title="{{tooltip}}">
        <input type="text" name="{{name}}[][{{optionKey}}]" value="{{value[optionKey]}}" class="text pp-field-multitext pp-field-multitext-default input-small-m valid" placeholder="{{placeholder}}" />
        <# if ( field.responsive && responsive.length > 0 ) { #>
            <input type="text" name="{{name}}[][responsive_medium][{{optionKey}}]" value="{{value[responsive_medium][optionKey]}}" class="text pp-field-multitext pp-field-multitext-responsive pp-field-multitext-medium input-small-m valid" placeholder="{{placeholder}}" />
            <input type="text" name="{{name}}[][responsive_small][{{optionKey}}]" value="{{value[responsive_small][optionKey]}}" class="text pp-field-multitext pp-field-multitext-responsive pp-field-multitext-small input-small-m valid" placeholder="{{placeholder}}" />
        <# } #>
        <# if ( 0 === count ) { #>
            <span class="pp-responsive-toggle fa fa-chevron-right pp-tip" title="<?php esc_html_e( 'Responsive Options', 'bb-powerpack-lite' ); ?>"></span>
        <# } #>
    </span>
    <#
        count++;
    }
    #>
</div>
