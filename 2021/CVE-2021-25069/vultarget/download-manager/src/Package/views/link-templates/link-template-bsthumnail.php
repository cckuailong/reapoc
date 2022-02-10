<!-- WPDM Link Template: List Group -->

    <div class="list-group wpdm-lt-card" style="margin: 0 0 15px 0">
        <a href="[page_url]" class="d-block">
            <?php wpdm_thumb($ID, [500, 400], true, ['class' => 'card-img-top']) ?>
        </a>

        <div class="list-group-item d-flex justify-content-between align-items-center">
            <h3 class="p-0 m-0 elipsis">[page_link]</h3>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-cente">
            File Size <span class="badge">[file_size]</span>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-cente">
            Downloads <span class="badge">[download_count]</span>
        </div>
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <?php //echo self::downloadLink($ID, 0, ['btnclass' => 'btn btn-block btn-primary', 'template_type' => 'link']); ?>
            [download_link]
        </div>
    </div>


