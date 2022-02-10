<!-- WPDM Link Template: Tabbed Card -->


<div class="card link-template-tabcard">
    <div class="card-img-top">
        [thumb_800x400]
    </div>
    <div class="card-header  d-flex justify-content-between align-items-center">
        <h3 class="package-title" style="max-width: 60%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-size: 14px;margin-top: 10px">[title]</h3>
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" data-target="#desc" href="#desc">[txt=Description]</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" data-target="#info" href="#info">[txt=Package Info]</a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <div class="tab-pane active p-4" id="desc" role="tabpanel">[description]</div>
            <div class="tab-pane" id="info" role="tabpanel">
                <ul class="list-group list-group-flush ml-0 mb-2">
                    <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:version]">
                        [txt=Version]
                        <span class="badge">[version]</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:download_count]">
                        [txt=Download]
                        <span class="badge">[download_count]</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:file_size]">
                        [txt=File Size]
                        <span class="badge">[file_size]</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:file_count]">
                        [txt=File Count]
                        <span class="badge">[file_count]</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:create_date]">
                        [txt=Create Date]
                        <span class="badge">[create_date]</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:update_date]">
                        [txt=Last Updated]
                        <span class="badge">[update_date]</span>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        [download_link]
    </div>
</div>
