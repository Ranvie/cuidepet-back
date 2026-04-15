<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilterDiscoveryController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicAnnouncementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/login',             [AuthController::class, 'login']);
Route::post('/register',          [AuthController::class, 'register']);
Route::post('/recovery-password', [AuthController::class, 'recoveryPassword']);
Route::get('/use-terms',          [AuthController::class, 'getUseTerms']);

Route::get('/storage/{path}', [StorageController::class, 'get']);

Route::get('announcements',     [PublicAnnouncementController::class, 'list']);
Route::get('announcement/{id}', [PublicAnnouncementController::class, 'get']);

Route::get('/filters', [FilterDiscoveryController::class, 'list']);
Route::get('/filter',  [FilterDiscoveryController::class, 'get']);

Route::middleware(['auth:sanctum', 'hasRole:reset-password'])
  ->post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', 'hasRole:confirm-email'])
  ->post('/email-confirmation', [AuthController::class, 'confirmUserEmail']);

Route::middleware(['auth:sanctum', 'checkUser'])->prefix('address')->group(function () {
  Route::get('/{zipCode}', [AddressController::class, 'get']);
});

Route::middleware(['auth:sanctum', 'hasRole:admin', 'checkUser'])->prefix('admin/user')->group(function () {
  Route::get('/',               [UserController::class, 'list']);
  Route::get('/{id}',           [UserController::class, 'get']);
  Route::post('/',              [UserController::class, 'create']);
  Route::put('/{id}',           [UserController::class, 'update']);
  Route::delete('/{id}',        [UserController::class, 'delete']);

  Route::get('/report/',        [ReportController::class, 'list']);
  Route::get('/report/{id}',    [ReportController::class, 'get']);
  Route::delete('/report/{id}', [ReportController::class, 'delete']);
});

Route::middleware(['auth:sanctum', 'checkUser'])->prefix('user')->group(function () {

  Route::get('/species',                               [SpecieController::class, 'list']);

  Route::post('/accept-terms',                         [AuthController::class, 'acceptTerms']);

  Route::post('/logout',                               [AuthController::class, 'logout']);

  Route::post('/report/announcement/{announcementId}', [ReportController::class, 'create']);

  Route::get('/announcement/{announcementId}/form',    [FormController::class, 'getByAnnouncement']);

  Route::prefix('profile')->group(function () {
    Route::get('/',            [UserController::class, 'getProfile']);
    Route::put('/',            [UserController::class, 'updateProfile']);
    Route::put('/password',    [UserController::class, 'updatePassword']);
    Route::post('/inactivate', [UserController::class, 'inactivate']);
  });

  Route::middleware(['validate:BelongsToUser,AnnouncementModel,list|listAnswers|create'])->prefix('my-announcements')->group(function () {
    Route::get('/',                         [AnnouncementController::class, 'list']);
    Route::get('/{announcementId}',         [AnnouncementController::class, 'get']);
    Route::get('/answers/{announcementId}', [AnnouncementController::class, 'listAnswers']);
    Route::post('/',                        [AnnouncementController::class, 'create']);
    Route::put('/{announcementId}',         [AnnouncementController::class, 'update']);
    Route::delete('/{announcementId}',      [AnnouncementController::class, 'delete']);
  });

  Route::middleware(['validate:BelongsToUser,NotificationModel,list'])->prefix('/notifications')->group(function () {
    Route::get('/',                        [NotificationController::class, 'list']);
    Route::delete('/{notificationId}',     [NotificationController::class, 'delete']);
    Route::patch('/read/{notificationId}', [NotificationController::class, 'readNotification']);
  });

  Route::middleware(['validate:BelongsToUser,FormResponseModel,list|create'])->prefix('/form-responses')->group(function () {
    Route::get('/',            [FormResponseController::class, 'list']);
    Route::get('/{formId}',    [FormResponseController::class, 'get']);
    Route::post('/',           [FormResponseController::class, 'create']);
    Route::delete('/{formId}', [FormResponseController::class, 'delete']);
    Route::put('/{formId}',    [FormResponseController::class, 'update']);
  });

  Route::middleware(['validate:BelongsToUser,FormModel,list|listAll|getByAnnouncement|create'])->prefix('/my-forms')->group(function () {
    Route::get('/',            [FormController::class, 'list']);
    Route::get('/all',         [FormController::class, 'listAll']);
    Route::get('/{formId}',    [FormController::class, 'getById']);
    Route::post('/',           [FormController::class, 'create']);
    Route::put('/{formId}',    [FormController::class, 'update']);
    Route::delete('/{formId}', [FormController::class, 'delete']);
  });

  Route::middleware(['validate:BelongsToUser,FavoriteModel,list|create'])->prefix('/favorite')->group(function () {
    Route::get('/',                [FavoriteController::class, 'list']);
    Route::post('/',               [FavoriteController::class, 'create']);
    Route::delete('/{favoriteId}', [FavoriteController::class, 'delete']);
  });
});
