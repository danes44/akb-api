<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index(){
        $transaksi = DB::table('transaksi')
            ->leftJoin('kartu','transaksi.id_kartu','=','kartu.id_kartu')
            ->join('order','order.id_order','=','transaksi.id_order')
            ->join('reservasi','reservasi.id_reservasi','=','order.id_reservasi')
            ->join('customer','customer.id_customer','=','reservasi.id_customer')
            ->join('pegawai','transaksi.id_kasir','=','pegawai.id_pegawai')
            ->select('transaksi.*','kartu.no_kartu','order.tgl_order','pegawai.nama_pegawai',
                'customer.nama_customer','reservasi.id_reservasi','reservasi.id_customer','reservasi.no_meja')
            ->get();

        if(count($transaksi)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function show ($id){
        $transaksi = Transaksi::find($id);

        if(!is_null($transaksi)){
            return response([
                'message'  => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ],200);
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ],404);
    }

    public function showStruk ($id){
        $transaksi = DB::table('transaksi')
            ->leftJoin('kartu','transaksi.id_kartu','=','kartu.id_kartu')
            ->join('order','order.id_order','=','transaksi.id_order')
//            ->join('detail_order','order.id_order','=','detail_order.id_order')
            ->join('reservasi','reservasi.id_reservasi','=','order.id_reservasi')
            ->join('customer','customer.id_customer','=','reservasi.id_customer')
            ->join('pegawai','transaksi.id_kasir','=','pegawai.id_pegawai')
            ->select('transaksi.*','kartu.no_kartu','pegawai.nama_pegawai',
                'customer.nama_customer','reservasi.id_reservasi','reservasi.id_customer','reservasi.no_meja')
            ->where('transaksi.no_transaksi','=',$id)
            ->get();

        if(count($transaksi)>0){
            return response([
                'message'  => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ],200);
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'id_transaksi' => 'required|unique:transaksi',
            'payment_method' => 'required|string|in:cash,credit/debit card',
            'no_verifikasi' => 'nullable|string',
            'id_reservasi' => 'required|numeric|exists:reservasi', //buat cari id_order dari id_reservasi
            'id_kasir' => 'required|numeric|exists:pegawai,id_pegawai',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $reservasi =  DB::table('reservasi')
            ->join('order','reservasi.id_reservasi','=','order.id_reservasi')
            ->select('order.id_order')
            ->where('reservasi.status_reservasi','=','aktif')
            ->where('order.id_reservasi','=',$storeData['id_reservasi'])
            ->first();

        return response([
            'message' => 'Add Transaksi Success',
            'data' => $reservasi,
        ],200);

        if(is_null($reservasi))
        {
            return response(['message'=> 'Reservasi Tidak Aktif, Belum Pesan, atau Tidak Ada'],400);
        }

        $pesanan = DB::table('detail_order')
            ->join('order','order.id_order','=','detail_order.id_order')
//            ->join('reservasi','reservasi.id_reservasi','=','order.id_reservasi')
            ->select(DB::raw('count(distinct detail_order.id_menu) as jumlah_item'),
                DB::raw('sum(detail_order.harga_jumlah) as subtotal'))
            ->where('order.id_order','=',$reservasi->id_order)//satu order cuma bisa 1 reservasi
            ->first();

        $storeData['id_order'] = $reservasi->id_order;
        $storeData['subtotal'] = $pesanan->subtotal;
        $storeData['waktu_transaksi']= Carbon::now()->format('Y-m-d H:i:s');
        $storeData['service'] = 0.05*$storeData['subtotal'];
        $storeData['tax'] = 0.1*$storeData['subtotal'];
        $storeData['total'] = $storeData['service']+$storeData['tax']+$storeData['subtotal'];

        $transaksi = Transaksi::create($storeData);
        return response([
            'message' => 'Add Transaksi Success',
            'data' => $transaksi,
        ],200);

    }

    public function destroy($id){
        $transaksi = Transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi Not Found',
                'data'=>null
            ],404);
        }

        if($transaksi->delete()){
            return response([
                'message' => 'Delete Transaksi Success',
                'data' =>$transaksi,
            ],200);
        }

        return response([
            'message' => 'Delete Transaksi Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $transaksi = Transaksi::find($id);
        if(is_null($transaksi)){
            return response([
                'message'=>'Transaksi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_transaksi' => 'required|numeric|unique:transaksi',
            'payment_method' => 'required|string|in:cash,credit/debit card',
            'id_kartu' => 'nullable|numeric|exists:kartu',
            'no_verifikasi' => 'nullable|numeric',
            'id_order' => 'required|numeric|exists:order',
            'id_kasir' => 'required|numeric|exists:pegawai,id_pegawai',
            'subtotal' => 'required|numeric',
            'service' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $transaksi->id_transaksi =  $updateData['id_transaksi'];
        $transaksi->payment_method =  $updateData['payment_method'];
        $transaksi->id_kartu =  $updateData['id_kartu'];
        $transaksi->no_verifikasi =  $updateData['no_verifikasi'];
        $transaksi->id_order =  $updateData['id_order'];
        $transaksi->id_kasir =  $updateData['id_kasir'];
        $transaksi->subtotal =  $updateData['subtotal'];
        $transaksi->service =  $updateData['expired_date'];
        $transaksi->tax =  $updateData['nama_pemilik'];
        $transaksi->total =  $updateData['expired_date'];

        if($transaksi->save()){
            return response([
                'message' => 'Update Transaksi Success',
                'data'=> $transaksi,
            ],200);
        }

        return response([
            'message'=>'Update Transaksi Failed',
            'data'=>null,
        ],400);
    }
}
