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

namespace PPVersionNotices;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use PPVersionNotices\Module\TopNotice\Module as TopNoticeModule;
use PPVersionNotices\Module\MenuLink\Module as MenuLinkModule;
use PPVersionNotices\Template\TemplateLoader;

class ServicesProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['TEMPLATES_PATH'] = function (Container $c) {
            return PP_VERSION_NOTICES_BASE_PATH . '/templates';
        };

        $pimple['module_top_notice'] = function (Container $c) {
            return new TopNoticeModule($c['template_loader']);
        };

        $pimple['module_menu_link'] = function (Container $c) {
            return new MenuLinkModule($c['template_loader']);
        };

        $pimple['template_loader'] = function (Container $c) {
            return new TemplateLoader($c['TEMPLATES_PATH']);
        };
    }
}