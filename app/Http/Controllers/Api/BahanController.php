<?php

namespace App\Http\Controllers\Api;

use App\Bahan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function showKosong (){
        $bahan=DB::table('bahan')
            ->whereNull('deleted_at')
            ->where('jumlah_stok','=',0)
            ->get();


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

    public function showLaporanMakanan(){
//        $stokKeluar = DB::table('stok_keluar')
//            ->select('stok_keluar.id_bahan','stok_keluar.status',
//                DB::raw('sum(jumlah) as waste'))
//            ->where('stok_keluar.status','=','sisa')
//            ->groupBy('stok_keluar.id_bahan','stok_keluar.status')
//            ->get();

//        $bahan=DB::table('bahan')
//            ->join('stok_masuk','bahan.id_bahan','=','stok_masuk.id_bahan')
//            ->join('menu','bahan.id_bahan','=','menu.id_bahan')
//            ->select('bahan.*',
//                DB::raw('count(stok_masuk.jumlah) as jumlah_masuk'),'menu.tipe_menu')
//            ->where('menu.tipe_menu','=','side dish')
//            ->whereNull('bahan.deleted_at')
//            ->groupBy('bahan.id_bahan')
//            ->get();
        $bahan=DB::table('bahan')
            ->join('menu','menu.id_bahan','=','bahan.id_bahan')
//            ->where('menu.tipe_menu','=','utama')
            ->whereNull('bahan.deleted_at')
            ->get();

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

    public function showLaporanSideDish(){
//        $stokKeluar = DB::table('stok_keluar')
//            ->select('stok_keluar.id_bahan','stok_keluar.status',
//                DB::raw('sum(jumlah) as waste'))
//            ->where('stok_keluar.status','=','sisa')
//            ->groupBy('stok_keluar.id_bahan','stok_keluar.status')
//            ->get();

        $bahan=DB::table('bahan')
            ->leftJoin('stok_masuk','bahan.id_bahan','=','stok_masuk.id_bahan')
            ->leftJoin('stok_keluar','bahan.id_bahan','=','stok_keluar.id_bahan')
            ->select('bahan.*',DB::raw('count(stok_masuk.jumlah) as jumlah_masuk'),
                'stok_keluar.id_bahan','stok_keluar.status')
//            ->where('stok_keluar.status','=','sisa')
            ->whereNull('bahan.deleted_at')
            ->groupBy('bahan.id_bahan','stok_keluar.status')
//            ->select('bahan.*','stok_masuk.jumlah as jumlah_masuk' )
            ->get();


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
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        if($storeData['jumlah_stok']>=$storeData['jumlah_per_sajian'])
            $storeData['ketersediaan']=true;
        else
            $storeData['ketersediaan']=false;
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
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $bahan->nama_bahan =  $updateData['nama_bahan'];
        $bahan->jumlah_stok = $updateData['jumlah_stok'];
        $bahan->jumlah_per_sajian = $updateData['jumlah_per_sajian'];
        $bahan->unit = $updateData['unit'];

        if($updateData['jumlah_stok']>=$updateData['jumlah_per_sajian'])
            $updateData['ketersediaan']=true;
        else
            $updateData['ketersediaan']=false;

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

    public function updateStokMasuk(Request $request, $id){
        $bahan = Bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message'=>'Bahan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'jumlah_stok' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $bahan->jumlah_stok = $bahan->jumlah_stok+$updateData['jumlah_stok'];

        if($bahan->jumlah_stok>=$bahan->jumlah_per_sajian)
            $updateData['ketersediaan']=true;
        else
            $updateData['ketersediaan']=false;

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

    public function updateStokKeluar(Request $request, $id){
        $bahan = Bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message'=>'Bahan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'jumlah_stok' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $bahan->jumlah_stok = $bahan->jumlah_stok-$updateData['jumlah_stok'];

        if($bahan->jumlah_stok>=$bahan->jumlah_per_sajian)
            $updateData['ketersediaan']=true;
        else
            $updateData['ketersediaan']=false;

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
