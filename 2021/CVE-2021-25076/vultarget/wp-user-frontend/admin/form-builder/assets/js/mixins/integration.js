/**
 * Integration mixin
 *
 * @type {Object}
 */
wpuf_mixins.integration_mixin = {
    props: {
        id: String
    },

    computed: {

        integrations: function() {
            return wpuf_form_builder.integrations;
        },

        store: function() {
            return this.$store.state.integrations;
        },

        settings: function() {
            // find settings in store, otherwise take from default integration settings
            if ( this.store[this.id] ) {
                return this.store[this.id];
            }

            // we dont't have this on store, insert the default one
            // and return it. It happens only for the first time
            var defaultSettings = this.getIntegration(this.id).settings;

            this.$store.commit('updateIntegration', {
                index: this.id,
                value: defaultSettings
            });

            return defaultSettings;
        },
    },

    methods: {

        getIntegration: function(id) {
            return this.integrations[id];
        },

        insertValue: function(type, field, prop) {
            var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';

            this.settings[prop] = this.settings[prop] + value;
        }
    }
};
