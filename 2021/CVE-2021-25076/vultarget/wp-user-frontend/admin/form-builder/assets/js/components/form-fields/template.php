<div class="wpuf-form-builder-form-fields">
    <template v-for="(section, index) in panel_sections">
        <div v-if="section.fields.length" class="panel-form-field-group clearfix">
            <h3 class="clearfix" @click="panel_toggle(index)">
                {{ section.title }} <i :class="[section.show ? 'fa fa-angle-down' : 'fa fa-angle-right']"></i>
            </h3>

            <transition name="slide-fade">
                <ul
                    v-show="section.show"
                    class="panel-form-field-buttons clearfix"
                    :id="'panel-form-field-buttons-' + section.id"
                >
                    <template v-for="field in section.fields">
                        <li
                            v-if="is_pro_feature(field)"
                            class="button button-faded"
                            :data-form-field="field"
                            data-source="panel"
                            @click="alert_pro_feature(field)"
                        >
                            <i v-if="field_settings[field].icon" :class="['fa fa-' + field_settings[field].icon]" aria-hidden="true"></i> {{ field_settings[field].title }}
                        </li>

                        <li
                            v-if="is_failed_to_validate(field)"
                            :class="['button', get_invalidate_btn_class(field)]"
                            :data-form-field="field"
                            data-source="panel"
                            @click="alert_invalidate_msg(field)"
                        >
                            <i v-if="field_settings[field].icon" :class="['fa fa-' + field_settings[field].icon]" aria-hidden="true"></i> {{ field_settings[field].title }}
                        </li>

                        <li
                            v-if="!is_pro_feature(field) && !is_failed_to_validate(field)"
                            class="button"
                            :data-form-field="field"
                            data-source="panel"
                            @click="add_form_field(field)"
                        >
                            <i v-if="field_settings[field].icon" :class="['fa fa-' + field_settings[field].icon]" aria-hidden="true"></i> {{ field_settings[field].title }}
                        </li>
                    </template>
                </ul>
            </transition>
        </div>
    </template>
</div>
