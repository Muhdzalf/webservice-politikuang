<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProvinsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // $provinsi = ['Jawa Barat', 'DKI Jakarta', 'Jawa Tengah', 'Jawa Timur', 'Banten'];

        // foreach ($provinsi as $item) {

        //     DB::table('provinsis')->insert([
        //         'nama' => 'Provinsi ' . $item,
        //         'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        //         'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        //     ]);
        // }

        $sql = file_get_contents(database_path() . '\data\provinsi.sql');

        DB::statement($sql);
    }
}
