<?php

namespace App\Http\Controllers\Api;

use App\DetailOrder;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DetailOrderController extends Controller
{
    public function index(){
        $detail_order = DB::table('detail_order')
            ->join('order','detail_order.id_order','=','detail_order.id_order')
            ->join('menu','menu.id_menu','=','detail_order.id_menu')
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
            ->join('order','order.id_order','=','detail_order.id_order')
            ->join('menu','menu.id_menu','=','detail_order.id_menu')
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

    public function showCart($id){
        $detail_order = DB::table('detail_order')
            ->join('order','order.id_order','=','detail_order.id_order')
            ->join('menu','menu.id_menu','=','detail_order.id_menu')
            ->where('order.id_reservasi','=',$id)
            ->where('detail_order.status_order','=','dalam keranjang')
            ->get();

        if(count($detail_order)>0){
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

    public function showOrderChef(){
        $detail_order = DB::table('detail_order')
            ->join('order','order.id_order','=','detail_order.id_order')
            ->join('reservasi','reservasi.id_reservasi','=','order.id_reservasi')
            ->join('menu','menu.id_menu','=','detail_order.id_menu')
            ->select('order.*','reservasi.no_meja','menu.*','detail_order.*')
            ->where('detail_order.status_order','<>','dalam keranjang')
            ->get();

        if(count($detail_order)>0){
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

    public function showStruk ($id){
        $transaksi = DB::table('detail_order')
//            ->join('order','order.id_order','=','detail_order.id_order')
            ->join('menu','menu.id_menu','=','detail_order.id_menu')
            ->select('detail_order.id_menu','menu.nama_menu','menu.harga',
                DB::raw('sum(jumlah_order) as qty'),
                DB::raw('sum(harga_jumlah) as subtotal_qty'))
            ->where('detail_order.id_order','=',$id)
            ->groupBy('detail_order.id_menu','menu.nama_menu','menu.harga')
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
            'id_order' => 'required|exists:order',
            'id_menu' => 'required|exists:menu',
            'jumlah_order' => 'required|numeric',
            'harga_jumlah' => 'required|numeric',
            'status_order' => 'required|in:dalam keranjang,sedang dimasak,siap disajikan',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $menu = DB::table('menu')
            ->join('bahan','menu.id_bahan','=','bahan.id_bahan')
            ->select('bahan.ketersediaan')
            ->where('id_menu','=',$storeData['id_menu'])
            ->first();

        if($menu->ketersediaan!=true)
        {
            return response(['message' => 'Menu tidak tersedia'],400);
        }

        $storeData['waktu_order']= Carbon::now()->format('H:i:s');
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
            'id_menu' => 'required|exists:menu',
            'jumlah_order' => 'required|numeric',
            'harga_jumlah' => 'required|numeric',
            'status_order' => 'required|in:dalam keranjang,sedang dimasak,siap disajikan',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $detail_order->id_order =  $updateData['id_order'];
        $detail_order->id_menu =  $updateData['id_menu'];
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

    public function updateStatusCart($id){
        $detail_order = DB::table('detail_order')
            ->where('detail_order.id_order','=',$id)
            ->where('detail_order.status_order','=','dalam keranjang')
            ->update(['status_order'=>"sedang dimasak"]);

        return response([
            'message'=>'Update Detail Order Success',
            'data'=>$detail_order,
        ],200);
    }

    public function updateStatus(Request $request,$id){
        $detail_order = DetailOrder::find($id);
        if(is_null($detail_order)){
            return response([
                'message'=>'Detail Order Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_order' => 'required|in:dalam keranjang,sedang dimasak,siap disajikan',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

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
