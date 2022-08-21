<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KabupatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //seeder kabupaten
        $jabar = ['Kabupaten Garut', 'Kabupaten Bandung', 'Kabupaten Tasikmalaya', 'Kota Tasikmalaya', 'Kota Bandung', 'Kabupaten Bandung Barat', 'Kota Bekasi', 'Kota Bogor', 'Kota Depok'];

        foreach ($jabar as $item) {

            DB::table('kabupatens')->insert([
                'nama' => $item,
                'provinsi_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
