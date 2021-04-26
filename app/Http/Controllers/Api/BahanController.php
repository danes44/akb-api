<?php

namespace App\Http\Controllers\Api;

use App\Bahan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BahanController extends Controller
{
    public function index(){
        $bahan = Bahan::all();

        if(count($bahan)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$bahan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);

    }

    public function show ($id){
        $bahan=Bahan::find($id);


        if(!is_null($bahan)){
            return response([
                'message'  => 'Retrieve Bahan Success',
                'data' => $bahan
            ],200);

        }

        return response([
            'message' => 'Bahan Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_bahan' => 'required|string',
            'jumlah_stok' => 'required|numeric',
            'jumlah_per_sajian' => 'required|numeric',
            'unit'=>'required|string',
            'ketersediaan' => 'required|boolean',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $bahan = Bahan::create($storeData);
        return response([
            'message' => 'Add Bahan Success',
            'data' => $bahan,
        ],200);

    }

    public function destroy($id){
        $bahan = Bahan::find($id);

        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Not Found',
                'data'=>null
            ],404);
        }

        if($bahan->delete()){
            return response([
                'message' => 'Delete Bahan Success',
                'data' =>$bahan,
            ],200);
        }

        return response([
            'message' => 'Delete Bahan Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $bahan = Bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message'=>'Bahan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_bahan' => 'required|string',
            'jumlah_stok' => 'required|numeric',
            'jumlah_per_sajian' => 'required|numeric',
            'unit'=>'required|string',
            'ketersediaan' => 'required|boolean',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $bahan->nama_bahan =  $updateData['nama_bahan'];
        $bahan->jumlah_stok = $updateData['jumlah_stok'];
        $bahan->jumlah_per_sajian = $updateData['jumlah_per_sajian'];
        $bahan->unit = $updateData['unit'];
        $bahan->ketersediaan = $updateData['ketersediaan'];

        if($bahan->save()){
            return response([
                'message' => 'Update Bahan Success',
                'data'=> $bahan,
            ],200);
        }

        return response([
            'message'=>'Update Bahan Failed',
            'data'=>null,
        ],400);
    }
}
