<?php

// Save item image info during import
use Psr\Log\LoggerInterface;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Logger\FeedLoggerInterface;

class Wpra_Rss_Namespace {
    const ITUNES = 'http://www.itunes.com/dtds/podcast-1.0.dtd';
}

add_action('wprss_items_create_post_meta', 'wpra_detect_item_type', 10, 3);
add_action('wprss_items_create_post_meta', 'wpra_import_item_images', 11, 3);
add_filter('wprss_ftp_post_meta', function ($meta, $id, $source, $item) {
    wpra_detect_item_type($id, $item, $source);

    return $meta;
}, 11, 4);

/**
 * Imports images for a feed item.
 *
 * The "import" process here basically just fetches the images from the item's content/excerpt, the media:thumbnail
 * tag and the enclosures. The entire list of images is saved, along with the URL of the best image.
 *
 * @param int|string     $itemId   The ID of the feed item.
 * @param SimplePie_Item $item     The simple pie item object.
 * @param int|string     $sourceId The ID of the feed source from which the item was imported.
 */
function wpra_detect_item_type($itemId, $item, $sourceId)
{
    $logger = wpra_get_logger($sourceId);
    $url = parse_url($item->get_permalink());
    $url['query_str'] = isset($url['query']) ? $url['query'] : '';
    parse_str($url['query_str'], $url['query']);

    if (stripos($url['host'], 'youtube.com') !== false && !empty($url['query']['v'])) {
        $logger->info('Detected YouTube feed item');

        $videoCode = $url['query']['v'];
        $embedUrl = sprintf('https://youtube.com/embed/%s', $videoCode);

        update_post_meta($itemId, 'wprss_item_is_yt', '1');
        update_post_meta($itemId, 'wprss_item_yt_embed_url', $embedUrl);
        update_post_meta($itemId, 'wprss_item_embed_url', $embedUrl);
    }
}

/**
 * Retrieves the logger instance to use for image importing.
 *
 * @since 4.15.1
 *
 * @param int $feedId Optional feed source ID to log messages specifically for that feed source.
 *
 * @return LoggerInterface
 */
function wpra_get_images_logger($feedId = null)
{
    $logger = wpra_container()->get('wpra/images/logging/logger');

    return ($feedId !== null && $logger instanceof FeedLoggerInterface)
        ? $logger->forFeedSource($feedId)
        : $logger;
}

/**
 * Imports images for a feed item.
 *
 * The "import" process here basically just fetches the images from the item's content/excerpt, the media:thumbnail
 * tag and the enclosures. The entire list of images is saved, along with the URL of the best image.
 *
 * @param int|string     $itemId   The ID of the feed item.
 * @param SimplePie_Item $item     The simple pie item object.
 * @param int|string     $sourceId The ID of the feed source from which the item was imported.
 */
