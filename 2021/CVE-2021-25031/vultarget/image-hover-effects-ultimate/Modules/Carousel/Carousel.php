<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Carousel;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of General
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Create as Create;

class Carousel extends Create {

    public function Admin_header() {
        ?>
        <div class="oxi-addons-wrapper">
            <div class="oxi-addons-import-layouts">
                <h1>Carousel or Slider › Create New</h1>
                <p> Select Image Hover layouts, Gives your Image Hover name and create new Image Hover.</p>
            </div>
        </div>
        <?php
    }

    public function Import_header() {
        ?>
        <div class="oxi-addons-wrapper">
            <div class="oxi-addons-import-layouts">
                <h1>Carousel or Slider › Import Templates</h1>
                <p> Select Image Hover layouts, Import Templates for future Use.</p>
            </div>
        </div>
        <?php
    }

    public function create_new() {
        echo __('<div class="modal fade" id="oxi-addons-style-create-modal" >
                        <form method="post" id="oxi-addons-style-modal-form">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">New Carousel Style</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class=" form-group row">
                                            <label for="addons-style-name" class="col-sm-6 col-form-label" oxi-addons-tooltip="Give your Shortcode Name Here">Name</label>
                                            <div class="col-sm-6 addons-dtm-laptop-lock">
                                                <input class="form-control" type="text" value="" id="style-name"  name="style-name" required>
                                            </div>
                                        </div>
                                        <div class="form-group row d-none">
                                            <label for="oxi-tabs-link" class="col-sm-5 col-form-label" title="Select Layouts">Layouts</label>
                                            <div class="col-sm-7">
                                                <div class="btn-group" data-toggle="buttons">
                                                    <label class="btn btn-secondary active">
                                                        <input type="radio" name="image-hover-box-layouts"value="1"  checked="">1st
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" id="oxistyledata" name="oxistyledata" value="">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-success" name="addonsdatasubmit" id="addonsdatasubmit" value="Save">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>');
    }

    public function JSON_DATA() {
        $basename = array_map('basename', glob(OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst($this->effects) . '/Layouts/*', GLOB_ONLYDIR));

        foreach ($basename as $key => $effects) {
            $temp = array_map('basename', glob(OXI_IMAGE_HOVER_PATH . 'Modules/' . ucfirst($this->effects) . '/Layouts/' . $effects . '/*.json', GLOB_BRACE));
            $template_data[(int) $effects] = $temp;
        }
        ksort($template_data);
        $this->TEMPLATE = $template_data;
        $this->pre_active = [
            'carousel-1',
        ];
    }

}
