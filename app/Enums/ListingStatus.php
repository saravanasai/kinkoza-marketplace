<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Draft = 'DRAFT';
    case PendingReview = 'PENDING_REVIEW';
    case Published = 'PUBLISHED';
    case Expired = 'EXPIRED';
}
