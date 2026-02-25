<?php

namespace App\Enums;

enum status:string
{
    case active = 'active';
    case inactive = 'inactive';
    case pending = 'pending approval';
    case deleted = 'deleted';
    case rejected = 'rejected';
    case approved = 'approved';
    case absent = 'absent';
    case present = 'present';
    case excused = 'excused';

}
