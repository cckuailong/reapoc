<?php
namespace Braintree\Dispute;

use Braintree\Instance;

/**
 * Evidence details for a dispute
 *
 * @package    Braintree
 *
 * @property-read string $category
 * @property-read string $comment
 * @property-read date   $created_at
 * @property-read string $id
 * @property-read string $sent_to_processor_at
 * @property-read string $url
 * @property-read string $tag
 * @property-read string $sequenceNumber
 */
class EvidenceDetails extends Instance
{
    public function __construct($attributes)
    {
        if (array_key_exists('category', $attributes)) {
            $attributes['tag'] = $attributes['category'];
        }
        parent::__construct($attributes);
    }
}

class_alias('Braintree\Dispute\EvidenceDetails', 'Braintree_Dispute_EvidenceDetails');
