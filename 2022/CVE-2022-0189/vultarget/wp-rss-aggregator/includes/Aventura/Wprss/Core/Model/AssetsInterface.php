<?php

namespace Aventura\Wprss\Core\Model;

/**
 * Something that can be used as an assets controller.
 *
 * @since 4.8.1
 */
interface AssetsInterface
{
    /**
     * Enqueues the styles for the front-end.
     *
     * @since 4.8.1
     */
    public function enqueuePublicStyles();

    /**
     * Enqueues the scripts for the front-end.
     *
     * @since 4.8.1
     */
    public function enqueuePublicScripts();

    /**
     * Enqueues the styles for the front-end.
     *
     * @since 4.8.1
     */
    public function enqueueAdminStyles();

    /**
     * Enqueues the scripts for the front-end.
     *
     * @since 4.8.1
     */
    public function enqueueAdminScripts();
}