function wpra_import_item_images($itemId, $item, $sourceId)
{
    // Start with empty meta
    update_post_meta($itemId, 'wprss_images', []);
    update_post_meta($itemId, 'wprss_best_image', '');

    /* @var $logger FeedLoggerInterface */
    $logger = wpra_get_images_logger($sourceId);

    $title = $item->get_title();
    $logger->debug('Importing images for item "{title}"', ['title' => $title]);

    $collection = wpra_container()->get('wpra/feeds/sources/collection');
    try {
        $source = $collection[$sourceId];
    } catch (Exception $exception) {
        $logger->warning('Feed source #{id} could not be found', ['id' => $sourceId]);
        return;
    }

    // Get the featured image option from the feed source
    $ftImageOpt = $source['import_ft_images'];

    // Stop if source has featured images disabled
    if (empty($ftImageOpt)) {
        $logger->debug('Feed source has featured images disabled');
        return;
    }

    // Get all of the item's images
    $allImages = wpra_get_item_images($item);
    // Process the images, removing duds, and find the best image
    $images = wpra_process_images($allImages, $source, $bestImage);

    // If the featured image importing feature is enabled, import the featured image
    if (wpra_image_feature_enabled('import_ft_images')) {
        $ftImageUrl = null;
        switch ($ftImageOpt)
        {
            case 'auto':
                if (!empty($bestImage)) {
                    $ftImageUrl = $bestImage;
                }
                break;

            case 'media':
                if (isset($images['media']) && !empty($images['media'])) {
                    $ftImageUrl = reset($images['media']);
                }
                break;

            case 'enclosure':
                if (isset($images['enclosure']) && !empty($images['enclosure'])) {
                    $ftImageUrl = reset($images['enclosure']);
                }
                break;

            case 'content':
                if (isset($images['content']) && !empty($images['content'])) {
                    $ftImageUrl = reset($images['content']);
                }
                break;

            case 'itunes':
                if (isset($images['itunes']) && !empty($images['itunes'])) {
                    $ftImageUrl = reset($images['itunes']);
                }
                break;

            case 'default':
            default:
                $ftImageUrl = '';
                break;
        }

        // Filter the featured image URL
        $ftImageUrl = apply_filters('wpra/importer/images/ft_image_url', $ftImageUrl, $item, $sourceId);

        if (empty($ftImageUrl)) {
            // If not always using the default image, and items must have an image, delete the item
            if ($ftImageOpt !== 'default' && wpra_image_feature_enabled('must_have_ft_image') && $source['must_have_ft_image']) {
                $logger->debug('Rejecting item "{title}" due to a lack of a featured image.', [
                    'title' => get_post($itemId)->post_title,
                ]);

                wp_delete_post($itemId, true);
            } else {
                // Get the feed source's default featured image
                $defaultFtImage = get_post_thumbnail_id($sourceId);
                // Assign it to the feed item
                $defaultSuccessful = set_post_thumbnail($itemId, $defaultFtImage);
                // The feed item is classified as using the default image if:
                // - the default image was successfully assigned
                // - the user did NOT explicitly want to use the default
                $usedDefault = $defaultSuccessful && $ftImageOpt !== 'default';

                if ($usedDefault) {
                    update_post_meta($itemId, 'wprss_item_is_using_def_image', '1');
                    $logger->notice('Used the feed source\'s default featured image for "{title}"', ['title' => $title]);
                } else {
                    $logger->notice('No featured image was found for item "{title}"', ['title' => $title]);
                }
            }
        } else {
            $logger->info('Set featured image from URL: "{url}"', ['url' => $ftImageUrl]);
            wpra_set_featured_image_from_url($itemId, $ftImageUrl);

            if (wpra_image_feature_enabled('siphon_ft_image') && $source['siphon_ft_image']) {
                $content = get_post($itemId)->post_content;
                $newContent = wpra_remove_image_from_content($content, $ftImageUrl);

                wp_update_post([
                    'ID' => $itemId,
                    'post_content' => $newContent
                ]);
            }
        }
    }

    // Maybe download other images and replace the URLs in the item's content
    if (wpra_image_feature_enabled('download_images') && $source['download_images']) {
        // Flatten the list of images from a 2d array to a 1d array
        $imagesList = call_user_func_array('array_merge', $images);
        // Download the images and attach them to the item
        $urlMapping = wpra_download_item_images($imagesList, $itemId);
        // Replace the URLs in the item's content
        $content = str_replace(
            array_keys($urlMapping),
            array_values($urlMapping),
            get_post($itemId)->post_content
        );
        // Update the post content
        wp_update_post([
            'ID' => $itemId,
            'post_content' => $content
        ]);
    }

    // Save the image URLs in meta
    update_post_meta($itemId, 'wprss_images', $images);
    update_post_meta($itemId, 'wprss_best_image', $bestImage);

    // Log number of found images
    $logger->info('Found {count} images', ['count' => count($images)]);
}

