wps_js.hitsmap_meta_box = {

    placeholder: function () {
        return wps_js.rectangle_placeholder();
    },

    view: function (args = []) {
        return `<div id="wp-statistics-visitors-map"></div>`;
    },

    meta_box_init: function (args = []) {
        let pin = Array();

        // Prepare Country Pin
        if (args.hasOwnProperty('country')) {
            Object.keys(args['country']).forEach(function (key) {
                let t = `<div class='map-html-marker'><img src='${args['country'][key]['flag']}' alt="${args['country'][key]['name']}" title='${args['country'][key]['name']}' class='log-tools'/> ${args['country'][key]['name']} [${args['total_country'][key]}]<hr />`;

                // Get List visitors
                Object.keys(args['visitor'][key]).forEach(function (visitor_id) {
                    t += `<p><img src='${args['visitor'][key][visitor_id]['browser']['logo']}' alt="${args['visitor'][key][visitor_id]['browser']['name']}" class='log-tools' title='${args['visitor'][key][visitor_id]['browser']['name']}'/> ${args['visitor'][key][visitor_id]['ip']} ` + (args['visitor'][key][visitor_id]['city'] !== "Unknown" ? '- ' + args['visitor'][key][visitor_id]['city'] : '') + `</p>`;
                });
                t += `</div>`;

                pin[key] = t;
            });
        }

        // Load Jquery Map
        jQuery('#wp-statistics-visitors-map').vectorMap({
            map: 'world_en',
            backgroundColor: '#fff',
            borderColor: '#7e7e7e',
            borderOpacity: 0.60,
            color: '#e6e5e2',
            hoverColor: '#c3403c',
            colors: args['color'],
            onLabelShow: function (element, label, code) {
                if (pin[code] !== undefined) {
                    label.html(pin[code]);
                } else {
                    label.html(label.html() + ' [0]<hr />');
                }
            },
        });
    }
};