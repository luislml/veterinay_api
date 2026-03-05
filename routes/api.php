<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\{
    VeterinaryController,
    ProductController,
    ClientController,
    PetController,
    ReservationController,
    SaleController,
    ShoppingController,
    ConfigurationController,
    ScheduleController,
    AddressController,
    AdvertisingController,
    PromotionController,
    PlanController,
    VaccineController,
    ConsultationController,
    TypePetController,
    RaceController,
    FileController,
    AdvertisementController,
    MovementController,
    ReportController,
    ImageVeterinaryController,
    ContentVeterinaryController,
    VeterinaryPublicController
};
use App\Http\Controllers\Admin\UserAdminController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Bienvenido al dashboard']);
    })->middleware('can:ver-dashboard');

    Route::get('/perfil', function () {
        return response()->json(['message' => 'Perfil de usuario']);
    })->middleware('can:editar-profile');
});
// Obtener usuario autenticado
Route::middleware('auth:sanctum')->get('users/me', [UserAdminController::class, 'me']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile', [ProfileController::class, 'update']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('veterinaries', VeterinaryController::class);
    Route::post('veterinaries/{veterinary}/clients', [VeterinaryController::class, 'addClients']);
    Route::post('vet-products', [VeterinaryController::class, 'attachProduct']);
    Route::post('veterinaries/{id}/restore', [VeterinaryController::class, 'restore']);

    Route::apiResource('users', UserAdminController::class);
    Route::post('users/{id}/restore', [UserAdminController::class, 'restore']);

    Route::apiResource('plans', PlanController::class);
    Route::apiResource('products', ProductController::class);

    Route::apiResource('clients', ClientController::class);
    Route::post('clients/{id}/restore', [ClientController::class, 'restore']);

    Route::apiResource('pets', PetController::class);
    Route::post('pets/{id}/restore', [PetController::class, 'restore']);

    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('shoppings', ShoppingController::class);
    Route::apiResource('configurations', ConfigurationController::class);
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('addresses', AddressController::class);
    Route::apiResource('advertisings', AdvertisingController::class);
    Route::apiResource('promotions', PromotionController::class);
    Route::apiResource('vaccines', VaccineController::class);
    Route::apiResource('consultations', ConsultationController::class);
    Route::apiResource('type-pets', TypePetController::class);
    Route::apiResource('races', RaceController::class);
    Route::apiResource('images-veterinaries', ImageVeterinaryController::class);
    Route::apiResource('content-veterinaries', ContentVeterinaryController::class);

    Route::post('files/upload', [FileController::class, 'upload']);
    Route::get('files/list', [FileController::class, 'list']);
    Route::get('files/{file}', [FileController::class, 'show']);
    Route::delete('files/{file}', [FileController::class, 'destroy']);

    Route::apiResource('advertisements', AdvertisementController::class);
    Route::apiResource('movements', MovementController::class);

    // Reports

    Route::get('sales-analytics', [ReportController::class, 'salesAnalytics']);
    Route::get('purchases-analytics', [ReportController::class, 'purchasesAnalytics']);
    Route::get('consultations-analytics', [ReportController::class, 'consultationsAnalytics']);
    Route::get('dashboard', [ReportController::class, 'dashboard']);
    Route::get('low-stock', [ReportController::class, 'lowStock']);
    Route::get('movements-analytics', [ReportController::class, 'movementsAnalytics']);
    Route::get('kpi-summary', [ReportController::class, 'kpiSummary']);
    Route::get('recent-patients', [ReportController::class, 'recentPatients']);
    Route::get('top-selling-products', [ReportController::class, 'topSellingProducts']);
    Route::get('frequent-consultations', [ReportController::class, 'frequentConsultations']);
    Route::get('critical-inventory', [ReportController::class, 'criticalInventory']);

    Route::get('advertisements/{advertisement}/pdf', [AdvertisementController::class, 'downloadPdf']);
});

// Rutas públicas
Route::get('/landing/{slug}', [VeterinaryPublicController::class, 'show']);



require __DIR__ . '/auth.php';