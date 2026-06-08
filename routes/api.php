<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TargetController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\AdminController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/categories',       [CategoryController::class, 'index']);
Route::get('/categories/{id}',  [CategoryController::class, 'show']);

//(user biasa) 
Route::middleware('auth:api')->group(function () {
    Route::get('/user',    function () { return auth()->user(); });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('transactions', TransactionController::class)->except(['update']);
    Route::apiResource('targets', TargetController::class)->except(['update']);

    Route::get('/targets/{target_id}/progress',[ProgressController::class, 'index']);
    Route::post('/targets/{target_id}/progress',[ProgressController::class, 'store']);

    Route::get('/export/tasks',[ExportController::class, 'exportTasks']);
    Route::get('/export/transactions',[ExportController::class, 'exportTransactions']);

    // Khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::put('/users/{id}/promote',[AuthController::class, 'promoteToAdmin']);

        Route::post('/categories',[CategoryController::class, 'store']);
        Route::put('/categories/{id}',[CategoryController::class, 'update']);
        Route::delete('/categories/{id}',[CategoryController::class, 'destroy']);

        Route::get('/export/users',[ExportController::class, 'exportUsers']);

        Route::get('/admin/statistics',[AdminController::class, 'statistics']);
        Route::get('/admin/users',[AdminController::class, 'users']);
        Route::get('/admin/chart',[AdminController::class, 'chart']);
        Route::get('/admin/tasks',[AdminController::class, 'tasks']);
        Route::get('/admin/transactions',[AdminController::class, 'transactions']);
        Route::get('/admin/targets',[AdminController::class, 'targets']);
        Route::get('/admin/categories',[AdminController::class, 'categories']);
        Route::post('/admin/categories',[CategoryController::class, 'store']);
        Route::put('/admin/categories/{id}',[CategoryController::class, 'update']);
        Route::delete('/admin/categories/{id}',[CategoryController::class, 'destroy']);
    });
});