/**
 * Downloads a given list of images and attaches them to an imported item.
 *
 * @since 4.14
 *
 * @param string[] $images The URLs of the images to download.
 * @param int $postId The ID of the WordPress post to which the images will be attached.
 *
 * @return string[] A mapping of the given URLs pointing to the corresponding local URLs of the downloaded images.
 */
function wpra_download_item_images($images, $postId)
{
    $mapping = [];

    foreach ($images as $url) {
        // No need to download
        if (wpra_is_url_local($url)) {
            continue;
        }

        $imageId = wpra_download_item_image($postId, $url);

        // Failed to download
        if ($imageId === null) {
            continue;
        }

        $mapping[$url] = wp_get_attachment_url($imageId);
    }

    return $mapping;
}

/**
 * Retrieves the URLs of all the images in a feed item.
 *
 * @param SimplePie_Item $item The simple pie item object.
 *
 * @return string[] A list of image URLs.
 */
function wpra_get_item_images($item)
{
    return apply_filters('wpra/images/detect_from_item', [
        'thumbnail' => array_filter([wpra_get_sp_thumbnail_image($item)]),
        'image' => wpra_get_item_rss_images($item),
        'media' => [wpra_get_item_media_thumbnail_image($item)],
        'enclosure' => wpra_get_item_enclosure_images($item),
        'content' => wpra_get_item_content_images($item),
        'itunes' => wpra_get_item_itunes_images($item),
        'feed' => array_filter([$item->get_feed()->get_image_url()]),
    ], $item);
}

/**
 * Retrieves the thumbnail as determined by SimplePie.
 *
 * @param SimplePie_Item $item
 *
 * @return string|null
 */
function wpra_get_sp_thumbnail_image($item)
{
    $thumbnail = $item->get_thumbnail();

    if (!is_array($thumbnail) || !isset($thumbnail['url'])) {
        return null;
    }

    return $thumbnail['url'];
}

/**
 * Processes a list of image URLs to strip away images that are unreachable or too small, as well as identify which
 * image in the list is the best image (in terms of dimensions and aspect ratio).
 *
 * @param array $images The image URLs.
 * @param array|DataSetInterface $source The feed source data set.
 * @param string|null $bestImage This variable given as this parameter will be set to the URL of
 *                               the best found image.
 *
 * @return mixed
 */
function wpra_process_images($images, $source, &$bestImage = null)
{
    if (!wpra_container()->has('wpra/images/container')) {
        return [];
    }

    $imgContainer = wpra_container()->get('wpra/images/container');

    // The list of images of keep and their sizes
    $imageInfos = [];
    // The largest image size found so far, as width * height
    $maxSize = 0;

    // The minimum dimensions for an image to be valid
    $minWidth = 0;
    $minHeight = 0;
    if (wpra_image_feature_enabled('image_min_size')) {
        $minWidth = (int)apply_filters('wprss_thumbnail_min_width', $source['image_min_width']);
        $minHeight = (int)apply_filters('wprss_thumbnail_min_height', $source['image_min_height']);
    }

    foreach ($images as $group => $urls) {
        foreach ($urls as $imageUrl) {
            if (empty($imageUrl)) {
                continue;
            }
            try {
                /* @var WPRSS_Image_Cache_Image $tmp_img */
                $tmp_img = $imgContainer->get($imageUrl);

                $dimensions = ($tmp = $tmp_img->get_local_path())
                    ? $tmp_img->get_size()
                    : null;

                // Ignore image if too small in either dimension
                if ($dimensions === null || $dimensions[0] < $minWidth || $dimensions[1] < $minHeight) {
                    continue;
                }

                $area = $dimensions[0] * $dimensions[1];
                $ratio = floatval($dimensions[0]) / floatval($dimensions[1]);

                // If larger than the current best image and its aspect ratio is between 1 and 2,
                // then set this image as the new best image
                if ($maxSize === 0 || ($area > $maxSize && $ratio > 1.0 && $ratio < 2.0)) {
                    $maxSize = $area;
                    $bestImage = $imageUrl;
                }

                // Add to the list of images to save
                $imageInfos[$group][] = [$imageUrl, $dimensions, $area, $ratio];
            } catch (Exception $exception) {
                // If failed to get dimensions, skip the image
                continue;
            }
        }
    }

    $finalImages = [];
    foreach ($imageInfos as $group => $infos) {
        // Do not sort images found in the content
        if ($group !== 'content') {
            // Sort each group by image size in descending order (largest image first)
            usort($infos, function ($img1, $img2) {
                $area1 = $img1[1];
                $area2 = $img2[1];

                return ($area1 >= $area2) ? -1 : 1;
            });
        }
        // Save only the URLs
        $finalImages[$group] = array_map(function ($info) {
            return $info[0];
        }, $infos);
    }

    return $finalImages;
}

