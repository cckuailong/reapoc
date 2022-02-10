<div class="cff-fb-full-wrapper cff-fb-fs">
    <?php
        /**
         * CFF Admin Notices
         *
         * @since 4.0
         */
        do_action('cff_admin_notices');
    ?>
    <div class="section-header">
        <h1>{{genericText.settings}}</h1>
    </div>

    <div class="sb-tabs-container" id="sb-tabs-container">
        <form action="">
            <div class="sb-tabs">
                <div class="left-buttons">
                    <tab v-bind:section="section" v-bind:index="index" v-for="(section, index) in sections" v-bind:class="{ active : section === currentTab }" v-bind:data-index="index+1" key="index"></tab>
                </div>
                <div class="right-buttons">
                    <button class="cff-btn sb-btn-orange" @click.prevent="saveSettings" :disabled="btnStatus !== null">
                        <span v-html="saveChangesIcon()"></span>
                        {{genericText.saveSettings}}
                    </button>
                </div>
                <span class="tab-indicator" v-bind:style="getStyle"></span>
            </div>
            <transition :name="chooseDirection">
                <div class="sb-tab-content" v-bind:is="selected" :class="'cff-license-' + licenseType">
                    <?php
                        CustomFacebookFeed\CFF_View::render( 'settings.tab.general' );
                        CustomFacebookFeed\CFF_View::render( 'settings.tab.feeds' );
                        CustomFacebookFeed\CFF_View::render( 'settings.tab.translation' );
                        CustomFacebookFeed\CFF_View::render( 'settings.tab.advanced' );
                    ?>
                </div>
            </transition>
            <div class="cff-save-button">
                <button class="cff-btn sb-btn-orange" @click.prevent="saveSettings" :disabled="btnStatus !== null">
                    <span v-html="saveChangesIcon()"></span>
                    {{genericText.saveSettings}}
                </button>
            </div>

            <div v-if="licenseType == 'free'" class="cff-settings-cta">
                <?php
                    CustomFacebookFeed\CFF_View::render( 'sections.settings_footer_cta' );
                ?>
            </div>
        </form>
    </div>
</div>
<?php
    include_once CFF_BUILDER_DIR . 'templates/sections/popup/add-source-popup.php';
    include_once CFF_BUILDER_DIR . 'templates/sections/popup/confirm-dialog-popup.php';
    include_once CFF_BUILDER_DIR . 'templates/sections/popup/source-instances.php';
?>
<div class="sb-notification-ctn" :data-active="notificationElement.shown" :data-type="notificationElement.type">
	<div class="sb-notification-icon" v-html="svgIcons[notificationElement.type+'Notification']"></div>
	<span class="sb-notification-text" v-html="notificationElement.text"></span>
</div>

<sb-add-source-component
:sources-list="sourcesList"
:select-source-screen="selectSourceScreen"
:views-active="viewsActive"
:generic-text="genericText"
:selected-feed="selectedFeed"
:svg-icons="svgIcons"
:links="links"
ref="addSourceRef"
>
</sb-add-source-component>

<sb-confirm-dialog-component
:dialog-box.sync="dialogBox"
:source-to-delete="sourceToDelete"
:svg-icons="svgIcons"
:parent-type="'settings'"
:generic-text="genericText"
></sb-confirm-dialog-component>