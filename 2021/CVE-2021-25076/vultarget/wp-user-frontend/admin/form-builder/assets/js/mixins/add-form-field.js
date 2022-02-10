wpuf_mixins.add_form_field = {
    methods: {
        add_form_field: function (field_template) {
            var payload = {};
            var event_type = event.type;

            if( 'click' === event_type ){
                payload.toIndex = this.$store.state.index_to_insert === 0 ? this.$store.state.form_fields.length : this.$store.state.index_to_insert;
            }

            if ( 'mouseup' === event_type ){
                payload.toIndex = this.$store.state.index_to_insert === 0 ? 0 : this.$store.state.index_to_insert;
            }

            this.$store.state.index_to_insert = 0;

            // check if these are already inserted
            if ( this.isSingleInstance( field_template ) && this.containsField( field_template ) ) {
                swal({
                    title: "Oops...",
                    text: "You already have this field in the form"
                });
                return;
            }

            var field = $.extend(true, {}, this.$store.state.field_settings[field_template].field_props);

            field.id = this.get_random_id();

            if (!field.name && field.label) {
                field.name = field.label.replace(/\W/g, '_').toLowerCase();

                var same_template_fields = this.form_fields.filter(function (form_field) {
                   return (form_field.template === field.template);
                });

                if (same_template_fields.length) {
                    field.name += '_' + same_template_fields.length;
                }
            }

            payload.field = field;

            // add new form element
            this.$store.commit('add_form_field_element', payload);
        },
    },
};
