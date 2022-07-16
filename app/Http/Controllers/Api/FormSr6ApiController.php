<?php

namespace App\Http\Controllers\Api;

use App\Models\FormSr6;
use App\Models\FormSr4;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Encore\Admin\Controllers\AdminController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB; 
use App\Traits\ApiResponser;



class FormSr6ApiController extends AdminController 
{
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function form_sr6_list()
    {
        $user = auth()->user();
        $query = DB::table('form_sr6s')->where('administrator_id', '=', $user->id)->get();
        // $query = FormSr6::all();

        return $this->successResponse($query, $message="SR6 forms"); 
    } 


    // create new sr4 form
    public function form_sr6_create(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $data = $request->only(
            'type',
            'address',
            'premises_location',
            'years_of_expirience',
            'form_sr6_has_crops',
            'crop_id',
            'seed_grower_in_past',
            'cropping_histroy',
            'have_adequate_storage',
            'have_adequate_isolation',
            'have_adequate_labor',
            'aware_of_minimum_standards',
            'signature_of_applicant',
        );

        $post_data = Validator::make($data, [
            'type' => 'required',
            'address' => 'required',
            'premises_location' => 'required',
            'years_of_expirience' => 'required|min:1|integer',
            'form_sr6_has_crops' => 'required',
            'crop_id' => 'required',
            'seed_grower_in_past' => 'required',
            'cropping_histroy' => 'required',
            'have_adequate_storage' => 'required',
            'have_adequate_isolation' => 'required',
            'have_adequate_labor' => 'required',
            'aware_of_minimum_standards' => 'required',
            'signature_of_applicant',
        ]);

        if ($post_data->fails()) {
            return $this->errorResponse("SR6 form submit error", 200); 
        }

        $form = FormSr6::create([
            'administrator_id' => $user->id,
            'type' => $request->input('type'),
            'name_of_applicant' => $user->name,
            'address' => $request->input('address'),
            'premises_location' => $request->input('premises_location'),
            'years_of_expirience' => $request->input('years_of_expirience'),
            'form_sr6_has_crops' => $request->input('form_sr6_has_crops'),
            'seed_grower_in_past' => $request->input('seed_grower_in_past'),
            'cropping_histroy' => $request->input('cropping_histroy'),
            'have_adequate_storage' => $request->input('have_adequate_storage'),
            'have_adequate_isolation' => $request->input('have_adequate_isolation'),
            'have_adequate_labor' => $request->input('have_adequate_labor'),
            'aware_of_minimum_standards' => $request->input('aware_of_minimum_standards'),
            'signature_of_applicant' => $request->input('signature_of_applicant'),
        ]);

        // Form created, return success response
        return $this->successResponse($form, "SR6 form submit success!", 201); 
    }
}
