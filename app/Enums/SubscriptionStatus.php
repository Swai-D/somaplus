<?php

namespace App\Enums;

/**
 * Where a school's subscription stands. A lapsed subscription never deletes data:
 * it moves from active to grace and then to read-only.
 */
enum SubscriptionStatus: string
{
    case Active = 'active';
    case Grace = 'grace';
    case ReadOnly = 'read_only';
}
