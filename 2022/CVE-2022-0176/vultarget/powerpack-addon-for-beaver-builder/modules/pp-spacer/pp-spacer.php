<?php

/**
 * @class PPSpacerModule
 */
class PPSpacerModule extends FLBuilderModule {

    /**
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Spacer', 'bb-powerpack-lite'),
            'description'   => __('Spacer module.', 'bb-powerpack-lite'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'creative' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-spacer/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-spacer/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPSpacerModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack-lite'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => __('Height', 'bb-powerpack-lite'), // Section Title
                'fields'        => array( // Section Fields
                    'spacer_height_lg' => array(
                        'type'          => 'unit',
                        'label'         => __('Large Device', 'bb-powerpack-lite'),
                        'units'			=> array('px'),
                        'default'       => 15,
                        'slider'		=> true,
                        'preview'		=> array(
                            'type'              => 'css',
                            'selector'          => '.pp-spacer-module',
                            'property'          => 'height',
                            'unit'              => 'px'
                        ),
                    ),
                    'spacer_height_md' => array(
                        'type'          => 'unit',
                        'label'         => __('Medium Device', 'bb-powerpack-lite'),
                        'default'       => 15,
                        'units'			=> array('px'),
                        'slider'		=> true,
                        'preview'		=> array(
                            'type'              => 'none',
                        ),
                    ),
                    'spacer_height_sm' => array(
                        'type'          => 'unit',
                        'label'         => __('Small Device', 'bb-powerpack-lite'),
                        'units'			=> array('px'),
                        'default'       => 15,
                        'slider'		=> true,
                        'preview'		=> array(
                            'type'              => 'none',
                        ),
                    ),
                )
            ),
            'visibility'    => array(
                'title'         => __('Visibility', 'bb-powerpack-lite'),
                'fields'        => array(
                    'hide_on'   => array(
                        'type'      => 'select',
                        'label'     => __('Hide On', 'bb-powerpack-lite'),
                        'default'   => 'none',
                        'options'   => array(
                            'none'      => __('None', 'bb-powerpack-lite'),
                            'large'     => __('Large Device', 'bb-powerpack-lite'),
                            'medium'    => __('Medium Device', 'bb-powerpack-lite'),
                            'small'     => __('Small Device', 'bb-powerpack-lite'),
                            'custom'    => __('Custom', 'bb-powerpack-lite')
                        ),
                        'toggle'    => array(
                            'large'    => array(
                                'fields'    => array('hide_column')
                            ),
                            'medium'    => array(
                                'fields'    => array('hide_column')
                            ),
                            'small'    => array(
                                'fields'    => array('hide_column')
                            ),
                            'custom'    => array(
                                'fields'    => array('custom_breakpoint', 'breakpoint_condition', 'hide_column')
                            )
                        ),
                        'preview'   => array(
                            'type'      => 'none'
                        )
                    ),
                    'custom_breakpoint'     => array(
                        'type'                  => 'unit',
                        'label'                 => __('Custom Breakpoint', 'bb-powerpack-lite'),
                        'default'               => '',
                        'units'					=> array('px'),
                        'slider'				=> true,
                        'preview'               => array(
                            'type'                  => 'none'
                        )
                    ),
                    'breakpoint_condition'  => array(
                        'type'                  => 'select',
                        'label'                 => __('Condition', 'bb-powerpack-lite'),
                        'default'               => 'lt_equals_to',
                        'options'               => array(
                            'lt_equals_to'          => __('below or equals to', 'bb-powerpack-lite'),
                            'gt_equals_to'          => __('above or equals to', 'bb-powerpack-lite'),
                        ),
                        'preview'       => array(
                            'type'          => 'none'
                        )
                    ),
                    'hide_column'   => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Hide Entire Column', 'bb-powerpack-lite'),
                        'default'       => 'no',
                        'options'       => array(
                            'yes'           => __('Yes', 'bb-powerpack-lite'),
                            'no'            => __('No', 'bb-powerpack-lite')
                        ),
                        'preview'       => array(
                            'type'          => 'none'
                        )
                    )
                )
            )
        )
    )
));
