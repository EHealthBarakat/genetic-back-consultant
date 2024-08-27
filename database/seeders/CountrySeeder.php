<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $countries = json_decode(file_get_contents(database_path('seeders/data/countries.json')), true);

        $data = [];
        $now = now();

        foreach ($countries as $country) {
            $data[] = [
                'id' => $country['id'],
                'name' => $country['name'],
                'nationality_name'=> $country['nationality_name'],
                'iso_code' => $country['iso_code'],
                'phone_code' => $country['phone_code'],
                'currency' => $country['currency'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }


        DB::table('countries')->insert($data);
    }
}
