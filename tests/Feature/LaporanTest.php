<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\JenisPemilu;
use App\Models\Laporan;
use App\Models\Masyarakat;
use App\Models\Pemilu;
use App\Models\Pengawas;
use App\Models\ProgressLaporan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class LaporanTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // Masyarakat Membuat Laporan
    public function test_masyarakat_success_create_laporan()
    {
        /// PERSIAPAN
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['createLaporan']);

        $faker = Faker::create('id_ID');

        // membuat jenis pemilu baru
        $jenisPemilu = JenisPemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa'
        ]);

        // Alamat
        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();

        // membuat pemilu baru
        $pemilu = Pemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa Sukaratu',
            'tanggal_pelaksanaan' => '2023-03-20',
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemilu->id_jenis,
            'alamat_id' => $alamat->id_alamat
        ]);

        $payload = [
            'judul' => 'Laporan Pemberian Uang Oleh Partai A',
            'tanggal_kejadian' => '2023-03-13',
            'pemberi' => 'Bapak Samsudin',
            'penerima' => 'Warga Kampung Sukamentri',
            'nominal' => 200000,
            'alamat_kejadian' => 'Kp Sukamentri, Garut',
            'kronologi_kejadian' => 'hari senin pagi Bapak Samsudin berkunjung ke Kampung sukamentri dengan sambal membagikan uang kepada setiao warga',
            'bukti' => 'https://www.drive.google.com',
            'pemilu_id' => $pemilu->id_pemilu, // Pemilihan Kepala Desa Sukaratu
            // 'nik' => $masyarakat->nik
        ];

        /// PENGUJIAN
        $response = $this->postJson('api/laporan', $payload, ['Accept' => 'Application/json']);

        /// VERIFKASI
        $response->assertOk()->assertJsonStructure(
            [
                'kode',
                'status',
                'message',
                'data'
            ]
        );
    }

    // Masyarakat membuat laporan dengan data tidak lengkap
    public function test_masyarakat_get_a_validation_error_when_try_to_create_laporan_with_pemberi_and_penerima_field_on_null()
    {

        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['createLaporan']);

        // membuat jenis pemilu baru
        $jenisPemilu = JenisPemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa'
        ]);

        // Alamat
        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();

        // membuat pemilu baru
        $pemilu = Pemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa Sukaratu',
            'tanggal_pelaksanaan' => '2023-03-20',
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemilu->id_jenis,
            'alamat_id' => $alamat->id_alamat
        ]);

        $payload = [
            'judul' => 'Laporan Pemberian Uang Oleh Partai A',
            'tanggal_kejadian' => '2023-03-13',
            'pemberi' => '',
            'penerima' => '',
            'nominal' => 200000,
            'alamat_kejadian' => 'Kp Sukamentri, Garut',
            'kronologi_kejadian' => 'hari senin pagi Bapak Samsudin berkunjung ke Kampung sukamentri dengan sambal membagikan uang kepada setiao warga',
            'bukti' => 'https://www.drive.google.com',
            'pemilu_id' => $pemilu->id_pemilu // Pemilihan Kepala Desa Sukaratu
            // 'nik' => $masyarakat->nik
        ];

        $response = $this->postJson('api/laporan', $payload, ['Accept' => 'Application/json']);

        $response->assertUnprocessable()->assertJson(
            [
                "message" => "The pemberi field is required. (and 1 more error)",
                "errors" => [
                    "pemberi" => ["The pemberi field is required."],
                    "penerima" => ["The penerima field is required."],
                ]
            ]
        );
    }

    public function test_masyarakat_cannot_create_laporan_without_login()
    {
        $faker = Faker::create('id_ID');

        // membuat jenis pemilu baru
        $jenisPemilu = JenisPemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa'
        ]);

        // Alamat
        $alamat = Alamat::factory()->generateGarutJawaBarat()->create();

        // membuat pemilu baru
        $pemilu = Pemilu::factory()->create([
            'nama' => 'Pemilihan Kepala Desa Sukaratu',
            'tanggal_pelaksanaan' => '2023-03-20',
            'waktu_pelaksanaan' => $faker->time('H:i'),
            'jenis_id' => $jenisPemilu->id_jenis,
            'alamat_id' => $alamat->id_alamat
        ]);

        $payload = [
            'judul' => 'Laporan Pemberian Uang Oleh Partai B',
            'tanggal_kejadian' => '2023-03-13',
            'pemberi' => 'Bapak Samsudin',
            'penerima' => 'Warga Kampung Sukamentri',
            'nominal' => 200000,
            'alamat_kejadian' => 'Kp Sukaratu, Garut',
            'kronologi_kejadian' => 'hari senin pagi Bapak Samsudin berkunjung ke Kampung sukamentri dengan sambal membagikan uang kepada setiao warga',
            'bukti' => 'https://www.drive.google.com',
            'pemilu_id' => $pemilu->id_pemilu, // Pemilihan Kepala Desa Sukaratu
            // 'nik' => $masyarakat->nik
        ];

        $response = $this->postJson('api/laporan', $payload, ['Accept' => 'Application/json']);

        $response->assertUnauthorized()->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );
    }
    // masyarakat melihat detail laporan berdasarkan nomor laporan
    public function test_masyarakat_can_get_laporan_by_nomor_laporan()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat);

        //get user nik
        $data = Masyarakat::where('user_id', $masyarakat->id)->first();
        $usernik = $data->nik;

        $payload = [
            //dummy nomor laporan
            'nomor_laporan' => $faker->numerify('2023-01-0#-##'),
            'judul' => 'Laporan Pemberian Uang Oleh Partai A',
            'tanggal_kejadian' => '2023-03-13',
            'pemberi' => 'Bapak Samsudin',
            'penerima' => 'Ormas sejahtera',
            'nominal' => 200000,
            'alamat_kejadian' => 'Kp Sukamentri, Garut',
            'kronologi_kejadian' => 'hari senin pagi Bapak Samsudin berkunjung ke Kampung sukamentri dengan sambal membagikan uang kepada setiap warga',
            'bukti' => 'https://www.drive.google.com',
            'pemilu_id' => $faker->randomElement($pemiluID),
            'nik' => $usernik
        ];

        $laporan = Laporan::factory()->create($payload);

        $response = $this->getJson('api/laporan/' . $laporan->nomor_laporan);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nomor_laporan',
                'judul',
                'pemberi',
                'penerima',
                'nominal',
                'alamat_kejadian',
                'kronologi_kejadian',
                'tanggal_kejadian',
                'bukti',
                'pemilu_id',
                'nik'
            ]
        ]);
    }

    // masyarakat tidak dapat mengakses laporan yang dimiliki oleh orang lain
    public function test_masyarakat_cannot_access_laporan_owned_by_other_masyarakat()
    {
        $faker = Faker::create('id_ID');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['details']);

        //mengambil secara acak nomor laporan dari database
        $laporanID = DB::table('laporan')->pluck('nomor_laporan');

        // 2 adalah id laporan milik orang lain
        $response = $this->getJson('api/laporan/' . $faker->randomElement($laporanID));

        $response->assertForbidden()->assertJson(
            [
                "kode" => 403,
                "status" => false,
                "message" => "Anda Tidak Memiliki Akses Untuk Melihat Laporan Ini"
            ]
        );
    }

    public function test_masyarakat_can_update_their_laporan()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        Sanctum::actingAs($masyarakat, ['createLaporan', 'updateByUser']);

        //data tetap
        $tanggal = $faker->date('Y-m-d');
        $nominal = 100000;
        $alamat = $faker->address();
        $kronologi = $faker->text();
        $bukti = $faker->url();
        $pemiluId = $faker->randomElement($pemiluID);

        //get user nik
        $data = Masyarakat::where('user_id', $masyarakat->id)->first();
        $usernik = $data->nik;

        $payload = [
            'nomor_laporan' => $faker->numerify('202#-0#-1#-2#'),
            'judul' => 'Kades Sukamaju',
            'tanggal_kejadian' => '2023-03-03',
            'pemberi' => 'mister X',
            'penerima' => 'mister Y',
            'nominal' => $nominal,
            'alamat_kejadian' => $alamat,
            'kronologi_kejadian' => $kronologi,
            'bukti' => $bukti,
            'pemilu_id' => $pemiluId,
            'nik' => $usernik
        ];
        $laporan = Laporan::factory()->create($payload);

        // data baru akan menyertakan perubahan data judul serta nominal
        $dataBaru = [
            'judul' => 'Dugaan Politik Uang Calon Kades Sukamaju',
            'tanggal_kejadian' => $laporan->tanggal_kejadian,
            'pemberi' => $laporan->pemberi,
            'penerima' => $laporan->penerima,
            'nominal' => $laporan->nominal,
            'alamat_kejadian' => $alamat,
            'kronologi_kejadian' => $kronologi,
            'bukti' => $bukti,
            'pemilu_id' => $pemiluId,
            'nik' => $usernik
        ];


        ProgressLaporan::factory()->create([
            'nomor_laporan' => $laporan->nomor_laporan,
            'status' => 'menunggu',
            'keterangan' => 'laporan telah dibuat oleh ' . $masyarakat->nama,
        ]);

        $response = $this->putJson('api/laporan/' . $laporan->nomor_laporan, $dataBaru);

        $response->assertOk()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                'nomor_laporan',
                'judul',
                'pemberi',
                'penerima',
                'nominal',
                'alamat_kejadian',
                'kronologi_kejadian',
                'tanggal_kejadian',
                'bukti',
                'pemilu_id',
                'nik'
            ]
        ]);
    }

    public function test_masyarakat_cannot_update_laporan_with_progress_status_is_diproses()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        Sanctum::actingAs($masyarakat, ['createLaporan', 'updateByUser']);

        //data tetap
        $tanggal = $faker->date('Y-m-d');
        $nominal = 100000;
        $alamat = $faker->address();
        $kronologi = $faker->text();
        $bukti = $faker->url();
        $pemiluId = $faker->randomElement($pemiluID);

        //get user nik
        $data = Masyarakat::where('user_id', $masyarakat->id)->first();
        $usernik = $data->nik;

        $payload = [
            'nomor_laporan' => $faker->numerify('202#-##-00-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $tanggal,
            'pemberi' => 'mister X',
            'penerima' => 'mister Y',
            'nominal' => $nominal,
            'alamat_kejadian' => $alamat,
            'kronologi_kejadian' => $kronologi,
            'bukti' => $bukti,
            'pemilu_id' => $pemiluId,
            'nik' => $usernik
        ];

        // data baru akan menyertakan perubahan data judul serta nominal
        $dataBaru = [
            'judul' => 'Judul Telah Diedit',
            'tanggal_kejadian' => $tanggal,
            'pemberi' => 'mister X',
            'penerima' => 'mister Y',
            'nominal' => 100000000,
            'alamat_kejadian' => $alamat,
            'kronologi_kejadian' => $kronologi,
            'bukti' => $bukti,
            'pemilu_id' => $pemiluId,
        ];

        $laporan = Laporan::factory()->create($payload);

        // ProgressLaporan::factory()->create([
        //     'nomor_laporan' => $laporan->nomor_laporan,
        //     'status' => 'menunggu',
        //     'keterangan' => 'laporan telah dibuat oleh ' . $masyarakat->nama,
        // ]);

        ProgressLaporan::factory()->create([
            'nomor_laporan' => $laporan->nomor_laporan,
            'status' => 'diproses',
            'keterangan' => 'laporan sedang dilaksanakan peninjauan oleh pengawas',
        ]);

        $response = $this->putJson('api/laporan/' . $laporan->nomor_laporan, $dataBaru);

        $response->assertForbidden()->assertJsonStructure([
            'kode',
            'status',
            'message'
        ])->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Akses Ditolak. Laporan sedang diproses, Tidak dapat diubah'
            ]
        );
    }

    public function test_masyarakat_cannot_update_unowned_laporan()
    {
        $faker = Faker::create('id_ID');
        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat);

        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');
        $nik = DB::table('masyarakat')->pluck('nik');

        $payload1 = [
            'nomor_laporan' => $faker->numerify('2023-0#-0#-##'),
            'judul' => 'Laporan Pemberian Uang Oleh Partai A',
            'tanggal_kejadian' => '2023-03-13',
            'pemberi' => 'Bapak Samsudin',
            'penerima' => 'Warga Kampung Sukamentri',
            'nominal' => 200000,
            'alamat_kejadian' => 'Kp Sukamentri, Garut',
            'kronologi_kejadian' => 'hari senin pagi Bapak Samsudin berkunjung ke Kampung sukamentri dengan sambal membagikan uang kepada setiao warga',
            'bukti' => 'https://www.drive.google.com',
            'pemilu_id' => $faker->randomElement($pemiluID), // Pemilihan Kepala Desa Sukaratu
            'nik' => $faker->randomElement($nik)
        ];

        $laporan = Laporan::factory()->create($payload1);

        // data baru akan menyertakan perubahan data judul serta nominal
        $payload2 = [
            'judul' => 'Laporan Pemberian Uang Oleh Partai A',
            'tanggal_kejadian' => '2023-03-13',
            'pemberi' => 'Bapak Samsudin',
            'penerima' => 'Warga Kampung Sukamentri',
            'nominal' => 200000,
            'alamat_kejadian' => 'Kp Sukamentri, Garut',
            'kronologi_kejadian' => 'hari senin pagi Bapak Samsudin berkunjung ke Kampung sukamentri dengan sambal membagikan uang kepada setiao warga',
            'bukti' => 'https://www.drive.google.com',
            'pemilu_id' => $faker->randomElement($pemiluID), // Pemilihan Kepala Desa Sukaratu
            // 'nik' => $masyarakat->nik
        ];

        $response = $this->putJson('api/laporan/' . $laporan->nomor_laporan, $payload2);

        $response->assertForbidden()->assertJsonStructure([
            'kode',
            'status',
            'message'
        ])->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Akses Ditolak!. Hanya pemilik Laporan yang dapat menggunakan fitur ini'
            ]
        );
    }

    public function test_masyarakat_can_delete_their_laporan()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['delete']);

        $payload = [
            'nomor_laporan' => $faker->numerify('2023-01-0#-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister A',
            'penerima' => 'mister B',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'nik' => $masyarakat->masyarakat->nik
        ];

        $laporan = Laporan::factory()->create($payload);

        ProgressLaporan::factory()->create([
            'nomor_laporan' => $laporan->nomor_laporan,
            'status' => 'menunggu',
            'keterangan' => 'Laporan telah dibuat oleh ' . $masyarakat->nama,
        ]);

        $response = $this->deleteJson('api/laporan/' . $laporan->nomor_laporan);

        $response->assertOk()->assertJson(
            [
                'kode' => 200,
                'status' => true,
                'message' => 'Data Laporan ' . $laporan->judul . ' berhasil dihapus'
            ]
        );
    }

    public function test_masyarakat_cannot_delete_laporan_when_progress_status_is_not_menunggu()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');

        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['delete']);

        $payload = [
            'nomor_laporan' => $faker->numerify('2023-01-0#-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister A',
            'penerima' => 'mister B',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'nik' => $masyarakat->masyarakat->nik
        ];

        $laporan = Laporan::factory()->create($payload);

        ProgressLaporan::factory()->create([
            'nomor_laporan' => $laporan->nomor_laporan,
            'status' => 'diproses',
            'keterangan' => 'laporan sedang dilaksanakan peninjauan oleh pengawas',
        ]);

        $response = $this->deleteJson('api/laporan/' . $laporan->nomor_laporan);

        $response->assertOk()->assertJson(
            [
                'kode' => 403,
                'status' => false,
                'message' => 'Permintaan Ditolak. Laporan sedang diproses, tidak dapat dihapus'
            ]
        );
    }

    public function test_masyarakat_can_get_a_list_of_laporan_that_heve_been_made()
    {
        $faker = Faker::create('id_ID');
        $pemiluID = DB::table('pemilu')->pluck('id_pemilu');


        $masyarakat = User::factory()->has(Masyarakat::factory())->create();
        Sanctum::actingAs($masyarakat, ['getUserLaporan']);

        //get nik
        $nik = Masyarakat::where('user_id', $masyarakat->id)->first()->nik;
        $payload1 = [
            'nomor_laporan' => $faker->numerify('202#-20-01-##'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister A',
            'penerima' => 'mister B',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'nik' => $nik
        ];

        $payload2 = [
            'nomor_laporan' => $faker->numerify('2023-20-0#-1#'),
            'judul' => 'Judul ' . $faker->numberBetween(0, 100),
            'tanggal_kejadian' => $faker->date('Y-m-d'),
            'pemberi' => 'mister A',
            'penerima' => 'mister B',
            'nominal' => $faker->numberBetween(1000, 100000000),
            'alamat_kejadian' => $faker->address(),
            'kronologi_kejadian' => $faker->text(),
            'bukti' => $faker->url(),
            'pemilu_id' => $faker->randomElement($pemiluID),
            'nik' => $nik
        ];


        Laporan::factory()->create($payload1);
        Laporan::factory()->create($payload2);

        $response = $this->getJson('api/user/laporan');

        $response->assertOK()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                '*' => [
                    'nomor_laporan',
                    'judul',
                    'pemberi',
                    'penerima',
                    'nominal',
                    'alamat_kejadian',
                    'kronologi_kejadian',
                    'tanggal_kejadian',
                    'bukti',
                    'pemilu_id',
                    'nik'
                ]
            ]
        ]);
    }

    public function test_petugas_can_get_all_laporan_masyarakat()
    {
        $petugas = User::factory()->petugas()->has(Pengawas::factory())->create();
        Sanctum::actingAs($petugas, ['getAll']);

        $response = $this->getJson('api/laporan');

        $response->assertOK()->assertJsonStructure([
            'kode',
            'status',
            'message',
            'data' => [
                '*' => [
                    'nomor_laporan',
                    'judul',
                    'pemberi',
                    'penerima',
                    'nominal',
                    'alamat_kejadian',
                    'kronologi_kejadian',
                    'tanggal_kejadian',
                    'bukti',
                    'pemilu_id',
                    'nik'
                ]
            ]
        ]);
    }
}
