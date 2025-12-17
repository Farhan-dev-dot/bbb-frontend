<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\master\master_barangController;
use App\Http\Controllers\master\master_customerController;
use App\Http\Controllers\report\LaporanContoller;
use App\Http\Controllers\system\AuthController;
use App\Http\Controllers\transaksi\riwayatstokController;
use App\Http\Controllers\transaksi\stokopnameContoller;
use App\Http\Controllers\transaksi\transaksikeluarController;
use App\Http\Controllers\transaksi\transaksimasukController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });



// Authentication Routes
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/postlogin', [AuthController::class, 'postLogin'])->name('postlogin');
Route::get('/logout', [HomeController::class, 'logout'])->name('home.logout');

// Protected Routes Group - Membutuhkan Token
Route::middleware(['check.token'])->group(function () {
    // Home Routes
    Route::get('/', [HomeController::class, 'index'])->name('/');
    Route::get('/check-session', [HomeController::class, 'checkSession'])->name('check-session');
    Route::get('/fresh-data-customer', [HomeController::class, 'freshdatacustomer'])->name('fresh-data-customer');
    Route::get('/fresh-data-barang', [HomeController::class, 'freshdatabarang'])->name('fresh-data-barang');
    Route::get('/fresh-data-pendapatan-hari-ini', [HomeController::class, 'freshdatapendapatanhariini'])->name('data-pendapatan-hari-ini');
    Route::get('/fresh-data-tabung-kosong', [HomeController::class, 'freshdatatabungkosong'])->name('fresh-data-tabung-kosong');
    Route::get('/static-data-pendapatan-harian', [HomeController::class, 'staticDataPendapatanHarian'])->name('static-data-pendapatan-harian');
    Route::get('/static-data-pendapatan-pertahun', [HomeController::class, 'freshdatapendapatanpertahun']);
    Route::get('/fresh-data-transaksi-hari-ini', [HomeController::class, 'freshdatatransaksi'])->name('data-transaksi-hari-ini');
    Route::get('/fresh-data-transaksi-hari-ini', [HomeController::class, 'freshdatatransaksi'])->name('data-transaksi-hari-ini');

    // Master Barang Routes
    Route::get('/master-barang', [master_barangController::class, 'index'])->name('master-barang');
    Route::get('/master-barang/getdata', [master_barangController::class, 'getData']);
    Route::get('/master-barang/get-data-by-id/{id}', [master_barangController::class, 'getDatabyid']);
    Route::post('/master-barang/insert-data', [master_barangController::class, 'InsertData']);
    Route::patch('/master-barang/update-data/{id}', [master_barangController::class, 'UpdateData']);
    Route::delete('/master-barang/delete-data/{id}', [master_barangController::class, 'DeleteData']);

    // Master Customer Routes
    Route::get('/master-customer', [master_customerController::class, 'index'])->name('master-customer');
    Route::get('/master-customer/getdata', [master_customerController::class, 'getData']);
    Route::get('/master-customer/get-data-by-id/{id}', [master_customerController::class, 'getDatabyid']);
    Route::post('/master-customer/insert-data', [master_customerController::class, 'InsertData']);
    Route::patch('/master-customer/update-data/{id}', [master_customerController::class, 'UpdateData']);
    Route::delete('/master-customer/delete-data/{id}', [master_customerController::class, 'DeleteData']);

    // Transaksi Keluar Routes
    Route::get('/transaksi-keluar', [transaksikeluarController::class, 'index'])->name('transaksi-keluar');
    Route::get('/transaksi-keluar/getdata', [transaksikeluarController::class, 'getData']);
    Route::get('/transaksi-keluar/get-data-by-id/{id}', [transaksikeluarController::class, 'getDatabyid']);
    Route::post('/transaksi-keluar/insert-data', [transaksikeluarController::class, 'InsertData']);
    Route::patch('/transaksi-keluar/update-data/{id}', [transaksikeluarController::class, 'UpdateData']);
    Route::get('/transaksi-keluar/barang-list', [transaksikeluarController::class, 'getBarangList']);
    Route::get('/transaksi-keluar/customer-list', [transaksikeluarController::class, 'getCustomerList']);
    Route::delete('/transaksi-keluar/delete-data/{id}', [transaksikeluarController::class, 'DeleteData']);

    //Transaksi Masuk Routes
    Route::get('/transaksi-masuk', [transaksimasukController::class, 'index'])->name('transaksi-masuk');
    Route::get('/transaksi-masuk/getdata', [transaksimasukController::class, 'getData']);
    Route::get('/transaksi-masuk/get-data-by-id/{id}', [transaksimasukController::class, 'getDatabyid']);
    Route::post('/transaksi-masuk/insert-data', [transaksimasukController::class, 'InsertData']);
    Route::patch('/transaksi-masuk/update-data/{id}', [transaksimasukController::class, 'UpdateData']);
    Route::get('/transaksi-masuk/customer-list', [transaksimasukController::class, 'getCustomerList']);
    Route::delete('/transaksi-masuk/delete-data/{id}', [TransaksiMasukController::class, 'deleteData']);
    // Stok Opname Routes
    Route::get('/stok-opname', [stokopnameContoller::class, 'index'])->name('stok-opname');
    Route::get('/stok-opname/getdata', [stokopnameContoller::class, 'getData']);
    Route::get('/stok-opname/current-stok', [stokopnameContoller::class, 'getCurrentStok']);
    Route::get('/stok-opname/history/{id_barang}', [stokopnameContoller::class, 'getHistory']);
    Route::get('/stok-opname/cetak', [stokopnameContoller::class, 'Cetakprint']);
    Route::post('/stok-opname/export', [stokopnameContoller::class, 'exportExcel']);
    Route::post('/stok-opname/koreksi-stok', [stokopnameContoller::class, 'KoreksiStok']);
    Route::delete('/stok-opname/delete-data/{id}', [stokopnameContoller::class, 'deleteRiwayat']);
    //Riwayat Stok Routes
    Route::get('/riwayat-stok', [riwayatstokController::class, 'index'])->name('riwayat-stok');
    Route::get('/riwayat-stok/getdata', [riwayatstokController::class, 'getData']);

    // Report Routes
    Route::get('/form-laporan', [LaporanContoller::class, 'index'])->name('form-laporan');
    Route::get('/laporan-transaksi/getdata', [LaporanContoller::class, 'getData']);
    Route::post('/laporan-transaksi/export', [LaporanContoller::class, 'exportExcel']);
});
