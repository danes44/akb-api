<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\StokMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StokMasukController extends Controller
{
    public function index(){
        $StokMasuk = DB::table('stok_masuk')
            ->join('bahan','bahan.id_bahan','=','stok_masuk.id_bahan')
            ->select('stok_masuk.*','bahan.nama_bahan')
            ->whereNull('stok_masuk.deleted_at')
            ->get();

        if(count($StokMasuk)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$StokMasuk
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);
    }

    public function show ($id){
        $StokMasuk = DB::table('stok_masuk')
            ->join('bahan','bahan.id_bahan','=','stok_masuk.id_bahan')
            ->select('stok_masuk.*','bahan.nama_bahan')
            ->where('stok_masuk.id_stok_masuk','=',$id)
            ->whereNull('stok_masuk.deleted_at')
            ->get();


        if(!is_null($StokMasuk)){
            return response([
                'message'  => 'Retrieve Stok Masuk Success',
                'data' => $StokMasuk
            ],200);

        }

        return response([
            'message' => 'Stok Masuk Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'jumlah' => 'required|numeric',
            'harga' => 'required|numeric',
            'id_bahan' => 'required|exists:bahan',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $StokMasuk = StokMasuk::create($storeData);
        return response([
            'message' => 'Add Stok Masuk Success',
            'data' => $StokMasuk,
        ],200);

    }

    public function destroy($id){
        $StokMasuk = StokMasuk::find($id);

        if(is_null($StokMasuk)){
            return response([
                'message' => 'StokMasuk Not Found',
                'data'=>null
            ],404);
        }

        if($StokMasuk->delete()){
            return response([
                'message' => 'Delete Stok Masuk Success',
                'data' =>$StokMasuk,
            ],200);
        }

        return response([
            'message' => 'Delete Stok Masuk Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $StokMasuk = StokMasuk::find($id);
        if(is_null($StokMasuk)){
            return response([
                'message'=>'Stok Masuk Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'jumlah' => 'required|numeric',
            'harga' => 'required|numeric',
            'id_bahan' => 'required|exists:bahan',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $StokMasuk->jumlah =  $updateData['jumlah'];
        $StokMasuk->harga =  $updateData['harga'];
        $StokMasuk->id_bahan =  $updateData['id_bahan'];

        if($StokMasuk->save()){
            return response([
                'message' => 'Update Stok Masuk Success',
                'data'=> $StokMasuk,
            ],200);
        }

        return response([
            'message'=>'Update Stok Masuk Failed',
            'data'=>null,
        ],400);
    }
}
