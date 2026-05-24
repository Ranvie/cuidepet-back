<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnnouncementResponseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\NewsletterController;
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

// GRUPO DE AUTENTICAÇÃO
Route::middleware('throttle:5,1')->post('/login',             [AuthController::class, 'login']);
Route::middleware('throttle:5,1')->post('/register',          [AuthController::class, 'register']);
Route::middleware('throttle:5,1')->post('/recovery-password', [AuthController::class, 'recoveryPassword']);
Route::middleware('throttle:240,1')->get('/use-terms',        [AuthController::class, 'getUseTerms']);

// ENDPOINT DE CONTATO
Route::middleware('throttle:5,1')->post('/contact-us', [ContactUsController::class, 'contactUs']);

// ENDPOINT PARA BUSCAR MÍDIAS
Route::middleware('throttle:240,1')->get('/storage/{path}', [StorageController::class, 'get']);

// ANÚNCIOS PÚBLICOS (com autenticação opcional)
Route::middleware('optionalAuth')->group(function () {
  Route::middleware('throttle:240,1')->get('announcements',     [PublicAnnouncementController::class, 'list']);
  Route::middleware('throttle:240,1')->get('announcement/{id}', [PublicAnnouncementController::class, 'get']);
});

// ENDPOINT QUE RETORNA FILTROS DE LISTAGENS ESPECÍFICAS 
Route::middleware('throttle:240,1')->get('/filters', [FilterDiscoveryController::class, 'list']);
Route::middleware('throttle:240,1')->get('/filter',  [FilterDiscoveryController::class, 'get']);

// ALTERAÇÃO DE SENHA
Route::middleware(['auth:sanctum', 'hasRole:reset-password', 'throttle:5,1'])
  ->post('/reset-password', [AuthController::class, 'resetPassword']);

// CONFIRMAÇÃO DE E-MAIL
Route::middleware(['auth:sanctum', 'hasRole:confirm-email', 'throttle:240,1'])
  ->post('/email-confirmation', [AuthController::class, 'confirmUserEmail']);

// RESGATA ENDEREÇO POR CEP
Route::middleware(['auth:sanctum', 'notHasRole:reset-password,confirm-email', 'throttle:30,1'])->prefix('address')->group(function () {
  Route::get('/{zipCode}', [AddressController::class, 'get']);
});

// ENDPOINTS ADMINISTRATIVOS
Route::middleware(['auth:sanctum', 'hasRole:admin', 'notHasRole:reset-password,confirm-email'])->prefix('admin/user')->group(function () {
  Route::middleware('throttle:240,1')->get('/',        [UserController::class, 'list']);
  Route::middleware('throttle:240,1')->get('/{id}',    [UserController::class, 'get']);
  Route::middleware('throttle:240,1')->post('/',       [UserController::class, 'create']);
  Route::middleware('throttle:240,1')->put('/{id}',    [UserController::class, 'update']);
  Route::middleware('throttle:240,1')->delete('/{id}', [UserController::class, 'delete']);

  Route::middleware('throttle:240,1')->get('/report',         [ReportController::class, 'list']);
  Route::middleware('throttle:240,1')->get('/report/{id}',    [ReportController::class, 'get']);
  Route::middleware('throttle:240,1')->delete('/report/{id}', [ReportController::class, 'delete']);
});

