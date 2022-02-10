<?php

$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

include __DIR__.'/attach-file/upload-file.php';
include __DIR__.'/attach-file/remote-url.php';
include __DIR__.'/attach-file/media-library-file.php';
include __DIR__.'/attach-file/server-file.php';

do_action("wpdm_attach_file_metabox");
