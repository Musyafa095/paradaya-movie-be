<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('v1')->group(function(){
  Route::apiResource('/genre', CategoryController::class);
  Route::apiResource('/movie', NewsController::class); 
// middleware roleAdmin
  Route::middleware('auth:api', 'admin')->group(function(){
    Route::resource('role', RoleController::class);
  });
  //Auth
  Route::prefix('auth')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'currentuser'])->middleware('auth:api');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/account_verification', [AuthController::class, 'verifikasi'])->middleware('auth:api');
    Route::post('/generate_otp_code', [AuthController::class, 'generateOtp'])->middleware('auth:api');
  })->middleware('api');
  //middleware update profile
  Route::post('/profile', [ProfileController::class, 'updateProfile'])->middleware(['auth:api', 'verifiedAccount']);
  Route::post('/review', [CommentController::class, 'updateReview'])->middleware(['auth:api', 'verifiedAccount']);
  });

