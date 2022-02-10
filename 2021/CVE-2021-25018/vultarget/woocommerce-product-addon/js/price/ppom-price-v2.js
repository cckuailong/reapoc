/**
    import $ from "jquery";
**/
const $ = jQuery;


/**
    NMedia Helper Object
**/
const nmh = {

    show_console: true,
    working_dom: 'loading-data',
    decimal_separator: ppom_input_vars.wc_decimal_sep,
    no_of_decimal: ppom_input_vars.wc_no_decimal,
    thousand_sep: ppom_input_vars.wc_thousand_sep,
    fields_meta: ppom_input_vars.field_meta,
    input_selector: $('.ppom-input'),
    dom_product_qty: $('form.cart').find('input[name="quantity"]'),

    l: function(a, b) {
        this.show_console && console.log(a, b);
    },

    strip_slashes: function(s) {
        if (s !== undefined) { return s.replace(/\\/g, ''); }
    },

    working: function(m) {
        $("#" + this.working_dom).html(m);
    },

    percentage: function(num, per) {
        // console.log(per);
        per = per.replace('%', '');
        return (Number(num) / 100) * per;
    },

    get_product_qty: function() {

        const quantity = this.dom_product_qty.val() || 0;
        return parseInt(quantity);
    },

    get_formatted_price: function(price) {

        const is_positive = price > 0;
        let price_cloner = $("#ppom-price-cloner").clone();

        // adding separator and decimal place
        let formatted_price = Math.abs(parseFloat(price));
        formatted_price = formatted_price.toFixed(this.no_of_decimal);
        formatted_price = formatted_price.toString().replace('.', this.decimal_separator);
        formatted_price = this.add_thousand_separator(formatted_price);

        price_cloner.find('.ppom-price').html(formatted_price);

        // Adding (-) symbol
        if (!is_positive) price_cloner.prepend('-');

        return $(price_cloner).html();
    },

    add_thousand_separator: function(p) {

        var rx = /(\d+)(\d{3})/;

        return String(p).replace(/^\d+/, function(w) {
            if (this.thousand_sep) {
                while (rx.test(w)) {
                    w = w.replace(rx, '$1' + this.thousand_sep + '$2');
                }
            }
            return w;
        });
    },

    error: function(field_id) {
        let t = true;

        if (field_id === undefined) {
            this.l('Input dataname not found');
            t = false;
        }
        return t;
    },

    ppom_get_quantity: function(option) {

        let qty = this.get_product_qty();

        switch (option.apply) {
            case 'fixed':
                qty = 1;
                break;
        }
        return qty;
    },
};


/**
    DOM Manipulation
**/
const ppom_input = {
    dom: {},

    init: function(dom) {
        this.dom = dom;
        return this;
    },

    dataname: function() {

        let Field_id = $(this.dom).attr('data-data_name');

        if (Field_id == undefined) {
            Field_id = $(this.dom).attr('data-dataname');
        }

        return Field_id;
    },

    type: function() {
        return this.dom.type;
    },

    value: function() {
        let v = $(this.dom).val();
        if (this.type() === 'checkbox' || this.type() === 'radio') {
            v = $(this.dom).is(':checked') ? v : '';
        }
        return v;
    },
};


/**
    Build OptionPrice Class
**/
class PPOM_Price_Class {

    constructor(field, value) {

        this.field = field;

        //parse for image/audio input
        this.value = this.get_value(value);

        // Object Destructruing
        const {
            type: ppom_type,
            title: price_label,
            data_name,
        } = this.field;

        this.dataname = data_name;
        this.type = ppom_type;
        this.label = price_label;
        this.label_val = this.get_label_value();
        this.options = this.get_options();
        this.id = this.get_id();
        this.has_percent = this.get_has_percent();
        this.price = Number(this.get_price());
        this.formatted_price = nmh.get_formatted_price(this.price);
        this.apply = this.get_apply();
        this.is_positive = this.price > 0 ? true : false;

        //console.log('PPOM Fields', this.field);
    }


