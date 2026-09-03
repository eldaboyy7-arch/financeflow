<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReceiptScannerController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\RecurringController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\AiAdvisorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Auth Routes (With Dedicated Rate Limiters)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth.register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth.login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:auth.forgot');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:auth.reset');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (requires auth:sanctum + general api rate limiter)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:api.general'])->group(function () {

    // Auth — profile & session management
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword'])->middleware('throttle:auth.reset');
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/income-expense', [DashboardController::class, 'incomeExpenseChart']);
        Route::get('/expense-breakdown', [DashboardController::class, 'expenseBreakdown']);
    });

    // Accounts
    Route::apiResource('accounts', AccountController::class);

    // Categories
    Route::apiResource('categories', CategoryController::class)->except(['show']);

    // Transactions & Transfers
    Route::get('/transactions/export', [TransactionController::class, 'export']);
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt']);
    Route::delete('/transactions/{transaction}/receipt', [TransactionController::class, 'deleteReceipt'])->middleware('throttle:financial.mutations');
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('transfers', TransferController::class);

    // AI Features (Dedicated AI Rate Limiter: 20/min/user)
    Route::post('/transactions/scan-receipt', [ReceiptScannerController::class, 'scan'])->middleware('throttle:ai.endpoints');
    Route::post('/ai/advisor', [AiAdvisorController::class, 'ask'])->middleware('throttle:ai.endpoints');

    // Budgets
    Route::get('/budgets/summary', [BudgetController::class, 'summary']);
    Route::post('/budgets/impact', [BudgetController::class, 'impact']);
    Route::apiResource('budgets', BudgetController::class);

    // Smart Spending Insights
    Route::get('/insights', [InsightController::class, 'index']);

    // Financial Goals
    Route::post('/goals/{goal}/contribute', [GoalController::class, 'contribute'])->middleware('throttle:financial.mutations');
    Route::apiResource('goals', GoalController::class);

    // Recurring Subscriptions & Bills
    Route::get('/recurring/upcoming', [RecurringController::class, 'upcoming']);
    Route::post('/recurring/{recurring}/pay', [RecurringController::class, 'pay'])->middleware('throttle:financial.mutations');
    Route::apiResource('recurring', RecurringController::class);

    // Notification Center
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Reports
    Route::get('/reports/monthly', [ReportController::class, 'monthly']);
});
