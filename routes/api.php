<?php

use App\Models\Crop;
use App\Models\SeedLab;
use App\Models\SeedLabel;
use App\Models\SeedLabelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormSr4ApiController;
use App\Http\Controllers\Api\FormSr6ApiController;
use App\Http\Controllers\Api\FormQDSApiController;
use App\Http\Controllers\Api\JWTAuthApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserAPIController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\PProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PreOrderApiController;
use App\Http\Controllers\Api\QuotationApiController;
use App\Http\Controllers\Api\FormStockExaminationRequestApiController;
use App\Http\Controllers\Api\MarketableSeedApiController;
use App\Http\Controllers\Api\ImportPermitApiController;
use App\Http\Controllers\Api\ExportPermitApiController;
use App\Http\Controllers\Api\PlantingReturnsCompanyApiController;
use App\Http\Controllers\Api\PlantingReturnsGrowerApiController;
use App\Http\Controllers\Api\FormSr10ApiController;
use App\Http\Controllers\Api\QDSCropDeclarationApiController;
use App\Http\Controllers\Api\SeedLabApiController;
use App\Http\Controllers\Api\SeedLabelApiController;
use App\Http\Controllers\Api\FormStockRecordApiController;
use Illuminate\Routing\Router;
use App\Http\Middleware\EnsureTokenIsValid;
use Encore\Admin\Facades\Admin;

Admin::routes();

Route::post("/register", [AuthApiController::class, "register"]);
Route::post("/login", [AuthApiController::class, "login"]);

Route::group(['middleware' => 'api'], function ($router) {
    Route::post("/logout", [AuthApiController::class, "logout"]);
    Route::get("/me", [AuthApiController::class, "me"]);
    
    Route::post('/refresh', [AuthApiController::class, 'refresh']);

    // password reset/ change
    Route::post('reset_password', [ResetPasswordController::class, 'sendEmail']);


    // users
    Route::get("/user/list", [UserAPIController::class, "index"]);
    Route::get("/user/{id}", [UserAPIController::class, "show"]);
    Route::put("/user/{id}", [UserAPIController::class, "update"]);
    Route::delete("/user/{id}", [UserAPIController::class, "destroy"]);
    Route::get("/user/search/{name}", [UserAPIController::class, "where"]);


    // Application forms
    Route::post("/forms/sr4/new", [FormSr4ApiController::class, "new_sr4_form"]);
    Route::post("/forms/sr6/new", [FormSr6ApiController::class, "new_sr6_form"]);
    // Route::post("/forms/qds/new", [FormQDSApiController::class, "form"]);

    // marketplace
    Route::get("/products/list", [ProductApiController::class, "products_list"]);
    Route::get("/orders/list", [OrderApiController::class, "order_list"]);
    Route::get("/pre-orders/list", [PreOrderApiController::class, "pre_order_list"]);
    Route::get("/quotations/list", [QuotationApiController::class, "quotations_list"]);

    // SeedStock
    Route::get("/form-stock-examination-requests/list", [FormStockExaminationRequestApiController::class, "form_stock_examination_requests_list"]);
    Route::get("/form-stock-records/list", [FormStockRecordApiController::class, "form_stock_records_list"]);
    Route::get("/marktable-seed/list", [MarketableSeedApiController::class, "marktable_seed_list"]);

    // Quality Assurance
    Route::get("/import-permit/list", [ImportPermitApiController::class, "import_permits_list"]);
    Route::get("/export-permit/list", [ExportPermitApiController::class, "export_permits_list"]);
    Route::get("/planting-returns-company/list", [PlantingReturnsCompanyApiController::class, "planting_returns_company_list"]);
    Route::get("/planting-returns-grower/list", [PlantingReturnsGrowerApiController::class, "planting_returns_grower_list"]);
    Route::get("/form-sr10/list", [FormSr10ApiController::class, "form_sr10_list"]);
    Route::get("/qds-crop-declarations/list", [QDSCropDeclarationApiController::class, "qds_crop_declarations_list"]);
    Route::get("/seed-labs/list", [SeedLabApiController::class, "seed_lab_list"]);
    Route::get("/seed-labels/list", [SeedLabelApiController::class, "seed_label_list"]);


    // $router->resource('products', ProductApiController::class);  
});

    
