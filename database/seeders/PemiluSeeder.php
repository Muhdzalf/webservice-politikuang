<?php

namespace Database\Seeders;

use App\Models\Alamat;
use App\Models\JenisPemilu;
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

        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();

        DB::table('Pemilu')->insert([
            'nama' => 'Pemilihan Kepala Desa',
            'tanggal_pelaksanaan' => $faker->date(),
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => JenisPemilu::factory()->create()->id_jenis, // Pemilihan Kepala Desa
            'alamat_id' => $alamat->id_alamat,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
