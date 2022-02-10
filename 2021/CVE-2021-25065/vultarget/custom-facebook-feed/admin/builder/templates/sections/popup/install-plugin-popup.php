<script type="text/x-template" id="install-plugin-popup">
    <div class="cff-fb-source-ctn sb-fs-boss cff-fb-center-boss" v-if="viewsActive.installPluginPopup">
        <div class="cff-fb-source-popup cff-fb-popup-inside cff-install-plugin-modal">
            <div class="cff-fb-popup-cls" @click.prevent.default="$parent.activateView('installPluginPopup')">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
                </svg>
            </div>
            <div class="cff-install-plugin-body cff-fb-fs">
                <div class="cff-install-plugin-header">
                    <div class="sb-plugin-image" v-html="plugins.svgIcon"></div>
                    <div class="sb-plugin-name">
                        <h3>
                            {{plugins.name}}
                            <span>{{genericText.free}}</span>
                        </h3>
                        <p>
                            <span class="sb-author-logo">
                                <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.72226 4.70098C4.60111 4.19717 3.43332 3.44477 2.34321 3.09454C2.73052 4.01824 3.05742 5.00234 3.3957 5.97507C2.72098 6.48209 1.93286 6.8757 1.17991 7.30453C1.82065 7.93788 2.72809 8.3045 3.45109 8.85558C2.87196 9.57021 1.73414 10.3129 1.45689 10.9606C2.65579 10.8103 4.05285 10.5668 5.16832 10.5174C5.41343 11.7495 5.53984 13.1002 5.88845 14.2288C6.40758 12.7353 6.87695 11.192 7.49488 9.79727C8.44849 10.1917 9.61069 10.6726 10.5416 10.9052C9.88842 9.98881 9.29237 9.01536 8.71356 8.02465C9.57007 7.40396 10.4364 6.79309 11.2617 6.14122C10.0952 6.03375 8.88647 5.96834 7.66107 5.91968C7.46633 4.65567 7.5175 3.14579 7.21791 1.98667C6.76462 2.93671 6.2297 3.80508 5.72226 4.70098ZM6.27621 15.1705C6.12214 15.8299 6.62974 16.1004 6.55318 16.5C6.052 16.3273 5.67498 16.2386 5.00213 16.3338C5.02318 15.8194 5.48587 15.7466 5.3899 15.1151C-1.78016 14.3 -1.79456 1.34382 5.3345 0.546422C14.2483 -0.450627 14.528 14.9414 6.27621 15.1705Z" fill="#E34F0E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.21769 1.98657C7.51728 3.1457 7.46611 4.65557 7.66084 5.91955C8.88625 5.96824 10.0949 6.03362 11.2615 6.14113C10.4362 6.79299 9.56984 7.40386 8.71334 8.02454C9.29215 9.01527 9.8882 9.98869 10.5414 10.9051C9.61046 10.6725 8.44827 10.1916 7.49466 9.79716C6.87673 11.1919 6.40736 12.7352 5.88823 14.2287C5.53962 13.1001 5.41321 11.7494 5.16809 10.5173C4.05262 10.5667 2.65558 10.8102 1.45666 10.9605C1.73392 10.3128 2.87174 9.57012 3.45087 8.85547C2.72786 8.30438 1.82043 7.93778 1.17969 7.30443C1.93264 6.8756 2.72074 6.482 3.39547 5.97494C3.05719 5.00224 2.73031 4.01814 2.34299 3.09445C3.43308 3.44467 4.60089 4.19707 5.72204 4.70088C6.22947 3.80499 6.7644 2.93662 7.21769 1.98657Z" fill="white"/></svg>
                            </span>
                            <span class="sb-author-name">{{plugins.author}}</span>
                        </p>
                    </div>
                </div>
                <div class="cff-install-plugin-content">
                    <p v-html="plugins.description"></p>
                    <button v-if="plugins.installed && plugins.activated" class="cff-install-plugin-btn cff-btn-orange" :disabled="plugins.installed">{{genericText.installedAndActivated}}</button>
                    <button v-else-if="plugins.installed && !plugins.activated" @click.prevent.default="installOrActivatePlugin(plugins, plugins.plugin, 'cff_activate_addon')" class="cff-install-plugin-btn cff-btn-orange">
                        <span v-html="svgIcons.spinner" v-if="installerStatus !== null"></span>
                        {{genericText.activate}}
                    </button>
                    <button v-else @click.prevent.default="installOrActivatePlugin(plugins, plugins.download_plugin, 'cff_install_addon')" class="cff-install-plugin-btn cff-btn-orange">
                        <span v-html="svgIcons.spinner" v-if="installerStatus !== null"></span>
                        {{genericText.install}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>