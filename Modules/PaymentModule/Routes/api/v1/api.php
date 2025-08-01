<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PaymentModule\Http\Controllers\Api\V1\Customer\BonusController;
use Modules\PaymentModule\Http\Controllers\Api\V1\Customer\OfflinePaymentController;
use Modules\PaymentModule\Http\Controllers\MoyasarPaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'admin', 'as'=>'admin.', 'namespace' => 'Api\V1\Admin','middleware'=>['auth:api']], function () {
    Route::group(['prefix'=>'payment-config'],function (){
        Route::get('get', 'PaymentConfigController@payment_config_get');
        Route::put('set', 'PaymentConfigController@payment_config_set');
    });
});

Route::get('customer/offline-payment/methods', [OfflinePaymentController::class, 'getMethods']);

Route::group(['prefix' => 'customer', 'as'=>'customer.', 'middleware'=>['auth:api']], function () {
    Route::get('bonus-list', [BonusController::class, 'getBonuses']);
});

Route::post('/payment/process', [MoyasarPaymentController::class, 'paymentProcess'])->middleware('auth:api');
Route::match(['GET','POST'],'/payment/callback', [MoyasarPaymentController::class, 'callBack']);

