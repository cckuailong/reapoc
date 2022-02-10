<?php

defined( 'ABSPATH' ) || exit;
/**
 * Registers the `qubely/postgrid` block on server.
 *
 * @since 1.1.0
 */
function register_block_qubely_postgrid()
{
	// Check if the register function exists.
	if (!function_exists('register_block_type')) {
		return;
	}
	register_block_type(
		'qubely/postgrid',
		array(
			'attributes' => array(
				'uniqueId' => array(
					'type' => 'string',
					'default' => '',
				),
				//general
				'postType' => array(
					'type' => 'string',
					'default' => 'post',
				),
				'taxonomy' => array(
					'type' => 'string',
					'default' => 'categories',
				),
				'taxonomyType' => array(
					'type' => 'string',
					'default' => 'category',
				),
				'categories' => array(
					'type' => 'array',
					'default' => [],
					'items'   => [
						'type' => 'object'
					],
				),
				'customTaxonomies' => array(
					'type' => 'array',
					'default' => [],
					'items'   => [
						'type' => 'object'
					],
				),
				'tags' => array(
					'type' => 'array',
					'default' => [],
					'items'   => [
						'type' => 'object'
					],
				),
				'order' => array(
					'type'    => 'string',
					'default' => 'desc',
				),
				'orderBy' => array(
					'type'    => 'string',
					'default' => 'date',
				),
				//layout
				'layout' => array(
					'type' => 'number',
					'default' => 1
				),
				'style' => array(
					'type' => 'number',
					'default' => 1
				),
				'column' => array(
					'type' => 'object',
					'default' => array('md' => 3, 'sm' => 2, 'xs' => 1),
				),

				//content
				'showTitle' => array(
					'type' => 'boolean',
					'default' => true
				),
				'titlePosition' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'showCategory' => array(
					'type' => 'string',
					'default' => 'default',
				),
				'categoryPosition' => array(
					'type' => 'string',
					'default' => 'leftTop',
				),
				'badgePosition' => array(
					'type' => 'string',
					'default' => 'default',
				),
				'badgePadding' => array(
					'type' => 'object',
					'default' => (object) [
						'paddingType' => 'custom',
						'unit' => 'px',
					],
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 2,],
								(object) ['key' => 'style', 'relation' => '!=', 'value' => 4],
								(object) ['key' => 'badgePosition', 'relation' => '!=', 'value' => 'default'],
							],
							'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-post-grid-wrapper .qubely-postgrid-cat-position'
						]
					]
				),
				'showDates' => array(
					'type' => 'boolean',
					'default' => true
				),
				'showComment' => array(
					'type' => 'boolean',
					'default' => true
				),
				'showAuthor' => array(
					'type' => 'boolean',
					'default' => true
				),
				'showExcerpt' => array(
					'type' => 'boolean',
					'default' => true
				),
				'excerptLimit' => array(
					'type' => 'number',
					'default' => 20
				),
				'showReadMore' => array(
					'type' => 'boolean',
					'default' => false
				),
				'verticalAlignment' => array(
					'type'    => 'string',
					'default' => 'center',
				),
				'items' => array(
					'type' => 'number',
					'default' => 2,
				),
				'excerptCharLength' => array(
					'type' => 'number',
					'default' => 45,
				),
				'postsToShow' => array(
					'type' => 'number',
					'default' => 4,
				),
				'excerptLength' => array(
					'type'    => 'number',
					'default' => 55,
				),
				//pagination
				'enablePagination' => array(
					'type' => 'boolean',
					'default' => true
				),
				//                    'paginationType' => array(
				//						'type' => 'string',
				//						'default' => 'pagition'
				//					),
				'page' => array(
					'type' => 'number',
					'default' => 1,
				),
				'pageAlignment' => array(
					'type' => 'string',
					'default' => 'center',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination {text-align: {{pageAlignment}};}'
					]]
				),
				'paginationTypography' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				'pagesColor' => array(
					'type' => 'string',
					'default' => '',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *{color: {{pagesColor}};}'
					]]
				),
				'pagesHoverColor' => array(
					'type' => 'string',
					'default' => 'center',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > a:hover, ' .
							'{{QUBELY}} .qubely-postgrid-pagination > button:hover{color: {{pagesHoverColor}};}'
					]]
				),
				'pagesActiveColor' => array(
					'type' => 'string',
					'default' => 'center',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *.current{color: {{pagesActiveColor}};}'
					]]
				),

				'pagesbgColor' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				'pagesbgHoverColor' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination a:hover, ' .
							'{{QUBELY}} .qubely-postgrid-pagination button:hover'
					]]
				),
				'pagesbgActiveColor' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > .current'
					]]
				),

				'pagesBorder' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				'pagesHoverBorder' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > a:hover, ' .
							'{{QUBELY}} .qubely-postgrid-pagination > button:hover'
					]]
				),
				'pagesActiveBorder' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *.current '
					]]
				),
				'pagesShadow' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				'pagesHoverShadow' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > a:hover, ' .
							'{{QUBELY}} .qubely-postgrid-pagination > button:hover'
					]]
				),
				'pagesActiveShadow' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *.current'
					]]
				),
				'pagesBorderRadius' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				'pagePadding' => array(
					'type' => 'object',
					'default' => (object) [
						'openPadding' => 1,
						'paddingType' => 'custom',
						'custom' => [
							'md' => '0 20 0 20',
						],
						'unit' => 'px'
					],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				'pageMargin' => array(
					'type' => 'object',
					'default' => (object) [
						'openMargin' => 1,
						'marginType' => 'custom',
						'custom' => [
							'md' => '20 7 12 7',
						],
						'unit' => 'px'
					],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'enablePagination', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-pagination > *'
					]]
				),
				//Seperator
				'showSeparator' => array(
					'type' => 'boolean',
					'default' => true
				),

				'separatorColor' => array(
					'type'    => 'string',
					'default' => '#e5e5e5',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 1],
							(object) ['key' => 'showSeparator', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-1:not(:last-child) {border-bottom-color: {{separatorColor}};}'
					]]
				),

				'separatorHeight' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 1,
						'unit' => 'px'
					),
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'style', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'showSeparator', 'relation' => '==', 'value' => true]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-1:not(:last-child){border-bottom-style: solid;border-bottom-width: {{separatorHeight}};}'
						],
					],
				),

				'separatorSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 20,
						'unit' => 'px'
					),
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'style', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'showSeparator', 'relation' => '==', 'value' => true]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-1:not(:last-child){padding-bottom: {{separatorSpace}};margin-bottom: {{separatorSpace}};}'
						],
					],
				),


				//card
				'cardBackground' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 2]],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-2'
						]
					]
				),
				'cardBorder' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'widthType' => 'global',
						'global' => (object) array(
							'md' => '1',
						),
					),
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 2]],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-2'
						]
					]
				),
				'cardBorderRadius' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openBorderRadius' => true,
						'radiusType' => 'global',
						'global' => (object) array(
							'md' => 10,
						),
					),
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 2]],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-2'
						]
					]
				),
				'cardSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 25,
						'unit' => 'px'
					),
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 2]],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-2:not(:last-child) {margin-bottom: {{cardSpace}};}'
						]
					]
				),
				'cardPadding' => array(
					'type' => 'object',
					'default' => (object) [
						'openPadding' => 1,
						'paddingType' => 'global',
						'unit' => 'px',
						'global' => (object) ['md' => 25],
					],
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 2]],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-2'
						]
					]
				),
				'cardBoxShadow' => array(
					'type' => 'object',
					'default' => (object) array(
						'blur' => 8,
						'color' => "rgba(0,0,0,0.10)",
						'horizontal' => 0,
						'inset' => 0,
						'openShadow' => true,
						'spread' => 0,
						'vertical' => 4
					),
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 2]],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-2'
						]
					]
				),

				//scart
				'stackBg' => array(
					'type' => 'object',
					'default' => (object) [],
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-3 .qubely-post-list-wrapper .qubely-post-list-content'
						],
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 2],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-grid-view.qubely-postgrid-style-3 .qubely-post-grid-content'
						]
					]
				),
				'stackBorderRadius' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openBorderRadius' => true,
						'radiusType' => 'global',
						'global' => (object) array(
							'md' => 10,
						),
					),
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-3 .qubely-post-list-wrapper .qubely-post-list-content'
						],
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 2],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-grid-view.qubely-postgrid-style-3 .qubely-post-grid-content'
						]
					]
				),
				'stackWidth' => array(
					'type' => 'object',
					'default' => (object) array(),

					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 2],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-grid-view.qubely-postgrid-style-3 .qubely-post-grid-img + .qubely-post-grid-content {width: {{stackWidth}};}'
						]
					]
				),
				'stackSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 40,
						'unit' => 'px'
					),
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-3:not(:last-child) {margin-bottom: {{stackSpace}};}'
						]
					]

				),
				'stackPadding' => array(
					'type' => 'object',
					'default' => (object) [
						'openPadding' => 1,
						'paddingType' => 'global',
						'unit' => 'px',
						'global' => (object) ['md' => 30],
					],
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-3 .qubely-post-list-wrapper .qubely-post-list-content'
						],
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 2],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-grid-view.qubely-postgrid-style-3 .qubely-post-grid-wrapper .qubely-post-grid-content'
						]
					]
				),
				'stackBoxShadow' => array(
					'type' => 'object',
					'default' => (object) array(
						'blur' => 28,
						'color' => "rgba(0,0,0,0.15)",
						'horizontal' => 0,
						'inset' => 0,
						'openShadow' => true,
						'spread' => -20,
						'vertical' => 34
					),
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 1],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-3 .qubely-post-list-wrapper .qubely-post-list-content'
						],
						(object) [
							'condition' => [
								(object) ['key' => 'layout', 'relation' => '==', 'value' => 2],
								(object) ['key' => 'style', 'relation' => '==', 'value' => 3]
							],
							'selector' => '{{QUBELY}} .qubely-post-grid-view.qubely-postgrid-style-3 .qubely-post-grid-content'
						]
					]
				),

				//typography
				'titleTypography' => array(
					'type' => 'object',
					'default' => (object) [
						'openTypography' => 1,
						'family' => "Roboto",
						'type' => "sans-serif",
						'size' => (object) ['md' => 32, 'unit' => 'px'],
					],
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showTitle', 'relation' => '==', 'value' => true]],
						'selector' => '{{QUBELY}} .qubely-postgrid-title'
					]]
				),
				'metaTypography' => array(
					'type' => 'object',
					'default' => (object) [
						'openTypography' => 1,
						'family' => "Roboto",
						'type' => "sans-serif",
						'size' => (object) ['md' => 12, 'unit' => 'px'],
					],
					'condition' => [
						(object) ['key' => 'showAuthor', 'relation' => '==', 'value' => true],
						(object) ['key' => 'showDates', 'relation' => '==', 'value' => true],
						(object) ['key' => 'showComment', 'relation' => '==', 'value' => true]
					],
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-meta']]
				),
				'excerptTypography' => array(
					'type' => 'object',
					'default' => (object) [
						'openTypography' => 1,
						'family' => "Roboto",
						'type' => "sans-serif",
						'size' => (object) ['md' => 16, 'unit' => 'px'],
					],
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showExcerpt', 'relation' => '==', 'value' => true]],
						'selector' => '{{QUBELY}} .qubely-postgrid-intro, {{QUBELY}} .qubely-postgrid-intro p'
					]]
				),
				'categoryTypography' => array(
					'type' => 'object',
					'default' => (object) [
						'openTypography' => 1,
						'family' => "Roboto",
						'type' => "sans-serif",
						'size' => (object) ['md' => 12, 'unit' => 'px'], 'spacing' => (object) ['md' => 1.1, 'unit' => 'px'], 'transform' => 'uppercase'
					],
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '!=', 'value' => 'none']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a'
					]]
				),

				//image
				'showImages' => array(
					'type' => 'boolean',
					'default' => true
				),
				'enableFixedHeight' => array(
					'type' => 'boolean',
					'default' => true
				),
				'fixedHeight' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-post-image{object-fit: cover;height: {{fixedHeight}};}']]
				),
				'imgSize' => array(
					'type'    => 'string',
					'default' => 'large',
				),
				'imageRadius' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openBorderRadius' => true,
						'radiusType' => 'global',
						'global' => (object) array(
							'md' => 10,
						),
					),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-post-img']]
				),
				'imageAnimation' => array(
					'type' => 'string',
					'default' => 'zoom-out'
				),

				//readmore link
				'buttonText' => array(
					'type' => 'string',
					'default' => 'Read More'
				),
				'readmoreStyle' => array(
					'type' => 'string',
					'default' => 'fill'
				),
				'readmoreSize' => array(
					'type' => 'string',
					'default' => 'small'
				),
				'readmoreCustomSize' => array(
					'type' => 'object',
					'default' => (object) [
						'openPadding' => 1,
						'paddingType' => 'custom',
						'unit' => 'px',
						'custom' => (object) ['md' => '5 10 5 10'],
					],
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill'],
							(object) ['key' => 'readmoreSize', 'relation' => '==', 'value' => 'custom']
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn-wrapper .qubely-postgrid-btn.qubely-button-fill.is-custom'
					]]
				),

				'readmoreTypography' => array(
					'type' => 'object',
					'default' => (object) [
						'openTypography' => 1,
						'family' => "Roboto",
						'type' => "sans-serif",
						'size' => (object) ['md' => 14, 'unit' => 'px'],
					],
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showTitle', 'relation' => '==', 'value' => true]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn'
					]]
				),
				'readmoreColor' => array(
					'type'    => 'string',
					'default' => '#fff',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'showReadMore', 'relation' => '==', 'value' => true],
							(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill']
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid a.qubely-postgrid-btn {color: {{readmoreColor}};}'
					]]

				),
				'readmoreColor2' => array(
					'type'    => 'string',
					'default' => '#2184F9',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'showReadMore', 'relation' => '==', 'value' => true],
							(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'outline']
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid a.qubely-postgrid-btn {color: {{readmoreColor2}};}'
					]]

				),
				'readmoreHoverColor' => array(
					'type'    => 'string',
					'default' => '',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showReadMore', 'relation' => '==', 'value' => true]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid a.qubely-postgrid-btn:hover {color: {{readmoreHoverColor}};}'
					]]

				),
				'readmoreBg' => array(
					'type' => 'object',
					'default' => (object) array(
						'openColor' => 1,
						'type' => 'color',
						'color' => '#2184F9',
						'gradient' => (object) [
							'color1' => '#16d03e',
							'color2' => '#1f91f3',
							'direction' => 45,
							'start' => 0,
							'stop' => 100,
							'type' => 'linear'
						],
					),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill']],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn'
					]]
				),
				'readmoreHoverBg' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill']],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn:hover'
					]]
				),
				'readmoreBorder' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill']],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn'
					]]
				),
				'readmoreBorderRadius' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openBorderRadius' => true,
						'radiusType' => 'global',
						'global' => (object) array(
							'md' => 2,
						),
					),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill']],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn'
					]]
				),
				'readmoreBoxShadow' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'readmoreStyle', 'relation' => '==', 'value' => 'fill']],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-postgrid-btn'
					]]
				),

				//color
				'categoryPadding' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openPadding' => true,
						'paddingType' => 'custom',
						'custom' => (object) array(
							'md' => '4 8 4 8',
						),
					),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'badge']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a'
					]]
				),
				'contentPadding' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-post-grid-content,{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-post-list-content']]
				),
				'categoryRadius' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openBorderRadius' => true,
						'radiusType' => 'global',
						'global' => (object) array(
							'md' => 2,
						),
					),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-category a']]
				),
				'titleColor' => array(
					'type'    => 'string',
					'default' => '#1b1b1b',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '!=', 'value' => 4],
							(object) ['key' => 'showTitle', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-title a {color: {{titleColor}};}'
					]]
				),
				'titleOverlayColor' => array(
					'type'    => 'string',
					'default' => '#fff',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 4],
							(object) ['key' => 'showTitle', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-title a {color: {{titleOverlayColor}};}'
					]]
				),
				'titleHoverColor' => array(
					'type'    => 'string',
					'default' => '#FF0096',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showTitle', 'relation' => '==', 'value' => true]],
						'selector' => '{{QUBELY}} .qubely-postgrid-title a:hover {color: {{titleHoverColor}};}'
					]]
				),
				'categoryColor' => array(
					'type'    => 'string',
					'default' => '#FF0096',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'default']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a {color: {{categoryColor}};}'
					]]
				),
				'categoryColor2' => array(
					'type'    => 'string',
					'default' => '#fff',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'badge']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a {color: {{categoryColor2}};}'
					]]
				),
				'categoryHoverColor' => array(
					'type'    => 'string',
					'default' => '#FF0096',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'default']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a:hover {color: {{categoryHoverColor}};}'
					]]
				),
				'categoryBackground' => array(
					'type'    => 'string',
					'default' => '#FF0096',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'badge']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a {background: {{categoryBackground}};}'
					]]
				),
				'categoryHoverBackground' => array(
					'type'    => 'string',
					'default' => '#e00e89',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'badge']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a:hover {background: {{categoryHoverBackground}};}'
					]]
				),

				'categoryHoverColor2' => array(
					'type'    => 'string',
					'default' => '#fff',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'badge']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category a:hover {color: {{categoryHoverColor2}};}'
					]]
				),
				'metaColor' => array(
					'type'    => 'string',
					'default' => '#9B9B9B',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '!=', 'value' => 4]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-meta a {color: {{metaColor}};} {{QUBELY}} .qubely-postgrid-meta {color: {{metaColor}};} {{QUBELY}} .qubely-postgrid-meta span:before {background: {{metaColor}};}'
					]]
				),
				'metaOverlayColor' => array(
					'type'    => 'string',
					'default' => '#fff',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 4]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-meta a {color: {{metaOverlayColor}};} {{QUBELY}} .qubely-postgrid-meta {color: {{metaOverlayColor}};} {{QUBELY}} .qubely-postgrid-meta span:before {background: {{metaOverlayColor}};}'
					]]
				),
				'excerptColor' => array(
					'type'    => 'string',
					'default' => '#9B9B9B',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '!=', 'value' => 4],
							(object) ['key' => 'showExcerpt', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-intro {color: {{excerptColor}};}'
					]]
				),
				'excerptColor2' => array(
					'type'    => 'string',
					'default' => '#fff',
					'style' => [(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 4],
							(object) ['key' => 'showExcerpt', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{QUBELY}} .qubely-postgrid-intro {color: {{excerptColor2}};}'
					]]
				),

				//design
				'spacer' => 	array(
					'type' => 'object',
					'default' => (object) array(
						'spaceTop' => (object) ['md' => '10', 	'unit' => "px"],
						'spaceBottom' => (object) ['md' => '10', 'unit' => "px"],
					),
					'style' => [(object) ['selector' => '{{QUBELY}}']]
				),
				'contentPosition' =>  array(
					'type' => 'string',
					'default' => 'center',
				),
				'girdContentPosition' =>  array(
					'type' => 'string',
					'default' => 'center',
				),
				'color' => array(
					'type'    => 'string',
					'default' => '',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 1]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid .qubely-post-list-content {color: {{color}};}'
					]]
				),
				'bgColor' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 1]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper'
					]]
				),
				'border' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 1]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper'
					]]
				),
				'borderRadius' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 1]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper'
					]]
				),
				'padding' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 1]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper'
					]]
				),
				'boxShadow' => array(
					'type' => 'object',
					'default' => (object) array(),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 1]],
						'selector' => '{{QUBELY}} .qubely-postgrid-wrapper'
					]]
				),

				//overlay
				'overlayBg' => array(
					'type' => 'object',
					'default' => (object) [
						'openColor' => 1,
						'type' => 'color',
						'color' => '#101a3b',
						'gradient' => (object) [
							'color1' => '#071b0b',
							'color2' => '#101a3b',
							'direction' => 45,
							'start' => 0,
							'stop' => 100,
							'type' => 'linear'
						],
					],
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 4]],
						'selector' => '{{QUBELY}} .qubely-postgrid-style-4:before'
					]]
				),
				'overlayHoverBg' => array(
					'type' => 'object',
					'default' => (object) [
						'openColor' => 1,
						'type' => 'color',
						'color' => '#4c4e54',
						'gradient' => (object) [
							'color1' => '#4c4e54',
							'color2' => '#071b0b',
							'direction' => 45,
							'start' => 0,
							'stop' => 100,
							'type' => 'linear'
						],
					],
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 4]],
						'selector' => '{{QUBELY}} .qubely-postgrid-style-4:hover:before'
					]]
				),
				'overlayBorderRadius' => array(
					'type' => 'object',
					'default' => (object) array(
						'unit' => 'px',
						'openBorderRadius' => true,
						'radiusType' => 'global',
						'global' => (object) array(
							'md' => 20,
						),
					),
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 4]],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-4'
						]
					]
				),
				'overlaySpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 30,
						'unit' => 'px'
					),
					'style' => [
						(object) [
							'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 4]],
							'selector' => '{{QUBELY}} .qubely-post-list-view.qubely-postgrid-style-4:not(:last-child) {margin-bottom: {{overlaySpace}};}'
						]
					]
				),
				'overlayHeight' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 300,
						'unit' => 'px'
					),
					'style' => [
						(object) [
							'condition' => [
								(object) ['key' => 'style', 'relation' => '==', 'value' => 4]
							],
							'selector' => '{{QUBELY}} .qubely-postgrid-style-4 {height: {{overlayHeight}};}'
						]
					]
				),
				'overlayBlend' => array(
					'type'    => 'string',
					'default' => '',
					'style' => [(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 4]],
						'selector' => '{{QUBELY}} .qubely-postgrid.qubely-post-list-view.qubely-postgrid-style-4:before {mix-blend-mode: {{overlayBlend}};}'
					]]
				),
				//Spacing
				'columnGap' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 30,
						'unit' => 'px'
					),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '==', 'value' => 2]],
						'selector' => '{{QUBELY}} .qubely-postgrid-column {grid-column-gap: {{columnGap}};}, {{QUBELY}} .qubely-postgrid-column {grid-row-gap: {{columnGap}};}'
					]]
				),
				'titleSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 10,
						'unit' => 'px'
					),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-title {padding-bottom: {{titleSpace}};}']]
				),
				'categorySpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 5,
						'unit' => 'px'
					),
					'style' => [(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => 'default']],
						'selector' => '{{QUBELY}} .qubely-postgrid-category {display:inline-block;padding-bottom: {{categorySpace}};}'
					]]
				),
				'metaSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 10,
						'unit' => 'px'
					),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-meta {padding-bottom: {{metaSpace}};}']]
				),
				'excerptSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 10,
						'unit' => 'px'
					),
					'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-intro {padding-bottom: {{excerptSpace}};}']]
				),
				'postSpace' => array(
					'type' => 'object',
					'default' => (object) array(
						'md' => 10,
						'unit' => 'px'
					),
					// 'style' => [(object) ['selector' => '{{QUBELY}} .qubely-postgrid-wrapper .qubely-postgrid']]
				),
				'interaction' => array(
					'type' => 'object',
					'default' => (object) array(),
				),
				'animation' => array(
					'type' => 'object',
					'default' => (object) array(),
				),
				'globalZindex' => array(
					'type' => 'string',
					'default' => '0',
					'style' => [(object) ['selector' => '{{QUBELY}} {z-index:{{globalZindex}};}']]
				),
				'hideTablet' => array(
					'type' => 'boolean',
					'default' => false,
					'style' => [(object) ['selector' => '{{QUBELY}}{display:none;}']]
				),
				'hideMobile' => array(
					'type' => 'boolean',
					'default' => false,
					'style' => [(object) ['selector' => '{{QUBELY}}{display:none;}']]
				),
				'globalCss' => array(
					'type' => 'string',
					'default' => '',
					'style' => [(object) ['selector' => '']]
				),
				// 'showContextMenu' => array(
				// 	'type' => 'boolean',
				// 	'default' => true
				// ),
			),
			'render_callback' => 'render_block_qubely_postgrid'
		)
	);
}

