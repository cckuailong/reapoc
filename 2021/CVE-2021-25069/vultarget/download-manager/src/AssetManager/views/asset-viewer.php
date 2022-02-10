<?php
        if(!defined("ABSPATH")) die();

        if(!$asset->hasAccess())
        {
            \WPDM\__\Messages::error(array('title' => 'Access Denied!', 'message' => __( "You do not have access to this asset", "download-manager" )), 1);
        }

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="https://cdn.wpdownloadmanager.com/wp-content/uploads/2019/07/download-manager-logo-only-clean-150x150.png" />
        <title><?php echo $asset->name; ?></title>
        <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Mono|IBM+Plex+Sans:400,600&display=swap" rel="stylesheet">
        <script src="<?php echo includes_url('js/jquery/jquery.js') ?>"></script>
        <style>

            *, *:before, *:after {
                box-sizing: inherit;
            }
            html {
                box-sizing: border-box;
                font-family: sans-serif;
            }
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                color: #637282;
                background: #F6F9FC;
                font-family: 'IBM Plex Sans', sans-serif;
            }

            .mb-1 {
                margin-bottom: 15px;
            }
            .mt-2 {
                margin-top: 25px;
            }

            /******* Nav *******/
            .nav {
                background: #fff;
                min-height: 60px;
                padding: 0 20px;
                border-bottom: 1px solid #f1f1f1;
                position: fixed;
                width: 100%;
                z-index: 999;
            }
            .nav__links {
                text-transform: capitalize;
                display: inline-block;
                float:right;
            }
            .nav__links a {
                text-decoration: none;
                color: #637282;
                font-size: 14px;
                padding: 10px 15px;
                line-height: 64px;
                letter-spacing: .5px;
                margin-right: 5px;
            }
            .nav__links a span {
                /* transform: scaleX(2) translateY(-4px); */
                display: inline-block;
                font-size: 30px;
                line-height: 0;
            }
            .nav__links a span svg {
                width: 20px;
                transform: translateY(6px);
            }
            .nav__logo {
                display: inline-block;
                padding: 15px 15px 15px 0;
            }
            .nav__logo .file-logo {
                width: 30px;
            }
            .file-names {
                max-width: 70%;
                display: inline-block;
                text-transform: capitalize;
            }
            .file-name {
                font-weight: 700;
                font-size: 14px;
                line-height: 15px;
            }
            .file-tag {
                font-size: 13px;
                margin-top: 7px;
            }
            label {
                font-size: 20px;
                display: none;
                width: 2rem;
                float: right;
                text-align: center;
                margin-top: 18px;
            }
            #toggle {
                display: none;
            }

            .nav__links .btn {
                border: 1px solid #e9e9e9;
                padding: 10px 22px;
                border-radius: 3px;
            }
            .btn.btn-success {
                background-color: #18CE0F;
                color: #fff;
                border: none;
            }

            /******* Sidebar *******/
            .sidenav {
                height: calc(100% - 60px);
                position: fixed;
                z-index: 1;
                top: 63px;
                right: 0;
                width: 300px;
                background-color: #EBF4FE;
                padding: 20px;
                transition: 0.3s;
                border-left: 1px solid #E3ECF6;
            }
            .sidenav a {
                cursor: pointer;
                position: relative;
                padding: 8px 0 17px;
                text-decoration: none;
                display: inline-block;
                transition: 0.3s;
                color: #637282;
                margin-right: 12px;
                text-transform: uppercase;
                letter-spacing: .5px;
                font-size: 12px;
            }
            .sidenav a:hover {
                color: #0DC304;
            }
            a.open-close-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 26px;
                margin-left: 50px;
                line-height: 0;
                width: 30px;
                height: 30px;
                text-align: center;
                text-decoration: none;
                color: #637282;
                padding: 5px 0;
            }

            /* Tabs */
            .buttons {
                margin-top: -12px;
                border-bottom: 1px solid #D1DAE4;
            }
            .buttons a.button--active::before {
                content: '';
                position: absolute;
                width: 100%;
                height: 2px;
                bottom: 0;
                background-color: #0DC304;
            }
            .tab-content {
                position: relative;
            }
            .tab {
                padding: 25px 0;
                width: 100%;
            }
            .file-names .file-name,
            .tab .file-tag {
                color: #233242;
                text-transform: capitalize;
            }
            .tab .file-name {
                font-weight: 500;
            }
            .tab form {
                position: relative;
            }

            [data-target] {
                position: absolute;
                visibility: hidden;
            }
            .button--active {
                color: #0DC304;
            }
            .tab--active {
                visibility: visible;
            }

            /* comments */
            input {
                padding: 16px 40px 16px 16px;
                font-size: 14px;
                width: 100%;
                border-radius: 4px;
                border: 1px solid #DCE5EF;
            }
            .submit {
                cursor: pointer;
                position: absolute;
                top: 16px;
                right: 14px;
                width: 18px;
            }
            input:focus::-webkit-input-placeholder {
                color: transparent;
            }
            .comment-content {
                padding-top: 15px;
            }
            .comment-media {
                font-size: 12px;
                margin-bottom: 18px;
                display: flex;
            }
            .comment-media .avatar{
                width: 48px;
                height: auto;
                border-radius: 500px;
                margin-right: 10px;
            }
            .comment-media .comment-data .comment-head{
                text-transform: capitalize;
            }
            .comment-media .comment-data .comment-head strong{
                font-size: 11pt;
            }
            .comment-media p{
                margin: 5px 0;
            }
            .file-comment > div {
                font-size: 13px;
                margin-bottom: 8px;
            }
            .file-comment span {
                background: #18CE0F;
                color: #fff;
                padding: 3px;
                border-radius: 30px;
                text-transform: uppercase;
                font-size: 12px;
                font-weight: 700;
                margin-right: 8px;
                width: 22px;
                display: inline-block;
            }

            /******* Main *******/
            #main {
                margin-right: 300px;
                padding: 80px 20px 20px;
                transition: margin-right 0.3s;
            }

            /******* Svg *******/
            .nav-svg {
                width: 12px;
                margin-right: 3px;
            }
            .nav-svg.arrow {
                width: 18px;
            }
            .nav-svg .color-white {
                fill: #fff;
            }
            .nav-svg.color-gray {
                fill: #637282;
            }

            /******* Media Queries *******/
            @media (max-width: 589.98px) {
                label {
                    display: block;
                    cursor: pointer;
                }
                .sidenav {
                    right: -300px;
                    overflow: visible;
                }
                .nav__links {
                    z-index: 2;
                    width: 100%;
                    display: none;
                    float: none;
                    background: white;
                    padding: 20px;
                    position: absolute;
                    border-top: 1px solid #eee;
                    transform: translateX(-18px);
                }
                .nav__links a {
                    line-height: 1;
                    display: block;
                    margin-top: 12px;
                }
                .nav__links a {
                    line-height: 1;
                    display: block;
                    margin-top: 12px;
                }
                #toggle:checked + .nav__links {
                    display: block;
                }
            }


            #asset-viewer{
                white-space: nowrap;
                font-family: 'IBM Plex Mono', monospace;
                font-size: 11pt;
            }

            #asset-viewer .wpdm-asset-video,
            #asset-viewer .wpdm-asset-audio,
            #asset-viewer .wpdm-asset-text{
                max-width: 960px;
                margin: 30px auto;
                padding: 50px;
                background: #ffffff;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                line-height: 1.5;
                overflow: hidden;
            }
            #asset-viewer .wpdm-asset-text .hljs{
                background: #ffffff;
                line-break: anywhere !important;
            }
            #asset-viewer .wpdm-asset-image {
                max-width: 80%;
                margin: 30px auto;
                padding: 30px;
            }
            #asset-viewer .wpdm-asset-image img{
                max-width: 100%;
                height: auto;
                margin: 0 auto;
                display: inherit;
            }
            #asset-viewer .wpdm-asset-video .wp-video{
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #asset-viewer .wpdm-asset-video,
            #asset-viewer .wpdm-asset-audio{
                padding: 28px 28px 20px !important;
            }
            #asset-viewer .wpdm-asset-video .wp-video video{
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                height: 100%;
            }
            .d-flex{
                display: flex;
                margin-bottom: 10px;
                font-size: 9pt;
                background: rgba(255, 255, 255, 0.81);
                padding: 15px;
                border-radius: 3px;
            }

            .d-flex .meta-name{
                font-weight: 400;
                width: 50%;
            }
            .d-flex .meta-value{
                width: 50%;text-align: right;color: #007eff;font-weight: bold;
            }

        </style>
    </head>
    <body>

        <!--==== Navbar ====-->
        <header class="nav">
            <div class="nav__logo">

                <svg class="file-logo" enable-background="new 0 0 222.2 229.6" version="1.1" viewBox="0 0 222.2 229.6" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                    <style type="text/css">
                        .st0{fill:#239CEF;}
                        .st1{fill:#094168;}
                    </style>
                    <path class="st0" d="m97.3 131.4-91.4-91c-7.8-7.7-7.9-20.4-0.1-28.2l5.7-5.7c7.8-7.8 20.5-7.9 28.3-0.1l71.5 71.2 71-71.7c7.8-7.8 20.4-7.9 28.3-0.1l5.7 5.6c7.8 7.8 7.9 20.4 0.1 28.3l-90.8 91.6c-7.7 7.9-20.4 7.9-28.3 0.1v0z"/>
                    <path class="st1" d="m134.1 154.1c-12.4 12.6-32.7 12.6-45.3 0.2l-43.8-43.7c-22.6 36.7-11.3 84.8 25.4 107.4s84.7 11.3 107.3-25.4c15.6-25.3 15.5-57.2-0.3-82.4l-43.3 43.9z"/>
                </svg>

            </div>
            <div class="file-names">
                <div class="file-name"><?php echo $asset->name; ?></div>
                <div class="file-tag">Modified on <?php echo date ("F d, Y", fileatime($asset->path)); ?></div>
            </div>
            <label for="toggle">&#9776;</label>
            <input type="checkbox" id="toggle">
            <div class="nav__links">
                <?php if(!is_user_logged_in()){ ?>
                <a class="btn btn-border" href="<?php echo wpdm_login_url($_SERVER['REQUEST_URI']) ?>">

                    <svg class="nav-svg color-white" enable-background="new 0 0 488 488" version="1.1" viewBox="0 0 488 488" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                        <path d="m398.85 216.6h-14.7v-76.5c0-77.2-62.8-140.1-140.1-140.1s-140.1 62.9-140.1 140.1v27.6c0 9.9 8.1 18 18 18s18-8.1 18-18v-27.6c0-57.4 46.7-104.1 104.1-104.1s104.1 46.7 104.1 104.1v76.5h-259c-24.6 0-44.7 20-44.7 44.7v182c0 24.6 20 44.7 44.7 44.7h270.5c9.9 0 18-8.1 18-18s-8.1-18-18-18h-270.5c-4.8 0-8.7-3.9-8.7-8.7v-182c0-4.8 3.9-8.7 8.7-8.7h309.7c4.8 0 8.7 3.9 8.7 8.7v182c0 9.9 8.1 18 18 18s18-8.1 18-18v-182c0-24.6-20-44.7-44.7-44.7z"/>
                        <path d="m287.15 334.3c-9.9 0-18 8.1-18 18 0 13.8-11.3 25.1-25.1 25.1s-25.1-11.3-25.1-25.1 11.3-25.1 25.1-25.1c9.9 0 18-8.1 18-18s-8.1-18-18-18c-33.7 0-61.1 27.4-61.1 61.1s27.4 61.1 61.1 61.1 61.1-27.4 61.1-61.1c0-9.9-8.1-18-18-18z"/>
                    </svg>
                    Sign in</a>
                <?php } ?>

                <a class="btn btn-success" href="<?php echo $asset->temp_download_url;  ?>">

                    <svg style="margin-bottom: -1px;" class="nav-svg color-gray" enable-background="new 0 0 490.1 490.1" version="1.1" viewBox="0 0 490.1 490.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                        <path  fill="#fff" d="m407.3 0.05h-162.3c-9.9 0-18 8.1-18 18v277.1l-39.8-39.8c-7-7-18.4-7-25.5 0-7 7-7 18.4 0 25.5l70.6 70.6c3.5 3.5 8.1 5.3 12.7 5.3s9.2-1.8 12.7-5.3l70.6-70.6c7-7 7-18.4 0-25.5-7-7-18.4-7-25.5 0l-39.8 39.8v-259.1h144.3c25.7 0 46.7 20.8 46.7 46.5v325c0 25.6-20.9 46.5-46.7 46.5h-324.6c-25.8 0-46.7-20.8-46.7-46.5v-325c0-25.7 20.9-46.5 46.7-46.5h42.1c9.9 0 18-8.1 18-18s-8.1-18-18-18h-42.1c-45.6 0-82.7 37-82.7 82.5v325c0 45.5 37.1 82.5 82.7 82.5h324.7c45.6 0 82.7-37 82.7-82.5v-325c-0.1-45.5-37.2-82.5-82.8-82.5z"/>
                    </svg>
                    Download</a>

                <!-- a href="#"><span>&#8230;</span></a>
                <a href="#">
                    <span>

                    <svg class="nav-svg color-gray" enable-background="new 0 0 510 510" version="1.1" viewBox="0 0 510 510" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                            <path d="m255 510c28.05 0 51-22.95 51-51h-102c0 28.05 22.95 51 51 51zm165.75-153v-140.25c0-79.05-53.55-142.8-127.5-160.65v-17.85c0-20.4-17.85-38.25-38.25-38.25s-38.25 17.85-38.25 38.25v17.85c-73.95 17.85-127.5 81.6-127.5 160.65v140.25l-51 51v25.5h433.5v-25.5l-51-51zm-51 25.5h-229.5v-165.75c0-63.75 51-114.75 114.75-114.75s114.75 51 114.75 114.75v165.75z"/>
                    </svg>

                    </span>
                </a -->
            </div>
        </header>

        <!--==== Sidebar ====-->
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="open-close-btn" onclick="closeNav()">
                <svg class="nav-svg arrow color-gray" aria-hidden="true" data-icon="arrow-to-right" data-prefix="far" focusable="false" role="img" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                    <path class="" d="M200.1 99.5l148.4 148c4.7 4.7 4.7 12.3 0 17l-148.4 148c-4.7 4.7-12.3 4.7-17 0l-19.6-19.6c-4.8-4.8-4.7-12.5.2-17.1l97.1-93.7H12c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h248.8l-97.1-93.7c-4.8-4.7-4.9-12.4-.2-17.1l19.6-19.6c4.7-4.9 12.3-4.9 17-.2zM396 76v360c0 6.6 5.4 12 12 12h28c6.6 0 12-5.4 12-12V76c0-6.6-5.4-12-12-12h-28c-6.6 0-12 5.4-12 12z" fill="currentColor"/>
                </svg>
            </a>
            <a href="javascript:void(0)" class="open-close-btn" onclick="openNav()" style="left: -90px">

                <svg class="nav-svg arrow color-gray" aria-hidden="true" data-icon="arrow-to-left" data-prefix="far" focusable="false" role="img" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                    <path class="" d="M247.9 412.5l-148.4-148c-4.7-4.7-4.7-12.3 0-17l148.4-148c4.7-4.7 12.3-4.7 17 0l19.6 19.6c4.8 4.8 4.7 12.5-.2 17.1L187.2 230H436c6.6 0 12 5.4 12 12v28c0 6.6-5.4 12-12 12H187.2l97.1 93.7c4.8 4.7 4.9 12.4.2 17.1l-19.6 19.6c-4.7 4.8-12.3 4.8-17 .1zM52 436V76c0-6.6-5.4-12-12-12H12C5.4 64 0 69.4 0 76v360c0 6.6 5.4 12 12 12h28c6.6 0 12-5.4 12-12z" fill="currentColor"/>
                </svg>
            </a>

            <!-- Tabs -->
            <nav class="buttons" data-buttons>
                <a class="tab-button button--active" data-trigger="tab-a">Comments</a>
                <a class="tab-button" data-trigger="tab-b">Asset Info</a>
            </nav>
            <div class="tab-content">
                <!-- Comment -->
                <div data-target="tab-a" class="tab tab--active">
                    <?php if(is_user_logged_in()){ ?>
                    <form action="">
                        <input id="commentcont" type="text" placeholder="Comment here..">

                        <svg id="newcomment" class="submit nav-svg arrow color-gray" enable-background="new 0 0 485.808 485.808" version="1.1" viewBox="0 0 485.81 485.81" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                            <path d="m472.22 13.597c-13.4-13.4-32.5-17.2-50-10l-393.7 162c-17.9 7.4-29.1 24.6-28.5 43.9 0.4 19.3 12.6 35.8 30.9 42.1l134.6 46c6 2.1 12.6 0.8 17.5-3.3l170.1-144.3c7.6-6.4 8.5-17.8 2.1-25.4s-17.8-8.5-25.4-2.1l-162.3 137.7-124.9-42.7c-5.9-2-6.7-7.1-6.8-9.1-0.1-2.1 0.4-7.2 6.2-9.5l393.7-162.1c5.6-2.3 9.5 0.8 10.9 2.2s4.5 5.3 2.2 10.9l-162 393.7c-2.4 5.8-7.5 6.2-9.5 6.2-2.1-0.1-7.1-0.8-9.1-6.8l-36.7-107.5c-3.2-9.4-13.4-14.4-22.9-11.2-9.4 3.2-14.4 13.4-11.2 22.9l36.7 107.5c6.3 18.3 22.8 30.5 42.1 31.1h1.5c18.7 0 35.3-11.1 42.5-28.5l162-393.7c7.2-17.5 3.4-36.6-10-50z"/>
                        </svg>
                    </form>
                    <?php } ?>
                    <div class="comment-content" id="comment-contents">
                        <?php
                        if(is_array($asset->comments)){

                        foreach ($asset->comments as $comment){ $comment = (array)$comment; ?>
                        <div class="comment-media">
                            <div class="avatar"><?php echo $comment['avatar']; ?></div>
                            <div class="comment-data">
                                <div class="comment-head"><strong><?php echo $comment['name']; ?></strong> - <?php echo date(get_option('date_format'), $comment['time']); ?></div>
                                <?php echo wpautop($comment['comment']); ?>
                            </div>
                        </div>
                        <?php }} ?>
                    </div>
                </div>

                <!-- About -->
                <div data-target="tab-b" class="tab">
                    <div class="d-flex"><div class="meta-name">File Size</div><div class="meta-value"><?php echo $asset->size ?></div></div>
                    <?php
                    $mime = wp_check_filetype($asset->path);
                    ?>
                    <div class="d-flex"><div class="meta-name">Content Type</div><div class="meta-value"><?php echo wpdm_valueof($mime, 'type') ?></div></div>

                    <?php
                    if(is_array($asset->metadata)) {
                        foreach ($asset->metadata as $name => $value) { ?>
                            <div class="d-flex">
                                <div class="meta-name"><?php echo $name; ?></div>
                                <div class="meta-value"><?php echo $value; ?></div>
                            </div>
                        <?php }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!--==== Content ====-->
        <div id="main">
            <div id="asset-viewer">
                <?php
                echo $asset->view($asset->path); ?>
            </div>
<?php //wpdmprecho($asset); ?>
        </div>


        <script>

            //=========== SideBar
            var sidebar = document.getElementById("mySidenav");
            var main = document.getElementById("main");

            function openNav() {
                // Sidebar
                sidebar.style.right = "0";
                sidebar.style.overflow = "hidden";
                // Main
                main.style.marginRight = "300px";
                main.style.paddingRight = "20px";
            }

            function closeNav() {
                // Sidebar
                sidebar.style.right = "-300px";
                sidebar.style.overflow = "visible";
                // Main
                main.style.marginRight = "0";
                main.style.paddingRight = "50px";
            }

            //=========== Tabs
            const allBtns = [...document.querySelectorAll('[data-trigger]')]
            const allTargets = [...document.querySelectorAll('[data-target]')]

            document.querySelector('[data-buttons]').addEventListener('click', function(e) {

                const clickedEl = e.target
                if (clickedEl.hasAttribute('data-buttons')) return
                if (clickedEl.classList.contains('button--active')) return

                allBtns.forEach(i => i.classList.remove('button--active'))
                allTargets.forEach(i => i.classList.remove('tab--active'))

                clickedEl.classList.add('button--active')

                const target = document.querySelector(`[data-target=${e.target.dataset.trigger}]`)
                target.classList.toggle('tab--active')
            });

            jQuery(function ($) {
                <?php if(is_user_logged_in()){ ?>
                $('#newcomment').on('click', function (e) {
                    e.preventDefault();
                    var filepath = $(this).data('path');
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {__wpdm_addcomment:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_addcomment', comment: $('#commentcont').val(), assetid: '<?php echo $asset->ID; ?>' }, function (data) {
                        var comment = data[0];
                        $('#commentcont').val('');
                        $('#comment-contents').prepend('<div class="comment-media">\n' +
                            '                            <div class="avatar">'+comment.avatar+'</div>\n' +
                            '                            <div class="comment-data">\n' +
                            '                                <div class="comment-head"><strong>'+comment.name+'</strong> - Just now</div>\n' +
                            '                                <p>'+comment.comment+'</p>\n' +
                            '                            </div>\n' +
                            '                        </div>');

                    });
                });
                <?php } ?>
            });


        </script>
    </body>
</html>
