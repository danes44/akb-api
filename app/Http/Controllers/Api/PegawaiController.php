<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Pegawai;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index(){
        $pegawai = DB::table('pegawai')
            ->join('role','pegawai.id_role','=','role.id_role')
            ->select('pegawai.id_pegawai','pegawai.id_role',
                'pegawai.nama_pegawai',
                'pegawai.jenis_kelamin',
                'pegawai.tgl_gabung',
                'pegawai.tgl_keluar',
                'pegawai.status_pegawai',
                'pegawai.email',
                'pegawai.created_at',
                'pegawai.updated_at','role.role_pegawai')
            ->get();

        if(count($pegawai)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$pegawai
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function show ($id){
        $pegawai = DB::table('pegawai')
            ->join('role','role.id_role','=','pegawai.id_role')
            ->select('pegawai.id_pegawai','pegawai.id_role',
                'pegawai.nama_pegawai',
                'pegawai.jenis_kelamin',
                'pegawai.tgl_gabung',
                'pegawai.tgl_keluar',
                'pegawai.status_pegawai',
                'pegawai.email',
                'pegawai.created_at',
                'pegawai.updated_at','role.role_pegawai')
            ->where('pegawai.id_pegawai','=',$id)
            ->get();


        if(count($pegawai)>0){
            return response([
                'message'  => 'Retrieve Pegawai Success',
                'data' => $pegawai
            ],200);
        }

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'id_role' => 'required|exists:role',
            'nama_pegawai' => 'required|string|regex:/^[\pL\s\-]+$/u',
            'jenis_kelamin' => 'required|string|in:pria,wanita',
            'tgl_gabung' => 'required|date',
            'tgl_keluar' => 'nullable|date',
            'status_pegawai' => 'required|string|in:aktif,non aktif',
            'email' => 'required|string|email|unique:pegawai',
            'password' => 'required|string'
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }
        $storeData['password'] = bcrypt($request->password); //enkripsi password
        $pegawai = Pegawai::create($storeData);
        return response([
            'message' => 'Add Pegawai Success',
            'data' => $pegawai,
        ],200);

    }

    public function destroy($id){
        $pegawai = Pegawai::find($id);

        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
                'data'=>null
            ],404);
        }

        if($pegawai->delete()){
            return response([
                'message' => 'Delete Pegawai Success',
                'data' =>$pegawai,
            ],200);
        }

        return response([
            'message' => 'Delete Pegawai Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $pegawai = Pegawai::find($id);
        if(is_null($pegawai)){
            return response([
                'message'=>'Pegawai Not Found',
                'data'=>null
            ],404);
        }


        $updateData = $request->all();
        $validate = Validator::make($updateData,[
//            'role_pegawai' => 'required|exists:role',
            'id_role'=> 'required|numeric|exists:role',
            'nama_pegawai' => 'required|string|regex:/^[\pL\s\-]+$/u',
            'jenis_kelamin' => 'required|string|in:pria,wanita',
            'tgl_gabung' => 'required|date',
            'tgl_keluar' => 'nullable|date',
            'status_pegawai' => 'required|string|in:aktif,non aktif',
            'email' => ['required','string','email:rfc,dns',Rule::unique('pegawai')->ignore($pegawai)],

        ]);

//        $id_role = DB::table('role')
//            ->select('id_role')
//            ->where('role_pegawai','=',$role)
//            ->first();

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

//        $pegawai->id_role =  $id_role;
        $pegawai->id_role =  $updateData['id_role'];
        $pegawai->nama_pegawai =  $updateData['nama_pegawai'];
        $pegawai->jenis_kelamin =  $updateData['jenis_kelamin'];
        $pegawai->tgl_gabung =  $updateData['tgl_gabung'];
        $pegawai->tgl_keluar =  $updateData['tgl_keluar'];
        $pegawai->status_pegawai =  $updateData['status_pegawai'];
        $pegawai->email =  $updateData['email'];

        if($pegawai->save()){
            return response([
                'message' => 'Update Pegawai Success',
                'data'=> $pegawai,
            ],200);
        }

        return response([
            'message'=>'Update Pegawai Failed',
            'data'=>null,
        ],400);
    }

    public function updatePassword(Request $request,$id){
        $pegawai = Pegawai::find($id);

        if(is_null($pegawai)){
            return response([
                'message'=>'$pegawai Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'password'=>'required',
            'newPassword'=>'required',
            'confirmPassword'=>'required'
        ]);

        if($validate->fails()){
            return response(['message'=>$validate->errors()],404);//return error invalid input
        }else{
            if((Hash::check(request('password'), Auth::user()->password))==false){
                return response([
                    'message'=>'Please check your old password ',
                    'data'=>null,
                ],404);//return message saat user gagal diedit
            }else if($updateData['newPassword'] != $updateData['confirmPassword']){
                return response([
                    'message'=>'new password doesnt match',
                    'data'=>null,
                ],404);//return message saat user gagal diedit
            }else{
                $pegawai->password = bcrypt($updateData['newPassword']);
            }
        }

        if($pegawai->save()){
            return response([
                'message'=>'Update User Success',
                'data'=>$pegawai,
            ],200);
        }//return user yg telah diedit

        return response([
            'message'=>'Update User Failed',
            'data'=>null,
        ],404);//return message saat user gagal diedit
    }
}
