<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\EventLivestreamController;
use App\Http\Controllers\LivestreamController;
use App\Http\Controllers\VtuberController;



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/channels/add', [ChannelController::class, 'add'])->name('channels.add');
Route::post('/channels/search', [ChannelController::class, 'search'])->name('channels.search');

Route::get('/dashboard', [ChannelController::class, 'index'])->name('dashboard');
Route::prefix('vn-admin')->group(function () {

Route::get('/dashboard/assign-livestreams', [EventLivestreamController::class, 'index'])->name('dashboard.assign_livestreams');
Route::post('/dashboard/assign-livestreams/store', [EventLivestreamController::class, 'store'])->name('dashboard.assign_livestreams.store');
Route::resource('vtubers', VtuberController::class);
Route::resource('events', VtuberController::class);
Route::resource('livestreams', LivestreamController::class);


Route::delete('/channels/{id}', [ChannelController::class, 'destroy'])->name('channels.destroy');
Route::get('/clips', [LivestreamController::class, 'index'])->name('clips');
}
);

Route::get('/', [HomeController::class, 'index'])->name('home');