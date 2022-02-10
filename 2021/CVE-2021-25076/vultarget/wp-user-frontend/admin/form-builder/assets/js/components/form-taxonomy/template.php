<div class="wpuf-fields">
    <select
        v-if="'select' === field.type"
        :class="field.name"
        v-html ="get_term_dropdown_options()"
    />

    <div v-if="'ajax' === field.type" class="category-wrap">
        <div>
            <select>
                <option><?php _e( '— Select —', 'wp-user-frontend' ); ?></option>
                <option v-for="term in sorted_terms" :value="term.id">{{ term.name }}</option>
            </select>
        </div>
    </div>

    <div v-if="'multiselect' === field.type" class="category-wrap">
        <select
            :class="field.name"
            v-html="get_term_dropdown_options()"
            multiple
        >
        </select>
    </div>

    <div v-if="'checkbox' === field.type" class="category-wrap">
        <div v-if="'yes' === field.show_inline" class="category-wrap">
            <div v-html="get_term_checklist_inline()"></div>
        </div>
        <div v-else class="category-wrap">
            <div v-html="get_term_checklist()"></div>
        </div>
    </div>

    <input
        v-if="'text' === field.type"
        class="textfield"
        type="text"
        value=""
        size="40"
        autocomplete="off"
    >
    <span v-if="field.help" class="wpuf-help" v-html="field.help" />
</div>
