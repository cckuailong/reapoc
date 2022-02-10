<div v-bind:class="['wpuf-field-columns', 'has-columns-'+field.columns]">
    <div class="wpuf-column-field-inner-columns">
        <div class="wpuf-column">
            <!-- don't change column class names -->
            <div v-for="column in columnClasses" :class="[column, 'items-of-column-'+field.columns, 'wpuf-column-inner-fields']" :style="{ width: field.inner_columns_size[column], paddingRight: field.column_space+'px'}">
                <ul class="wpuf-column-fields-sortable-list">
                    <li
                        v-for="(field, index) in column_fields[column]"
                        :key="field.id"
                        :class="[
                            'column-field-items', 'wpuf-el', field.name, field.css, 'form-field-' + field.template,
                            field.width ? 'field-size-' + field.width : '',
                            parseInt(editing_form_id) === parseInt(field.id) ? 'current-editing' : ''
                        ]"
                        :column-field-index="index"
                        :in-column="column"
                        data-source="column-field-stage"
                    >
                        <div v-if="!is_full_width(field.template)" class="wpuf-label wpuf-column-field-label">
                            <label v-if="!is_invisible(field)" :for="'wpuf-' + field.name ? field.name : 'cls'">
                                {{ field.label }} <span v-if="field.required && 'yes' === field.required" class="required">*</span>
                            </label>
                        </div>

                        <component v-if="is_template_available(field)" :is="'form-' + field.template" :field="field"></component>

                        <div v-if="is_pro_feature(field.template)" class="stage-pro-alert">
                            <label class="wpuf-pro-text-alert">
                                <a :href="pro_link" target="_blank"><strong>{{ get_field_name(field.template) }}</strong> <?php _e( 'is available in Pro Version', 'wp-user-frontend' ); ?></a>
                            </label>
                        </div>

                        <div class="wpuf-column-field-control-buttons">
                            <p>
                                <i class="fa fa-arrows move"></i>
                                <i class="fa fa-pencil" @click="open_column_field_settings(field, index, column)"></i>
                                <i class="fa fa-clone" @click="clone_column_field(field, index, column)"></i>
                                <i class="fa fa-trash-o" @click="delete_column_field(index, column)"></i>
                            </p>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>