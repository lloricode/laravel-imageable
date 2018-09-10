<?php

Route::group([
    'middleware' => 'auth',
], function () {
    // Route::get('/{imageable}', 'ImageableController@download')->name('download');
    Route::get('/{image}', 'ImageableController@show')->name('show');
    Route::delete('/{image}', 'ImageableController@delete')->name('delete');
});
