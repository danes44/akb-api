<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OrderController extends Controller
{
    public function index(){
        $order = DB::table('order')
            ->join('reservasi','order.id_reservasi','=','reservasi.id_reservasi')
            ->select('order.*','reservasi.id_reservasi')
            ->get();

        if(count($order)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$order
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function show ($id){
        $reservasi = DB::table('order')
            ->join('reservasi','order.id_reservasi','=','reservasi.id_reservasi')
            ->select('order.*','reservasi.id_reservasi')
            ->where('order.id_order','=',$id)
            ->get();


        if(!is_null($reservasi)){
            return response([
                'message'  => 'Retrieve Order Success',
                'data' => $reservasi
            ],200);

        }

        return response([
            'message' => 'Order Not Found',
            'data' => null
        ],404);
    }

    public function showByReservasi ($id){
        $reservasi = DB::table('order')
            ->join('reservasi','order.id_reservasi','=','reservasi.id_reservasi')
            ->select('order.*','reservasi.id_reservasi')
            ->where('order.id_reservasi','=',$id)
            ->get();


        if(!is_null($reservasi)){
            return response([
                'message'  => 'Retrieve Order Success',
                'data' => $reservasi
            ],200);

        }

        return response([
            'message' => 'Order Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'id_reservasi' => 'required|exists:reservasi',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $storeData['tgl_order'] = Carbon::now()->format('Y-m-d');
        $order = Order::create($storeData);
        return response([
            'message' => 'Add Order Success',
            'data' => $order,
        ],200);

    }
}
