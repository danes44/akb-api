<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(){
        $role = Role::all();

        if(count($role)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$role
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);


    }

    public function show ($id){
        $role = Role::find($id);


        if(!is_null($role)){
            return response([
                'message'  => 'Retrieve Role Success',
                'data' => $role
            ],200);

        }

        return response([
            'message' => 'Role Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'role_pegawai' => 'required|string|regex:/^[\pL\s\-]+$/u',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $role = Role::create($storeData);
        return response([
            'message' => 'Add Role Success',
            'data' => $role,
        ],200);

    }

    public function destroy($id){
        $role = Role::find($id);

        if(is_null($role)){
            return response([
                'message' => 'Role Not Found',
                'data'=>null
            ],404);
        }

        if($role->delete()){
            return response([
                'message' => 'Delete Role Success',
                'data' =>$role,
            ],200);
        }

        return response([
            'message' => 'Delete Role Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $role = Role::find($id);
        if(is_null($role)){
            return response([
                'message'=>'Role Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'role_pegawai' => 'required|string|regex:/^[\pL\s\-]+$/u',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $role->role_pegawai =  $updateData['role_pegawai'];

        if($role->save()){
            return response([
                'message' => 'Update Role Success',
                'data'=> $role,
            ],200);
        }

        return response([
            'message'=>'Update Role Failed',
            'data'=>null,
        ],400);
    }
}
