<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of General
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Create as Create;

class Caption extends Create {

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
            'caption-1',
            'caption-2',
            'caption-3',
            'caption-4',
            'caption-5',
            'caption-6',
            'caption-7',
            'caption-8',
            'caption-9',
            'caption-10',
        ];
    }

}
