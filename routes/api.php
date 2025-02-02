<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreedSpecieController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MyFormController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicAnnouncementController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/login',             [AuthController::class, 'login']);
Route::post('/register',          [AuthController::class, 'register']);
Route::post('/recovery-password', [AuthController::class, 'recoveryPassword']);
Route::get('/use-terms',          [AuthController::class, 'useTerms']);
Route::post('/accept-terms',      [AuthController::class, 'acceptTerms']);

Route::get('announcement/{id}',   [PublicAnnouncementController::class, 'get']);
Route::get('announcements/{type}', [PublicAnnouncementController::class, 'list']);

Route::middleware(['auth:sanctum', 'hasRole:reset-password'])->group(function () {})
    ->post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', 'hasRole:admin'])->prefix('admin/user')->group(function () {

    Route::get('/',               [UserController::class, 'list']);
    Route::get('/{id}',           [UserController::class, 'get']);
    Route::post('/',              [UserController::class, 'create']);
    Route::put('/{id}',           [UserController::class, 'update']);
    Route::delete('/{id}',        [UserController::class, 'delete']);

    Route::get('/report/',        [ReportController::class, 'list']);
    Route::get('/report/{id}',    [ReportController::class, 'get']);
    Route::delete('/report/{id}', [ReportController::class, 'delete']);

});

Route::middleware(['auth:sanctum', 'notHasRole:reset-password', 'checkUser'])->prefix('user/{userId}')->group(function () {

    Route::post('/inactivate', [UserController::class, 'inactivate']);
    Route::post('/logout',     [AuthController::class, 'logout']);

    //TODO: Tem que pensar na questão dos DTOs.. Eles retornam dados sensíveis, como senhas...
    Route::prefix('my-announcements')->group(function () {
        Route::get('/',                         [AnnouncementController::class, 'list']);
        Route::get('/{announcementId}',         [AnnouncementController::class, 'get']);
        Route::get('/answers/{announcementId}', [AnnouncementController::class, 'listAnswers']);
        Route::post('/',                        [AnnouncementController::class, 'create']);
        Route::put('/{announcementId}',         [AnnouncementController::class, 'update']);
        Route::delete('/{announcementId}',      [AnnouncementController::class, 'delete']);
    });

    Route::get('/species', [BreedSpecieController::class, 'list']);

    Route::get('/notification',                     [NotificationController::class, 'list']);
    Route::delete('/notification/{notificationId}', [NotificationController::class, 'delete']);
    Route::patch('/notification/{notificationId}',  [NotificationController::class, 'setViewed']);

    Route::get('/form',             [FormController::class, 'list']);
    Route::get('/form/{formId}',    [FormController::class, 'get']);
    Route::post('/form',            [FormController::class, 'create']);
    Route::delete('/form/{formId}', [FormController::class, 'delete']);
    Route::put('/form/{formId}',    [FormController::class, 'update']);

    Route::prefix('/myForms')->group(function () {

        Route::get('/',            [MyFormController::class, 'list']);
        Route::get('/{formId}',    [MyFormController::class, 'get']);
        Route::post('/',           [MyFormController::class, 'create']);
        Route::delete('/{formId}', [MyFormController::class, 'delete']);
        Route::put('/{formId}',    [MyFormController::class, 'update']);

    });

    Route::get('/favorite',    [FavoriteController::class, 'list']);
    Route::post('/favorite',   [FavoriteController::class, 'create']);
    Route::delete('/favorite', [FavoriteController::class, 'delete']);

    Route::post('/report/announcement/{announcementId}', [ReportController::class, 'create']);

});
