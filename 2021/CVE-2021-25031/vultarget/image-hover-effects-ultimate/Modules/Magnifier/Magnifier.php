<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Magnifier;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of General
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Create as Create;

class Magnifier extends Create {

    public function JSON_DATA() {
        $template_data = [];

        $basename = array_map('basename', glob(OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst($this->effects) . '/Layouts/*', GLOB_ONLYDIR));

        foreach ($basename as $key => $effects) {
            $temp = array_map('basename', glob(OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst($this->effects) . '/Layouts/' . $effects . '/*.json', GLOB_BRACE));
            $template_data[(int) $effects] = $temp;
        }
        ksort($template_data);
        $this->TEMPLATE = $template_data;
        $this->pre_active = [
            'magnifier-1',
        ];
    }

}
