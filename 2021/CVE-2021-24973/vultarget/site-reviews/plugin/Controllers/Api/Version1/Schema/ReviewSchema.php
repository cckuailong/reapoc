<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema;

class ReviewSchema
{
    /**
     * @var \WP_REST_Post_Meta_Fields
     */
    protected $meta;

    public function __construct()
    {
        $this->meta = new \WP_REST_Post_Meta_Fields(glsr()->post_type);
    }

    /**
     * @return array
     */
    public function schema()
    {
        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'properties' => $this->properties(),
            'title' => glsr()->post_type,
            'type' => 'object',
        ];
        $schema['properties']['links'] = $this->links();
        $schema['properties']['meta'] = $this->meta->get_field_schema();
        $propertyKeys = array_keys($schema['properties']);
        $schema = apply_filters('rest_'.glsr()->post_type.'_item_schema', $schema);
        $newPropertyKeys = array_diff(array_keys($schema['properties']), $propertyKeys);
        if (count($newPropertyKeys) > 0) {
            _doing_it_wrong(__METHOD__, sprintf(_x('Please use %s to add new schema properties.', 'admin-text', 'site-reviews'), 'register_rest_field'), '5.4.0');
        }
        return $schema;
    }

    /**
     * @return array
     */
    protected function links()
    {
        $href = rest_url(glsr()->id.'/v1/reviews/{id}');
        return [
            [
                'href' => $href,
                'rel' => 'https://api.w.org/action-publish',
                'title' => _x('The current user can publish this review.', 'admin-text', 'site-reviews'),
                'targetSchema' => [
                    'type'  => 'object',
                    'properties' => [
                        'status' => [
                            'enum' => ['future', 'publish'],
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            [
                'href' => $href,
                'rel' => 'https://api.w.org/action-unfiltered-html',
                'title' => _x('The current user can post unfiltered HTML markup and JavaScript.', 'admin-text', 'site-reviews'),
                'targetSchema' => [
                    'type'  => 'object',
                    'properties' => [
                        'content' => [
                            'raw' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
            [
                'href' => $href,
                'rel' => 'https://api.w.org/action-assign-author',
                'title' => _x('The current user can change the author on this review.', 'admin-text', 'site-reviews'),
                'targetSchema' => [
                    'type'  => 'object',
                    'properties' => [
                        'author' => [
                            'raw' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
            [
                'href' => $href,
                'rel' => 'https://api.w.org/action-assign-'.glsr()->taxonomy,
                'title' => sprintf(_x('The current user can assign terms in the %s taxonomy.', 'admin-text', 'site-reviews'), glsr()->taxonomy),
                'targetSchema' => [
                    'type'  => 'object',
                    'properties' => [
                        glsr()->taxonomy => [
                            'items' => ['type' => 'integer'],
                            'type' => 'array',
                        ],
                    ],
                ],
            ],
            [
                'href' => $href,
                'rel' => 'https://api.w.org/action-create-'.glsr()->taxonomy,
                'title' => sprintf(_x('The current user can create terms in the %s taxonomy.', 'admin-text', 'site-reviews'), glsr()->taxonomy),
                'targetSchema' => [
                    'type'  => 'object',
                    'properties' => [
                        glsr()->taxonomy => [
                            'items' => ['type' => 'integer'],
                            'type' => 'array',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function properties()
    {
        return [
            'assigned_posts' => [
                'context' => ['edit', 'view'],
                'description' => _x('The posts assigned to the review of any public post type.', 'admin-text', 'site-reviews'),
                'items' => ['type' => 'integer'],
                'type' => 'array',
            ],
            'assigned_terms' => [
                'context' => ['edit', 'view'],
                'description' => sprintf(_x('The terms assigned to the review in the %s taxonomy.', 'admin-text', 'site-reviews'), glsr()->taxonomy),
                'items' => ['type' => 'integer'],
                'type' => 'array',
            ],
            'assigned_users' => [
                'context' => ['edit', 'view'],
                'description' => _x('The users assigned to the review.', 'admin-text', 'site-reviews'),
                'items' => ['type' => 'integer'],
                'type' => 'array',
            ],
            'author' => [
                'context' => ['edit', 'view'],
                'description' => _x('The ID for the author of the review.', 'admin-text', 'site-reviews'),
                'type' => 'integer',
            ],
            'avatar' => [
                'context' => ['edit', 'view'],
                'description' => _x('The avatar of the person who submitted the review.', 'admin-text', 'site-reviews'),
                'type' => 'string',
            ],
            'content' => [
                'context' => ['edit', 'view'],
                'description' => _x('The content of the review.', 'admin-text', 'site-reviews'),
                'type' => 'string',
            ],
            'custom' => [
                'arg_options' => [
                    'sanitize_callback' => null,
                    'validate_callback' => null,
                ],
                'context' => ['edit', 'view'],
                'description' => _x('Custom fields.', 'admin-text', 'site-reviews'),
                'type' => 'object',
            ],
            'date' => [
                'context' => ['edit', 'view'],
                'description' => _x('The date the review was published, in the site\'s timezone.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'type' => ['null', 'string'],
            ],
            'date_gmt' => [
                'context' => ['edit', 'view'],
                'description' => _x('The date the review was published, as GMT.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'type' => ['null', 'string'],
            ],
            'email' => [
                'context' => ['edit'],
                'description' => _x('The email of the person who submitted the review.', 'admin-text', 'site-reviews'),
                'type' => 'string',
            ],
            'id' => [
                'context' => ['edit', 'view'],
                'description' => _x('Unique identifier for the review.', 'admin-text', 'site-reviews'),
                'readonly' => true,
                'type' => 'integer',
            ],
            'ip_address' => [
                'context' => ['edit'],
                'description' => _x('The IP address of the person who submitted the review.', 'admin-text', 'site-reviews'),
                'format' => 'ip',
                'type' => 'string',
            ],
            'is_approved' => [
                'context' => ['view'],
                'description' => _x('If the review has an approved status.', 'admin-text', 'site-reviews'),
                'readonly' => true,
                'type' => 'boolean',
            ],
            'is_modified' => [
                'context' => ['view'],
                'description' => _x('If the review has been modified.', 'admin-text', 'site-reviews'),
                'readonly' => true,
                'type' => 'boolean',
            ],
            'is_pinned' => [
                'context' => ['edit', 'view'],
                'description' => _x('If the review has been pinned.', 'admin-text', 'site-reviews'),
                'type' => 'boolean',
            ],
            'modified' => [
                'context' => ['edit', 'view'],
                'description' => _x('The date the review was last modified, in the site\'s timezone.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'readonly' => true,
                'type' => 'string',
            ],
            'modified_gmt' => [
                'context' => ['edit', 'view'],
                'description' => _x('The date the review was last modified, as GMT.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'readonly' => true,
                'type' => 'string',
            ],
            'name' => [
                'context' => ['edit', 'view'],
                'description' => _x('The name of the person who submitted the review.', 'admin-text', 'site-reviews'),
                'type' => 'string',
            ],
            'rating' => [
                'context' => ['edit', 'view'],
                'description' => _x('The rating of the review.', 'admin-text', 'site-reviews'),
                'type' => 'integer',
            ],
            'response' => [
                'context' => ['edit', 'view'],
                'description' => _x('The response given to the review.', 'admin-text', 'site-reviews'),
                'type' => 'string',
            ],
            'status' => [
                'context' => ['edit', 'view'],
                'description' => _x('A named status for the review.', 'admin-text', 'site-reviews'),
                'enum' => ['all', 'approved', 'pending', 'publish', 'unapproved'],
                'type' => 'string',
            ],
            'terms' => [
                'context' => ['edit', 'view'],
                'description' => _x('If the terms were accepted when the review was submitted.', 'admin-text', 'site-reviews'),
                'type' => 'boolean',
            ],
            'title' => [
                'context' => ['edit', 'view'],
                'description' => _x('The title of the review.', 'admin-text', 'site-reviews'),
                'type' => 'string',
            ],
            'type' => [
                'context' => ['edit', 'view'],
                'description' => _x('Type of Review for the object.', 'admin-text', 'site-reviews'),
                'enum' => glsr()->retrieveAs('array', 'review_types', []),
                'type' => 'string',
            ],
        ];
    }
}
