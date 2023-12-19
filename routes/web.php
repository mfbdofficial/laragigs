<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;

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

/*
Route::get('/', function () {
    return view('welcome');
});
*/

//Listings Table
//Show Home Page, read and show all datas
Route::get('/', [ListingController::class, 'index']); //1
//Show Create Listing Page, create form
Route::get('/listings/create', [ListingController::class, 'create'])->middleware('auth'); //9
//Create Listing, do action to INSERT a listing in listings database
Route::post('/listings', [ListingController::class, 'store'])->middleware('auth'); //10
//Show Manage Listing Page
Route::get('/listings/manage', [ListingController::class, 'manage'])->middleware('auth'); //8
//Show Detail Page, read and show one data
Route::get('/listings/{listing}', [ListingController::class, 'detail']); //7
//Show Edit Listing Page, update form
Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->middleware('auth'); //11
//Update Listing, do action to UPDATE a listing in listings database
Route::put('/listings/{listing}', [ListingController::class, 'update'])->middleware('auth'); //12
//Delete Listing, do action to DELETE a listing in listings database
Route::delete('/listings/{listing}', [ListingController::class, 'delete'])->middleware('auth'); //13

//Users Table
//Show Register Page, show register form
Route::get('/register', [UserController::class, 'create'])->middleware('guest'); //2
//Create User, do action to do INSERT into users database
Route::post('/register', [UserController::class, 'store']); //3
//Logout, do action to logout from current account
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth'); //4
//Show Login Page, show login form
Route::get('/login', [UserController::class, 'login'])->name('login')->middleware('guest'); //5
//Login, do action to login (authenticate action) to an account
Route::post('/login', [UserController::class, 'authenticate']); //6