<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AdministratorController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FQAController;
use App\Http\Controllers\API\JenisPemiluController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\MasyarakatController;
use App\Http\Controllers\API\PemiluController;
use App\Http\Controllers\API\PengawasController;
use App\Http\Controllers\API\ProgressController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(
    function () {

        /// API APLIKASI MASYARAKAT DAN PENGAWAS
        // User
        Route::get('/user', [UserController::class, 'fetchUser']);
        Route::post('user/update', [UserController::class, 'updateProfile']);

        /// API APLIKASI MASYARAKAT
        //LAPORAN
        Route::post('/laporan/create', [LaporanController::class, 'create']);
        Route::delete('/laporan/delete/{nomor_laporan}', [LaporanController::class, 'delete']);
        Route::put('/laporan/update/{nomor_laporan}', [LaporanController::class, 'update']);
        Route::get('user/laporan/', [LaporanController::class, 'getUserLaporan']);

        Route::get('/user/profile', [MasyarakatController::class, 'getProfile']);


        /// API APLIKASI PENGAWAS
        // Laporan
        Route::get('/laporan', [LaporanController::class, 'getAll']);
        Route::get('/laporan/{nomor_laporan}', [LaporanController::class, 'details']);

        // Progress Laporan
        Route::post('/laporan/respon/{id}', [ProgressController::class, 'responLaporan']);
        Route::get('/laporan/{nomor_laporan}/progress/', [ProgressController::class, 'getProgressLaporan']);


        // Pemilu
        Route::post('/pemilu/create', [PemiluController::class, 'create']);
        Route::put('/pemilu/update/{id}', [PemiluController::class, 'update']);
        Route::get('/pemilu/{id}', [PemiluController::class, 'details']);
        Route::delete('/pemilu/delete/{id}', [PemiluController::class, 'delete']);

        // FQA
        Route::post('/fqa/create', [FQAController::class, 'create']);
        Route::put('/fqa/update/{id}', [FQAController::class, 'update']);
        Route::delete('/fqa/delete/{id}', [FQAController::class, 'delete']);

        // Jenis Pemilu
        Route::post('/jenis-pemilu/create', [JenisPemiluController::class, 'create']);
        Route::put('/jenis-pemilu/update/{id}', [JenisPemiluController::class, 'update']);
        Route::delete('/jenis-pemilu/delete/{id}', [JenisPemiluController::class, 'delete']);

        /// API ADMIN
        // PENGAWAS
        Route::post('pengawas/create', [PengawasController::class, 'create']);

        // ADMIN
        Route::post('admin/create', [AdministratorController::class, 'create']);

        Route::get('user/all', [UserController::class, 'getAllUser']);


        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
    }
);


// API UMUM
// ALAMAT
Route::get('/provinsi', [AddressController::class, 'getAllProvinsi']);
Route::get('/provinsi/{id}/kabupaten-kota', [AddressController::class, 'getAllKabupatenByProvinsiId']);
Route::get('/kabupaten-kota', [AddressController::class, 'getAllKabupaten']);
Route::get('/kabupaten-kota/{id}/kecamatan', [AddressController::class, 'getAllKecamatanByKabupatenKotaId']);
Route::get('/kecamatan', [AddressController::class, 'getAllKecamatan']);

// Pemilu Route
Route::get('/pemilu', [PemiluController::class, 'getAll']);;

// FQA API ROUTE
Route::get('/fqa', [FQAController::class, 'getAll']);

// REGISTRASI MASYARAKAT
Route::post('/registrasi', [MasyarakatController::class, 'registerMasyarakat']);

// AUTH ROUTE
Route::Post('/user/login', [AuthController::class, 'login']);

// REGISTRASI MASYARAKAT
Route::Post('/user/register', [AuthController::class, 'register']);

// Jenis Pemilu
Route::get('/jenis-pemilu', [JenisPemiluController::class, 'getAll']);
