<?php
/**
 * Don't change it, it's supporting modal in other place
 * if get_the_ID() empty, then it's means we are passing $post variable from another place
 */
if (get_the_ID())
	global $post;

$video = maybe_unserialize(get_post_meta($post->ID, '_video', true));

$videoSource = tutor_utils()->avalue_dot('source', $video);
$runtimeHours = tutor_utils()->avalue_dot('runtime.hours', $video);
$runtimeMinutes = tutor_utils()->avalue_dot('runtime.minutes', $video);
$runtimeSeconds = tutor_utils()->avalue_dot('runtime.seconds', $video);
$sourceVideoID = tutor_utils()->avalue_dot('source_video_id', $video);
$poster = tutor_utils()->avalue_dot('poster', $video);

$video_sources = array(
    'html5' => array('title' => __('HTML 5 (mp4)', 'tutor'), 'icon' => 'html5'),
    'external_url' => array('title' => __('External URL', 'tutor'), 'icon' => 'link'),
    'youtube' => array('title' => __('Youtube', 'tutor'), 'icon' => 'youtube'),
    'vimeo' => array('title' => __('Vimeo', 'tutor'), 'icon' => 'vimeo'),
    'embedded' => array('title' => __('Embedded', 'tutor'), 'icon' => 'code')
);

$supported_sources = tutor_utils()->get_option('supported_video_sources', $video_sources);
$supported_sources = array_keys($supported_sources);

$default_source = tutor_utils()->get_option('default_video_source', null);
(!$videoSource && $default_source && in_array($default_source, $supported_sources)) ? $videoSource=$default_source : 0;

