<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Draft = 'Draft';
    case PendingReview = 'Pending Review';
    case Published = 'Published';
    case Expired = 'Expired';
}
