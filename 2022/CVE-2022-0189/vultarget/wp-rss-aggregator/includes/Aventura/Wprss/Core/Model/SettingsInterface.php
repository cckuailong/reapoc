<?php

namespace Aventura\Wprss\Core\Model;

/**
 * Something that can represent settings.
 *
 * @since 4.8.1
 */
interface SettingsInterface
{
    public function validate($settings);

    public function getSectionsFields();

    public function getData();
}