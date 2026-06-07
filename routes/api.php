// routes/api.php
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminUserController;

// Public
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Authenticated
Route::middleware('jwt.auth')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::get('me',       [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::get('rooms',                         [RoomController::class, 'index']);
    Route::get('rooms/recommendations',         [RoomController::class, 'recommendations']);
    Route::get('rooms/{room}',                  [RoomController::class, 'show']);
    Route::get('rooms/{room}/availability',     [RoomController::class, 'availability']);

    Route::apiResource('reservations', ReservationController::class)->only(['index', 'store', 'show']);
    Route::patch('reservations/{reservation}/cancel',  [ReservationController::class, 'cancel']);
    Route::post('reservations/checkin',                [ReservationController::class, 'checkin']);

    Route::get('notifications',              [NotificationController::class, 'index']);
    Route::patch('notifications/{id}/read',  [NotificationController::class, 'markRead']);
    Route::patch('notifications/read-all',   [NotificationController::class, 'markAllRead']);

    Route::get('analytics/personal',  [AnalyticsController::class, 'personal']);
});

// Admin
Route::middleware(['jwt.auth', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('rooms',        AdminRoomController::class);
    Route::apiResource('users',        AdminUserController::class);
    Route::apiResource('reservations', AdminReservationController::class);
    Route::patch('reservations/{reservation}/approve', [AdminReservationController::class, 'approve']);
    Route::patch('reservations/{reservation}/reject',  [AdminReservationController::class, 'reject']);
    Route::get('analytics',            [AnalyticsController::class, 'admin']);
    Route::get('analytics/occupancy',  [AnalyticsController::class, 'occupancy']);
});