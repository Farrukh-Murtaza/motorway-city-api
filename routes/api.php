<?php

use App\Http\Controllers\AppDataController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\NomineeRelationController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PlotController;
use App\Http\Controllers\PlotSaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/profile', [AuthController::class, 'profile']);

});


Route::middleware('auth:api')->group(function () {

    Route::get('/app-data', [AppDataController::class, 'getAppData']);

    Route::apiResource('persons', PersonController::class)->only(['index', 'show', 'update', 'store', 'destroy']);
    Route::apiResource('occupations', OccupationController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('nominee-relations', NomineeRelationController::class)->only(['index', 'store', 'destroy']);
   
    Route::apiResource('plots', PlotController::class)->only(['index']);
    Route::patch('plots/{plot}/status', [PlotController::class, 'updateStatus']);

    Route::group([ 'prefix' => 'plot-sales'],function () {
        Route::get('/' , [PlotSaleController::class , 'index']);
        Route::Post('/' , [PlotSaleController::class , 'store']);
        Route::get('/{id}' , [PlotSaleController::class , 'show']);

        // Installment Routes
        Route::get('/{id}/installments', [InstallmentController::class, 'getPlotSaleInstallments']);
        Route::get('/{id}/installments/pending', [InstallmentController::class, 'getPendingInstallments']);
        Route::get('/{id}/installments/statistics', [InstallmentController::class, 'getStatistics']);
        
    });
   
    Route::get('installments/overdue', [InstallmentController::class, 'getOverdueInstallments']);
    Route::get('installments/upcoming', [InstallmentController::class, 'getUpcomingInstallments']);
    
    
    Route::post('payments', [PaymentController::class , 'recordFlexiblePayment']);
    Route::get('payments/plot-sale/{plotSaleId}', [PaymentController::class, 'getPaymentHistory']);
    Route::get('/payments/grouped-by-date', [PaymentController::class, 'getAllPaymentsGroupedByDate']);
    
    Route::post('installments/mark-overdue', [InstallmentController::class, 'markOverdueInstallments']);
    // Route::post('installments/{installmentId}/calculate-late-fee', [InstallmentController::class, 'calculateLateFee']);
});


Route::get('files/{path}', [FileController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth');









// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
