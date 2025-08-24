<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversalLoginController;

Route::get('/', function () {
    return redirect('/admin');
});