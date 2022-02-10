<div class="cff-fb-full-wrapper cff-fb-fs">
    <?php
        /**
         * CFF Admin Notices
         * 
         * @since 4.0
         */
        do_action('cff_admin_notices'); 
    ?>
    <div class="cff-oembeds-container">
        <div class="cff-section-header">
            <h3>{{genericText.title}}</h3>
            <p>{{genericText.description}}</p>
        </div>

        <div class="cff-oembed-plugin-box-group">
            <div class="cff-oembed-plugin-box cff-oembed-facebook">
                <span class="oembed-icon" v-html="images.fbIcon"></span>
                <span class="oembed-text" v-if="facebook.doingOembeds">{{genericText.facebookOEmbedsEnabled}}</span>
                <span class="oembed-text" v-else="facebook.doingOembeds">{{genericText.facebookOEmbeds}}</span>
                <span class="cff-oembed-btn">
                    <button v-if="facebook.doingOembeds" @click="disableFboEmbed()" class="cff-btn cff-btb disable-oembed" :class="{loading: fboEmbedLoader}">
                        <span v-if="fboEmbedLoader" v-html="loaderSVG"></span>
                        {{genericText.disable}}
                    </button>
                    <a v-else :href="connectionURL" class="cff-btn-blue cff-btn" :class="{loading: fboEmbedLoader}" @click="fboEmbedLoader = true">
                        <span v-if="fboEmbedLoader" v-html="loaderSVG"></span>
                        {{genericText.enable}}
                    </a>
                </span>
            </div>
            <div class="cff-oembed-plugin-box cff-oembed-instagram">
                <span class="oembed-icon" v-html="images.instaIcon"></span>
                <span class="oembed-text" v-if="instagram.doingOembeds">{{genericText.instagramOEmbedsEnabled}}</span>
                <span class="oembed-text" v-else="instagram.doingOembeds">{{genericText.instagramOEmbeds}}</span>
                <span class="cff-oembed-btn">
                    <button v-if="instagram.doingOembeds" @click="disableInstaoEmbed()" class="cff-btn disable-oembed" :class="{loading: instaoEmbedLoader}">
                        <span v-if="instaoEmbedLoader" v-html="loaderSVG"></span>
                        {{genericText.disable}}
                    </button>
                    <button v-else @click="InstagramShouldInstallOrEnable()" class="cff-btn cff-btn-blue" :class="{loading: instaoEmbedLoader}">
                        <span v-if="instaoEmbedLoader" v-html="loaderSVG"></span>
                        {{genericText.enable}}
                    </button>
                </span>
            </div>
        </div>

        <div class="cff-oembed-information">
            <div class="sb-box-header">
                <h3 v-if="isoEmbedsEnabled()">{{genericText.whatElseOembeds}}</h3>
                <h3 v-else>{{genericText.whatAreOembeds}}</h3>
            </div>
            <?php
                CustomFacebookFeed\CFF_View::render( 'oembeds.oembed_features' );
                CustomFacebookFeed\CFF_View::render( 'oembeds.plugin_info' );
            ?>
        </div>
    </div>
</div>
<?php
    CustomFacebookFeed\CFF_View::render( 'oembeds.modal' );
?>