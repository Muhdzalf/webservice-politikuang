<?php

namespace Database\Seeders;

use App\Models\Alamat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Faker\Factory as Faker;

class PemiluSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        DB::table('alamat')->insert([
            'kecamatan_id' => 3205230, // Banyuresmi
            'kabupaten_id' => 3205, // Kabupaten Garut
            'provinsi_id' => 32, // Jawa Barat
            'detail_alamat' => 'Desa ' . $faker->numberBetween(0, 20),
        ]);

        $alamat = Alamat::latest()->first();
        $jenisPemiluID = DB::table('jenis_pemilu')->pluck('id');

        DB::table('Pemilu')->insert([
            'nama' => 'Pemilihan Kepala Desa',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $faker->randomElement($jenisPemiluID), // Pemilihan Kepala Desa
            'alamat_id' => $alamat->id,
        ]);
    }
}
