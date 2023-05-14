<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/roles', function (){
    return view('roles', ['roles'=>Role::all()
    ]);
});
#roles
Route::get('roles/{id}',[RoleController::class, 'edit']);
Route::put('roles/{id}',[RoleController::class, 'update']);
Route::delete('roles/{id}', [RoleController::class, 'destroy']);
Route::post('roles', [\App\Http\Controllers\RoleController::class, 'store']);
#users
Route::get('users', [UserController::class, 'index']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::put('user/role/{id}', [UserController::class, 'updateRole']);
Route::delete('users/{id}', [UserController::class, 'destroy']);


Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout']);
