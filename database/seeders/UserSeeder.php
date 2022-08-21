<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //user Seeder
        DB::table('users')->insert([
            'nama' => 'Muhammad Dzalfiqri Sabani',
            'nik' => 3205061112980002,
            'email' => 'muhdzalfikri@gmail.com',
            'password' => Hash::make('12345678'),
            'tanggal_lahir' => Carbon::parse('2021-12-12'),
            'jenis_kelamin' => 'L',
            'nomor_tlp' => 6285156184235,
            'alamat_id' => 1,
            'pekerjaan' => 'Wiraswasta',
            'kewarganegaraan' => 'Indonesia',
            'role' => 'Masyarakat Umum',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')

        ]);
    }
}
