<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => 'auth:sanctum'], function() {

    Route::group(['prefix' => 'auth'], function() {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::put('users/{id}', [UserController::class, 'update']);

        Route::get('user', [UserController::class, 'userData']);
        Route::put('user/name', [UserController::class, 'updateName']);
        Route::put('user/mail', [UserController::class, 'updateMail']);
        Route::put('user/password', [UserController::class, 'updatePassword']);

        Route::get('logout', [UserController::class, 'logout']);
    });

    Route::apiResource('workout', WorkoutController::class);
    Route::apiResource('exercise', ExerciseController::class);
    Route::apiResource('history', UserExerciseHistoryController::class);

    Route::apiResource('workout.exercise', WorkoutExerciseController::class);
});

Route::group(['prefix' => 'auth'], function() {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::get('recover/{hash}', [UserController::class, 'recoverToken']);
    Route::post('recover', [UserController::class, 'recover']);
    Route::post('reset', [UserController::class, 'resetPassword']);
});

Route::get('/setup', function() {
    $credentials = [
        'user_role_id' => 2,
        'login' => 'admin',
        'name' => 'Administrator',
        'email' => 'admin@test.pl',
        'password' => 'Piciu103'
    ];

    if (!Auth::attempt($credentials)) {
        $user = new User();

        $user->user_role_id = $credentials['user_role_id'];
        $user->login = $credentials['login'];
        $user->name = $credentials['name'];
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);
        $user->save();

        $cred = [
            'email' => 'admin@test.pl',
            'password' => 'Piciu103'
        ];

        if (Auth::attempt($cred)) {
            $user = Auth::user();

            $adminToken = $user->createToken('admin-token', ['all']);
            $verifiedToken = $user->createToken('verified-token', ['advanced']);
            $basicToken = $user->createToken('basic-token', ['basic']);

            return [
                'admin' => $adminToken->plainTextToken,
                'verified' => $verifiedToken->plainTextToken,
                'basic' => $basicToken->plainTextToken,
            ];
        }
    }
});

Route::fallback(function(){
    return response()->json([
        'message' => "Ups, it seems like you tried to access a route that doesn't exist!"], 404);
});