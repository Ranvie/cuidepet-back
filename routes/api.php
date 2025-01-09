<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MyFormController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/login',             [AuthController::class, 'login']);
Route::post('/logout',            [AuthController::class, 'logout']);
Route::post('/register',          [AuthController::class, 'register']);
Route::post('/recovery-password', [AuthController::class, 'recoveryPassword']);
Route::get('/use-therms',         [AuthController::class, 'useTherms']);
Route::post('/accept-therms',     [AuthController::class, 'acceptTherms']);

//TODO: Nome temporário, mas vão ser as rotas que SOMENTE admins podem acessar: criação de usuário, reports, etc;
//Não sei se vale a pena, mas uma opção seria criar um safeDTO de cada model que tenha registros sensíveis;
Route::prefix('admin/user/{userId}')->group(function () {

    Route::post('/',   [UserController::class, 'create']);
    Route::put('/',    [UserController::class, 'update']);
    Route::delete('/', [UserController::class, 'delete']);

    Route::get('/report/',        [ReportController::class, 'list']);
    Route::get('/report/{id}',    [ReportController::class, 'get']);
    Route::delete('/report/{id}', [ReportController::class, 'delete']);

});

Route::prefix('announcement/{type}')->group(function () {

    Route::get('/',          [AnnouncementController::class, 'list']);
    Route::get('/{id}',      [AnnouncementController::class, 'get']);

});

Route::prefix('user')->group(function () {

    //TODO: Tem que pensar na questão dos DTOs.. Eles retornam dados sensíveis, como senhas...
    Route::get('/',        [UserController::class, 'get']);
    Route::get('/',        [UserController::class, 'list']);
    Route::get('/page',    [UserController::class, 'paginate']);

    Route::prefix('{userId}')->group(function () {

        Route::get('/my-announcements',                           [AnnouncementController::class, 'list']);
        Route::get('/my-announcement-responses/{announcementId}', [AnnouncementController::class, 'listAnswers']);
        Route::get('/announcement/{announcementId}',              [AnnouncementController::class, 'get']);
        Route::post('/announcement',                              [AnnouncementController::class, 'create']);
        Route::put('/announcement/{announcementId}',              [AnnouncementController::class, 'update']);
        Route::delete('/announcement/{announcementId}',           [AnnouncementController::class, 'delete']);

        Route::get('/notification',                     [NotificationController::class, 'list']);
        Route::delete('/notification/{notificationId}', [NotificationController::class, 'delete']);
        Route::patch('/notification/{notificationId}',  [NotificationController::class, 'setViewed']);

        Route::prefix('/myForms')->group(function () {

            Route::get('/form',             [MyFormController::class, 'list']);
            Route::get('/form/{id}',        [MyFormController::class, 'get']);
            Route::post('/form',            [MyFormController::class, 'create']);
            Route::delete('/form/{formId}', [MyFormController::class, 'delete']);
            Route::put('/form/{formId}',    [MyFormController::class, 'update']);

        });

        Route::prefix('/forms')->group(function () {

            Route::get('/form',             [FormController::class, 'list']);
            Route::get('/form/{id}',        [FormController::class, 'get']);
            Route::post('/form',            [FormController::class, 'create']);
            Route::delete('/form/{formId}', [FormController::class, 'delete']);
            Route::put('/form/{formId}',    [FormController::class, 'update']);

        });

        Route::get('/favorite',    [FavoriteController::class, 'list']);
        Route::post('/favorite',   [FavoriteController::class, 'create']);
        Route::delete('/favorite', [FavoriteController::class, 'delete']);

        Route::post('/report/announcement/{id}', [ReportController::class, 'create']);

    });

});
