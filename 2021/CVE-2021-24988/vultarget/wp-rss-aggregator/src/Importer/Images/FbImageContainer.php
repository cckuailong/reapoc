<?php

namespace RebelCode\Wpra\Core\Importer\Images;

use Psr\Container\ContainerInterface;

/**
 * An image container decorator that can detect Facebook image URLs and convert them for large image URLs.
 *
 * @since 4.14
 */
class FbImageContainer implements ContainerInterface
{
    /**
     * The container instance to decorate.
     *
     * @since 4.14
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param ContainerInterface $container The container instance to decorate.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function get($url)
    {
        return $this->container->get($this->getFbUrl($url));
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function has($url)
    {
        return $this->container->has($url);
    }

    /**
     * Checks if a URL is from Facebook and attempts to modify the URL to point to the larger version of the image.
     *
     * @since 4.14
     *
     * @param string $url The original image URL.
     *
     * @return string The URL of the found large image, or the original image URL if no large image was found or the
     *                image is not from Facebook.
     */
    protected function getFbUrl($url)
    {
        // Check if image is provided from Facebook's CDN, and if so remove any "_s" small image extension in the URL
        if (stripos($url, 'fbcdn') > 0) {
            $imageExt = strrchr($url, '.');
            $largerImgUrl = str_replace('_s' . $imageExt, '_n' . $imageExt, $url);

            if ($this->has($largerImgUrl)) {
                $url = $largerImgUrl;
            }
        }

        // If the URL is from 'fbexternal-a.akamaihd.net',an included GET param points to the original image
        if (parse_url($url, PHP_URL_HOST) === 'fbexternal-a.akamaihd.net') {
            // Get the query string
            $queryStr = parse_url($url, PHP_URL_QUERY);
            // If not empty
            if (!empty($queryStr)) {
                // Parse it
                parse_str(urldecode($queryStr), $output);

                // If it has a url GET param, use it as the image URL
                if (isset($output['amp;url'])) {
                    $output['url'] = $output['amp;url'];
                }

                if (isset($output['url'])) {
                    $url = urldecode($output['url']);
                }
            }
        }

        return $url;
    }
}
