<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Zebra_Image;
use Encore\Admin\Facades\Admin;
use App\Models\FormSr10;
use Illuminate\Support\Facades\Mail;
use Encore\Admin\Auth\Database\Administrator;
use App\Mail\MyNotification;


class Utils
{
    public static function add_role($user_id)
    {
        $user = Administrator::find($user_id);
        $user->roles()->attach(3);
    }
    //Application forms
    //1.generic functions for all application forms
    //check if the status of the form is pending, rejected,halted or accepted
    public static function can_create_form($app_form)
    {
        if (!$app_form->valid_from) {
            return false;
        }

        if (!$app_form->valid_until) {
            return false;
        }

        $now = time();
        $then = strtotime($app_form->valid_until);

        if ($now < $then) {
            return true;
        } else {
            return false;
        }


        return true;
    }

    //renew or create a new form after its expired
    public static function can_renew_form($model_name)
    {
        $user = Admin::user();
        $model = "App\\Models\\" . ucfirst($model_name);
        $recs = $model::where('administrator_id',  $user->id)->get();

        foreach ($recs as $key => $value) {

            if ($value->status == 5) {
                $now = time();
                $then = strtotime($value->valid_until);
                if ($now < $then) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
    }

    //check expiration date for all Forms
    public static function check_expiration_date($model_name, $id)
    {
        $model = "App\\Models\\" . ucfirst($model_name);
        $form = $model::find($id);
        if ($form->status == 5) {
            if ($form->valid_until != null && $form->valid_from != null) {
                $now = time();
                $then = strtotime($form->valid_until);

                if ($now < $then) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    //check is form accepted
    public static function is_form_accepted($model_name)
    {
         $user = Admin::user();
        $model = "App\\Models\\" . ucfirst($model_name);
        $recs = $model::where('administrator_id',  $user->id)->get();
        foreach ($recs as $key => $value) {
            if ($value->status == 5) {
                return true;
            }
        }
    }

    //renew or create a new  form after its expired
    public static function can_renew_app_form($app_form)
    {
        if ($app_form->status == 5) {
            $now = time();
            $then = strtotime($app_form->valid_until);
            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    //2.Form_Sr4_functions

    //3.Form_SR6_functions

    //4.Form_Qds_functions
    //check if the status of the form is pending, rejected,halted or accepted
    public static function can_create_qds()
    {

        $user = Admin::user();
        $recs = FormQds::where('administrator_id',  $user->id)->get();

        foreach ($recs as $key => $value) {

            if (!$value->valid_until) {
                return false;
            }

            $now = time();
            $then = strtotime($value->valid_until);
            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    //generic for permits
    //renew or create a new import form after its expired
    public static function can_renew_permit($permit)
    {
        if ($permit->status == 5) {

            $now = time();
            $then = strtotime($permit->valid_until);

            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }


    //5.Import_form_functions    
    //check if the status of the form is pending, rejected,halted or accepted,and of type import before 
    //sumbitting
    public static function can_create_import($import_permit)
    {

        if ($import_permit->is_import == 1) {
            if ($import_permit->status == 4) {
                return true;
            }
            if (!$import_permit->valid_from) {
                return false;
            }

            if (!$import_permit->valid_until) {
                return false;
            }

            $now = time();
            $then = strtotime($import_permit->valid_until);

            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }


    //6.Export_form_functions
    //check if the status of the form is pending, rejected,halted or accepted,and of type import before 
    //sumbitting
    public static function can_create_export($export_permit)
    {
        if ($export_permit->is_import == 0) {

            if ($export_permit->status == 4) {
                return true;
            }
            if (!$export_permit->valid_from) {
                return false;
            }

            if (!$export_permit->valid_until) {
                return false;
            }

            $now = time();
            $then = strtotime($export_permit->valid_until);

            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }


        return true;
    }

    //sr4type import
    public static function sr4Check($form, $selected_type)
    {
        $form_sr4 = FormSr4::where('administrator_id',  Admin::user()->id)->where('valid_until','>=', Carbon::now())->where('type',$selected_type)->first();

        if ( !$form_sr4)
        {
            
            $form->html(' <p class="alert alert-danger"> 
           You do not have a valid SR4 of the selected type. <a href="/admin/form-sr4s">Register Now</a>
            </p> ');
            return;
        }
        else
        {
            $user = Auth::user();
            $import = ImportExportPermit::where('type', $selected_type)->where('administrator_id', $user->id)->where('is_import', 1)->first();
            if ($import) 
            {
                
                
                    //check if the status of the form is pending, rejected,halted or accepted
                    if(!Utils::can_create_import($import))
                    {
                       
                        $form->html('<p class="alert alert-warning"> You cannot create a new import permit form  while having PENDING one of the same category. <a href="/admin/import-export-permits/create"> Go Back </a></p>');
                        return;
                     }

                  
                    
                    //check if its still valid
                    if (Utils::can_renew_permit($import)) 
                    {
                        
                        $form->html('<p class="alert alert-warning"> You cannot create a new import permit form  while having VALID one of the same category. <a href="/admin/import-export-permits/create"> Go Back </a></p>');  
                        return;
                    }
            }
        }
        
            
    }

    public static function exportSr4Check($form, $selected_type)
    {
          
        $form_sr4 = FormSr4::where('administrator_id',  Admin::user()->id)->where('valid_until','>=', Carbon::now())->where('type',$selected_type)->first();

        if (!$form_sr4)
        {
            
            $form->html('<p class="alert alert-danger">You do not have a valid SR4 of the selected type. <a href="/admin/import-export-permits-2"> Go Back </a></p> '); 
            return;    
        }
        else
        {
         
            $user = Auth::user();
            $export = ImportExportPermit::where('type', $selected_type)->where('administrator_id', $user->id)->where('is_import', 0)->first();
            if ($export) 
            {
                
                
                    //check if the status of the form is pending, rejected,halted or accepted
                    if(!Utils::can_create_export($export))
                    {
                        $form->html(' <p class="alert alert-warning"> You cannot create a new export permit form  while having PENDING one of the same category. <a href="/admin/import-export-permits-2/create"> Go Back </a></p> ');
                        return;

                    }
                    
                    //check if its still valid
                    if (Utils::can_renew_permit($export)) 
                    {
                        
                        $form->html(' <p class="alert alert-warning"> You cannot create a new export permit form  while having VALID one of the same category. <a href="/admin/import-export-permits-2/create"> Go Back </a></p> '); 
                        return;  
                    }
            
            }
         
                       
        }
             
    }
    



    public static function create_default_tables()
    {
        $crop = Crop::find(1);
        if ($crop == null) {
            $cro = new Crop();
            $cro->name = "Default crop";
            $cro->number_of_inspection = 0;
            $cro->crop_id = 1;
            $cro->number_of_days_before_submision = 0;
            $cro->save();
        }

        $var = CropVariety::find(1);
        if ($var == null) {
            $var = new CropVariety();
            $var->name = "Default crop variety";
            $var->id = 1;
            $var->crop_id = 1;
            $var->save();
        }
    }

    public static function get_stock_balance($user_id, $crop_id)
    {
        $records = FormStockExaminationRequest::where([
            'administrator_id' => $user_id,
            'import_export_permit_id' => $crop_id
        ])->get();
        $tot = 0;
        foreach ($records as $key => $value) {
            $tot += $value->quantity;
        }
        return $tot;
    }


    public static function sr10_inialized(SubGrower $sr10)
    {
        if (!$sr10) {
            return false;
        }
        $forms = FormSr10::where('planting_return_id', $sr10->id)->get();
        if (count($forms) < 1) {
            return false;
        } else {
            return true;
        }
    }


    public static function start_session()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function alert($msg, $type)
    {
        Utils::start_session();
        $_SESSION['message'] = $msg;
        $_SESSION['type'] = $type;
    }

    public static function register_alert($message, $type = 'info', $errors = null)
    {
        Utils::start_session();
        if ($errors instanceof \Illuminate\Support\MessageBag) {
            $errors = implode('<br>', $errors->all());
        }
        $_SESSION['message'] = $message;
        $_SESSION['type'] = $type;
        $_SESSION['errors'] = $errors;
    }



    public static function has_valid_sr6()
    {
        $user = Auth::user();
        $recs = FormSr6::where('administrator_id',  $user->id)->get();
        foreach ($recs as $key => $value) {
            if (!$value->valid_from) {
                return null;
            }
            if (!$value->valid_until) {
                return null;
            }

            $now = time();
            $then = strtotime($value->valid_until);
            if ($now < $then) {
                return $value;
            }
        }
        return null;
    }





    public static function has_role($item, $role)
    {
        $roles = $item->roles()->get();
        foreach ($roles as $r) {
            if ($r->slug == $role) {
                return true;
            }
        }
        return false;
    }



    public static function can_create_qds_declaration()
    {
        $recs = FormCropDeclaration::where('administrator_id',  $user->id)->get();
        foreach ($recs as $key => $value) {

            if ($value->status == 4) {
                continue;
            }

            if (!$value->valid_from) {
                return false;
            }
            if (!$value->valid_until) {
                return false;
            }

            $now = time();
            $then = strtotime($value->valid_until);
            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }





    public static function can_create_import_form()
    {
        $sr4 =  FormSr4::where([
            'administrator_id' => $user->id,
            'status' => 5
        ])->first();

        if ($sr4 == null) {
            return false;
        }

        return true;
    }


    public static function can_create_export_form()
    {
        $recs = ImportExportPermit::where('administrator_id', '=',  $user->id)
            ->where('is_import', '!=', 1)
            ->get();

        foreach ($recs as $key => $value) {

            if ($value->status == 4) {  // if rejected, you cant apply again
                continue;
            }

            if (!$value->valid_from) {
                return false;
            }

            if (!$value->valid_until) {
                return false;
            }

            $now = time();
            $then = strtotime($value->valid_until);
            if ($now < $then) {
                return true;
            } else {
                return false;
            }
        }

        $recs_sr4 = FormSr4::where('administrator_id',  $user->id)->get();

        if (count($recs_sr4) == 0) {    // if no sr4 belongs to current user
            return false;
        }

        foreach ($recs_sr4 as $key => $value_sr4) {
            if (!($value_sr4->status == 5)) {
                return false;
            }
        }

        return true;
    }








    public static function previous_import_form_not_accepted()
    {
        $recs = ImportExportPermit::where('administrator_id', '=',  $user->id)
            ->where('is_import', '=', 1)
            ->get();

        foreach ($recs as $key => $value) {
            if (!$value->status == 5) {   // if status is not 'Accepted'
                return false;
            }
        }

        return true;
    }



    public static function previous_export_form_not_accepted()
    {
        $recs = ImportExportPermit::where('administrator_id', '=',  $user->id)
            ->where('is_import', '!=', 1)
            ->get();

        foreach ($recs as $key => $value) {
            if (!$value->status == 5) {
                return false;
            }
        }

        return true;
    }













    //check if the recommendation has been updated
    public static function is_recommendation_updated($model_name)
    {
        $user = Auth::user();
        $model = "App\\Models\\" . ucfirst($model_name);
        $recs = $model::where('administrator_id',  $user->id)->get();
        foreach ($recs as $key => $value) {
            if ($value->report_recommendation == 11) {
                return true;
            }
        }
    }

    //check if the form is pending or approved or null
    public static function form_status($model_name)
    {
        $model = "App\\Models\\" . ucfirst($model_name);
        $recs = $model::where('administrator_id',  $user->id)->get();
        foreach ($recs as $key => $value) {
            if ($value->status == 3 || $value->status == 4) {
                return true;
            }
        }
    }

    //check if comments exist in the table for a particular model
    public static function check_comments($model_name)
    {
        $model = "App\\Models\\" . ucfirst($model_name);
        //get the id of the current  model 

        $recs = Comment::where('commentable_type', $model)
            ->get();
        foreach ($recs as $key => $value) {
            if ($value->comment != null) {
                return true;
            }
        }
    }


    //check if inspector remarks exist
    public static function check_inspector_remarks()
    {
        $user = Auth::user();
        $recs = FormSr6::where('administrator_id',  $user->id)->get();
        foreach ($recs as $key => $value) {
            if ($value->inspector_remarks != null) {
                return true;
            }
        }
    }

    //check if the administrator id is the same as the authenticated user for an order
    public static function check_order()
    {
        $user = Auth::user();
        $orders = Order::where('administrator_id',  $user->id)->get();
        foreach ($orders as $key => $value) {
            return true;
        }
    }

    //check if order status is 3
    public static function check_order_status()
    {
        $user = Auth::user();
        $orders = Order::where('order_by',  $user->id)->get();
        foreach ($orders as $key => $value) {
            if ($value->status == 3) {
                return true;
            }
        }
    }

    //get next inspection
    public static function getNextInspection($model)
    {
        //dd($model->planting_return_id, $model->crop_variety_id);
        $otherInspections = FormSr10::where('planting_return_id', $model->planting_return_id)
        ->where('crop_variety_id', $model->crop_variety_id)
        ->orderBy('order_number', 'asc')
        ->get();

        $nextInspection = null;
        foreach ($otherInspections as $key => $inspection) {
            if ($inspection->order_number > $model->order_number) {
                $nextInspection = $inspection;
                break;
            }
            
        }

        return $nextInspection;
    }

        //get next inspection
        public static function getNextQdsInspection($model)
        {
            //dd($model->planting_return_id, $model->crop_variety_id);
            $otherInspections = FormSr10::where('qds_declaration_id', $model->qds_declaration_id)
            ->where('crop_variety_id', $model->crop_variety_id)
            ->orderBy('order_number', 'asc')
            ->get();
    
            $nextInspection = null;
            foreach ($otherInspections as $key => $inspection) {
                if ($inspection->order_number > $model->order_number) {
                    $nextInspection = $inspection;
                    break;
                }
                
            }
    
            return $nextInspection;
        }
    





    public static function tell_order_status($status)
    {
        if (!$status)
            return '<span class="badge badge-warning">Pending</span>';
        if ($status == 1)
            return '<span class="badge badge-warning">Pending</span>';
        if ($status == 2)
            return '<span class="badge badge-warning">Shipping</span>';
        if ($status == 3)
            return '<span class="badge badge-success">Delivered</span>';
        if ($status == 4)
            return '<span class="badge badge-danger">Canceled</span>';
        if ($status == 5)
            return '<span class="badge badge-warning">Processing</span>';
        if ($status == 6)
            return '<span class="badge badge-success">Received</span>';
    }

    public static function tell_status($status)
    {
        if (!$status)
            return '<span class="badge badge-warning">Pending</span>';
        if ($status == 1)
            return '<span class="badge badge-warning">Pending</span>';
        if ($status == 2)
            return '<span class="badge badge-warning">Inspector assigned</span>';
        if ($status == 3)
            return '<span class="badge badge-warning">Halted</span>';
        if ($status == 4)
            return '<span class="badge badge-danger">Rejected</span>';
        if ($status == 5)
            return '<span class="badge badge-success">Accepted</span>';
        if ($status == 6)
            return '<span class="badge badge-danger">Expired</span>';
        if ($status == 7)
            return '<span class="badge badge-warning">Provisional</span>';
        if ($status == 8)
            return '<span class="badge badge-warning">Used</span>';
        if ($status == 9)
            return '<span class="badge badge-warning">Lab inspection</span>';
        if ($status == 10)
            return '<span class="badge badge-warning">Lab test pending</span>';
        if ($status == 11)
            return '<span class="badge badge-success">Marketable</span>';
        if ($status == 12)
            return '<span class="badge badge-danger">Not Marketable</span>';
        if ($status == 13)
            return '<span class="badge badge-warning">Pending for labeling</span>';
        if ($status == 14)
            return '<span class="badge badge-success">Labels Printed</span>';
        if ($status == 15)
            return '<span class="badge badge-danger">Declined</span>';
        if ($status == 16)
            return '<span class="badge badge-success">Initialized</span>';
        if ($status == 17)
            return '<span class="badge badge-warning">Skipped</span>';
        if ($status == 18)
            return '<span class="badge badge-warning">Under review</span>';
        if ($status == 20)
            return '<span class="badge badge-success">Accepted</span>';
        if ($status == 21)
            return '<span class="badge badge-success">Confirmed</span>';
        return $status;
    }

    public static function can_be_deleted_by_user($status)
    {
        if (!$status)
            return true;
        if ($status == 1)
            return true;
        if ($status == 2)
            return false;
        if ($status == 3)
            return true;
        if ($status == 4)
            return true;
        if ($status == 5)
            return false;
        if ($status == 6)
            return false;
        if ($status == 7)
            return false;
        if ($status == 8)
            return false;
        if ($status == 9)
            return false;
        if ($status == 10)
            return false;
        if ($status == 11)
            return false;
        if ($status == 12)
            return false;
        if ($status == 13)
            return false;
        if ($status == 14)
            return false;
        if ($status == 15)
            return true;
        if ($status == 16)
            return false;
        if ($status == 17)
            return false;
        return $status;
    }

    public static function show_response($status = 0, $code = 0, $body = "")
    {
        $d['status'] = $status;
        $d['code'] = $code;
        $d['body'] = $body;
        print_r(json_encode($d));
        die();
    }
    public static function get_chat_threads($user_id)
    {

        $threads = Chat::where(
            "sender",
            $user_id
        )
            ->orWhere('receiver', $user_id)
            ->orderByDesc('id')
            ->get();

        $done_ids = array();
        $ready_threads = array();
        foreach ($threads as $key => $value) {
            if (in_array($value->thread, $done_ids)) {
                continue;
            }
            $done_ids[] = $value->thread;
            $ready_threads[] = $value;
        }
        return $ready_threads;
    }


    public static function get_chat_thread($sender, $receiver, $product)
    {
        if ($sender == $receiver) {
            return null;
        }
        $thread = $sender . "-" . $receiver . "-" . $product;

        $results = DB::select(
            'select * from chats where 
                (sender = :sender AND
                receiver = :receiver AND
                product_id = :product)
                OR 
                (sender = :_receiver AND
                receiver = :_sender AND
                product_id = :_product) 
                ',
            [
                'sender' => $sender,
                'receiver' => $receiver,
                'product' => $product,
                '_sender' => $sender,
                '_receiver' => $receiver,
                '_product' => $product
            ]
        );
        if (
            $results != null &&
            !empty($results)
        ) {
            $thread = $results[0]->thread;
        }
        return $thread;
    }

    public static function get_file_url($name)
    {
        // $url = url("storage/uploads");
        if ($name == null || (strlen($name) < 2)) {
            $url = url('storage/files/default.png');
            // } else if (file_exists(public_path('storage/uploads' . $name))) {
            //     $url = url("storage/uploads/" . $name);
        } else {
            $url = url("storage/" . $name);
        }
        return $url;
    }

    //function to get and display the uploaded file
    public static function storeUploadedPhoto($photoData)
    {
        list($type, $photoData) = explode(';', $photoData);
        list(, $photoData) = explode(',', $photoData);
        $photoData = base64_decode($photoData);
    
        $photoPath = 'images/' . uniqid() . '.jpg'; 
        Storage::disk('admin')->put($photoPath, $photoData);
        
        return $photoPath;
    }


    public static function make_slug($str)
    {
        $slug =  strtolower(Str::slug($str, "-"));

        if (
            (!empty(Product::where("slug", $slug)->First())) ||
            (!empty(Profile::where("username", $slug)->First()))
        ) {
            $slug .= rand(100, 1000);
        }

        return $slug;
    }





    public static function upload_images_1($files, $is_single_file = false)
    {

        ini_set('memory_limit', '-1');
        if ($files == null || empty($files)) {
            return $is_single_file ? "" : [];
        }
        $uploaded_images = array();
        foreach ($files as $file) {

            if (
                isset($file['name']) &&
                isset($file['type']) &&
                isset($file['tmp_name']) &&
                isset($file['error']) &&
                isset($file['size'])
            ) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = time() . "-" . rand(100000, 1000000) . "." . $ext;
                $destination = 'public/uploads/' . $file_name;

                $res = move_uploaded_file($file['tmp_name'], $destination);
                if (!$res) {
                    continue;
                }
                //$uploaded_images[] = $destination;
                $uploaded_images[] = $file_name;
            }
        }

        $single_file = "";
        if (isset($uploaded_images[0])) {
            $single_file = $uploaded_images[0];
        }


        return $is_single_file ? $single_file : $uploaded_images;
    }




    public static function upload_images($files)
    {
        if ($files == null || empty($files)) {
            return [];
        }
        if (!isset($files['name'])) {
            return [];
        }
        if (!is_array($files['name'])) {
            return [];
        }

        $uploaded_images = array();
        if (isset($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if (
                    isset($files['name'][$i]) &&
                    isset($files['type'][$i]) &&
                    isset($files['tmp_name'][$i]) &&
                    isset($files['error'][$i]) &&
                    isset($files['size'][$i])
                ) {
                    $img['name'] = $files['name'][$i];
                    $img['type'] = $files['type'][$i];
                    $img['tmp_name'] = $files['tmp_name'][$i];
                    $img['error'] = $files['error'][$i];
                    $img['size'] = $files['size'][$i];


                    $path = Storage::putFile('/', $img['tmp_name']);



                    $path_not_optimized =  "storage/" . $path;
                    $path_optimized = "storage/thumb_" . $path;
                    $thumbnail = Utils::create_thumbail(
                        array(
                            "source" => $path_not_optimized,
                            "target" => $path_optimized,
                        )
                    );

                    if (strlen($thumbnail) > 3) {
                        $thumbnail = str_replace("storage/", "", $thumbnail);
                        $thumbnail = str_replace("/storage", "", $thumbnail);
                        $thumbnail = str_replace("storage", "", $thumbnail);
                        $thumbnail = str_replace("/", "", $thumbnail);
                    } else {
                        $thumbnail = $path;
                    }

                    $ready_image['src'] = $path;
                    $ready_image['thumbnail'] = $thumbnail;

                    $ready_image['user_id'] = Auth::id();
                    if (!$ready_image['user_id']) {
                        $ready_image['user_id'] = 1;
                    }

                    $_ready_image = new Image($ready_image);
                    $_ready_image->save();
                    $uploaded_images[] = $ready_image;
                }
            }
        }

        return $uploaded_images;
    }

    public static function create_thumbail($params = array())
    {
        if (
            !isset($params['source']) ||
            !isset($params['target'])
        ) {
            return [];
        }



        $image = new Zebra_Image();

        $image->auto_handle_exif_orientation = false;
        $image->source_path = $params['source'];
        $image->target_path = $params['target'];



        $image->jpeg_quality = 75;
        if (isset($params['quality'])) {
            $image->jpeg_quality = $params['quality'];
        }

        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;
        $image->handle_exif_orientation_tag = true;

        $width = 380;
        $heigt = 220;
        if (isset($params['width'])) {
            $width = $params['width'];
        }
        if (isset($params['heigt'])) {
            $width = $params['heigt'];
        }


        if (!$image->resize($width, $heigt, ZEBRA_IMAGE_CROP_CENTER)) {
            return $image->source_path;
        } else {
            return $image->target_path;
        }
    }

    //take action button
    public static function take_action($model, $id , $url ,$show)
    {
        if (!Admin::user()->isRole('basic-user'))
        {
            //button link to the show-details form
            //check the status of the form being shown
            if($model->status == 1 || $model->status == 2 || $model->status == null)
            {
            $show->field('id','Action')->unescape()->as(function ($id)  use ($url)
            {
                return "<a href='/admin/$url/$id/edit' class='btn btn-primary' id='action-button-$id'>Take Action</a>";
            });
            }

            
        }

        if (Admin::user()->isRole('admin'))
        {
            //button link to the show-details form
           
            $show->field('id','Action')->unescape()->as(function ($id) use ($url)
            {
                return "<a href='/admin/$url/$id/edit' class='btn btn-primary' id='action-button-$id'>Take Action</a>";
            });
            
        }

        if (Admin::user()->isRole('basic-user')) 
        {
            if($model->status == 3 || $model->status == 4)
            {
                $show->field('id','Action')->unescape()->as(function ($id)  use ($url)
                {
                    return "<a href='/admin/$url/$id/edit' class='btn btn-primary' id='action-button-$id'>Take Action</a>";
                });
            }
        }

          //script to change the button to a loading button when clicked
          $script = <<<SCRIPT
          $(document).ready(function(){
              $('a.btn-primary').click(function(){
                  var id = $(this).attr('id');
                  $(this).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                  $(this).attr('disabled', 'disabled');
                  $(this).css('cursor', 'not-allowed');
              });
          });
          SCRIPT;
          Admin::script($script);
    }
}
