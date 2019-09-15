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

Route::group(['prefix'=>'/', 'middleware'=>'Login'], function () {
    Route::get('/', 'HomeController@get_index');
    Route::get('/index', 'HomeController@get_index');
    Route::get('/home', 'HomeController@get_index');


//admin
    Route::group(['prefix'=>'/admin', 'middleware'=>'Admin'], function () {
        Route::group(['prefix'=>'/roles'], function () {
            Route::get('/', 'RoleController@get_roles');
            Route::post('/', 'RoleController@post_roles');
        });

        Route::group(['prefix'=>'/levels'], function () {
            Route::get('/', 'LevelController@get_user_levels');
            Route::post('/', 'LevelController@post_user_levels');
        });

        Route::group(['prefix'=>'/currencies'], function () {
            Route::get('/', 'CurrencyController@get_currencies');
            Route::post('/', 'CurrencyController@post_currencies');
        });

        Route::group(['prefix'=>'/non-billable-codes'], function () {
            Route::get('/', 'NonBillableCodeController@get_non_billable_codes');
            Route::post('/', 'NonBillableCodeController@post_non_billable_codes');
        });

        Route::group(['prefix'=>'/form-of-business'], function () {
            Route::get('/', 'FormOfBusinessController@get_form_of_businesses');
            Route::post('/', 'FormOfBusinessController@post_form_of_businesses');
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

//chief (manager)
    Route::group(['prefix'=>'/chief', 'middleware'=>'Chief'], function () {
        Route::group(['prefix'=>'/time-tracer'], function () {
            Route::get('/', 'TimeTracerController@get_time_tracer_for_manager');
            Route::post('/', 'TimeTracerController@post_time_tracer');
        });

        Route::group(['prefix'=>'/client-roles'], function () {
            Route::get('/', 'ClientRoleController@get_client_roles');
            Route::post('/', 'ClientRoleController@post_client_roles');
        });

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

        Route::group(['prefix'=>'/categories'], function () {
            Route::get('/', 'CategoryController@get_categories');
            Route::post('/', 'CategoryController@post_categories');
        });

        Route::group(['prefix'=>'/users'], function () {
            Route::get('/', 'UserController@get_users_for_chief');
            Route::post('/', 'UserController@post_users');
        });

        Route::group(['prefix'=>'/levels'], function () {
            Route::get('/', 'LevelController@get_user_levels');
            Route::post('/', 'LevelController@post_user_levels');
        });

        Route::group(['prefix'=>'/currencies'], function () {
            Route::get('/', 'CurrencyController@get_currencies');
            Route::post('/', 'CurrencyController@post_currencies');
        });

        Route::group(['prefix'=>'/projects-library'], function () {
            Route::get('/', 'ProjectsLibraryController@get_projects');
            Route::post('/', 'ProjectsLibraryController@post_projects');
        });

        Route::group(['prefix'=>'/report'], function () {
            Route::get('/', 'ReportController@get_report');
            Route::post('/change/client', 'ReportController@change_client')->name("report_change_client");
            Route::post('/change/project', 'ReportController@change_project')->name("report_change_project");
            Route::post('/show', 'ReportController@show_report')->name("show_report");
        });
    });

//project manager
    Route::group(['prefix'=>'/project-manager', 'middleware'=>'ProjectManager'], function () {
        Route::group(['prefix'=>'/projects'], function () {
            Route::get('/', 'ProjectController@get_projects_for_project_manager');
            Route::post('/', 'ProjectController@post_projects');
        });

        Route::group(['prefix'=>'/tasks'], function () {
            Route::get('/', 'TaskController@get_tasks_for_project_manager');
            Route::post('/', 'TaskController@post_tasks');
        });

        Route::group(['prefix'=>'/tracer'], function () {
            Route::get('/', 'TimeTracerController@get_tracer_for_project_manager');
            Route::post('/', 'TimeTracerController@post_tracer_for_chief');
        });

        Route::group(['prefix'=>'/time-tracer'], function () {
            Route::get('/', 'TimeTracerController@get_time_tracer_for_project_manager');
            Route::post('/', 'TimeTracerController@post_time_tracer');
        });
    });

//user
    Route::group(['prefix'=>'/user', 'middleware'=>'User'], function () {
        Route::group(['prefix'=>'/tracer'], function () {
            Route::get('/', 'TimeTracerController@get_time_tracer');
            Route::post('/', 'TimeTracerController@post_time_tracer');
        });
    });
});

Auth::routes(['register'=>false, 'reset'=>false]);

Route::get('/logout', 'Auth\LoginController@logout');