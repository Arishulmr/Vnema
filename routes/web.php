<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ChannelController;

Route::get('/dashboard', [ChannelController::class, 'index'])->name('dashboard');
Route::post('/channels/search', [ChannelController::class, 'search'])->name('channels.search');
Route::post('/channels/add', [ChannelController::class, 'add'])->name('channels.add');


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/clips', [VideoController::class, 'index'])->name('clips');