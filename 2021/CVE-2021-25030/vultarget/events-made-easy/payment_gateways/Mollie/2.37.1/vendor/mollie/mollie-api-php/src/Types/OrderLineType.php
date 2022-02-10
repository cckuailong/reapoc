<?php

namespace Mollie\Api\Types;

class OrderLineType
{
    const TYPE_PHYSICAL = 'physical';
    const TYPE_DISCOUNT = 'discount';
    const TYPE_DIGITAL = 'digital';
    const TYPE_SHIPPING_FEE = 'shipping_fee';
    const TYPE_STORE_CREDIT = 'store_credit';
    const TYPE_GIFT_CARD = 'gift_card';
    const TYPE_SURCHARGE = 'surcharge';
}
