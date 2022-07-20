<?php

namespace App\Http\Controllers\Api;

use Encore\Admin\Controllers\AdminController;  
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PlantingReturn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class PlantingReturnsCompanyApiController extends AdminController
{
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function planting_returns_company_list()
    {
        /*  ---attributes---
        */
        $user = auth()->user();
        $query = DB::table('planting_returns')->where('administrator_id', '=', $user->id)->get();
        // $query = PlantingReturn::all();
        
        return $this->successResponse($query, $message="Planting Returns- Company");
    } 


    // create new planting returns company form via api
    public function planting_returns_company_create(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $data = $request->only(
            'address', 
            'telephone', 
            'amount_enclosed',
            'payment_receipt', 
            'registerd_dealer',
            'sub_growers_file',
        );

        $validator = Validator::make($data, [
            'address' => 'required', 
            'telephone' => 'required|integer', 
            'amount_enclosed' => 'required|integer',
            'payment_receipt' => 'required', 
            'registerd_dealer',
            'sub_growers_file' => 'required',
        ]);

        $messages= [
            'eid.required' => "The :attribute field is required",
            'eid.email' => "The :attribute :input format should be example@example.com/.in/.edu/.org....",
            'eid.unique' => "The :attribute :input is taken. Please use another email address",
            'confirm_password.same' => "Password and Confirm password fields must match exactly",
            'mobno.digits' => "The :attribute  field accepts only numbers",
            'mobno.digits:10' => "The :attribute should be 10 digits long",
            'dob.date_format' => "The date format :input should be YYYY-MM-DD",
            'address.string' => "The :attribute :input must be in the form of a string"
        ];

        $validate =  Validator::make($request->all(), $validator, $messages);
        

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toJson(), 200)
            ->withErrors($validate->messages())->withInput(); 
        }


        $form = PlantingReturn::create([
            'administrator_id' => $user->id,
            'name' => $user->name,
            'address' => $request->input('address'), 
            'telephone' => $request->input('telephone'), 
            'amount_enclosed' => $request->input('amount_enclosed'),
            'payment_receipt' => $request->input('payment_receipt'), 
            'registerd_dealer' => $request->input('registerd_dealer'),
            'sub_growers_file' => $request->input('sub_growers_file'),
        ]);

        // Form created, return success response
        return $this->successResponse($form, "Planting returns company submit success!", 201); 
    }
}

