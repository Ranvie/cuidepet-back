<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnnouncementResponseController;
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
use App\Http\Controllers\UserResponseController;

Route::post('/login',             [AuthController::class, 'login']);
Route::post('/register',          [AuthController::class, 'register']);
Route::post('/recovery-password', [AuthController::class, 'recoveryPassword']);
Route::get('/use-terms',          [AuthController::class, 'getUseTerms']);

// ENDPOINT PARA BUSCAR MÍDIAS
Route::get('/storage/{path}', [StorageController::class, 'get']);

// ANÚNCIOS PÚBLICOS
Route::get('announcements',     [PublicAnnouncementController::class, 'list']);
Route::get('announcement/{id}', [PublicAnnouncementController::class, 'get']);

// ENDPOINT QUE RETORNA FILTROS DE LISTAGENS ESPECÍFICAS 
Route::get('/filters', [FilterDiscoveryController::class, 'list']);
Route::get('/filter',  [FilterDiscoveryController::class, 'get']);

// ALTERAÇÃO DE SENHA
Route::middleware(['auth:sanctum', 'hasRole:reset-password'])
  ->post('/reset-password', [AuthController::class, 'resetPassword']);

// CONFIRMAÇÃO DE E-MAIL
Route::middleware(['auth:sanctum', 'hasRole:confirm-email'])
  ->post('/email-confirmation', [AuthController::class, 'confirmUserEmail']);

// RESGATA ENDEREÇO POR ZIPCODE
Route::middleware(['auth:sanctum', 'checkUser'])->prefix('address')->group(function () {
  Route::get('/{zipCode}', [AddressController::class, 'get']);
});

// ENDPOINTS ADMINISTRATIVOS
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

// ENDPOINTS DE USUÁRIO AUTENTINCADO
Route::middleware(['auth:sanctum', 'checkUser'])->prefix('user')->group(function () {

  Route::get('/species',       [SpecieController::class, 'list']);

  Route::post('/accept-terms', [AuthController::class, 'acceptTerms']);

  Route::post('/logout',       [AuthController::class, 'logout']);

  Route::post('/report',       [ReportController::class, 'create']);

  // ENDPOINTS "PÚBLICOS" DE BUSCA E RESPOSTA DE FORMULÁRIO
  Route::prefix('announcement/{announcementId}/form')->group(function () {
    Route::get('/',               [FormController::class, 'getByAnnouncement']);
    Route::get('/check-response', [AnnouncementResponseController::class, 'validateResponse']);
    Route::post('/',              [AnnouncementResponseController::class, 'create']);
  });

  // GERENCIAMENTO MINHA CONTA
  Route::prefix('profile')->group(function () {
    Route::get('/',            [UserController::class, 'getProfile']);
    Route::put('/',            [UserController::class, 'updateProfile']);
    Route::put('/password',    [UserController::class, 'updatePassword']);
    Route::post('/inactivate', [UserController::class, 'inactivate']);
  });

  // GERENCIAMENTO DE ANÚNCIO
  Route::middleware(['validate:policy=BelongsToUser,model=AnnouncementModel,ignored=list|listAnswers|create'])->prefix('my-announcements')->group(function () {
    Route::get('/',  [AnnouncementController::class, 'list']);
    Route::post('/', [AnnouncementController::class, 'create']);

    Route::prefix('/{announncementId}')->group(function () {
        
      Route::get('/',         [AnnouncementController::class, 'get']);
      Route::get('/response', [AnnouncementController::class, 'listAnswers']);
      Route::put('/',         [AnnouncementController::class, 'update']);
      Route::delete('/',      [AnnouncementController::class, 'delete']);

    });
  });
  
  // GERENCIAMENTO DE RESPOSTAS DO ANÚNCIOS
  Route::middleware(['validate:policy=BelongsToUser,model=AnnouncementModel,fieldId=announcementId'])->prefix('my-announcements/{announcementId}/form-responses')
    ->group(function () {
        Route::get('/',                [AnnouncementResponseController::class, 'list']);
        Route::get('/{responseId}',    [AnnouncementResponseController::class, 'get']);
        Route::delete('/{responseId}', [AnnouncementResponseController::class, 'delete']);
  });

  // GERENCIAMENTO DE NOTIFICAÇÕES
  Route::middleware(['validate:policy=BelongsToUser,model=NotificationModel,ignored=list'])->prefix('/notifications')->group(function () {
    Route::get('/',                        [NotificationController::class, 'list']);
    Route::delete('/{notificationId}',     [NotificationController::class, 'delete']);
    Route::patch('/read/{notificationId}', [NotificationController::class, 'readNotification']);
  });

  // GERENCIAMENTO DOS MEUS FORMULÁRIOS
  Route::middleware(['validate:policy=BelongsToUser,model=FormModel,ignored=list|listAll|getByAnnouncement|create'])->prefix('/my-forms')->group(function () {
    Route::get('/',            [FormController::class, 'list']);
    Route::get('/all',         [FormController::class, 'listAll']);
    Route::get('/{formId}',    [FormController::class, 'getById']);
    Route::post('/',           [FormController::class, 'create']);
    Route::put('/{formId}',    [FormController::class, 'update']);
    Route::delete('/{formId}', [FormController::class, 'delete']);
  });

  // GERENCIAMENTO DOS MINHAS RESPOSTAS
  Route::middleware(['validate:policy=BelongsToUser,model=FormResponseModel,ignored=list'])->prefix('/my-responses')->group(function () {
    Route::get('/',                [UserResponseController::class, 'list']);
    Route::get('/{responseId}',    [UserResponseController::class, 'get']);
    Route::delete('/{responseId}', [UserResponseController::class, 'delete']);
  });

  // GERENCIAMENTO DE FAVORITOS
  Route::prefix('/favorite')->group(function () {
    Route::get('/',                                 [FavoriteController::class, 'list']);
    Route::post('/announcement/{announcementId}',   [FavoriteController::class, 'create']);
    Route::delete('/announcement/{announcementId}', [FavoriteController::class, 'delete']);
  });
});