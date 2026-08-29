<?php

namespace App\Enums;

enum ListingCategory: string
{
    case Machinery = 'Machinery';
    case Vehicles = 'Vehicles';
    case CommercialProperty = 'Commercial Property';
    case IntangibleAssets = 'Intangible Assets';
}
