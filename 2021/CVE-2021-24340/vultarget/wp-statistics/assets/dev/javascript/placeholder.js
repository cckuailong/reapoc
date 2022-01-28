/**
 * Default PlaceHolder if Custom MetaBox have not this Method
 */
wps_js.placeholder = function (html = false) {
    return `
<div class="wps-ph-item">
    <div class="wps-ph-col-12">
        ${wps_js.placeholder_content('picture')}
        ${wps_js.placeholder_content('line')}
    </div>
    ` + (html !== false ? html : '') + `
</div>
`;
};

/**
 * Line Placeholder
 */
wps_js.line_placeholder = function (number = 1) {
    let html = `<div class="wps-ph-item">`;
    for (let i = 0; i < number; i++) {
        html += `
                <div class="wps-ph-col-12">
                   <div class="wps-ph-row">
                    <div class="wps-ph-col-6 big"></div>
                    <div class="wps-ph-col-4 empty big"></div>
                    <div class="wps-ph-col-4"></div>
                    <div class="wps-ph-col-8 empty"></div>
                    <div class="wps-ph-col-6"></div>
                    <div class="wps-ph-col-6 empty"></div>
                    <div class="wps-ph-col-12"></div>
                     </div>
                </div>
            `;
    }
    html += `</div>`;
    return html;
};

/**
 * Default Circle PlaceHolder
 */
wps_js.circle_placeholder = function () {
    return `
<div class="wps-ph-item">
     ${wps_js.placeholder_content('circle')}
</div>
`;
};

/**
 * Default Circle PlaceHolder
 */
wps_js.rectangle_placeholder = function (cls = '') {
    return `
<div class="wps-ph-item` + (cls.length > 0 ? ' ' + cls : '') + `">
    <div class="wps-ph-col-12">
        ${wps_js.placeholder_content('picture')}
    </div>
</div>
`;
};

/**
 * Type Of Place Holder Content
 *
 * @param type
 */
wps_js.placeholder_content = function (type = 'line') {

    // Create Empty Html
    let html = '';
    switch (type) {
        case "picture": {
            html = `<div class="wps-ph-picture"></div>`;
            break;
        }
        case "line": {
            html = `<div class="wps-ph-row">
                    <div class="wps-ph-col-6 big"></div>
                    <div class="wps-ph-col-4 empty big"></div>
                    <div class="wps-ph-col-2 big"></div>
                    <div class="wps-ph-col-4"></div>
                    <div class="wps-ph-col-8 empty"></div>
                    <div class="wps-ph-col-6"></div>
                    <div class="wps-ph-col-6 empty"></div>
                    <div class="wps-ph-col-12"></div>
                     </div>`;
            break;
        }
        case "circle": {
            html = `<div class="wps-ph-col-2"></div>
                    <div class="wps-ph-col-8">
                        <div class="wps-ph-avatar"></div>
                    </div>`;
            break;
        }
        default: {
            break;
        }
    }

    return html;
};