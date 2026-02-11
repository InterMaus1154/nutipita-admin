<?php
use App\Http\Controllers\TestController;
/*
 * Test routes for testing different stuff
 */


Route::group(['prefix' => '__test__', 'controller' => TestController::class], function(){

    // order list component -> playing around with styles and etc
    Route::get('livewire-order-list', 'testLivewireOrderList');


});