    get_id() {

        let id = this.dataname;

        if (this.options && this.options.length > 0) {

            const option_found = this.options.find(o => o.price !== 0 && nmh.strip_slashes(o.title) === this.value);


            if (option_found) {
                id = this.dataname + '_' + option_found.id;
            }
        }
        return id;
    }

    get_label_value() {

        let value_label = '';

        switch (this.type) {
            case 'file':
            case 'cropper':
                value_label = this.label;
                break;

                // case 'pricematrix':

                // //console.log('pricematrix label', this.value);
                //     $.each(this.value, (range, meta) => {
                //       value_label = `${this.label} [${meta.label}]`;
                //     });
                // break;

            default:
                value_label = `${this.label} [${this.value}]`;
        }

        return value_label;
    }

    get_price() {

        const price_key = this.type == 'cropper' || this.type == 'file' ? 'file_cost' : 'price';

        let p = this.field[price_key] || '';
        // If Field Have Options
        if (this.options && this.options.length > 0) {

            const priced = this.options.find(o => o.price !== '' && (nmh.strip_slashes(o.title) === this.value || nmh.strip_slashes(o.id) === this.value));
            if (priced) {

                if (this.has_percent) {
                    p = nmh.percentage(ppomPrice.base_price, priced.price);
                }
                else {
                    p = priced.price;
                }
            }
        }
        return p;
    }

    get_apply() {
        const { onetime } = this.field;
        return onetime == 'on' ? 'fixed' : 'variable';
    }

    get_value(value) {

        const { type } = this.field;

        switch (type) {
            case 'audio':
                value = JSON.parse(value);
                value = value.title;
                break;
            case 'image':
                value = JSON.parse(value);
                value = value.image_id;
                break;
            case 'pricematrix':
                value = JSON.parse(value);
                break;
        }
        return value;
    }

    get_options() {

        const { options, images, audio } = this.field;

        let field_options = options || images || audio || [];
        if (field_options) {
            // set option.title field for title
            field_options.map(fo => fo.title = fo.option || fo.title);

        }

        return field_options;
    }

    get_has_percent() {
        const p = this.get_price();
        return typeof p == "string" && p.includes("%");
    }
}


