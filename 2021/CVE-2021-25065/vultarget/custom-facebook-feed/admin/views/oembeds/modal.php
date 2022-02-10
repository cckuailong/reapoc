<div class="cff-oembed-modal" v-if="openInstaInstaller">
    <div class="cff-modal-content">
        <button type="button" class="cancel-btn cff-btn" v-html="modal.timesIcon" @click="closeModal"></button>
        <div class="modal-icon">
            <img :src="modal.instaIcon" :alt="modal.title">
        </div>
        <h2>{{modal.title}}</h2>
        <p>{{modal.description}}</p>
        <div class="sb-action-buttons">
            <button type="button" class="cff-btn cff-install-btn" @click="installInstagram()" :class="installerStatus" :disabled="isIntagramActivated">
                <span v-html="installIcon()"></span>
                <span v-html="instagramInstallBtnText"></span>
            </button>
            <button type="button" class="cff-btn" @click="closeModal" v-if="!isIntagramActivated">{{modal.cancel}}</button>
        </div>
    </div>
</div>