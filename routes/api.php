<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ConfirmationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/signup',[SignupController::class,'signingUp']);
Route::get('/confirmation/{email}/{token}',[ConfirmationController::class,'confirming']);
Route::post('/login',[LoginController::class,'loggingIn']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();});

Route::group(['middleware' => "tokenAuth"], function()
{
    Route::post('/new_name',[LoginController::class,'updateName']);
    Route::post('/forget_password',[LoginController::class,'forgetPassword']);
    Route::post('/update_gender',[LoginController::class,'updateGender']);
    Route::post('/post',[PostController::class,'posting']);
    Route::post('/delete_post',[PostController::class,'deletePost']);
    Route::post('/update_post_file',[PostController::class,'updateFile']);
    Route::post('/update_post_access',[PostController::class,'updateAccess']);
    Route::post('/comment',[CommentController::class,'commenting']);
    Route::post('/delete_comment',[CommentController::class,'deleteComment']);
    Route::post('/update_comment_file',[CommentController::class,'updateFile']);
    Route::post('/update_comment_comment',[CommentController::class,'updateComment']);
    Route::post('/friend',[FriendController::class,'addFriend']);
    Route::post('/user_deactivate',[SignupController::class,'deactivate']);
    Route::post('/logout',[LogoutController::class,'loggingOut']);
});
