<div class="cff-fb-full-wrapper cff-fb-fs">
    <?php
        /**
         * CFF Admin Notices
         * 
         * @since 4.0
         */
        do_action('cff_admin_notices'); 
    ?>
    <div class="cff-extensions-container">
        <div class="cff-section-header">
            <h2>{{genericText.title}}</h2>
            <p>{{genericText.description}}</p>
        </div>

        <div class="cff-extensions-boxes-container">
            <div class="sb-extensions-box" v-for="(extension, name, index) in extensions">
                <span class="icon" v-html="extension.icon"></span>
                <h4 class="sb-box-title">{{extension.title}}</h4>
                <p class="sb-box-description">{{extension.description}}</p>
                <div class="sb-action-buttons">
                    <a class="cff-btn sb-btn-add" v-if="!extension.installed" :href="extension.permalink" target="_blank">
                        <span v-html="icons.plusIcon"></span>
                        {{buttons.add}}
                    </a>
                    <button class="cff-btn sb-btn-installed" v-if="extension.installed">
                        <span v-html="icons.checkmarkSVG"></span>
                        {{buttons.installed}}
                    </button>
                    <button class="cff-btn sb-btn-activate" v-if="extension.installed && ! extension.activated" @click="activatePlugin(extension.plugin, name, index, 'extension')" :class="btnStatus">
                        <span v-html="buttonIcon()" v-if="btnClicked == index + 1 && btnName == name"></span>
                        {{buttons.activate}}
                    </button>
                    <button class="cff-btn sb-btn-deactivate" v-if="extension.installed && extension.activated" @click="deactivatePlugin(extension.plugin, name, index, 'extension')" :class="btnStatus">
                        <span v-html="buttonIcon()" v-if="btnClicked == index + 1 && btnName == name"></span>
                        {{buttons.deactivate}}
                    </button>
                </div>
            </div>
        </div>

        <div class="cff-section-second-header">
            <h3>{{genericText.title2}}</h3>
            <p>{{genericText.description2}}</p>
        </div>

        <div class="cff-plugins-boxes-container">
            <div class="sb-plugins-box" v-for="(plugin, name, index) in plugins">
                <div class="icon"><img :src="plugin.icon" :alt="plugin.title"></div>
                <div class="plugin-box-content">
                    <h4 class="sb-box-title">{{plugin.title}}</h4>
                    <p class="sb-box-description">{{plugin.description}}</p>
                    <div class="sb-action-buttons">
                        <button class="cff-btn sb-btn-install" v-if="!plugin.installed" @click="installPlugin(plugin.download_plugin, name, index)">
                            <span v-html="buttonIcon()" v-if="btnClicked == index + 1 && btnName == name"></span>
                            {{buttons.install}}
                        </button>
                        <button class="cff-btn sb-btn-installed" v-if="plugin.installed">
                            <span v-html="icons.checkmarkSVG"></span>
                            {{buttons.installed}}
                        </button>
                        <a class="cff-btn sb-btn-activate" v-if="plugin.installed && plugin.open" :href="plugin.dashboard_permalink" :class="btnStatus">
                            {{buttons.open}}
                        </a>
                        <button class="cff-btn sb-btn-activate" v-if="plugin.installed && !plugin.activated && !plugin.open" @click="activatePlugin(plugin.plugin, name, index, 'plugin')" :class="btnStatus">
                            <span v-html="buttonIcon()" v-if="btnClicked == index + 1 && btnName == name"></span>
                            {{buttons.activate}}
                        </button>
                        <button class="cff-btn sb-btn-deactivate" v-if="plugin.installed && plugin.activated && name != 'facebook'" @click="deactivatePlugin(plugin.plugin, name, index, 'plugin')" :class="btnStatus">
                            <span v-html="buttonIcon()" v-if="btnClicked == index + 1 && btnName == name"></span>
                            {{buttons.deactivate}}
                        </button>
                    </div>
                </div>
            </div>

            <div class="sb-plugins-box cff-social-wall-plugin-box">
                <span class="sb-box-bg-image">
                    <img :src="social_wall.graphic">
                </span>
                <div class="plugin-box-content">
                    <h4 class="sb-box-title">{{social_wall.title}}</h4>
                    <p class="sb-box-description">{{social_wall.description}}</p>
                    <div class="sb-action-buttons">
                        <a class="cff-btn sb-btn-install" v-if="!social_wall.installed" :href="social_wall.permalink" target="_blank">
                            {{buttons.viewDemo}}
                            <span v-html="icons.link"></span>
                        </a>
                        <button class="cff-btn sb-btn-installed" v-if="social_wall.installed">
                            <span v-html="icons.checkmarkSVG"></span>
                            {{buttons.installed}}
                        </button>
                        <button class="cff-btn sb-btn-activate" v-if="social_wall.installed && ! social_wall.activated" @click="activatePlugin(social_wall.plugin, 'social_wall', 1, 'plugin')" :class="btnStatus">
                            <span v-html="buttonIcon()" v-if="btnClicked == 2 && btnName == 'social_wall'"></span>
                            {{buttons.activate}}
                        </button>
                        <button class="cff-btn sb-btn-deactivate" v-if="social_wall.installed && social_wall.activated" @click="deactivatePlugin(social_wall.plugin, 'social_wall', 1, 'plugin')" :class="btnStatus">
                            <span v-html="buttonIcon()" v-if="btnClicked == 2 && btnName == 'social_wall'"></span>
                            {{buttons.deactivate}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>