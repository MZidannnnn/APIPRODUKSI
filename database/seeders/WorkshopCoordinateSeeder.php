<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkshopCoordinate;

class WorkshopCoordinateSeeder extends Seeder
{
    public function run(): void
    {
        WorkshopCoordinate::create([
            'latitude' => '-3.2994',
            'longitude' => '114.5933'
        ]);
    }
}
