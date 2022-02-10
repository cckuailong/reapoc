<?php if(!defined('ABSPATH')) die(); ?>

<style>
    #logo-block img{
        box-shadow: none;
        width:128px;
        height: 128px;
        border-radius: 500px;
        background: #ffffff;
    }

    #wpdm-user-profile{
        padding-top: 90px;
    }
    #wpdm-user-profile .profile-card{
        border-radius: 5px;
        color: <?php echo (isset($store['txtcolor']) && !empty($store['txtcolor']) ? $store['txtcolor'] : '#333333'); ?> !important;
    <?php if($store['banner'] !== ''){ ?>
        background: linear-gradient(45deg, rgba(<?php echo $rgb; ?>,0.5), rgba(<?php echo $rgb ?>,0.95)), <?php echo $store['bgcolor']; ?> url("<?php echo $store['banner']; ?>");
        background-size: cover;
        background-repeat: repeat-x;
        background-position: center;
    <?php } else { ?>
        background: <?php echo $store['bgcolor']; ?>;
    <?php } ?>
    }
    #wpdm-user-profile .store-intro{
        opacity: 0.7;
    }
    #profile-buttons .profile-button{
        color: #ffffff;
        background: rgba(0,0,0,0.2);
        font-size: 12px;
        letter-spacing: 1px;
        border-radius: 200px;
        padding: 5px 15px;
        margin: 10px 3px;
        text-decoration: none;
        transition: all ease-in-out 300ms;
    }
    #profile-buttons .profile-button:not(.active):hover{
        background: rgba(0,0,0,0.5);
    }
    #profile-buttons .profile-button.active{
        background: rgba(0,0,0,0.6);
    }
    #wpdm-profile-contents{
        min-height: 400px;
    }
</style>


<div class="w3eden" id="wpdm-user-profile">
    <div class="row">

        <div class="col-md-12">
            <div class="profile-card p-4 text-center bg-light">

                <div class="profile-card-inner">
                    <div id="logo-block">
                        <img class="shop-logo m-0 p-0 box-shadow-none" id="shop-logo" src="<?php echo isset($store['logo']) && $store['logo'] != '' ? $store['logo'] : get_avatar_url( $current_user->user_email, array('size' => 512) ); ?>"/>
                    </div>
                    <h2 class="mt-4 mb-0" id="profile-title"><?php echo $store['title']; ?></h2>
                    <?php echo isset($store['intro']) && !empty($store['intro']) ? "<div class='mt-2 mb-3 store-intro'>{$store['intro']}</div>":""; ?>
                    <div class="text-small mb-3"><?php echo $store['description']; ?></div>
                    <div id="profile-buttons" class="mt-4">
                        <?php
                        foreach ($this->profile_menu as $id => $menu){
                            if(isset($menu['content']) && !empty($menu['content'])) {
                                ?>
                                <a class="profile-button with-content profile-button-<?php echo $id; ?>" data-menu="<?php echo $id; ?>" id="profile-button-<?php echo $id; ?>" href="#<?php echo $id; ?>">
                                    <i class="<?php echo $menu['icon'] ?> mr-2"></i><?php echo $menu['name']; ?>
                                </a>
                                <?php
                            } else {
                                ?>
                                <a class="profile-button instant profile-button-<?php echo $id; ?>" data-menu="<?php echo $id; ?>" id="profile-button-<?php echo $id; ?>" href="#">
                                    <i class="<?php echo $menu['icon'] ?> mr-2"></i><?php echo $menu['name']; ?>
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12 pt-4" id="wpdm-profile-contents">


        </div>
    </div>
    <?php
    foreach ($this->profile_menu as $id => $menu){
        if(isset($menu['static_content']))
            call_user_func($menu['static_content']);
    }
    ?>
</div>

<script>
    jQuery(function ($) {
        $('.profile-button.with-content').on('click', function () {
            $('.profile-button').removeClass('active');
            $(this).addClass('active');
            var cc = $(this).children('fa').attr('class');
            $(this).children('fa').removeClass(cc).addClass('fa fa-sun fa-spin');
            var abtn = $(this);
            WPDM.blockUI('#wpdm-profile-contents');
            $.get(wpdm_url.ajax, { action: 'wpdm_get_profile_menu_content', __pu: '<?php echo $user_ID; ?>', __pmenu: $(this).data('menu'), __scp: '<?php echo \WPDM\__\Crypt::encrypt($params); ?>'}, function (response) {
                $('#wpdm-profile-contents').html(response);
                WPDM.unblockUI('#wpdm-profile-contents');
                abtn.attr('class', cc);
            });
        });
        $('.profile-button.instant').on('click', function (e) {
            e.preventDefault();
            var menu = $(this).data('menu');
        });
        var hash = location.hash.substr(1);
        WPDM.blockUI('#wpdm-profile-contents');
        $.get(wpdm_url.ajax, { action: 'wpdm_get_profile_menu_content', __pu: '<?php echo $user_ID; ?>', __pmenu: (hash ? hash : '<?php echo $first_menu; ?>'), __scp: '<?php echo \WPDM\__\Crypt::encrypt($params); ?>'}, function (response) {
            $('#wpdm-profile-contents').html(response);
            $('#profile-button-'+(hash ? hash : '<?php echo $first_menu; ?>')).addClass('active');
            WPDM.unblockUI('#wpdm-profile-contents');
        });
    });
</script>