/**
 * Returns the images from the RSS <image> tags for the given feed item.
 *
 * @param SimplePie_Item $item The feed item
 *
 * @return array The string URLs of the images.
 */
function wpra_get_item_rss_images($item)
{
    if (isset($item->data['child']['']['image'])) {
        $imageTags = $item->data['child']['']['image'];

        $urls = array_map(function ($tag) {
            return isset($tag['data']) ? trim($tag['data']) : null;
        }, $imageTags);

        return array_filter($urls);
    }

    return [];
}

/**
 * Returns the <media:thumbnail> image for the given feed item.
 *
 * @since 4.14
 *
 * @param SimplePie_Item $item The feed item
 *
 * @return string|null The string URL of the image, or null if the item does not contain a <media:thumbnail> image.
 */
function wpra_get_item_media_thumbnail_image($item)
{
    // Try to get image from enclosure if available
    $enclosure = $item->get_enclosure();

    // Stop if item has no enclosure tag
    if (is_null($enclosure)) {
        return null;
    }

    // Stop if enclosure is not an image
    $type = $enclosure->get_type();
    if (!empty($type) && stripos($type, 'image/') !== 0) {
        return null;
    }

    // Stop if enclosure tag has no link
    $url = $enclosure->get_link();
    if (empty($url)) {
        return null;
    }

    // Check if image can be downloaded
    if (wpra_container()->has('wpra/images/container')) {
        try {
            /* @var $image WPRSS_Image_Cache_Image */
            $image = wpra_container()->get('wpra/images/container')->get($url);
        } catch (Exception $exception) {
            return null;
        }
    }

    if ($image->get_local_path()) {
        return $url;
    }

    return null;
}

/**
 * Returns the enclosure images for the given feed item.
 *
 * @since 4.14
 *
 * @param SimplePie_Item $item The feed item
 *
 * @return string[] The string URLs of the found enclosure images.
 */
function wpra_get_item_enclosure_images($item)
{
    $enclosure = $item->get_enclosure();

    // Stop if item has no enclosure image
    if (is_null($enclosure) || stripos($enclosure->get_type(), 'image') === false) {
        return [];
    }

    // Get all the thumbnails from the enclosure
    return (array) $enclosure->get_thumbnails();
}

/**
 * Returns the images found in the given item's content
 *
 * @since 4.14
 *
 * @param SimplePie_Item $item The feed item
 *
 * @return string[] Returns the string URLs of the images found.
 */
function wpra_get_item_content_images($item)
{
    // Extract all images from the content into the $matches array
    preg_match_all('/<img.*?src=[\'"](.*?)[\'"].*?>/xis', $item->get_content(), $matches);

    $i = 0;
    $images = [];
    while (!empty($matches[1][$i])) {
        $imageUrl = urldecode(trim(html_entity_decode($matches[1][$i])));
        // Increment early to allow the iteration body to use "continue" statements
        $i++;

        // Add http prefix if not included
        if (stripos($imageUrl, '//') === 0) {
            $imageUrl = 'http:' . $imageUrl;
        }

        // Add to the list
        $images[] = $imageUrl;
    }

    return $images;
}

