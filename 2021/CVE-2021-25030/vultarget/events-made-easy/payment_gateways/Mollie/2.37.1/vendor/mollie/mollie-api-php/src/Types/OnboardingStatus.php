<?php

namespace Mollie\Api\Types;

class OnboardingStatus
{
    /**
     * The onboarding is not completed and the merchant needs to provide (more) information
     */
    const NEEDS_DATA = 'needs-data';

    /**
     * The merchant provided all information and Mollie needs to check this
     */
    const IN_REVIEW = 'in-review';

    /**
     * The onboarding is completed
     */
    const COMPLETED = 'completed';
}
