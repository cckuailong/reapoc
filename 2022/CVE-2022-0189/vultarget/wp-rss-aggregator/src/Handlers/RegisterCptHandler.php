<?php

namespace RebelCode\Wpra\Core\Handlers;

/**
 * A handler for registering custom post types.
 *
 * @since 4.13
 */
class RegisterCptHandler
{
    /**
     * The CPT name.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $cptName;

    /**
     * The CPT args.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $cptArgs;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string $cptName The CPT name.
     * @param array  $cptArgs The CPT args.
     */
    public function __construct($cptName, array $cptArgs)
    {
        $this->cptName = $cptName;
        $this->cptArgs = $cptArgs;
    }

    /**
     * @since 4.13
     */
    public function __invoke()
    {
        register_post_type($this->cptName, $this->cptArgs);
    }
}