/**
 * Returns the itunes images for the given feed item.
 *
 * @since 4.14
 *
 * @param SimplePie_Item $item The feed item
 *
 * @return string[] Returns the string URLs of the images found.
 */
function wpra_get_item_itunes_images($item)
{
    $tags = $item->get_item_tags(Wpra_Rss_Namespace::ITUNES,'image');

    if (!is_array($tags) || empty($tags)) {
        return [];
    }

    $images = [];
    foreach ($tags as $tag) {
        if (empty($tag['attribs']) || empty($tag['attribs'][''])) {
            continue;
        }

        $attribs = $tag['attribs'][''];

        if (!empty($attribs['href'])) {
            $images[] = $attribs['href'];
        }
    }

    return $images;
}

/**
 * Removes an image tag, matched by an image URL, from a string of HTML content.
 *
 * @since 4.14
 *
 * @param string $content The content in which to search for and remove the image.
 * @param string $url     The URL of the image to remove.
 * @param int    $limit   Optional number of image occurrences to remove.
 *
 * @return string The new content, with any matching `img` HTML tags removed.
 */
function wpra_remove_image_from_content($content, $url, $limit = 1)
{
    $tag_search = array(
        '<img[^<>]*?src="%s"[^<>]*?>',
        '<img[^<>]*?srcset="[^<>]*?%s.[^<>]*?"[^<>]*?>'
    );

    foreach ($tag_search as $regex) {
        // This will transform the expression to match images in html-encoded content
        $regex = wprss_html_regex_encodify($regex);
        // Prepare the URL to be inserted into the sprintf-style regex string
        $regexUrl = preg_quote(esc_attr($url), '!');
        // Insert the URL into the regex string, and add the regex delimiter and modifiers
        $regex = sprintf($regex, $regexUrl);
        $regex = sprintf('!%s!Uis', $regex);
        // Replace the tag with an empty string, and get the new content
        $content = preg_replace($regex, '', $content, $limit);
    }

    $filtered = wp_filter_post_kses($content);
    $trimmed = trim($filtered);

    return $trimmed;
}

/**
 * Downloads a feed item image.
 *
 * This function will attempt to download the image at the given URL. If the image URL is a local URL, the function
 * will skip the downloading process. The post ID is required in order to use WordPress side-loading function.
 *
 * @since 4.14
 *
 * @param int $post_id The ID of the post.
 * @param string $url The URL of the image.
 *
 * @return string|null The ID of the locally downloaded image or null on failure.
 */
function wpra_download_item_image($post_id, $url)
{
    // Download image if needed
    if (wpra_is_url_local($url)) {
        return null;
    }

    $id = wpra_media_sideload_image($url, $post_id, false);

    if ($id instanceof WP_Error) {
        return null;
    }

    return $id;
}

/**
 * Sets a featured image to a post, from a URL.
 *
 * This function will attempt to download the image at the given URL and then assign it to the post with the given ID.
 * If the image URL is a local URL, the function will skip the downloading process.
 *
 * @since 4.14
 *
 * @param int $post_id The ID of the post.
 * @param string $url The URL of the image.
 */
function wpra_set_featured_image_from_url($post_id, $url)
{
    // Download image if needed
    if (!wpra_is_url_local($url)) {
        wpra_media_sideload_image($url, $post_id, true);

        return;
    }

    // Otherwise, get the attachment ID for the URL from the database
    set_post_thumbnail( $post_id, wpra_get_attachment_id_from_url($url) );
}

/**
 * Checks if the given url is a local or external one
 *
 * @since 4.14
 */
