<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = json_decode(file_get_contents(database_path('seeders/data/states.json')), true);

        $country = Country::where("name", "ایران")->first();

        $data = [];
        $now = now();

        foreach ($states as $state) {
            $data[] = [
                'id'         => $state['id'],
                'name'       => $state['name'],
                'country_id' => $country->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }


        DB::table('states')->insert($data);
    }
}