function pagination_bar($max_pages, $current_page)
{
	if ($max_pages > 1) {
		$big = 9999999;
		return paginate_links(array(
			'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
			'format'        => '?paged=%#%',
			'current' => $current_page,
			'total' => $max_pages,
			'prev_text'          => sprintf(__('%1$s Prev', 'qubely-pro'), "<span class='fas fa-angle-left'></span>"),
			'next_text'          => sprintf(__('Next %1$s', 'qubely-pro'), "<span class='fas fa-angle-right'></span>"),
		));
	}
}

function render_block_qubely_postgrid($att)
{
	$layout 		        = isset($att['layout']) ? $att['layout'] : 3;
	$uniqueId 		        = isset($att['uniqueId']) ? $att['uniqueId'] : '';
	$className 		        = isset($att['className']) ? $att['className'] : '';
	$style 		            = isset($att['style']) ? $att['style'] : 3;
	$column 		        = isset($att['column']) ? $att['column'] : 3;
	$numbers 		        = isset($att['postsToShow']) ? $att['postsToShow'] : 3;
	$limit 		            = isset($att['excerptLimit']) ? $att['excerptLimit'] : 3;
	$showCategory 		    = isset($att['showCategory']) ? $att['showCategory'] : 'default';
	$categoryPosition 		= isset($att['categoryPosition']) ? $att['categoryPosition'] : 'leftTop';
	$contentPosition 		= isset($att['contentPosition']) ? $att['contentPosition'] : 'center';
	$girdContentPosition 	= isset($att['girdContentPosition']) ? $att['girdContentPosition'] : 'center';
	$showTitle 		        = isset($att['showTitle']) ? $att['showTitle'] : 1;
	$showAuthor 		    = isset($att['showAuthor']) ? $att['showAuthor'] : 1;
	$showDates 		        = isset($att['showDates']) ? $att['showDates'] : 1;
	$showComment 		    = isset($att['showComment']) ? $att['showComment'] : 1;
	$showExcerpt 		    = isset($att['showExcerpt']) ? $att['showExcerpt'] : 1;
	$showReadMore 		    = isset($att['showReadMore']) ? $att['showReadMore'] : 1;
	$titlePosition 		    = isset($att['titlePosition']) ? $att['titlePosition'] : 1;
	$buttonText 		    = isset($att['buttonText']) ? $att['buttonText'] : 'Read More';
	$readmoreSize 		    = isset($att['readmoreSize']) ? $att['readmoreSize'] : 'small';
	$readmoreStyle 		    = isset($att['readmoreStyle']) ? $att['readmoreStyle'] : 'fill';
	$showImages 		    = isset($att['showImages']) ? $att['showImages'] : 1;
	$imgSize 		        = isset($att['imgSize']) ? $att['imgSize'] : 'large';
	$showBadge 		        = isset($att['showBadge']) ? $att['showBadge'] : 1;
	$order 		            = isset($att['order']) ? $att['order'] : 'DESC';
	$imageAnimation 		= isset($att['imageAnimation']) ? $att['imageAnimation'] : '';
	$orderBy 		        = isset($att['orderBy']) ? $att['orderBy'] : 'date';
	$categories             = $att['categories'];
	$postType               = isset($att['postType']) ? $att['postType'] : 'post';
	$tags                   = $att['tags'];
	$taxonomy               = $att['taxonomy'];
	$taxonomyType           = isset($att['taxonomyType']) ? $att['taxonomyType'] : 'category';
	$customTaxonomies       = $att['customTaxonomies'];
	
	$animation 		        = isset($att['animation']) ? (count((array) $att['animation']) > 0 &&  $att['animation']['animation'] ? 'data-qubelyanimation="' . htmlspecialchars(json_encode($att['animation']), ENT_QUOTES, 'UTF-8') . '"' : '') : '';


	$interaction = '';
	if (isset($att['interaction'])) {
		if (!empty((array) $att['interaction'])) {
			if (isset($att['interaction']['while_scroll_into_view'])) {
				if ($att['interaction']['while_scroll_into_view']['enable']) {
					$interaction = 'qubley-block-interaction';
				}
			}
			if (isset($att['interaction']['mouse_movement'])) {
				if ($att['interaction']['mouse_movement']['enable']) {
					$interaction = 'qubley-block-interaction';
				}
			}
		}
	}


	$paged = 1;
	if (!empty(get_query_var('page')) || !empty(get_query_var('paged'))) {
		$paged = is_front_page() ? get_query_var('page') : get_query_var('paged');
	}

	$args = array(
		'post_type' 		=> $postType,
		'posts_per_page' 	=> esc_attr($numbers),
		'order' 			=> esc_attr($order),
		'orderby' 			=> esc_attr($orderBy),
		'status' 			=> 'publish',
		'paged'             => $paged
	);

	$active_taxonomy_array = $att['taxonomy'] == 'categories' ? $categories : $tags;
	$active_taxonomy_name  = $att['taxonomy'] == 'categories' ? 'category__in' : 'tag__in';

	$custom_tax_query = array(
		'taxonomy' => $taxonomyType,
		'terms'    => array_column( $customTaxonomies, 'value' ),
	);
	
	if ( 'post' === $postType ) {
		if ( is_array( $active_taxonomy_array ) && count( $active_taxonomy_array ) > 0 ) {
			$args[ $active_taxonomy_name ] = array_column( $active_taxonomy_array, 'value' );
		}
	} else {
		if ( is_array( $customTaxonomies ) && count( $customTaxonomies ) > 0 ) {
			$args['tax_query'] = array( $custom_tax_query );
		}
	}

	$query = new WP_Query($args);

	# The Loop.
	$html = '';
	//excerpt;
	if (!function_exists('qubely_excerpt_max_charlength')) :
		function qubely_excerpt_max_charlength($limit)
		{
			$excerpt = get_the_excerpt();
			if (str_word_count($excerpt, 0) > $limit) {
				$words = str_word_count($excerpt, 2);
				$pos = array_keys($words);
				$text = substr($excerpt, 0, $pos[$limit]);
				return $text;
			}
			return $excerpt;
		}
	endif;

	//column
	if ($layout == 2) {
		$col = (' qubely-postgrid-column qubely-postgrid-column-md' . $column['md'] . ' qubely-postgrid-column-sm' . $column['sm'] . ' qubely-postgrid-column-xs' . $column['xs']);
	} else {
		$col = "";
	}
	$class = 'wp-block-qubely-postgrid qubely-block-' . $uniqueId;
	if (isset($att['align'])) {
		$class .= ' align' . $att['align'];
	}
	if (isset($att['className'])) {
		$class .= $att['className'];
	}

	if ($query->have_posts()) {
		$html .= '<div class="' . $class . '">';
		$html .= '<div class="qubely-postgrid-wrapper ' . $interaction . ' qubely-postgrid-layout-' . esc_attr($layout) . esc_attr($col) . '" ' . $animation . '>';
		while ($query->have_posts()) {
			$query->the_post();
			$id = get_post_thumbnail_id();
			$src = wp_get_attachment_image_src($id, $imgSize);
			$src = has_post_thumbnail( get_the_ID() ) ? get_the_post_thumbnail_url( get_the_ID(), $imgSize ) : '';
			$image = '<img class="qubely-post-image" src="' . esc_url( $src ) . '" alt="' . get_the_title() . '"/>';
			$title = '<h3 class="qubely-postgrid-title"><a href="' . esc_url(get_the_permalink()) . '">' . get_the_title() . '</a></h3>';
			$category = '<span class="qubely-postgrid-category">' . ('post' === $postType ? get_the_category_list(' ') : get_the_term_list(get_the_ID(), $taxonomyType, ' ')) . '</span>';
			$meta = ($showAuthor == 1) ? '<span><i class="fas fa-user"></i> ' . __('By ', 'qubely') . get_the_author_posts_link() . '</span>' : '';
			$meta .= ($showDates == 1) ? '<span><i class="far fa-calendar-alt"></i> ' . get_the_date() . '</span>' : '';
			$meta .= ($showComment == 1) ? '<span><i class="fas fa-comment"></i> ' . get_comments_number('0', '1', '%') . '</span>' : '';
			$btn = '<div class="qubely-postgrid-btn-wrapper"><a class="qubely-postgrid-btn qubely-button-' . esc_attr($readmoreStyle) . ' is-' . esc_attr($readmoreSize) . '" href="' . esc_url(get_the_permalink()) . '">' . esc_attr($buttonText) . '</a></div>';
			$excerpt = '<div class="qubely-postgrid-intro">' . qubely_excerpt_max_charlength(esc_attr($limit)) . '</div>';

			if ($layout === 1) {
				$html .= '<div class="qubely-postgrid qubely-post-list-view qubely-postgrid-style-' . esc_attr($style) . '">';
				$html .= '<div class="qubely-post-list-wrapper qubely-post-list-' .  esc_attr(($layout == 2 && $style === 3) ? $contentPosition : $girdContentPosition)  . '">';
				if (($showImages == 1) && has_post_thumbnail()) {
					if ($showCategory == 'badge'  && $style == 4) {
						$html .= '<div class="qubely-postgrid-cat-position qubely-postgrid-cat-position-' . esc_attr($categoryPosition) . '">';
						$html .= $category;
						$html .= '</div>';
					}
					$html .= '<div class="qubely-post-list-img qubely-post-img qubely-post-img-' . esc_attr($imageAnimation) . '">';
					$html .= '<a href="' . esc_url(get_the_permalink()) . '">';
					$html .= $image;
					$html .= '</a>';
					if ($showCategory == 'badge'  && $style != 4) {
						$html .= '<div class="qubely-postgrid-cat-position qubely-postgrid-cat-position-' . esc_attr($categoryPosition) . '">';
						$html .= $category;
						$html .= '</div>';
					}
					$html .= '</div>'; //qubely-post-list-img
				}
				$html .= '<div class="qubely-post-list-content">';
				if ($showCategory == 'default') {
					$html .= $category;
				}
				if (($showTitle == 1) && ($titlePosition == 1)) {
					$html .= $title;
				}
				if (($showAuthor == 1) || ($showDates == 1) || ($showComment == 1)) {
					$html .= '<div class="qubely-postgrid-meta">';
					$html .= $meta;
					$html .= '</div>';
				}
				if (($showTitle === 1) || ($titlePosition == 0)) {
					$html .= $title;
				}
				if ($showExcerpt == 1) {
					$html .= $excerpt;
				}
				if ($showReadMore == 1) {
					$html .= $btn;
				}
				$html .= '</div>'; //qubely-post-list-content
				$html .= '</div>'; //qubely-post-list-wrap
				$html .= '</div>'; //qubely-postgrid
			}
			if ($layout === 2) {
				$html .= '<div class="qubely-postgrid qubely-post-grid-view qubely-postgrid-style-' . esc_attr($style) . '">';
				$html .= '<div class="qubely-post-grid-wrapper qubely-post-grid-' . esc_attr(($layout == 2 && $style === 3) ? $contentPosition : $girdContentPosition)  . '">';
				if (($showImages == 1) && has_post_thumbnail()) {
					$html .= '<div class="qubely-post-grid-img qubely-post-img qubely-post-img-' . esc_attr($imageAnimation) . '">';
					$html .= '<a href="' . esc_url(get_the_permalink()) . '">';
					$html .= $image;
					$html .= '</a>';
					if ($showCategory == 'badge'  && $style != 4) {
						$html .= '<div class="qubely-postgrid-cat-position qubely-postgrid-cat-position-' . esc_attr($categoryPosition) . '">';
						$html .= $category;
						$html .= '</div>';
					}
					$html .= '</div>'; //qubely-post-grid-img
				}
				$html .= '<div class="qubely-post-grid-content">';
				if ($showCategory == 'default') {
					$html .= $category;
				}
				if ($showCategory == 'badge'  && $style == 4) {
					$html .= '<div class="qubely-postgrid-cat-position qubely-postgrid-cat-position-' . esc_attr($categoryPosition) . '">';
					$html .= $category;
					$html .= '</div>';
				}
				if (($showTitle == 1) && ($titlePosition == 1)) {
					$html .= $title;
				}
				if (($showAuthor == 1) || ($showDates == 1) || ($showComment == 1)) {
					$html .= '<div class="qubely-postgrid-meta">';
					$html .= $meta;
					$html .= '</div>';
				}
				if (($showTitle === 1) || ($titlePosition == 0)) {
					$html .= $title;
				}
				if ($showExcerpt == 1) {
					$html .= $excerpt;
				}
				if ($showReadMore == 1) {
					$html .= $btn;
				}
				$html .= '</div>'; //qubely-post-grid-content
				$html .= '</div>'; //qubely-post-grid-wrap
				$html .= '</div>'; //qubely-postgrid
			}
		}
		$html .= '</div>';
		$html .= '<div class="qubely-postgrid-pagination">' . pagination_bar($query->max_num_pages, $paged) . '</div>';
		$html .= '</div>';
		wp_reset_postdata();
	}
	return $html;
}
if (!defined('QUBELY_PRO_VERSION')) {
	add_action('init', 'register_block_qubely_postgrid', 100);
}
