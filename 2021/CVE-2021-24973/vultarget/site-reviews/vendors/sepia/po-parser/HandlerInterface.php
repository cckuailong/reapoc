<?php

namespace GeminiLabs\Sepia\PoParser;

/**
 * Interface HandlerInterface
 * @package Sepia\PoParser\Handler
 */
interface HandlerInterface
{
    /**
     * @return string
     */
    public function getNextLine();

    /**
     * @return bool
     */
    public function ended();

    /**
     * Closes source handler.
     *
     * @return bool
     */
    public function close();

    /**
     * Saves translations into source.
     *
     * @param string $output  Compiled gettext data.
     * @param array $params   Extra parameters.
     *
     * @return mixed
     */
    public function save($output, $params);
}
