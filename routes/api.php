<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'Api\AuthController@login');//login karyawan

Route::group(['middleware'=>'auth:api'],function() {
    Route::post('logout', 'Api\AuthController@logout');//logout karyawan
});

Route::group([], function(){//jangan lupa dimasukin middleware pas udah kelar debug api
    //pegawai
    Route::get('pegawai','Api\PegawaiController@index'); //read
    Route::post('pegawai','Api\PegawaiController@store'); //create
    Route::get('pegawai/waiter','Api\PegawaiController@showWaiter'); //show waiter
    Route::get('pegawai/kasir','Api\PegawaiController@showKasir'); //show waiter
//    Route::put('pegawai/{id}{role}','Api\PegawaiController@update'); //update
    Route::put('pegawai/{id}','Api\PegawaiController@update'); //update
    Route::get('pegawai/{id}','Api\PegawaiController@show'); //search
    Route::delete('pegawai/{id}','Api\PegawaiController@destroy'); //delete
    Route::post('pegawai/{id}','Api\PegawaiController@updatePassword'); //update password

    //role
    Route::post('role','Api\RoleController@store'); //create
    Route::get('role','Api\RoleController@index'); //read
    Route::put('role/{id}','Api\RoleController@update'); //update
    Route::delete('role/{id}','Api\RoleController@destroy'); //delete
    Route::get('role/{id}','Api\RoleController@show'); //search

    //customer
    Route::post('customer','Api\CustomerController@store'); //create
    Route::get('customer','Api\CustomerController@index'); //read
    Route::put('customer/{id}','Api\CustomerController@update'); //update
    Route::delete('customer/{id}','Api\CustomerController@destroy'); //delete
    Route::get('customer/{id}','Api\CustomerController@show'); //search

    //meja
    Route::post('meja','Api\MejaController@store'); //create
    Route::get('meja','Api\MejaController@index'); //read
    Route::get('meja/tersedia','Api\MejaController@mejaTersedia'); //show meja tersedia
    Route::get('meja/pertanggal/{tanggal}','Api\MejaController@mejaTersedia'); //show meja per tanggal
    Route::put('meja/{id}','Api\MejaController@update'); //update
    Route::delete('meja/{id}','Api\MejaController@destroy'); //delete
    Route::get('meja/{id}','Api\MejaController@show'); //search

    //reservasi
    Route::post('reservasi','Api\ReservasiController@store'); //create
    Route::post('reservasi/select','Api\ReservasiController@showSelect'); //read
    Route::post('reservasi/updateStatus/{id}','Api\ReservasiController@updateStatus'); //update status reservasi
    Route::get('reservasi/aktif','Api\ReservasiController@showReservasiAktif'); //read
    Route::get('reservasi','Api\ReservasiController@index'); //read
    Route::put('reservasi/{id}','Api\ReservasiController@update'); //update
    Route::delete('reservasi/{id}','Api\ReservasiController@destroy'); //delete
    Route::get('reservasi/{id}','Api\ReservasiController@show'); //search

    //transaksi
    Route::post('transaksi','Api\TransaksiController@store'); //create
    Route::get('transaksi','Api\TransaksiController@index'); //read
    Route::get('transaksi/showStruk/{id}','Api\TransaksiController@showStruk'); //read
    Route::put('transaksi/{id}','Api\TransaksiController@update'); //update
    Route::get('transaksi/{id}','Api\TransaksiController@show'); //search

    //kartu
    Route::post('kartu','Api\KartuController@store'); //create
    Route::get('kartu','Api\KartuController@index'); //read
    Route::put('kartu/{id}','Api\KartuController@update'); //update
    Route::delete('kartu/{id}','Api\KartuController@destroy'); //delete
    Route::get('kartu/{id}','Api\KartuController@show'); //search

    //order
    Route::post('order','Api\OrderController@store'); //create
    Route::get('order','Api\OrderController@index'); //read
    Route::get('order/byReservasi/{id}','Api\OrderController@showByReservasi'); //search
    Route::put('order/{id}','Api\OrderController@update'); //update
    Route::get('order/{id}','Api\OrderController@show'); //search

    //detail order
    Route::post('detailOrder','Api\DetailOrderController@store'); //create
    Route::post('detailOrder/statusCart/{id}','Api\DetailOrderController@updateStatusCart'); //update cart
    Route::post('detailOrder/status/{id}','Api\DetailOrderController@updateStatus'); //update
    Route::get('detailOrder','Api\DetailOrderController@index'); //read
    Route::get('detailOrder/showOrder/','Api\DetailOrderController@showOrderChef'); //read
    Route::get('detailOrder/showStruk/{id}','Api\DetailOrderController@showStruk'); //read
    Route::get('detailOrder/showCart/{id}','Api\DetailOrderController@showCart'); //read
    Route::put('detailOrder/{id}','Api\DetailOrderController@update'); //update
    Route::delete('detailOrder/{id}','Api\DetailOrderController@destroy'); //delete
    Route::get('detailOrder/{id}','Api\DetailOrderController@show'); //search

    //menu
    Route::post('menu','Api\MenuController@store'); //create
    Route::get('menu','Api\MenuController@index'); //read
    Route::put('menu/{id}','Api\MenuController@update'); //update
    Route::delete('menu/{id}','Api\MenuController@destroy'); //delete
    Route::get('menu/{id}','Api\MenuController@show'); //search
    Route::post('menu/images/{id}', 'Api\MenuController@imageUpload');//upload images

    //bahan
    Route::post('bahan','Api\BahanController@store'); //create
    Route::get('bahan','Api\BahanController@index'); //read
    Route::get('bahan/kosong','Api\BahanController@showKosong'); //read kosong
    Route::get('bahan/laporan/makanan','Api\BahanController@showLaporanMakanan'); //read kosong
    Route::put('bahan/stokMasuk/{id}','Api\BahanController@updateStokMasuk'); //update dari stok masuk
    Route::put('bahan/stokKeluar/{id}','Api\BahanController@updateStokKeluar'); //update dari stok keluar
    Route::put('bahan/{id}','Api\BahanController@update'); //update
    Route::delete('bahan/{id}','Api\BahanController@destroy'); //delete
    Route::get('bahan/{id}','Api\BahanController@show'); //search

    //stok Masuk
    Route::post('stokMasuk','Api\StokMasukController@store'); //create
    Route::post('stokMasuk/showTanggal','Api\StokMasukController@showPerTanggal'); //search tanggal
    Route::get('stokMasuk','Api\StokMasukController@index'); //read
    Route::put('stokMasuk/{id}','Api\StokMasukController@update'); //update
    Route::delete('stokMasuk/{id}','Api\StokMasukController@destroy'); //delete
    Route::get('stokMasuk/{id}','Api\StokMasukController@show'); //search

    //stok Keluar
    Route::post('stokKeluar','Api\StokKeluarController@store'); //create
    Route::get('stokKeluar','Api\StokKeluarController@index'); //read
    Route::put('stokKeluar/{id}','Api\StokKeluarController@update'); //update
    Route::delete('stokKeluar/{id}','Api\StokKeluarController@destroy'); //delete
    Route::get('stokKeluar/{id}','Api\StokKeluarController@show'); //search
});

