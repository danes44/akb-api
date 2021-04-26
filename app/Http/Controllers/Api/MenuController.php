<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index(){
        $menu = DB::table('menu')
            ->join('bahan','menu.id_bahan','=','bahan.id_bahan')
            ->select('menu.*','bahan.jumlah_stok','bahan.jumlah_per_sajian','bahan.ketersediaan')
            ->whereNull('menu.deleted_at')
            ->get();

        if(count($menu)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$menu
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);
    }

    public function show ($id){
        $menu = DB::table('menu')
            ->join('bahan','menu.id_bahan','=','bahan.id_bahan')
            ->select('menu.*','bahan.jumlah_stok','bahan.jumlah_per_sajian','bahan.ketersediaan')
            ->where('menu.id_menu','=',$id)
            ->whereNull('menu.deleted_at')
            ->get();


        if(!is_null($menu)){
            return response([
                'message'  => 'Retrieve Menu Success',
                'data' => $menu
            ],200);

        }

        return response([
            'message' => 'Menu Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_menu' => 'required|string',
            'deskripsi' => 'required|string',
            'unit' => 'required|string',
            'tipe_menu' => 'required|string|in:utama,side dish,minuman',
            'harga' => 'required|numeric',
//            'is_available' => 'required|boolean',
            'id_bahan'=> 'required|string|exists:bahan',
            'str_gambar' => 'required|mimes:jpg,bmp,png|max:5000|',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        if ($request->hasFile('str_gambar')) {
            if ($request->file('str_gambar')->isValid()) {
                $extension = $request->str_gambar->extension();
                $name = $_SERVER['REQUEST_TIME'];
                $request->str_gambar->storeAs('/public', $name.".".$extension);
                $url = Storage::url($name.".".$extension);
            }else{
                return response([
                    'message'=> 'Upload Photo Menu Failed (Not Valid)',
                    'data'=> null,
                ],400);
            }
        }else{
            return response([
                'message' => 'Upload Photo Menu Failed (No File)',
                'data' => null,
            ],400);
        }

        $storeData['str_gambar'] = $url;
        $menu = Menu::create($storeData);
        return response([
            'message' => 'Add Menu Success',
            'data' => $menu,
        ],200);

    }

    public function destroy($id){
        $menu = Menu::find($id);

        if(is_null($menu)){
            return response([
                'message' => 'Menu Not Found',
                'data'=>null
            ],404);
        }

        if($menu->delete()){
            return response([
                'message' => 'Delete Menu Success',
                'data' =>$menu,
            ],200);
        }

        return response([
            'message' => 'Delete Menu Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $menu = Menu::find($id);
        if(is_null($menu)){
            return response([
                'message'=>'Menu Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_menu' => 'required|string',
            'deskripsi' => 'required|string',
            'unit' => 'required|string',
            'tipe_menu' => 'required|string|in:utama,side dish,minuman',
            'harga' => 'required|numeric',
//            'is_available' => 'required|boolean',
            'id_bahan'=> 'required|numeric|exists:bahan'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);
        $menu->id_bahan =  $updateData['id_bahan'];
        $menu->nama_menu =  $updateData['nama_menu'];
        $menu->deskripsi =  $updateData['deskripsi'];
        $menu->unit =  $updateData['unit'];
        $menu->tipe_menu =  $updateData['tipe_menu'];
        $menu->harga =  $updateData['harga'];
//        $menu->is_available =  $updateData['is_available'];

        if($menu->save()){
            return response([
                'message' => 'Update Menu Success',
                'data'=> $menu,
            ],200);
        }

        return response([
            'message'=>'Update Menu Failed',
            'data'=>null,
        ],400);
    }

    public function imageUpload(Request $request, $id){
        $menu = Menu::find($id);
        if(is_null($menu)){
            return response([
                'message' => 'Menu not found',
                'data' => null
            ],404);
        }

        if(!$request->hasFile('str_gambar')) {
            return response([
                'message' => 'Upload Photo Menu Failed (No File)',
                'data' => null,
            ],400);
        }
        $file = $request->file('str_gambar');

        if(!$file->isValid()) {
            return response([
                'message'=> 'Upload Photo Menu Failed (Not Valid)',
                'data'=> null,
            ],400);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'str_gambar' => 'required|mimes:jpg,bmp,png|max:5000|',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        // $imageName = $files->getClientOriginalName();
        // $request->gambar_product->move(public_path('images'),$imageName);
        $extension = $request->str_gambar->extension();
        $name = $_SERVER['REQUEST_TIME'];
        $request->str_gambar->storeAs('/public', $name.".".$extension);
        $url = Storage::url($name.".".$extension);

        $updateData['str_gambar'] = $url;
        $menu->str_gambar =  $updateData['str_gambar'];


        if($menu->save()){
            return response([
                'message' => 'Upload Photo Menu Success',
                'path' => $url,
            ],200);
        }

        return response([
            'messsage'=>'Upload Photo Menu Failed',
            'data'=>null,
        ],400);

    }

}