/**
    Render price table
**/
const PPOM_Price_Table = {

    price_container: $('#ppom-price-container'),
    show_value_in_label: true,
    base_price_label: ppom_input_vars.product_base_label,
    total_price_label: ppom_input_vars.total_without_fixed_label,
    total_price: 0,
    row_index: 0,

    init: function(ppom_prices) {

        //reset price table
        this.price_container.html('');
        this.total_price = 0;
        this.row_index = 0;
        this.show_base_price = this.enable_base_price();

        // init table
        this.table_container = $('<table/>').addClass('table table-striped').appendTo(this.price_container).css('width', '100%');

        // console.log('Price Table', ppom_prices);

        ppom_prices.map((option, i) => {

            const row_class = `ppom-${option.type}-${option.apply}`;

            const p_row = $('<tr/>')
                .addClass('ppom-option-option-list')
                .addClass(option.dataname)
                .addClass(row_class)
                .attr('data-option_id', option.id)
                .attr('data-data_name', option.dataname)
                .hide()
                .appendTo(this.table_container);

            let quantitify = true;
            switch (option.apply) {
                case 'fixed':
                    quantitify = false;
                    break;
            }

            const context = option.has_discount ? 'option_discount' : 'option_price';

            const price_calculate = this.price_with_product_qty(option, context);

            const label = this.show_value_in_label ? this.label_val(option, quantitify) : this.simple_label(option, quantitify);

            this.add_row(label, nmh.get_formatted_price(price_calculate), p_row);

            this.total_price += price_calculate;

            $.event.trigger({
                type: "ppom_option_price_added",
                option: option,
                price: price_calculate,
                time: new Date()
            });
        });

        // Base/Product Price
        if (this.show_base_price) {

            const row = $('<tr/>')
                .addClass('ppom-product-price')
                .addClass('ppom-product-base-price') // legacy
                .hide()
                .appendTo(this.table_container);

            const price = nmh.get_formatted_price(ppomPrice.base_price);
            const price_calculate = this.price_with_product_qty(ppomPrice, 'product_price');
            const label = `${this.base_price_label} ${price} x ${nmh.get_product_qty()}`;

            // const label = this.base_price_label;
            this.add_row(label, nmh.get_formatted_price(price_calculate), row);

            this.total_price += Number(price_calculate);
        }

        // TOTAL
        const row_total = $('<tr/>')
            .addClass('ppom-total-price')
            .addClass('ppom-total-without-fixed') // legacy
            .hide()
            .appendTo(this.table_container);

        const label = `<strong>${this.total_price_label}</strong>`;
        const price = `<strong>${nmh.get_formatted_price(this.total_price)}</strong>`;
        this.add_row(label, price, row_total);
    },

    add_row: function(label, price, row) {

        const p_label = $('<th/>')
            .html(label)
            .addClass('ppom-label-item')
            .appendTo(row);

        const p_price = $('<th/>')
            .html(price)
            .addClass('ppom-price-item')
            .appendTo(row);

        // Animate
        row.delay(this.row_index * 100).show();
        this.row_index++;
    },

    price_with_product_qty: function(option, context) {

        let price = 0;
        switch (context) {
            case 'option_price':
                price = option.price * nmh.ppom_get_quantity(option);
                break;

            case 'option_discount':
                price = option.price * nmh.ppom_get_quantity(option);
                break;

            case 'product_price':
                price = option.base_price * nmh.ppom_get_quantity(option);
                break;
        }

        return price;
    },

    label_val: function(option, quantitify) {
        //console.log('price formated', option.formatted_price);
        let label = `${option.label_val} ${option.formatted_price} x ${nmh.get_product_qty()}`;

        if (!quantitify) { label = option.label_val; }

        return label;
    },

    simple_label: function(option, quantitify) {

        let label = `${option.label} ${option.formatted_price} x ${nmh.get_product_qty()}`;

        if (!quantitify) { label = option.label; }

        return label;
    },

    enable_base_price: function() {

        let is_base_price = true;

        nmh.fields_meta.map((meta, index) => {
            // console.log(meta);
            if (meta.type == "pricematrix" && meta.discount !== 'on') {
                is_base_price = false;
            }
        });
        return is_base_price;
    },
};


/**
    Fields Price Handler Object
**/
const ppomPrice = {

    meta: [],
    field_prices: [],
    ppom_type: '', //ppom input type

    init: function() {

        this.base_price = this.get_base_price();

        // reset field_prices
        this.field_prices = [];

        let list = document.querySelectorAll(".ppom-input, .ppom-quantity");

        //convert to array
        list = Array.from(list);

        list.map((elem, index) => {

            // binding events
            const input = ppom_input.init(elem);

            this.set_ppom_type(input.dataname());

            const has_value = input.value() || false;
            const field_meta = this.meta.find(m => m.data_name === input.dataname());

            if (has_value && field_meta) {

                let ppom_price = new PPOM_Price_Class(field_meta, input.value());

                /**
                 ** Filter/third party addons/plugin to update price object
                 ** Example: https://gist.github.com/nmedia82/e2bcc4e9db4f8acc4cfb8bf29962bc19
                 **/
                if (window['ppom_get_price_' + this.ppom_type] != undefined) {
                    ppom_price = window['ppom_get_price_' + this.ppom_type](ppom_price, field_meta, input.value());
                }

                this.update_price(ppom_price);
                PPOM_Price_Table.init(this.field_prices);
            }
        });
        return this;
    },

    load_data: function() {

        this.meta = nmh.fields_meta;

        // Load Init
        this.init();
    },

    // Get ppom input type from meta by datame
    set_ppom_type: function(dataname) {

        if (nmh.error(dataname)) {
            const meta_found = this.meta.find(m => m.data_name === dataname);
            this.ppom_type = meta_found.type != '' ? meta_found.type : '';
        }
    },

    update_price: function(ppom_price) {

        // filter only those option which have prices
        let field_prices = this.field_prices.filter(f => f.id !== ppom_price.id);

        // If price found
        if (ppom_price && ppom_price.price != 0)
            field_prices = [...field_prices, ppom_price];

        this.field_prices = field_prices;

        $.event.trigger({
            type: "ppom_option_price_updated",
            ppom_price: ppom_price,
            time: new Date()
        });
    },

    get_base_price: function() {
        return ppom_input_vars.wc_product_price;
    },
};


