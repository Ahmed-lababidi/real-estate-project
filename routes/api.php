<?php

use App\Http\Controllers\Admin\ApartmentController;
use App\Http\Controllers\Admin\ApartmentOrientationController;
use App\Http\Controllers\Admin\ApartmentReservationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FarmController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TowerCategoryController;
use App\Http\Controllers\Admin\TowerController;
use App\Http\Controllers\Admin\FarmReservationController;
use App\Http\Controllers\Admin\LandController;
use App\Http\Controllers\Admin\LandReservationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Public\PublicApartmentController;
use App\Http\Controllers\Public\PublicApartmentReservationController;
use App\Http\Controllers\Public\PublicBannerController;
use App\Http\Controllers\Public\PublicContactRequestController;
use App\Http\Controllers\Public\PublicFacilityController;
use App\Http\Controllers\Public\PublicFarmController;
use App\Http\Controllers\Public\PublicFarmReservationController;
use App\Http\Controllers\Public\PublicHomeController;
use App\Http\Controllers\Public\PublicProjectController;
use App\Http\Controllers\Public\PublicSettingController;
use App\Http\Controllers\Public\PublicTowerController;
use App\Http\Controllers\Public\PublicFilterController;
use App\Http\Controllers\Public\PublicLandController;
use App\Http\Controllers\Public\PublicLandReservationController;
use App\Http\Controllers\Public\PublicFaqController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('throttle:public-api')->group(function () {
    Route::get('/home', [PublicHomeController::class, 'index']);
    Route::get('/settings', [PublicSettingController::class, 'index']);

    Route::get('/projects', [PublicProjectController::class, 'index']);
    Route::get('/projects/{project}', [PublicProjectController::class, 'show']);

    Route::get('/towers', [PublicTowerController::class, 'index']);
    Route::get('/towers/{tower}', [PublicTowerController::class, 'show']);

    Route::get('/banners', [PublicBannerController::class, 'index']);
    Route::get('/banners/{banner}', [PublicBannerController::class, 'show']);

    Route::get('/apartments', [PublicApartmentController::class, 'index']);
    Route::get('/apartments/{apartment}', [PublicApartmentController::class, 'show']);


    Route::post('/apartments/{apartment}/reserve', [PublicApartmentReservationController::class, 'store'])
        ->middleware('throttle:reservation-api');

    Route::get('/farms', [PublicFarmController::class, 'index']);
    Route::get('/farms/{farm}', [PublicFarmController::class, 'show']);

    Route::get('/lands', [PublicLandController::class, 'index']);
    Route::get('/lands/{land}', [PublicLandController::class, 'show']);

    Route::post('/farms/{farm}/reserve', [PublicFarmReservationController::class, 'store'])
        ->middleware('throttle:reservation-api');

    Route::post('/lands/{land}/reserve', [PublicLandReservationController::class, 'store'])
        ->middleware('throttle:reservation-api');

    Route::get('/facilities', [PublicFacilityController::class, 'index']);
    Route::get('/facilities/{facility}', [PublicFacilityController::class, 'show']);

    Route::post('/contact-requests', [PublicContactRequestController::class, 'store'])
        ->middleware('throttle:contact-api');

    Route::post('/store-token', [NotificationController::class, 'storeToken']);

    Route::get('/filters', [PublicFilterController::class, 'index']);

    Route::get('/faqs', [PublicFaqController::class, 'index']);
    Route::get('/faqs/{faq}', [PublicFaqController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Admin API
|--------------------------------------------------------------------------
*/

Route::prefix('v1/admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::apiResource('project-categories', ProjectCategoryController::class);
        Route::apiResource('projects', ProjectController::class);

        Route::apiResource('tower-categories', TowerCategoryController::class);
        Route::apiResource('towers', TowerController::class);

        Route::apiResource('apartment-orientations', ApartmentOrientationController::class);
        Route::apiResource('apartments', ApartmentController::class);

        Route::apiResource('facilities', FacilityController::class);

        Route::apiResource('farms', FarmController::class);

        Route::apiResource('lands', LandController::class);

        Route::apiResource('banners', BannerController::class);

        Route::apiResource('faqs', FaqController::class);

        Route::get('farm-reservations', [FarmReservationController::class, 'index']);
        Route::get('farm-reservations/{reservation}', [FarmReservationController::class, 'show']);
        Route::post('farm-reservations/{reservation}/confirm', [FarmReservationController::class, 'confirm']);
        Route::post('farm-reservations/{reservation}/cancel', [FarmReservationController::class, 'cancel']);

        Route::get('land-reservations', [LandReservationController::class, 'index']);
        Route::get('land-reservations/{reservation}', [LandReservationController::class, 'show']);
        Route::post('land-reservations/{reservation}/confirm', [LandReservationController::class, 'confirm']);
        Route::post('land-reservations/{reservation}/cancel', [LandReservationController::class, 'cancel']);

        Route::get('reservations', [ApartmentReservationController::class, 'index']);
        Route::get('reservations/{reservation}', [ApartmentReservationController::class, 'show']);
        Route::post('reservations/{reservation}/confirm', [ApartmentReservationController::class, 'confirm']);
        Route::post('reservations/{reservation}/cancel', [ApartmentReservationController::class, 'cancel']);


        Route::get('leads', [ContactRequestController::class, 'index']);
        Route::get('leads/{lead}', [ContactRequestController::class, 'show']);
        Route::patch('leads/{lead}/status', [ContactRequestController::class, 'updateStatus']);
        Route::delete('leads/{lead}', [ContactRequestController::class, 'destroy']);

        Route::post('/deactivate-token', [NotificationController::class, 'deactivateToken']);
        Route::post('/send-one', [NotificationController::class, 'sendToOne']);
        Route::post('/send-all', [NotificationController::class, 'sendToAll']);

        Route::get('settings', [SettingController::class, 'index']);
        Route::post('settings', [SettingController::class, 'update']);
    });
});