function wpra_is_url_local($url, $home_url = null)
{
    if (is_null($home_url)) {
        $home_url = get_option('siteurl');
    }

    // What about the URLs are we comparing?
    $relevant_parts = ['host', 'path'];

    // Get the site's url
    $siteurl = trim(wpra_rebuild_url($home_url, $relevant_parts), '/');
    // The URL in question
    $url = trim(wpra_rebuild_url(wpra_encode_and_parse_url($url), $relevant_parts), '/');

    return strpos($url, $siteurl) === 0;
}

/**
 * Builds a URL from a given URL, using only the specified parts of it.
 *
 * @since 4.14
 *
 * @see   parse_url()
 *
 * @param string|array $url   The URL which is to be rebuilt, or a result of parse_url().
 *
 * @param bool|array   $parts An array of which parts to use for building the new URL. Boolean false for all.
 *
 * @return null|string The rebuilt URL on success, or null of given URL is malformed.
 */
function wpra_rebuild_url($url, $parts = false)
{

    // Allow parsed array
    if (is_string($url)) {
        $url = parse_url($url);
    }

    // malformed or empty URL
    if (!$url) {
        return null;
    }

    // Include all parts
    if ($parts === false) {
        return http_build_url($url);
    }

    // Nothing to do here
    if (empty($parts)) {
        return '';
    }

    $newParts = [];
    foreach ($parts as $_idx => $_part) {
        $_part = trim($_part);
        if (isset($url[$_part])) {
            $newParts[$_part] = $url[$_part];
        }
    }

    // Rebuilding the URL from parts
    return http_build_url($newParts);
}

function wpra_encode_and_parse_url($url)
{
    $encodedUrl = @preg_replace_callback('%[^:/?#&=\.]+%usD', function ($matches) {
        return sprintf('urlencode(\'%s\')', $matches[0]);
    }, $url);
    $components = parse_url($encodedUrl);
    foreach ($components as &$component) {
        $component = urldecode($component);
    }

    return $components;
}

/**
 * Download an image from the specified URL and attach it to a post.
 *
 * Modified version of core function media_sideload_image() in /wp-admin/includes/media.php
 * (which returns an html img tag instead of attachment ID).
 * Additional functionality: ability override actual filename,
 * and to pass $post_data to override values in wp_insert_attachment (original only allowed $desc).
 *
 * Uses image cache to avoid re-downloading images. Keeps cache intact by
 * creating a copy of the cache file, which will eventually be moved.
 *
 * Credits to somatic
 * http://wordpress.stackexchange.com/questions/30284/media-sideload-image-file-name/44115#44115
 *
 * @since 2.7.4
 *
 * @param string $url       (required) The URL of the image to download
 * @param int    $post_id   (required) The post ID the media is to be associated with
 * @param bool   $attach    (optional) Whether to make this attachment the Featured Image for the post.
 * @param string $filename  (optional) Replacement filename for the URL filename (do not include extension)
 * @param array  $post_data (optional) Array of key => values for wp_posts table (ex: 'post_title' => 'foobar',
 *                          'post_status' => 'draft')
 *
 * @return int|object The ID of the attachment or a WP_Error on failure
 */
