<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index(){
        $transaksi = DB::table('transaksi')
            ->join('kartu','id_kartu','=','id_kartu')
            ->join('order','id_order','=','id_order')
            ->join('pegawai','id_kasir','=','id_pegawai')
            ->select('transaksi.*','kartu.tipe_kartu','order.tgl_order','pegawai.nama_pegawai')
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

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
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
        {
            return response(['message'=> $validate->errors()],400);
        }

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
