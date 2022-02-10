<?php
/**
 * Copyright (c) 2020 PublishPress
 *
 * GNU General Public License, Free Software Foundation <https://www.gnu.org/licenses/gpl-3.0.html>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     PPVersionNotices
 * @category    Core
 * @author      PublishPress
 * @copyright   Copyright (c) 2020 PublishPress. All rights reserved.
 **/

namespace PPVersionNotices\Module\Footer;

use PPVersionNotices\Module\AdInterface;
use PPVersionNotices\Template\TemplateLoaderInterface;
use PPVersionNotices\Template\TemplateInvalidArgumentsException;

/**
 * Class Module
 *
 * @package PPVersionNotices
 */
class Module implements AdInterface
{
    const SETTINGS_FILTER = 'pp_pro_ads_footer_settings';

    /**
     * @var TemplateLoaderInterface
     */
    private $templateLoader;

    /**
     * @var array
     */
    private $settings = [];

    public function __construct(TemplateLoaderInterface $templateLoader)
    {
        $this->templateLoader = $templateLoader;
    }
}