function wpra_media_sideload_image($url = null, $post_id = null, $attach = null, $filename = null, $post_data = [])
{
    if (!$url || !$post_id) {
        return new WP_Error('missing', "Need a valid URL and post ID...");
    }

    // Allow 30 seconds prior to beginning the actual download
    set_time_limit(apply_filters('wpra/images/time_limit/prepare', 30));

    // Check if the image already exists in the media library
    $existing = get_posts([
        'post_type' => 'attachment',
        'meta_key' => 'wprss_og_image_url',
        'meta_value' => $url,
    ]);
    // If so, use the existing image's ID
    if (!empty($existing)) {
        $att_id = reset($existing)->ID;
    } else {
        if (!wpra_container()->has('wpra/images/container')) {
            return new WP_Error('Images module is not loaded');
        }

        $logger = wpra_get_logger();
        $images = wpra_container()->get('wpra/images/container');

        try {
            /* @var $img WPRSS_Image_Cache_Image */
            $url = apply_filters('wpra/images/url_to_download', $url);

            // Allow 1 minute for the download process
            set_time_limit(apply_filters('wpra/images/time_limit/download', 60));

            $img = $images->get($url);
        } catch (Exception $e) {
            return new WP_Error('could_not_load_image', $e->getMessage(), $url);
        }

        $logger->debug('Image from cache: {url} -> {path}', [
            'url' => $img->get_url(),
            'path' => $img->get_local_path(),
        ]);

        // Get the path
        $tmp = $img->get_local_path();

        // Required for wp_tempnam() function
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        // media_handle_sideload() will move the file, but we need the cache to remain
        copy($tmp, $tmp = wp_tempnam());
        $tmpPath = pathinfo($tmp);
        $ext = isset($tmpPath['extension']) ? trim($tmpPath['extension']) : null;
        $url_filename = $img->get_unique_name();

        // override filename if given, reconstruct server path
        if (!empty($filename)) {
            $filename = sanitize_file_name($filename);
            // build new path
            $new = $tmpPath['dirname'] . "/" . $filename . "." . $ext;
            // renames temp file on server
            rename($tmp, $new);
            // push new filename (in path) to be used in file array later
            $tmp = $new;
        }

        // determine file type (ext and mime/type)
        $url_type = wp_check_filetype($url_filename);

        // If the wp_check_filetype function fails to determine the MIME type
        if (empty($url_type['type'])) {
            $url_type = wpra_check_file_type($tmp, $url);
        }
        $ext = $url_type['ext'];

        // assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
        $file_array = [];
        // full server path to temp file
        $file_array['tmp_name'] = $tmp;
        $parts = parse_url(trim($img->get_url()));
        $baseName = uniqid($parts['host']);

        $parts['path'] = apply_filters('wpra/images/cache_path', $parts['path']);

        if (!empty($filename)) {
            // user given filename for title, add original URL extension
            $baseName = $filename . "." . $ext;
        } else {
            // The original basename, falling back to auto-generated based on domain
            $base = basename($parts['path']);
            if (strlen($baseName) || trim($baseName) !== '/') {
                $baseName = $base;
            }
        }

        // Fix for Facebook images that come from a PHP endpoint
        if (stripos($baseName, 'safe_image.php') !== false) {
            $hash = crc32(trim($img->get_url()));

            $baseName = str_replace('safe_image.php', $hash . '.jpeg', $baseName);
        }

        $file_array['name'] = $baseName;

        // set additional wp_posts columns
        if (empty($post_data['post_title'])) {
            // just use the original filename (no extension)
            $post_data['post_title'] = $file_array['name'];
        }

        // make sure gets tied to parent
        if (empty($post_data['post_parent'])) {
            $post_data['post_parent'] = $post_id;
        }

        // required files for WP media_handle_sideload
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // NO FILENAME FIX
        // WordPress does not allow file images that are not in the form of a filename
        // ex: http://domain.com/thoufiqadsjucpqwuamoshfjnax8mtrh/iorqhewufjasj

        if (apply_filters('wpra_override_upload_security', true) === true) {
            // If we successfully retrieved the MIME type
            if ($url_type !== false && isset($url_type['type']) && !empty($url_type['type'])) {
                $mime_to_ext = wpra_get_mime_type_ext_mapping();

                $mime_type = $url_type['type'];
                $file_ext = isset($mime_to_ext[$mime_type])
                    ? $mime_to_ext[$mime_type]
                    : null;

                // If no file extension, check if the mime type begins with "image/" and if so default to "png"
                $mime_type_parts = explode('/', $mime_type);
                if ($file_ext === null && count($mime_type_parts) > 1 && $mime_type_parts[0] === 'image') {
                    $file_ext = 'png';
                }

                // Add a filter to ensure that the image ext and mime type get passed through WordPress' security
                add_filter('wp_check_filetype_and_ext', function ($image) use ($file_ext, $mime_type) {
                    $image['ext'] = empty($image['ext']) ? $file_ext : $image['ext'];
                    $image['type'] = empty($image['type']) ? $mime_type : $image['type'];

                    // If the image has a `proper_filename` and an `ext`
                    if (!empty($image['proper_filename']) && !empty($image['ext'])) {
                        $filename = strtolower($image['proper_filename']);
                        $extension = strtolower($image['ext']);

                        // Do a case insensitive check for the extension in the proper_filename. If not found, we
                        // add the extension
                        if (!preg_match('/'.$extension.'$/i', $filename)) {
                            $image['proper_filename'] .= '.' . $extension;
                        }
                    }

                    return $image;
                }, 10);
            }
        }

        // do the validation and storage stuff
        // For some reason, deep down filesize() returned 0 for the temporary file without this
        clearstatcache(false, $file_array['tmp_name']);

        // Allocate 30 for WordPress to copy and process the image
        set_time_limit(apply_filters('wpra/images/time_limit/copy', 30));

        // $post_data can override the items saved to wp_posts table,
        // like post_mime_type, guid, post_parent, post_title, post_content, post_status
        $att_id = media_handle_sideload($file_array, $post_id, '', $post_data);

        // If error storing permanently, unlink
        if (is_wp_error($att_id)) {
            $logger->warning('Failed to download and attach image to post #{id}. Image URL: {url}', [
                'id' => $post_id,
                'url' => $url,
            ]);

            // Delete the cache copy needed for media_handle_sideload()
            $img->delete();
            @unlink($tmp);

            return $att_id;
        }
    }

    // set as post thumbnail if desired
    if ($attach) {
        set_post_thumbnail( $post_id, $att_id );
    }

    // Save the original image URL in the attachment's meta data
    update_post_meta($att_id, 'wprss_og_image_url', $url);

    return $att_id;
}

