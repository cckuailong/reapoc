<?php

/**
 * Address Field Class
 */
class WPUF_Form_Field_Address extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Address Field', 'wp-user-frontend' );
        $this->input_type = 'address_field';
        $this->icon       = 'address-card-o';
    }
}

/**
 * Country Field Class
 */
class WPUF_Form_Field_Country extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Country List', 'wp-user-frontend' );
        $this->input_type = 'country_list_field';
        $this->icon       = 'globe';
    }
}

/**
 * Date Field Class
 */
class WPUF_Form_Field_Date extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Date / Time', 'wp-user-frontend' );
        $this->input_type = 'date_field';
        $this->icon       = 'calendar-o';
    }
}

class WPUF_Form_Field_Embed extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Embed', 'wp-user-frontend' );
        $this->input_type = 'embed';
        $this->icon       = 'address-card-o';
    }
}

/**
 * File Field Class
 */
class WPUF_Form_Field_File extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'File Upload', 'wp-user-frontend' );
        $this->input_type = 'file_upload';
        $this->icon       = 'upload';
    }
}

/**
 * Text Field Class
 */
class WPUF_Form_Field_GMap extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Google Map', 'wp-user-frontend' );
        $this->input_type = 'google_map';
        $this->icon       = 'map-marker';
    }
}

/**
 * Text Field Class
 */
class WPUF_Form_Field_Hook extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Action Hook', 'wp-user-frontend' );
        $this->input_type = 'action_hook';
        $this->icon       = 'anchor';
    }
}

/**
 * Numeric Field Class
 */
class WPUF_Form_Field_Numeric extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Numeric Field', 'wp-user-frontend' );
        $this->input_type = 'numeric_text_field';
        $this->icon       = 'hashtag';
    }
}

/**
 * Rating Field Class
 */
class WPUF_Form_Field_Rating extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Ratings', 'wp-user-frontend' );
        $this->input_type = 'ratings';
        $this->icon       = 'star-half-o';
    }
}

/**
 * Rating Field Class
 */
class WPUF_Form_Field_Linear_Scale extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Linear Scale', 'wp-user-frontend' );
        $this->input_type = 'linear_scale';
        $this->icon       = 'ellipsis-h';
    }
}

/**
 * Checkbox Grids Field Class
 */
class WPUF_Form_Field_Checkbox_Grid extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Checkbox Grid', 'wp-user-frontend' );
        $this->input_type = 'checkbox_grid';
        $this->icon       = 'th';
    }
}

/**
 * Multiple Choice Grids Field Class
 */
class WPUF_Form_Field_Multiple_Choice_Grid extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Multiple Choice Grid', 'wp-user-frontend' );
        $this->input_type = 'multiple_choice_grid';
        $this->icon       = 'braille';
    }
}

/**
 * Repeat Field Class
 */
class WPUF_Form_Field_Repeat extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Repeat Field', 'wp-user-frontend' );
        $this->input_type = 'repeat_field';
        $this->icon       = 'text-width';
    }
}

/**
 * Really Simple Captcha Field Class
 */
class WPUF_Form_Field_Really_Simple_Captcha extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Really Simple Captcha', 'wp-user-frontend' );
        $this->input_type = 'shortcode';
        $this->icon       = '';
    }
}

/**
 * Shortcode Field Class
 */
class WPUF_Form_Field_Shortcode extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Shortcode', 'wp-user-frontend' );
        $this->input_type = 'shortcode';
        $this->icon       = 'calendar-o';
    }
}

/**
 * Step Field Class
 */
class WPUF_Form_Field_Step extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Step Start', 'wp-user-frontend' );
        $this->input_type = 'step_start';
        $this->icon       = 'step-forward';
    }
}

/**
 * TOC Field Class
 */
class WPUF_Form_Field_Toc extends WPUF_Form_Field_Pro {
    public function __construct() {
        $this->name       = __( 'Terms & Conditions', 'wp-user-frontend' );
        $this->input_type = 'toc';
        $this->icon       = 'file-text';
    }
}

/**
 * Math Capctha Class
 */
class WPUF_Form_Field_Math_Captcha extends WPUF_Form_Field_Pro {

    public function __construct() {
        $this->name       = __( 'Math Captcha', 'wp-user-frontend' );
        $this->input_type = 'math_captcha';
        $this->icon       = 'hashtag';
    }
}

/**
 * QR Code Class
 */
class WPUF_Form_Field_QR_Code extends WPUF_Form_Field_Pro {

    public function __construct() {
        $this->name       = __( 'QR Code', 'wp-user-frontend' );
        $this->input_type = 'qr_code';
        $this->icon       = 'address-card-o';
    }
}
