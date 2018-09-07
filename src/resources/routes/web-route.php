<?php

Route::group([
    'middleware' => 'auth',
], function () {
    // Route::get('/{imageable}', 'ImageableController@download')->name('download');
    Route::get('/{imageFile}', 'ImageableController@show')->name('show');
});