?>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
            <?php
            if ($post->post_type === tutor()->course_post_type){
                _e('Course Intro Video', 'tutor');
            }else{
                _e('Video Source', 'tutor');
            }
            ?>
        </label>
    </div>

    <div class="tutor-option-field tutor-video-upload-wrap">

        <select name="video[source]" class="tutor_lesson_video_source videosource_select2">
            <option value="-1"><?php _e('Select Video Source', 'tutor'); ?></option>
            <?php
                foreach($video_sources as $value=>$source){
                    if(in_array($value, $supported_sources)){
                        echo '<option value="'.$value.'" '.selected($value, $videoSource).'  data-icon="'.$source['icon'].'" >'.$source['title'].'</option>';
                    }
                }
            ?>
        </select>

        <p class="desc">
            <?php _e('Select your preferred video type.', 'tutor'); ?>
        </p>

        <div class="video-metabox-source-input-wrap" style="display: <?php echo ! $videoSource ? 'none' : 'block'; ?>;">

            <div class="video-metabox-source-item video_source_wrap_html5" style="display: <?php echo $videoSource === 'html5' ? 'block' : 'none'; ?>;">

                <div class="video-metabox-source-html5-upload">
                    <p class="video-upload-icon"><i class="tutor-icon-upload"></i></p>
                    <p><strong><?php _e('Upload Your Video'); ?></strong></p>
                    <p><?php _e('File Format: '); ?> .mp4</p>

                    <div class="video_source_upload_wrap_html5">
                        <a href="javascript:;" class="video_upload_btn tutor-button bordered-button"><?php _e('Upload Video', 'tutor'); ?></a>
                        <input type="hidden" class="input_source_video_id" name="video[source_video_id]" value="<?php echo $sourceVideoID; ?>" >
                        <p style="display: <?php echo $sourceVideoID ? 'block' : 'none'; ?>;"><?php _e('Media ID', 'tutor'); ?>: <span class="video_media_id"><?php echo $sourceVideoID; ?></span></p>
                    </div>

                </div>

                <div class="video-metabox-source-html5-poster">
                    <div class="tutor-form-field tutor-form-field-course-thumbnail tutor-thumbnail-wrap">
                        <div class="tutor-row tutor-align-items-center">
                            <div class="tutor-col">
                                <div class="builder-course-thumbnail-img-src html5-video-poster">
                                    <?php
                                    $builder_course_img_src = tutor()->url . 'assets/images/placeholder-course.jpg';
                                    $poster_url = $builder_course_img_src;
                                    if ( $poster){
                                        $poster_url = wp_get_attachment_image_url($poster);
                                    }
                                    ?>
                                    <img src="<?php echo $poster_url; ?>" class="thumbnail-img" data-placeholder-src="<?php echo $builder_course_img_src; ?>">
                                    <a href="javascript:;" class="tutor-course-thumbnail-delete-btn" style="display: <?php echo
                                    $poster ? 'block':'none'; ?>;"><i class="tutor-icon-line-cross"></i></a>
                                    <div class="tutor-builder-course-video-poster-text">
                                        <h5><?php esc_html_e("Video Poster", 'tutor'); ?></h5>
                                        <span><?php esc_html_e("Thumb Size: 700x430 pixels. File Support: jpg, jpeg, or png", 'tutor'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="tutor-col-auto">
                                <div class="builder-course-thumbnail-upload-wrap">
                                    <input type="hidden" id="tutor_course_thumbnail_id" name="video[poster]" value="<?php echo $poster; ?>">
                                    <a href="javascript:;" class="tutor-course-thumbnail-upload-btn tutor-button bordered-button
                                    button-transparent"><i class="tutor-icon-photo-add"></i> <?php _e('Upload Image', 'tutor'); ?></a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="video-metabox-source-item video_source_wrap_external_url" style="display: <?php echo $videoSource === 'external_url' ? 'block' :
                'none'; ?>;">
                <input type="text" name="video[source_external_url]" value="<?php echo tutor_utils()->avalue_dot('source_external_url', $video);
                ?>" placeholder="<?php _e('External Video URL', 'tutor'); ?>">
            </div>

            <div class="video-metabox-source-item video_source_wrap_youtube" style="display: <?php echo $videoSource === 'youtube' ? 'block' :
                'none'; ?>;">
                <input type="text" name="video[source_youtube]" value="<?php echo tutor_utils()->avalue_dot('source_youtube', $video); ?>" placeholder="<?php _e('YouTube Video URL', 'tutor'); ?>" data-youtube_api_key="<?php echo tutils()->get_option('lesson_video_duration_youtube_api_key', ''); ?>">
            </div>
            <div class="video-metabox-source-item video_source_wrap_vimeo" style="display: <?php echo $videoSource === 'vimeo' ? 'block' : 'none'; ?>;">
                <input type="text" name="video[source_vimeo]" value="<?php echo tutor_utils()->avalue_dot('source_vimeo', $video); ?>" placeholder="<?php _e('Vimeo Video URL', 'tutor'); ?>">
            </div>
            <div class="video-metabox-source-item video_source_wrap_embedded" style="display: <?php echo $videoSource === 'embedded' ? 'block' : 'none'; ?>;">
                <textarea name="video[source_embedded]" placeholder="<?php _e('Place your embedded code here', 'tutor'); ?>"><?php echo tutor_utils()
                        ->avalue_dot
                        ('source_embedded', $video);
                    ?></textarea>
            </div>

        </div>

    </div>
</div>
<?php
    if ( $post->post_type !== tutor()->course_post_type){
        ?>
            <div class="tutor-option-field-row tutor-option-field-video-duration">
                <div class="tutor-option-field-label">
                    <label for=""><?php _e('Video playback time', 'tutor'); ?></label>
                </div>
                <div class="tutor-option-field">
                    <div class="tutor-option-gorup-fields-wrap">
                        <div class="tutor-lesson-video-runtime">
                            <div class="tutor-option-group-field">
                                <input type="number" value="<?php echo $runtimeHours ? $runtimeHours : '00'; ?>" name="video[runtime][hours]">
                                <p class="desc"><?php _e('HH', 'tutor'); ?></p>
                            </div>

                            <div class="tutor-option-group-field">
                                <input type="number" class="tutor-number-validation" data-min="0" data-max="59" value="<?php echo $runtimeMinutes ? $runtimeMinutes : '00'; ?>" name="video[runtime][minutes]">
                                <p class="desc"><?php _e('MM', 'tutor'); ?></p>
                            </div>

                            <div class="tutor-option-group-field">
                                <input type="number" class="tutor-number-validation" data-min="0" data-max="59" value="<?php echo $runtimeSeconds ? $runtimeSeconds : '00'; ?>" name="video[runtime][seconds]">
                                <p class="desc"><?php _e('SS', 'tutor'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
    }
?>