<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $kecamatan = ['Banyuresmi', 'Tarogong Kidul', 'Tarogong Kaler', 'Karangpawitan', 'Leles', 'Kadungora', 'Bayongbong', 'Malangbong', 'Garut Kota', 'Cibatu', 'Cilawu'];

        foreach ($kecamatan as $item) {
            DB::table('kecamatans')->insert([
                'nama' => $item,
                'kabupaten_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
