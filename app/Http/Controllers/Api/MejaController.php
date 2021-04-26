<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MejaController extends Controller
{
    public function index(){
        $meja = DB::table('meja')
            ->select('no_meja','status_meja')
            ->whereNull('meja.deleted_at')
            ->get();



//        foreach ($meja as $m) {
//            $reservasi= DB::table('reservasi')
//                ->join('meja','meja.no_meja','=','reservasi.id_reservasi')
//                ->select('reservasi.*')
//                ->where('reservasi.no_meja','=',$m->no_meja)
//                ->where('reservasi.tgl_reservasi','=',today())
//                ->get();
//        }
//


        if(count($meja)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$meja
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function show ($id){
        $meja = Meja::find($id);

        if(!is_null($meja)){
            return response([
                'message'  => 'Retrieve Meja Success',
                'data' => $meja
            ],200);
        }

        return response([
            'message' => 'Meja Not Found',
            'data' => null
        ],404);
    }

    public function mejaTersedia(){
        $meja = DB::table('meja')
            ->where('status_meja','=','tersedia')
            ->whereNull('deleted_at')
            ->get();

        if(count($meja)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$meja
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);
    }

    public function mejaPerTanggal($tanggal){
        $meja = DB::table('meja')
            ->join('reservasi','reservasi.no_meja','=','meja.no_meja')
            ->select('meja.no_meja','meja.status_meja','reservasi.tgl_reservasi')
            ->where('reservasi.tgl_reservasi','=',$tanggal)
            ->whereNull('reservasi.deleted_at')
            ->whereNull('meja.deleted_at')
            ->get();

        if(count($meja)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$meja
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'status_meja' => 'required|string|regex:/^[\pL\s\-]+$/u|in:tersedia,tidak tersedia',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $meja = Meja::create($storeData);
        return response([
            'message' => 'Add Meja Success',
            'data' => $meja,
        ],200);

    }

    public function destroy($id){
        $meja = Meja::find($id);

        if(is_null($meja)){
            return response([
                'message' => 'Meja Not Found',
                'data'=>null
            ],404);
        }

        if($meja->delete()){
            return response([
                'message' => 'Delete Meja Success',
                'data' =>$meja,
            ],200);
        }

        return response([
            'message' => 'Delete Meja Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $meja = Meja::find($id);
        if(is_null($meja)){
            return response([
                'message'=>'Meja Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_meja' => 'required|string|regex:/^[\pL\s\-]+$/u|in:tersedia,tidak tersedia',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $meja->status_meja =  $updateData['status_meja'];

        if($meja->save()){
            return response([
                'message' => 'Update Meja Success',
                'data'=> $meja,
            ],200);
        }

        return response([
            'message'=>'Update Meja Failed',
            'data'=>null,
        ],400);
    }
}
