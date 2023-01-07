<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EdukasiController;
use App\Http\Controllers\API\FQAController;
use App\Http\Controllers\API\JenisPemiluController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\PemiluController;
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

        //Provinsi Route
        // Route::post('/provinsi/create', [AddressController::class, 'createProvinsi']);
        // Route::post('/provinsi/update/{id}', [AddressController::class, 'updateProvinsi']);
        // Route::post('/provinsi/delete/{id}', [AddressController::class, 'deleteProvinsi']);

        // Kabupaten Route
        // Route::post('/kabupaten/create', [AddressController::class, 'createKabupaten']);
        // Route::post('/kabupaten/update/{id}', [AddressController::class, 'updateKabupaten']);
        // Route::post('/kabupaten/delete/{id}', [AddressController::class, 'deleteKabupaten']);

        // Kecamatan Route
        // Route::post('/kecamatan/create', [AddressController::class, 'createKecamatan']);
        // Route::post('/kecamatan/update/{id}', [AddressController::class, 'updateKecamatan']);
        // Route::post('/kecamatan/delete/{id}', [AddressController::class, 'deleteKecamatan']);

        // FQA Route
        Route::post('/fqa/create', [FQAController::class, 'create']);
        Route::post('/fqa/update/{id}', [FQAController::class, 'update']);
        Route::post('/fqa/delete/{id}', [FQAController::class, 'delete']);

        // Pemilu Route
        Route::post('/pemilu/create', [PemiluController::class, 'create']);
        Route::post('/pemilu/update/{id}', [PemiluController::class, 'updatePemilu']);
        Route::get('/pemilu/detail/{id}', [PemiluController::class, 'details']);
        Route::post('/pemilu/delete/{id}', [PemiluController::class, 'deletePemilu']);

        // Konten Edukasi Route
        Route::post('/edukasi/create', [EdukasiController::class, 'create']);
        Route::post('/edukasi/update/{id}', [EdukasiController::class, 'update']);
        Route::post('/edukasi/delete/{id}', [EdukasiController::class, 'delete']);

        // Laporan Route
        Route::get('/laporan', [LaporanController::class, 'allLaporan']);
        Route::get('/laporan/my-laporan', [LaporanController::class, 'getUserLaporan']);
        Route::get('/laporan/progress/{id}', [LaporanController::class, 'getProgressLaporan']);
        Route::post('/laporan/create', [LaporanController::class, 'createLaporan']);
        Route::post('/laporan/update/{id}', [LaporanController::class, 'updateByUser']);
        Route::get('/laporan/details/{id}', [LaporanController::class, 'details']);
        Route::post('/laporan/status/{id}', [LaporanController::class, 'changeStatus']);
        Route::post('/laporan/delete/{id}', [LaporanController::class, 'delete']);

        // Jenis Pemilu
        Route::post('/jenis-pemilu/create', [JenisPemiluController::class, 'create']);
        Route::post('/jenis-pemilu/update/{id}', [JenisPemiluController::class, 'update']);
        Route::post('/jenis-pemilu/delete/{id}', [JenisPemiluController::class, 'delete']);

        // Logout Route
        Route::post('/logout', [AuthController::class, 'logout']);

        // User
        Route::get('/user', [AuthController::class, 'fetchUser']);
        Route::post('user/update', [AuthController::class, 'updateProfile']);
    }
);

// ALAMAT API ROUTE
Route::get('/provinsi', [AddressController::class, 'getAllProvinsi']);
Route::get('/kabupaten', [AddressController::class, 'getAllKabupaten']);
Route::get('/kecamatan', [AddressController::class, 'getAllKecamatan']);

// Pemilu Route
Route::get('/pemilu', [PemiluController::class, 'getAll']);;

// FQA API ROUTE
Route::get('/fqa', [FQAController::class, 'getAll']);

// AUTH ROUTE
Route::Post('/login', [AuthController::class, 'login']);
Route::Post('/register', [AuthController::class, 'register']);



// Alamat
// Route::get('/alamat', [AddressController::class, '']);

// Jenis Pemilu
Route::get('/jenis-pemilu', [JenisPemiluController::class, 'getAll']);
