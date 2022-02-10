<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\General;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of General
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Create as Create;

class General extends Create {

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
            'general-1',
            'general-2',
            'general-3',
            'general-4',
            'general-5',
            'general-6',
            'general-7',
            'general-8',
            'general-9',
            'general-10',
            'general-11',
            'general-12',
            'general-13',
            'general-14',
            'general-15',
        ];
    }

}
