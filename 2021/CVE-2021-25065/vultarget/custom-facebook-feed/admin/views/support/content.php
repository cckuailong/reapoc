<div class="cff-fb-full-wrapper cff-fb-fs">
    <?php
        /**
         * CFF Admin Notices
         * 
         * @since 4.0
         */
        do_action('cff_admin_notices'); 
    ?>
    <div class="cff-sb-container">
        <div class="cff-section-header">
            <h2>{{genericText.title}}</h2>
            <div class="cff-search-doc">
                <div :href="links.doc" target="_blank" class="cff-search-doc-field">
                    <span class="sb-btn-icon" @click="goToSearchDocumentation()" v-html="icons.magnify"></span>
                    <input class="sb-btn-input" id="cff-search-doc-input" v-model="searchKeywords" v-on:keyup="searchDoc" v-on:paste="searchDocStrings" :placeholder="buttons.searchDoc">
                    <span class="sb-btn-link-icon" @click="goToSearchDocumentation()" v-html="icons.linkIcon"></span>
                </div>
            </div>
        </div>

        <div class="cff-support-blocks clearfix">
            <div class="cff-support-block">
                <div class="sb-block-header">
                    <img :src="icons.rocket" :alt="genericText.gettingStarted">
                </div>
                <h3>{{genericText.gettingStarted}}</h3>
                <p>{{genericText.someHelpful}}</p>
                <div class="sb-articles-list">
                    <ul>
                        <li v-for="article in articles.gettingStarted">
                            <a :href="article.link" target="_blank">
                                {{article.title}}
                                <span class="sb-list-icon" v-html="icons.rightAngle"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="cff-sb-button">
                    <a :href="links.gettingStarted" target="_blank">
                        {{buttons.moreHelp}}
                        <span class="sb-btn-icon" v-html="icons.rightAngle"></span>
                    </a>
                </div>
            </div>
            <div class="cff-support-block">
                <div class="sb-block-header">
                    <img :src="icons.book" :alt="genericText.docsN">
                </div>
                <h3>{{genericText.docsN}}</h3>
                <p>{{genericText.runInto}}</p>
                <div class="sb-articles-list">
                    <ul>
                        <li v-for="article in articles.docs">
                            <a :href="article.link" target="_blank">
                                {{article.title}}
                                <span class="sb-list-icon" v-html="icons.rightAngle"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="cff-sb-button">
                    <a :href="links.doc" target="_blank">
                        {{buttons.viewDoc}}
                        <span class="sb-btn-icon" v-html="icons.rightAngle"></span>
                    </a>
                </div>
            </div>
            <div class="cff-support-block">
                <div class="sb-block-header">
                    <img :src="icons.save" :alt="genericText.additionalR">
                </div>
                <h3>{{genericText.additionalR}}</h3>
                <p>{{genericText.toHelp}}</p>
                <div class="sb-articles-list">
                    <ul>
                        <li v-for="article in articles.resources">
                            <a :href="article.link" target="_blank">
                                {{article.title}}
                                <span class="sb-list-icon" v-html="icons.rightAngle"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="cff-sb-button">
                    <a :href="links.blog" target="_blank">
                        {{buttons.viewBlog}}
                        <span class="sb-btn-icon" v-html="icons.rightAngle"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="cff-support-contact-block clearfix">
            <div class="sb-contact-block-left">
                <div class="sb-cb-icon">
                    <span v-html="icons.forum"></span>
                </div>
                <div class="sb-cb-content">
                    <h3>{{genericText.needMore}}</h3>
                    <a :href="supportUrl" target="_blank" class="sb-cb-btn">
                        {{buttons.submitTicket}}
                        <span v-html="icons.rightAngle"></span>
                    </a>
                </div>
            </div>
            <div class="sb-contact-block-right">
                <div>
                    <img :src="images.supportMembers">
                </div>
                <p>{{genericText.ourFast}}</p>
            </div>
        </div>

        <div class="cff-system-info-section">
            <div class="cff-system-header">
                <h3>{{genericText.systemInfo}}</h3>
                <button class="cff-copy-btn" @click.stop.prevent="copySystemInfo">
                    <span v-html="icons.copy"></span>
                    <span v-html="buttons.copy"></span>
                </button>
            </div>
            <div class="cff-system-info">
                <div v-html="system_info" id="system_info" class="system_info" :class="systemInfoBtnStatus"></div>
                <button class="cff-expand-btn" @click="expandSystemInfo"> 
                    <span v-html="icons.downAngle"></span> 
                    <span v-html="expandBtnText()"></span>
                </button>
            </div>
        </div>
        <div class="cff-export-settings-section">
            <div class="cff-export-left">
                <h3>{{genericText.exportSettings}}</h3>
                <p>{{genericText.shareYour}}</p>
            </div>
            <div class="cff-export-right">
                <select name="" id="cff-feeds-list" class="cff-select" v-model="exportFeed" ref="export_feed">
                    <option value="none" selected disabled>Select Feed</option>
                    <option v-for="feed in feeds" :value="feed.id">{{ feed.name }}</option>
                </select>
                <button type="button" class="cff-btn sb-btn-grey" @click="exportFeedSettings" :disabled="exportFeed === 'none'">
                    <span v-html="icons.exportSVG"></span>
                    {{buttons.export}}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="sb-notification-ctn" :data-active="notificationElement.shown" :data-type="notificationElement.type">
	<div class="sb-notification-icon" v-html="svgIcons[notificationElement.type+'Notification']"></div>
	<span class="sb-notification-text" v-html="notificationElement.text"></span>
</div>