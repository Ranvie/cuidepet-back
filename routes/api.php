<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('user')->group(function () {

    Route::get('/',        [UserController::class, 'list']);
    Route::post('/',       [UserController::class, 'create']);

    Route::prefix('{userId}')->group(function () {

        Route::get('/',    [UserController::class, 'get']);
        Route::put('/',    [UserController::class, 'update']);
        Route::delete('/', [UserController::class, 'delete']);

        Route::get('/announcement', [UserController::class, 'listAnnouncements']);
        Route::post('/announcement', [UserController::class, 'createAnnouncement']);
        Route::put('/announcement/{announcementId}', [UserController::class, 'updateAnnouncement']);
        Route::delete('/announcement/{announcementId}', [UserController::class, 'deleteAnnouncement']);

        Route::get('/notification', [UserController::class, 'listNotifications']);
        Route::delete('/notification/{notificationId}', [UserController::class, 'removeNotification']);
        Route::put('/notification/{notificationId}', [UserController::class, 'setViewedNotification']);

    });

});

// ANOTAÇÕES E DEFINIÇÕES DE ROTAS PROVISÓRIOS A PARTIR DAQUI

/**
 * Rotas Públicas
 */
Route::prefix('public')->group(function () {

    Route::post('/login',             [AuthController::class, 'login']);
    Route::post('/logout',            [AuthController::class, 'logout']);
    Route::post('/register',          [AuthController::class, 'register']);
    Route::post('/recovery-password', [AuthController::class, 'recoveryPassword']);
    Route::get('/use-therms',         [AuthController::class, 'useTherms']);
    Route::post('/accept-therms',     [AuthController::class, 'acceptTherms']);

    /**
     * Grupo de Rotas Públicas de anúncios
     */
    Route::prefix('announcement/{type}')->group(function () {

        Route::get('/',       [PublicAnnouncementController::class, 'list']);
        Route::get('/{id}',   [PublicAnnouncementController::class, 'get']);
        Route::get('/form/{id}',  [PublicAnnouncementController::class, 'getForm']);
        Route::post('/form/send', [PublicAnnouncementController::class, 'sendForm']);

    });

});
