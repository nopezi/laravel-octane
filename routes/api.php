<?php

use App\Http\Controllers\api\TelegramBot;
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

Route::post("/coba", [TelegramBot::class, 'coba']);

Route::get("/telegram/get_update", [TelegramBot::class, 'getUpdate']);
Route::get("/telegram/get_update_database", [TelegramBot::class, 'getUpdateDatabase']);
Route::get("/telegram/set_webhook", [TelegramBot::class, 'setWebhook']);
Route::post("/telegram/hook", [TelegramBot::class, 'hook']);
Route::get("/telegram/delete_webhook", [TelegramBot::class, 'deleteWebhook']);

Route::get("/telegram/send_message", [TelegramBot::class, 'sendMessage']);
Route::get("/telegram/delete_message", [TelegramBot::class, 'deleteMessage']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
