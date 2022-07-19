<?php

Route::fallback(function () {
    return response()->json(['message' => 'Report tidak ditemukan!'], 404);
});

Route::get('/pb1.json', 'ReportJson\PB1JsonController@index');
Route::get('/locations', 'PublicAPI\PublicLocationController@index');
