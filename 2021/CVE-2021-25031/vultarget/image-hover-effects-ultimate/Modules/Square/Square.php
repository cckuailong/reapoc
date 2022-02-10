<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Square;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Square
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Create as Create;

class Square extends Create {

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
            'square-1',
            'square-2',
            'square-3',
            'square-4',
            'square-5',
            'square-6',
            'square-7',
            'square-8',
            'square-9',
            'square-10',
            'square-11',
            'square-12',
            'square-13',
            'square-14',
            'square-15',
        ];
    }

}
