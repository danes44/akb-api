<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Kartu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KartuController extends Controller
{
    public function index(){
        $kartu = Kartu::all();

        if(count($kartu)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$kartu
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function show ($id){
        $kartu = Kartu::find($id);


        if(!is_null($kartu)){
            return response([
                'message'  => 'Retrieve Kartu Success',
                'data' => $kartu
            ],200);

        }

        return response([
            'message' => 'Kartu Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'no_kartu' => 'required|digits_between:13,16|unique:kartu',
            'tipe_kartu' => 'required|string|in:debit,credit',
            'nama_pemilik' => 'required|string|regex:/^[\pL\s\-]+$/u',
            'expired_date' => 'required|date'
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $kartu = Kartu::create($storeData);
        return response([
            'message' => 'Add Kartu Success',
            'data' => $kartu,
        ],200);

    }

    public function destroy($id){
        $kartu = Kartu::find($id);

        if(is_null($kartu)){
            return response([
                'message' => 'kartu Not Found',
                'data'=>null
            ],404);
        }

        if($kartu->delete()){
            return response([
                'message' => 'Delete Kartu Success',
                'data' =>$kartu,
            ],200);
        }

        return response([
            'message' => 'Delete Kartu Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $kartu = Kartu::find($id);
        if(is_null($kartu)){
            return response([
                'message'=>'Kartu Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'no_kartu' => ['required','digits_between:13,16',Rule::unique('kartu')->ignore($kartu)],
            'tipe_kartu' => 'required|string|in:debit,credit',
            'nama_pemilik' => 'required|string|regex:/^[\pL\s\-]+$/u',
            'expired_date' => 'required|date'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $kartu->no_kartu =  $updateData['no_kartu'];
        $kartu->tipe_kartu =  $updateData['tipe_kartu'];
        $kartu->nama_pemilik =  $updateData['nama_pemilik'];
        $kartu->expired_date =  $updateData['expired_date'];

        if($kartu->save()){
            return response([
                'message' => 'Update Kartu Success',
                'data'=> $kartu,
            ],200);
        }

        return response([
            'message'=>'Update Kartu Failed',
            'data'=>null,
        ],400);
    }
}
