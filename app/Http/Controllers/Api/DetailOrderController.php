<?php

namespace App\Http\Controllers\Api;

use App\DetailOrder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DetailOrderController extends Controller
{
    public function index(){
        $detail_order = DB::table('detail_order')
            ->join('order','detail_order.id_order','=','detail_order.id_order')
            ->join('menu','menu.id_menu','=','detail_order.id_menu')
            ->select('detail_order.*','order.id_order')
            ->get();

        if(count($detail_order)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$detail_order
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);

    }

    public function show ($id){
        $detail_order = DB::table('detail_order')
            ->join('order','id_order','=','id_order')
            ->select('detail_order.*','order.id_order')
            ->where('detail_order.id_detail','=',$id)
            ->get();

        if(!is_null($detail_order)){
            return response([
                'message'  => 'Retrieve Detail Order Success',
                'data' => $detail_order
            ],200);

        }

        return response([
            'message' => 'Detail Order Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'id_order' => 'required|exists:order',
            'jumlah_order' => 'required|numeric',
            'harga_jumlah' => 'required|numeric',
            'status_order' => 'required|in:sedang dimasak,siap disajikan',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $detail_order = DetailOrder::create($storeData);
        return response([
            'message' => 'Add Detail Order Success',
            'data' => $detail_order,
        ],200);

    }

    public function destroy($id){
        $detail_order = DetailOrder::find($id);

        if(is_null($detail_order)){
            return response([
                'message' => 'Detail Order Not Found',
                'data'=>null
            ],404);
        }

        if($detail_order->delete()){
            return response([
                'message' => 'Delete Detail Order Success',
                'data' =>$detail_order,
            ],200);
        }

        return response([
            'message' => 'Delete Detail Order Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $detail_order = DetailOrder::find($id);
        if(is_null($detail_order)){
            return response([
                'message'=>'Detail Order Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_order' => 'required|exists:order',
            'jumlah_order' => 'required|numeric',
            'harga_jumlah' => 'required|numeric',
            'status_order' => 'required|in:sedang dimasak,siap disajikan',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $detail_order->id_order =  $updateData['id_order'];
        $detail_order->jumlah_order =  $updateData['jumlah_order'];
        $detail_order->harga_jumlah =  $updateData['harga_jumlah'];
        $detail_order->status_order =  $updateData['status_order'];


        if($detail_order->save()){
            return response([
                'message' => 'Update Detail Order Success',
                'data'=> $detail_order,
            ],200);
        }

        return response([
            'message'=>'Update Detail Order Failed',
            'data'=>null,
        ],400);
    }
}
