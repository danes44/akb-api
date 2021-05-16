<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\StokKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StokKeluarController extends Controller
{
    public function index(){
        $StokKeluar = DB::table('stok_keluar')
            ->join('bahan','bahan.id_bahan','=','stok_keluar.id_bahan')
            ->select('stok_keluar.*','bahan.nama_bahan')
            ->whereNull('stok_keluar.deleted_at')
            ->get();

        if(count($StokKeluar)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$StokKeluar
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);
    }

    public function show ($id){
        $StokKeluar = DB::table('stok_keluar')
            ->join('bahan','bahan.id_bahan','=','stok_keluar.id_bahan')
            ->select('stok_keluar.*','bahan.nama_bahan')
            ->where('stok_keluar.id_stok_keluar','=',$id)
            ->whereNull('stok_keluar.deleted_at')
            ->get();


        if(!is_null($StokKeluar)){
            return response([
                'message'  => 'Retrieve Stok Keluar Success',
                'data' => $StokKeluar
            ],200);

        }

        return response([
            'message' => 'Stok Keluar Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'jumlah' => 'required|numeric',
            'status' => 'required|string|in:keluar,sisa',
            'id_bahan' => 'required|numeric|exists:bahan',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $StokKeluar = StokKeluar::create($storeData);
        return response([
            'message' => 'Add Stok Keluar Success',
            'data' => $StokKeluar,
        ],200);

    }

    public function destroy($id){
        $StokKeluar = StokKeluar::find($id);

        if(is_null($StokKeluar)){
            return response([
                'message' => 'StokKeluar Not Found',
                'data'=>null
            ],404);
        }

        if($StokKeluar->delete()){
            return response([
                'message' => 'Delete Stok Keluar Success',
                'data' =>$StokKeluar,
            ],200);
        }

        return response([
            'message' => 'Delete Stok Keluar Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $StokKeluar = StokKeluar::find($id);
        if(is_null($StokKeluar)){
            return response([
                'message'=>'Stok Keluar Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'jumlah' => 'required|numeric',
            'status' => 'required|string|in:keluar,sisa',
            'id_bahan' => 'required|exists:bahan',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $StokKeluar->jumlah =  $updateData['jumlah'];
        $StokKeluar->status =  $updateData['status'];
        $StokKeluar->id_bahan =  $updateData['id_bahan'];

        if($StokKeluar->save()){
            return response([
                'message' => 'Update Stok Keluar Success',
                'data'=> $StokKeluar,
            ],200);
        }

        return response([
            'message'=>'Update Stok Keluar Failed',
            'data'=>null,
        ],400);
    }
}
