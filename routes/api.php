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
    Route::post('pegawai','Api\PegawaiController@store'); //create
    Route::get('pegawai','Api\PegawaiController@index'); //read
    Route::get('pegawai/waiter','Api\PegawaiController@showWaiter'); //show waiter
//    Route::put('pegawai/{id}{role}','Api\PegawaiController@update'); //update
    Route::put('pegawai/{id}','Api\PegawaiController@update'); //update
    Route::get('pegawai/{id}','Api\PegawaiController@show'); //search
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
    Route::put('meja/{id}','Api\MejaController@update'); //update
    Route::delete('meja/{id}','Api\MejaController@destroy'); //delete
    Route::get('meja/{id}','Api\MejaController@show'); //search

    //reservasi
    Route::post('reservasi','Api\ReservasiController@store'); //create
    Route::get('reservasi','Api\ReservasiController@index'); //read
    Route::put('reservasi/{id}','Api\ReservasiController@update'); //update
    Route::delete('reservasi/{id}','Api\ReservasiController@destroy'); //delete
    Route::get('reservasi/{id}','Api\ReservasiController@show'); //search

    //transaksi
    Route::post('transaksi','Api\PembayaranController@store'); //create
    Route::get('transaksi','Api\PembayaranController@index'); //read
    Route::put('transaksi/{id}','Api\PembayaranController@update'); //update
    Route::get('transaksi/{id}','Api\PembayaranController@show'); //search

    //kartu
    Route::post('kartu','Api\KartuController@store'); //create
    Route::get('kartu','Api\KartuController@index'); //read
    Route::put('kartu/{id}','Api\KartuController@update'); //update
    Route::delete('kartu/{id}','Api\KartuController@destroy'); //delete
    Route::get('kartu/{id}','Api\KartuController@show'); //search

    //detail order
    Route::post('detailOrder','Api\DetailPesananController@store'); //create
    Route::get('detailOrder','Api\DetailPesananController@index'); //read
    Route::put('detailOrder/{id}','Api\DetailPesananController@update'); //update
    Route::get('detailOrder/{id}','Api\DetailPesananController@show'); //search

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
    Route::put('bahan/{id}','Api\BahanController@update'); //update
    Route::delete('bahan/{id}','Api\BahanController@destroy'); //delete
    Route::get('bahan/{id}','Api\BahanController@show'); //search

    //stok Masuk
    Route::post('stokMasuk','Api\StokMasukController@store'); //create
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

