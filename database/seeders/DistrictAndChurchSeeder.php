<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictAndChurchSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Abeokuta District' => [
                'zone'     => 'Abeokuta Zone',
                'churches' => [
                    'Abeokuta Central SDA Church',
                    'Ake SDA Church',
                    'Iyana Mortuary SDA Church',
                    'Lafenwa SDA Church',
                ],
            ],
            'Sagamu District' => [
                'zone'     => 'Sagamu Zone',
                'churches' => [
                    'Sagamu Central SDA Church',
                    'Ikenne SDA Church',
                    'Ilishan SDA Church',
                ],
            ],
            'Ijebu-Ode District' => [
                'zone'     => 'Ijebu Zone',
                'churches' => [
                    'Ijebu-Ode Central SDA Church',
                    'Ijebu-Igbo SDA Church',
                    'Odogbolu SDA Church',
                ],
            ],
            'Ota District' => [
                'zone'     => 'Ota Zone',
                'churches' => [
                    'Ota Central SDA Church',
                    'Sango SDA Church',
                    'Owode SDA Church',
                ],
            ],
        ];

        foreach ($data as $districtName => $info) {
            $district = District::firstOrCreate(
                ['name' => $districtName],
                ['zone' => $info['zone']],
            );

            foreach ($info['churches'] as $churchName) {
                Church::firstOrCreate(
                    ['name' => $churchName, 'district_id' => $district->id],
                );
            }
        }

        $this->command->info('Districts and churches seeded.');
    }
}
