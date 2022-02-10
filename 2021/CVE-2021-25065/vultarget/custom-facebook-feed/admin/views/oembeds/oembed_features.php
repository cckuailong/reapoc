<div v-if="!isoEmbedsEnabled()">
    <div class="sb-two-column-box sb-two-column-box-1">
        <div class="sb-left">
            <img :src="images.image1_2x" alt="">
        </div>
        <div class="sb-right sb-embed-info-text">
            <h4>{{genericText.whenYouPaste}}</h4>
        </div>
    </div>
    <div class="sb-two-column-box sb-two-column-box-2">
        <div class="sb-left sb-embed-info-text">
            <h4>{{genericText.dueToRecent}}</h4>
        </div>
        <div class="sb-right">
            <img :src="images.image2_2x" alt="">
        </div>
    </div>
    <div class="sb-one-column-box">
        <h4>{{genericText.however}}</h4>
        <p>{{genericText.justEnable}}</p>
        <img :src="images.image3_2x" alt="">
    </div>
</div>