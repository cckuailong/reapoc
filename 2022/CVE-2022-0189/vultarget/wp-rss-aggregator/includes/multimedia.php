<?php

/**
 * Checks if a URI points to an audio file.
 *
 * @since 4.18
 *
 * @param string $uri The URI to check.
 *
 * @return bool True if the URI points to an audio file, false if not.
 */
function wpra_is_audio_file($uri)
{
    switch (wpra_get_uri_extension($uri)) {
        case 'aac':
        case 'adts':
        case 'aif':
        case 'aifc':
        case 'aiff':
        case 'cdda':
        case 'bwf':
        case 'kar':
        case 'mid':
        case 'midi':
        case 'smf':
        case 'm4a':
        case 'mp3':
        case 'swa':
        case 'wav':
        case 'wax':
        case 'wma':
            return true;
        default:
            return false;
    }
}

/**
 * Checks if a URI points to a video file.
 *
 * @since 4.18
 *
 * @param string $uri The URI to check.
 *
 * @return bool True if the URI points to a video file, false if not.
 */
function wpra_is_video_file($uri)
{
    switch (wpra_get_uri_extension($uri)) {
        case 'mp4':
        case 'mkv':
        case 'mov':
        case 'avi':
        case 'webm':
        case 'ogg':
            return true;
        default:
            return false;
    }
}