/**
 * Fallback function for determining mime type and extension of an image
 *
 * @since 4.14
 *
 * @param string $local_image_path  Local path of the downloaded image
 * @param string $remote_image_path Remote image url
 *
 * @return array Values with extension first and mime type.
 */
function wpra_check_file_type($local_image_path, $remote_image_path)
{
    $ext = false;
    $type = false;

    $mime_to_ext_mapping = wpra_get_mime_type_ext_mapping();

    $mime_var = 'mime';
    $image_response = @getimagesize($local_image_path);

    // Trying to get MIME type of the image
    if (!isset($image_response) || $image_response == false) {
        $image_response = @get_headers($remote_image_path, 1);
        if ($image_response !== false) {
            $mime_var = 'Content-Type';
        }
    }

    // If mime type successfully determined
    if (!empty($image_response[$mime_var])) {
        $type = $image_response[$mime_var];

        if (isset($mime_to_ext_mapping[$type])) {
            $ext = $mime_to_ext_mapping[$type];
        }
    }

    return compact('ext', 'type');
}

/**
 * Return Mime type and ext mapping array
 *
 * @since 4.14
 * @return array Mime type and ext mapping
 */
function wpra_get_mime_type_ext_mapping()
{

    // Get MIME to extension mappings ( from WordPress wp_check_filetype_and_ext() function )
    return apply_filters(
        'getimagesize_mimes_to_exts', [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tif',
        ]
    );
}

/**
 * Returns the attachment ID of the image with the given source
 *
 * @since 4.14
 */
function wpra_get_attachment_id_from_url( $image_src ) {
    global $wpdb;
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    return $id;
}

/**
 * Checks if an images feature is enabled.
 *
 * @since 4.14
 *
 * @param string $feature The feature name.
 *
 * @return bool True if the feature is enabled, false if the feature is disabled.
 */
function wpra_image_feature_enabled($feature)
{
    $c = wpra_container();
    $key = 'wpra/images/features/' . $feature;

    return $c->has($key) && $c->get($key) === true;
}
