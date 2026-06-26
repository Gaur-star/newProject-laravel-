<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;




Route::get('/', function () {
    return redirect('login');
});

// Route::get('/hash', function(){
//     return bcrypt('123456');
// });

// $2y$12$p9ixmkjUSN9EKklcxFy0T.tWegOgMcw.2HuUDnDBNy9aOnIR426t6


Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/add-user',[AuthController::class, 'showAddUser']);
Route::post('/add-user',[AuthController::class, 'storeUser']);

Route::get('/all-posts', [AuthController::class, 'allPosts']);

Route::get('/news-dashboard', [AuthController::class, 'newsDashboard']);
Route::get('/news-dashboard', [AuthController::class, 'newsDashboard'])->name('news.dashboard');

Route::get('/newsList', [AuthController::class, 'newsList']);
Route::get('/sync-news', [AuthController::class, 'syncNews'])->name('sync.news');

Route::get('/sync-site/{siteName}', [AuthController::class, 'syncOneSite'])->name('sync.site');
Route::get('/users', [AuthController::class, 'userList'])->name('users.list');

Route::get('/post/{post}/edit', [AuthController::class, 'editPost'])->name('post.edit');
Route::post('/post/{post}/update', [AuthController::class, 'updatePost'])->name('post.update');

Route::get('/clear-cache', function(){
    // Cache::flush();
    // return "Cache Cleared";
});



