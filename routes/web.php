<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@get_index');
Route::get('/index', 'HomeController@get_index');
Route::get('/home', 'HomeController@get_index');


//admin
Route::group(['prefix'=>'/admin', 'middleware'=>'Admin'], function () {
    Route::group(['prefix'=>'/roles'], function () {
        Route::get('/', 'RoleController@get_roles');
        Route::post('/', 'RoleController@post_roles');
    });

    Route::group(['prefix'=>'/non-billable-codes'], function () {
        Route::get('/', 'NonBillableCodeController@get_non_billable_codes');
        Route::post('/', 'NonBillableCodeController@post_non_billable_codes');
    });

    Route::group(['prefix'=>'/users'], function () {
        Route::get('/', 'UserController@get_users');
        Route::post('/', 'UserController@post_users');
    });

    Route::group(['prefix'=>'/categories'], function () {
        Route::get('/', 'CategoryController@get_categories');
        Route::post('/', 'CategoryController@post_categories');
    });

    Route::group(['prefix'=>'/fields'], function () {
        Route::get('/', 'FieldController@get_fields');
        Route::post('/', 'FieldController@post_fields');
    });
});

//chief
Route::group(['prefix'=>'/chief', 'middleware'=>'Chief'], function () {
    Route::group(['prefix'=>'/clients'], function () {
        Route::get('/', 'ClientController@get_clients');
        Route::post('/', 'ClientController@post_clients');
    });

    Route::group(['prefix'=>'/projects'], function () {
        Route::get('/', 'ProjectController@get_projects');
        Route::post('/', 'ProjectController@post_projects');
    });

    Route::group(['prefix'=>'/tasks'], function () {
        Route::get('/', 'TaskController@get_tasks');
        Route::post('/', 'TaskController@post_tasks');
    });

    Route::group(['prefix'=>'/tracer'], function () {
        Route::get('/', 'TimeTracerController@get_tracer_for_chief');
        Route::post('/', 'TimeTracerController@post_tracer_for_chief');
    });
});

//user
Route::group(['prefix'=>'/user', 'middleware'=>'User'], function () {
    Route::group(['prefix'=>'/tracer'], function () {
        Route::get('/', 'TimeTracerController@get_time_tracer');
        Route::post('/', 'TimeTracerController@post_time_tracer');
    });
});

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout');