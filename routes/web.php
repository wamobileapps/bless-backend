<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\StripeConnectController;
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

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::middleware([IsAdmin::class])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('users', [App\Http\Controllers\HomeController::class, 'index_user'])->name('users');
    Route::get('user', [App\Http\Controllers\HomeController::class, 'updateStatus'])->name('user.stauts');
    Route::get('/logout', [App\Http\Controllers\HomeController::class, 'logout'])->name('logout');
    Route::get('/activate-user/{user_id}', [App\Http\Controllers\HomeController::class, 'Activate'])->name('activate');

    // route for admin side 
    Route::get('/update/{user_id}', [App\Http\Controllers\HomeController::class, 'update'])->name('user.update');
    Route::get('/delete/{user_id}', [App\Http\Controllers\HomeController::class, 'delete'])->name('user.delete');
    Route::post('/user/update/{id}', [App\Http\Controllers\HomeController::class, 'updateUser'])->name('user.edit');
    Route::post('/user/status', [App\Http\Controllers\HomeController::class, 'status'])->name('user.status');
    Route::post('/user/adminadduser', [App\Http\Controllers\HomeController::class, 'adminadduser'])->name('user.adminadduser');


    // Route::get('/user/addvideo', [App\Http\Controllers\HomeController::class, 'addvideo'])->name('user.addvideo');

    Route::get('addvideo', [App\Http\Controllers\HomeController::class, 'addvideo'])->name('user.addvideo');

    Route::post('/user/adminaddvideo', [App\Http\Controllers\HomeController::class, 'adminaddvideo'])->name('user.adminaddvideo');


  



    Route::get('show_video_list', [App\Http\Controllers\HomeController::class, 'show_video_list'])->name('show_video_list');
    // show_video_list

    // delete video route

    Route::get('/show_video_list_delete/{user_id}', [App\Http\Controllers\HomeController::class, 'show_video_list_delete'])->name('user.show_video_list_delete');



    Route::get('/user/adminadduser', function () {
        return view('admin.addnewuser');
    })->name('user.viewadduser');

   

    Route::get('/inactivate-user/{user_id}', [App\Http\Controllers\HomeController::class, 'Inactivate'])->name('inactivate');
    Route::resource('quiz', App\Http\Controllers\admin\QuizController::class);
});


// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
