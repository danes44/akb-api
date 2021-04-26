<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReservasiController extends Controller
{
    public function index(){
        $reservasi = DB::table('reservasi')
            ->join('customer','customer.id_customer','=','reservasi.id_customer')
            ->join('meja','meja.no_meja','=','reservasi.no_meja')
            ->join('pegawai','pegawai.id_pegawai','=','reservasi.id_waiter')
            ->select('reservasi.*','meja.no_meja','customer.nama_customer','pegawai.nama_pegawai')
            ->whereNull('reservasi.deleted_at')
            ->get();

        if(count($reservasi)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$reservasi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function showSelect(Request $request){
        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'tgl_reservasi' => 'required|date',
            'sesi' => 'required|string|in:lunch,dinner',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $reservasi = DB::table('reservasi')
            ->where('tgl_reservasi','=',$updateData['tgl_reservasi'])
            ->where('sesi','=',$updateData['sesi'])
            ->where('status_reservasi','not like','selesai')
            ->whereNull('deleted_at')
            ->get();

        if(count($reservasi)>0){
            return response([
                'message'  => 'Retrieve Reservasi Success',
                'data' => $reservasi
            ],200);

        }

        return response([
            'message' => 'Reservasi Not Found',
            'data' => null
        ],404);
    }

    public function show ($id){
        $reservasi = DB::table('reservasi')
            ->join('customer','customer.id_customer','=','reservasi.id_customer')
            ->join('meja','meja.no_meja','=','reservasi.no_meja')
            ->join('pegawai','pegawai.id_pegawai','=','reservasi.id_waiter')
            ->select('reservasi.*','meja.no_meja','customer.nama_customer','pegawai.nama_pegawai')
            ->where('reservasi.id_reservasi','=',$id)
            ->whereNull('reservasi.deleted_at')
            ->get();


        if(count($reservasi)>0){
            return response([
                'message'  => 'Retrieve Reservasi Success',
                'data' => $reservasi
            ],200);

        }

        return response([
            'message' => 'Reservasi Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'id_customer' => 'required|exists:customer',
            'no_meja' => 'required|exists:meja',
            'id_waiter' => 'required|exists:pegawai,id_pegawai',
            'tgl_reservasi' => 'required|date',
            'sesi' => 'required|string|in:lunch,dinner',
            'status_reservasi' => 'required|string|in:aktif,non aktif,selesai',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $reservasi = Reservasi::create($storeData);
        return response([
            'message' => 'Add Reservasi Success',
            'data' => $reservasi,
        ],200);

    }

    public function destroy($id){
        $reservasi = Reservasi::find($id);

        if(is_null($reservasi)){
            return response([
                'message' => 'Reservasi Not Found',
                'data'=>null
            ],404);
        }

        if($reservasi->delete()){
            return response([
                'message' => 'Delete Reservasi Success',
                'data' =>$reservasi,
            ],200);
        }

        return response([
            'message' => 'Delete Reservasi Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $reservasi = Reservasi::find($id);
        if(is_null($reservasi)){
            return response([
                'message'=>'Reservasi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_customer' => 'required|exists:customer',
            'no_meja' => 'required|exists:meja',
            'id_waiter' => 'required|exists:pegawai,id_pegawai',
            'tgl_reservasi' => 'required|date',
            'sesi' => 'required|string|in:lunch,dinner',
            'status_reservasi' => 'required|string|in:aktif,non aktif,selesai',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $reservasi->id_customer =  $updateData['id_customer'];
        $reservasi->no_meja =  $updateData['no_meja'];
        $reservasi->id_waiter =  $updateData['id_waiter'];
        $reservasi->tgl_reservasi =  $updateData['tgl_reservasi'];
        $reservasi->sesi =  $updateData['sesi'];



        if($reservasi->save()){
            return response([
                'message' => 'Update Reservasi Success',
                'data'=> $reservasi,
            ],200);
        }

        return response([
            'message'=>'Update Reservasi Failed',
            'data'=>null,
        ],400);
    }

    public function updateStatus(Request $request, $id){
        $reservasi = Reservasi::find($id);
        if(is_null($reservasi)){
            return response([
                'message'=>'Reservasi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_reservasi' => 'required|string|in:aktif,non aktif,selesai',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $reservasi->status_reservasi =  $updateData['status_reservasi'];

        if($reservasi->save()){
            return response([
                'message' => 'Update Status Reservasi Success',
                'data'=> $reservasi,
            ],200);
        }

        return response([
            'message'=>'Update Status Reservasi Failed',
            'data'=>null,
        ],400);
    }
}
