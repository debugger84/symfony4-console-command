<?php

namespace App\Entity\Enum;

use MyCLabs\Enum\Enum;

/**
 * The type of event
 * @method static EventType install()
 * @method static EventType purchase()
 */
class EventType extends Enum
{
    private const install = 'install';
    private const purchase = 'purchase';
}
