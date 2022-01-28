<?php
/** no direct access **/
defined('MECEXEC') or die();
wp_enqueue_style('mec-lity-style', $this->main->asset('packages/lity/lity.min.css'));
wp_enqueue_script('mec-lity-script', $this->main->asset('packages/lity/lity.min.js'));
?>
<div id="webnus-dashboard" class="wrap about-wrap mec-addons">
    <div class="welcome-head w-clearfix">
        <div class="w-row">
            <div class="w-col-sm-9">
                <h1> <?php echo __('Addons', 'modern-events-calendar-lite'); ?> </h1>
            </div>
            <div class="w-col-sm-3">
                <?php $styling = $this->main->get_styling(); $darkadmin_mode = ( isset($styling['dark_mode']) ) ? $styling['dark_mode'] : ''; if ($darkadmin_mode == 1): $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w2.png'; else: $darklogo = plugin_dir_url(__FILE__ ) . '../../../assets/img/mec-logo-w.png'; endif; ?>
                <img src="<?php echo $darklogo; ?>" />
                <span class="w-theme-version"><?php echo __('Version', 'modern-events-calendar-lite'); ?> <?php echo MEC_VERSION; ?></span>
            </div>
        </div>
    </div>
    <div class="welcome-content w-clearfix extra">
    <?php if(current_user_can('read')): ?>
        <?php
        $data_url = 'https://webnus.net/modern-events-calendar/addons-api/addons-api.json';
        
        
        if(function_exists('file_get_contents') && ini_get('allow_url_fopen') )
        {
            $get_data = file_get_contents($data_url);
            if ( $get_data !== false AND !empty($get_data) )
            {
                $obj = json_decode($get_data);
                $i = count((array)$obj);
            }
        }
        elseif ( function_exists('curl_version') )
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $data_url);
            $result = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($result);
            $i = count((array)$obj);
        } else {
            $obj = '';
        }
        ?>
        <div class="w-row">
        <?php if ( !empty( $obj ) ) :  ?>
        <?php foreach ($obj as $key => $value) : ?>
            <div class="w-col-sm-3">
                <div class="w-box addon">
                    <div class="w-box-child mec-addon-box">
                        <div class="mec-addon-box-head">
                            <?php
                                $each_addons_url    = 'http://webnus.biz/webnus.net/addons-api/version?item_name=' . urlencode($value->name);
                                $addons_convert     = @file_get_contents($each_addons_url);
                                $addons_json        = json_decode($addons_convert, true);
                            ?>
                            <div class="mec-addon-box-title"><img src="<?php esc_html_e($value->img); ?>" /><span><?php esc_html_e($value->name); ?></span></div>
                            <?php if ( $value->comingsoon == 'false' ) : ?> 
                                <div class="mec-addon-box-version"><span><?php esc_html_e('Version', 'modern-events-calendar-lite'); ?> <strong><?php esc_html_e($addons_json['version']); ?></strong></span></div>
                                <?php if ( $value->pro == 'true' ) : ?>
                                    <div class="mec-addon-box-pro">Requires Pro Version</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mec-addon-box-body">
                            <div class="mec-addon-box-content">
                                <p><?php esc_html_e($value->desc); ?></p>
                            </div>
                        </div>
                        <div class="mec-addon-box-footer">
                            <?php if ( $value->comingsoon == 'false' ) : ?> 
                            <a class="mec-addon-box-intro" href="<?php esc_html_e($value->video); ?>" data-lity=""><i class="mec-sl-control-play"></i><div class="wn-p-t-right"><div class="wn-p-t-text-content"><h5>Introduction Video</h5>Watch to learn more about the features</div><i></i></div></a>
                            <a class="mec-addon-box-purchase" href="<?php esc_html_e($value->page); ?>" target="_blank"><i class="mec-sl-link"></i><div class="wn-p-t-right"><div class="wn-p-t-text-content"><h5>Details</h5>Learn everything about this addon</div><i></i></div></a>
                            <a class="mec-addon-box-purchase" href="<?php esc_html_e($value->purchase); ?>" target="_blank"><i class="mec-sl-basket"></i><div class="wn-p-t-right"><div class="wn-p-t-text-content"><h5>Add To Cart</h5>Add to cart and continue purchasing</div><i></i></div></a>
                            <?php else : ?>
                            <div class="mec-addon-box-comingsoon"><?php esc_html_e('Coming Soon', 'modern-events-calendar-lite'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
            <div class="w-col-sm-12">
                <div class="addons-page-error">
                    <p>
                    <?php echo __( '<strong>"file_get_contents"</strong> and <strong>"Curl"</strong> functions are <strong>not activated</strong> on your server. Please contact your host provider in this regard.', 'modern-events-calendar-lite'); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>
</div>