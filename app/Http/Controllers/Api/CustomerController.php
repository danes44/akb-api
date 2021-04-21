<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(){
        $customer = Customer::all();

        if(count($customer)>0){
            return response([
                'message' =>'Retrieve All Success',
                'data' =>$customer
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ],404);
    }

    public function show ($id){
        $customer=Customer::find($id);

        if(!is_null($customer)){
            return response([
                'message'  => 'Retrieve Customer Success',
                'data' => $customer
            ],200);
        }

        return response([
            'message' => 'Customer Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_customer' => 'required|string|regex:/^[\pL\s\-]+$/u',
            'no_telp' => 'numeric|digits_between:10,15|starts_with:08|nullable',
            'email_customer' => 'email:rfc,dns|nullable|unique:customer',
        ]);

        if($validate->fails())
        {
            return response(['message'=> $validate->errors()],400);
        }

        $customer = Customer::create($storeData);
        return response([
         'message' => 'Add Customer Success',
         'data' => $customer,
        ],200);

    }

    public function destroy($id){
        $customer = Customer::find($id);

        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data'=>null
            ],404);
        }

        if($customer->delete()){
            return response([
                'message' => 'Delete Customer Success',
                'data' =>$customer,
            ],200);
        }

        return response([
            'message' => 'Delete Customer Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $customer = Customer::find($id);
        if(is_null($customer)){
            return response([
                'message'=>'Customer Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_customer' => 'required|string|regex:/^[\pL\s\-]+$/u',
            'no_telp' => 'numeric|digits_between:10,15|starts_with:08|nullable',
            'email_customer' => ['nullable','email:rfc,dns',Rule::unique('customer')->ignore($customer)],
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $customer->nama_customer =  $updateData['nama_customer'];
        $customer->no_telp = $updateData['no_telp'];
        $customer->email_customer= $updateData['email_customer'];

        if($customer->save()){
            return response([
                'message' => 'Update Customer Success',
                'data'=> $customer,
            ],200);
        }

        return response([
            'message'=>'Update Customer Failed',
            'data'=>null,
        ],400);
    }

}