// ENDPOINTS DE NEWSLETTER
Route::group(['prefix' => 'newsletter'], function () {
  Route::middleware('throttle:5,1')->post('/subscribe',   [NewsletterController::class, 'subscribe']);
  Route::middleware('throttle:240,1')->get('/confirm',     [NewsletterController::class, 'confirmSubscription']);
  Route::middleware('throttle:10,1')->get('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
});

Route::middleware(['auth:sanctum', 'notHasRole:reset-password,confirm-email'])->prefix('user')->group(function () {
  Route::middleware('throttle:240,1')->post('/accept-terms', [AuthController::class, 'acceptTerms']);
  Route::middleware('throttle:240,1')->post('/logout',       [AuthController::class, 'logout']);
});

// ENDPOINTS DE USUÁRIO AUTENTICADO
Route::middleware(['auth:sanctum', 'notHasRole:reset-password,confirm-email', 'acceptTerms'])->prefix('user')->group(function () {

  Route::middleware('throttle:240,1')->get('/species', [SpecieController::class, 'list']);

  Route::prefix('/report')->group(function () {
    Route::middleware('throttle:240,1')->get('/{type}', [ReportController::class, 'listReportTemplates'])->whereIn('type', ['form', 'announcement']);
    Route::middleware('throttle:240,1')->post('',       [ReportController::class, 'create']);
  });

  // ENDPOINTS "PÚBLICOS" DE BUSCA E RESPOSTA DE FORMULÁRIO
  Route::prefix('announcement/{announcementId}/form')->group(function () {
    Route::middleware('throttle:240,1')->get('/',               [FormController::class, 'getByAnnouncement']);
    Route::middleware('throttle:240,1')->get('/check-response', [AnnouncementResponseController::class, 'validateResponse']);
    Route::middleware('throttle:240,1')->post('/',              [AnnouncementResponseController::class, 'create']);
  });

  // GERENCIAMENTO MINHA CONTA
  Route::prefix('profile')->group(function () {
    Route::middleware('throttle:240,1')->get('/',           [UserController::class, 'getProfile']);
    Route::middleware('throttle:240,1')->put('/',           [UserController::class, 'updateProfile']);
    Route::middleware('throttle:5,1')->put('/password',    [UserController::class, 'updatePassword']);
    Route::middleware('throttle:5,1')->post('/inactivate', [UserController::class, 'inactivate']);
  });

  // GERENCIAMENTO DE MEUS ANÚNCIOS
  Route::middleware(['validate:policy=BelongsToUser,model=AnnouncementModel,ignored=list|listAnswers|create'])->prefix('my-announcements')->group(function () {
    Route::middleware('throttle:240,1')->get('/',  [AnnouncementController::class, 'list']);
    Route::middleware('throttle:240,1')->post('/', [AnnouncementController::class, 'create']);

    Route::prefix('/{announncementId}')->group(function () {

      Route::middleware('throttle:240,1')->get('/',    [AnnouncementController::class, 'get']);
      Route::middleware('throttle:240,1')->put('/',    [AnnouncementController::class, 'update']);
      Route::middleware('throttle:240,1')->delete('/', [AnnouncementController::class, 'delete']);

    });
  });
  
  // GERENCIAMENTO DE RESPOSTAS DOS ANÚNCIOS
  Route::middleware(['validate:policy=BelongsToUser,model=AnnouncementModel,fieldId=announcementId'])->prefix('my-announcements/{announcementId}/form-responses')
    ->group(function () {
        Route::middleware('throttle:240,1')->get('/',                [AnnouncementResponseController::class, 'list']);
        Route::middleware('throttle:240,1')->get('/{responseId}',    [AnnouncementResponseController::class, 'get']);
        Route::middleware('throttle:240,1')->delete('/{responseId}', [AnnouncementResponseController::class, 'delete']);
  });

  // GERENCIAMENTO DE NOTIFICAÇÕES
  Route::middleware(['validate:policy=BelongsToUser,model=NotificationModel,ignored=list'])->prefix('/notifications')->group(function () {
    Route::middleware('throttle:240,1')->get('/',                        [NotificationController::class, 'list']);
    Route::middleware('throttle:240,1')->delete('/{notificationId}',     [NotificationController::class, 'delete']);
    Route::middleware('throttle:240,1')->patch('/read/{notificationId}', [NotificationController::class, 'readNotification']);
  });

  // GERENCIAMENTO DOS MEUS FORMULÁRIOS
  Route::middleware(['validate:policy=BelongsToUser,model=FormModel,ignored=list|listAll|getByAnnouncement|create'])->prefix('/my-forms')->group(function () {
    Route::middleware('throttle:240,1')->get('/',            [FormController::class, 'list']);
    Route::middleware('throttle:240,1')->get('/all',         [FormController::class, 'listAll']);
    Route::middleware('throttle:240,1')->get('/{formId}',    [FormController::class, 'getById']);
    Route::middleware('throttle:240,1')->post('/',           [FormController::class, 'create']);
    Route::middleware('throttle:240,1')->put('/{formId}',    [FormController::class, 'update']);
    Route::middleware('throttle:240,1')->delete('/{formId}', [FormController::class, 'delete']);
  });

  // GERENCIAMENTO DOS MINHAS RESPOSTAS
  Route::middleware(['validate:policy=BelongsToUser,model=FormResponseModel,ignored=list'])->prefix('/my-responses')->group(function () {
    Route::middleware('throttle:240,1')->get('/',                [UserResponseController::class, 'list']);
    Route::middleware('throttle:240,1')->get('/{responseId}',    [UserResponseController::class, 'get']);
    Route::middleware('throttle:240,1')->delete('/{responseId}', [UserResponseController::class, 'delete']);
  });

  // GERENCIAMENTO DE FAVORITOS
  Route::prefix('/favorite')->group(function () {
    Route::middleware('throttle:240,1')->get('/',                                 [FavoriteController::class, 'list']);
    Route::middleware('throttle:240,1')->post('/announcement/{announcementId}',   [FavoriteController::class, 'create']);
    Route::middleware('throttle:240,1')->delete('/announcement/{announcementId}', [FavoriteController::class, 'delete']);
  });
});