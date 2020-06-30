<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// v1

use App\Http\Controllers\API\v1\Main\TransactionController;

    Route::group(['middleware' => ['api']], function () {
                //TRANSACTIONS

        Route::post('/transactions/payment-bill-items', [TransactionController::class, 'create']);
        Route::get('/transactions', [TransactionController::class, 'getAll']);

            });


