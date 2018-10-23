<?php

Route::group([
    'middleware' => 'auth:api',
], function () {
    // Route::get('/{imageable}', 'ImageableController@download')->name('download');
});
