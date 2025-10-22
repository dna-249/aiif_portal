<?php

use Illuminate\Support\Facades\Route;

Route::get('/{?site}', function ($site) {
    if(!$site){
        return view("login");
    }else{
    return view($site);
    }
});



Route::get('/', function () {
    return view('login');
});