/* 
 **========== PriceMatrix Input Prices Handle  =========== 
 */
class PPOM_PriceMatrix_Class extends PPOM_Price_Class {

    constructor(field, value) {
        super(field, value);
        this.label = this.get_label_value();
        this.has_percent = this.get_has_percent();
        this.has_discount = field.discount === 'on' ? true : false;
        // console.log("Value", value);
    }

    get_price() {

        let p = 0;

        if (this.get_pricematrix_meta().price) {

            if (this.has_percent) {
                p = nmh.percentage(ppomPrice.base_price, this.get_pricematrix_meta().percent);
            }
            else {
                p = this.get_pricematrix_meta().price;
            }
        }

        const has_discount = this.field.discount === 'on' ? true : false;
        return has_discount ? p * -1 : p;
    }

    get_label_value() {

        return `${this.field.title} [${this.get_pricematrix_meta().label}]`;
    }

    get_pricematrix_meta() {

        let matrix_obj = {};
        $.each(this.value, (range, meta) => {

            const range_break = range.split("-");

            let range_from = parseInt(range_break[0]);
            let range_to = parseInt(range_break[1]);
            const product_qty = nmh.get_product_qty();

            if (product_qty >= range_from && product_qty <= range_to) {
                matrix_obj = meta;
            }
        });
        return matrix_obj;
    }

    get_has_percent() {
        // console.log(this.get_pricematrix_meta().percent);
        // const p = this.get_price();
        return this.get_pricematrix_meta().percent && true;
    }
}


function ppom_get_price_pricematrix(price_obj, field_meta, value) {

    const field_price = new PPOM_PriceMatrix_Class(field_meta, value);

    return field_price;
}

// For Variation Quantity
class PPOM_variationQuantity_Class extends PPOM_Price_Class {

    constructor(field, value) {
        super(field, value);
    }

    get_price() {

        let p = this.field.default_price || '';


        if (p) {
            p = Number(this.value) * p;
        }

        // if options found    
        if (this.field.options && this.field.options.length > 0) {
            // const option_title = o.option || o.title;

            const priced = this.field.options.find(o => o.price !== '' &&
                (nmh.strip_slashes(o.title) === this.value || nmh.strip_slashes(o.id) === this.value)
            );

            if (priced) {

                p = priced.price * Number(this.value);

            }
        }

        return p;
    }
}


//Filter function
function ppom_get_price_quantities(price_obj, field_meta, value) {

    const field_price = new PPOM_variationQuantity_Class(field_meta, value);
    //console.log("Value",field_price);

    return field_price;
}



/* 
 **========== PPOM Price INITs  =========== 
 * 1- Run PPOM Price Init
 * 2- Event Listeners
 */
ppomPrice.load_data();

const ppom_event_handler = selector => {

    selector.on('change keyup', (currentTarget) => {
        ppomPrice.init();
    });
};

// Event Listeners
ppom_event_handler(nmh.input_selector);
ppom_event_handler(nmh.dom_product_qty);

// Legacy price init function
function ppom_update_option_prices() {
    ppomPrice.init();
}
