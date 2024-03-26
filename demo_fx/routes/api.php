<?php

use App\Http\Controllers\API\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::controller(UserController::class)->group(function(){
    Route::post('login', 'loginUser');
    Route::post('registration', 'Register');
});

Route::controller(UserController::class)->group(function(){
    Route::get('user', 'getUserDetail'); // logged user
    Route::get('allUser', 'index'); // all user list
    Route::get('user/{id}', 'show'); // user by id
    Route::get('logout','userLogout');
})->middleware('auth:api');

Route::controller(PostController::class)->group(function(){
    Route::get('post', 'index'); // all post list
    Route::post('create-post', 'create'); // create post
    Route::get('post/{post}', 'show'); // post by id
    Route::get('post-by-user/{userId}', 'getPostByUserId'); // get post list by userId
    Route::put('update-post/{post}','update');
    Route::delete('delete-post/{post}', 'destroy');
})->middleware('auth:api');
