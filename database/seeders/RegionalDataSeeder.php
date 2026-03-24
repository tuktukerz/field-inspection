<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\SubDistrict;
use Illuminate\Database\Seeder;

class RegionalDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            '31.71.01' => [
                'name' => 'Gambir',
                'villages' => [
                    '31.71.01.1001' => 'Gambir',
                    '31.71.01.1002' => 'Kebon Kelapa',
                    '31.71.01.1003' => 'Petojo Selatan',
                    '31.71.01.1004' => 'Duri Pulo',
                    '31.71.01.1005' => 'Cideng',
                    '31.71.01.1006' => 'Petojo Utara',
                ],
            ],
            '31.71.02' => [
                'name' => 'Sawah Besar',
                'villages' => [
                    '31.71.02.1001' => 'Pasar Baru',
                    '31.71.02.1002' => 'Karang Anyar',
                    '31.71.02.1003' => 'Kartini',
                    '31.71.02.1004' => 'Gunung Sahari Utara',
                    '31.71.02.1005' => 'Mangga Dua Selatan',
                ],
            ],
            '31.71.03' => [
                'name' => 'Kemayoran',
                'villages' => [
                    '31.71.03.1001' => 'Gunung Sahari Selatan',
                    '31.71.03.1002' => 'Kemayoran',
                    '31.71.03.1003' => 'Kebon Kosong',
                    '31.71.03.1004' => 'Utan Panjang',
                    '31.71.03.1005' => 'Harapan Mulia',
                    '31.71.03.1006' => 'Cempaka Baru',
                    '31.71.03.1007' => 'Serdang',
                    '31.71.03.1008' => 'Sumur Batu',
                ],
            ],
            '31.71.04' => [
                'name' => 'Senen',
                'villages' => [
                    '31.71.04.1001' => 'Senen',
                    '31.71.04.1002' => 'Kwitang',
                    '31.71.04.1003' => 'Kenari',
                    '31.71.04.1004' => 'Paseban',
                    '31.71.04.1005' => 'Kramat',
                    '31.71.04.1006' => 'Bungur',
                ],
            ],
            '31.71.05' => [
                'name' => 'Cempaka Putih',
                'villages' => [
                    '31.71.05.1001' => 'Cempaka Putih Timur',
                    '31.71.05.1002' => 'Cempaka Putih Barat',
                    '31.71.05.1003' => 'Rawasari',
                ],
            ],
            '31.71.06' => [
                'name' => 'Menteng',
                'villages' => [
                    '31.71.06.1001' => 'Menteng',
                    '31.71.06.1002' => 'Kebon Sirih',
                    '31.71.06.1003' => 'Gondangdia',
                    '31.71.06.1004' => 'Cikini',
                    '31.71.06.1005' => 'Pegangsaan',
                ],
            ],
            '31.71.07' => [
                'name' => 'Tanah Abang',
                'villages' => [
                    '31.71.07.1001' => 'Gelora',
                    '31.71.07.1002' => 'Bendungan Hilir',
                    '31.71.07.1003' => 'Karet Tengsin',
                    '31.71.07.1004' => 'Petamburan',
                    '31.71.07.1005' => 'Kebon Melati',
                    '31.71.07.1006' => 'Kebon Kacang',
                    '31.71.07.1007' => 'Kampung Bali',
                ],
            ],
            '31.71.08' => [
                'name' => 'Johar Baru',
                'villages' => [
                    '31.71.08.1001' => 'Galur',
                    '31.71.08.1002' => 'Tanah Tinggi',
                    '31.71.08.1003' => 'Kampung Rawa',
                    '31.71.08.1004' => 'Johar Baru',
                ],
            ],
        ];

        foreach ($data as $districtCode => $districtData) {
            $district = District::create([
                'code' => $districtCode,
                'name' => $districtData['name'],
            ]);

            foreach ($districtData['villages'] as $villageCode => $villageName) {
                SubDistrict::create([
                    'district_id' => $district->id,
                    'code' => $villageCode,
                    'name' => $villageName,
                ]);
            }
        }
    }
}
