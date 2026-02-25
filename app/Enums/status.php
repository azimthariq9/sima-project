<?php

namespace App\Enums;

enum Status:string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending approval';
    case DELETED = 'deleted';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
    case ABSENT = 'absent';
    case PRESENT = 'present';
    case EXCUSED = 'excused';

}
