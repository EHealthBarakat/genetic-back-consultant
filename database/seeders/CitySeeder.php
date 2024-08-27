<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = json_decode(file_get_contents(database_path('seeders/data/cities.json')), true);


        $data = [];
        $now = now();

        foreach ($cities as $city) {
            $data[] = [
                'id'         => Str::uuid(),
                'name'       => $city['name'],
                'state_id'   => $city['state_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }


        DB::table('cities')->insert($data);
    }
}
