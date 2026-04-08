<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug/logs', function (Request $request) {
    $path = storage_path('logs/laravel.log');

    if (! file_exists($path)) {
        return response('No log file found.', 200)->header('Content-Type', 'text/plain');
    }

    $lines = (int) $request->query('lines', 100);
    $all = file($path, FILE_IGNORE_NEW_LINES);
    $tail = implode("\n", array_slice($all, -$lines));

    return response($tail, 200)->header('Content-Type', 'text/plain');
});
