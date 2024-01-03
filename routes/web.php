<?php

use App\Http\Controllers\RuleController;
use Illuminate\Support\Facades\Route;

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
Route::middleware(['verify.shopify'])->group(function () {
    // Home Route, Listing of Rules
    Route::get('/',[RuleController::class, 'index'])->name('home');

    // Create Route, Create a Rule
    Route::get('/create-rule',[RuleController::class, 'create'])->name('create-rule');

    // Store Route, Store Rule and RuleVariants
    Route::post('/store-rule',[RuleController::class, 'store'])->name('store-rule');

    // Edit Route, Edit Rule on Variants
    Route::get('/edit-rule/{id}',[RuleController::class, 'edit'])->name('edit-rule');

    // Update Route, Update Rule on Variants
    Route::post('/update-rule/{id}',[RuleController::class, 'update'])->name('update-rule');

    // Delete Route, Delete Rule
    Route::post('/delete-rule/{id}',[RuleController::class, 'destroy'])->name('delete-rule');

    // Search Route, Search the Rules
    Route::get('/search', [RuleController::class, 'search'])->name('search');

});

