<?php

use App\Http\Controllers\RuleController;
use App\Http\Controllers\RulesVariantsController;
use App\Http\Controllers\WebhookController;
use App\Models\RulesVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Find variant Route, Find whether variantID exists in DB
Route::get('/find-variant',[RuleController::class, 'findVariantInDB'])->name('find-variant');

// Retrieve RulesVariant Data Route, Get all the records of RulesVariants
Route::get('/all-rulesVariants/{type}', [RulesVariantsController::class, 'all'])->name('all-rulesVariants');
