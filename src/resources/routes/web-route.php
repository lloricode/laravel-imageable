<?php

Route::group([
    'middleware' => 'auth',
], function () {
    Route::get('/{image}', 'ImageableController@show')->name('show');
    Route::delete('/{image}', 'ImageableController@delete')->name('delete');
});
