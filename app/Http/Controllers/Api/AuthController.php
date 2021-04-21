<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Pegawai;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData,[
            'email'=>'required|email:rfc,dns',
            'password'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400);

        if(!Auth::attempt($loginData))
            return response(['message'=>'Invalid Credentials '],401);

        $user = Auth::user();
        $userJoin = DB::table('pegawai')
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
            ->where('pegawai.id_pegawai','=',$user->id_pegawai)
            ->first();

        $token = $user->createToken('Authentication Token')->accessToken;

        if($user->status_pegawai == 'non aktif')
            return  response(['message'=>'Account has been deactivated '],401);

        return response([
            'message'=>'Authenticated',
            'user'=>$user,
            'userJoin'=>$userJoin,
            'token_type'=>'Bearer',
            'access_token'=>$token
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'message'=>'Succesfully logged out'
        ]);
    }
}
