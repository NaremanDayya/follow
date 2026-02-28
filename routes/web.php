<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Client Management Routes
    Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [App\Http\Controllers\ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [App\Http\Controllers\ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [App\Http\Controllers\ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
    
    // Client Chat Routes
    Route::get('/chat', [App\Http\Controllers\ClientChatController::class, 'index'])->name('chat.index');
    Route::get('/clients/{client}/chat', [App\Http\Controllers\ClientChatController::class, 'show'])->name('clients.chat');
    Route::post('/clients/{client}/chat', [App\Http\Controllers\ClientChatController::class, 'store'])->name('clients.chat.store');
    Route::post('/clients/{client}/chat/mark-read', [App\Http\Controllers\ClientChatController::class, 'markAsRead'])->name('clients.chat.mark-read');
    Route::get('/clients/{client}/chat/unread-count', [App\Http\Controllers\ClientChatController::class, 'getUnreadCount'])->name('clients.chat.unread-count');
    Route::get('/clients/{client}/chat/recent', [App\Http\Controllers\ClientChatController::class, 'getRecentMessages'])->name('clients.chat.recent');
    Route::delete('/clients/{client}/chat/{message}', [App\Http\Controllers\ClientChatController::class, 'deleteMessage'])->name('clients.chat.delete');

    // Client Updates Routes
    Route::get('/clients/{client}/updates', [App\Http\Controllers\ClientUpdateController::class, 'index'])->name('clients.updates.index');
    Route::get('/clients/{client}/updates/create', [App\Http\Controllers\ClientUpdateController::class, 'create'])->name('clients.updates.create');
    Route::post('/clients/{client}/updates', [App\Http\Controllers\ClientUpdateController::class, 'store'])->name('clients.updates.store');
    Route::get('/clients/{client}/updates/{update}/edit', [App\Http\Controllers\ClientUpdateController::class, 'edit'])->name('clients.updates.edit');
    Route::put('/clients/{client}/updates/{update}', [App\Http\Controllers\ClientUpdateController::class, 'update'])->name('clients.updates.update');
    Route::delete('/clients/{client}/updates/{update}', [App\Http\Controllers\ClientUpdateController::class, 'destroy'])->name('clients.updates.destroy');
    Route::post('/clients/{client}/quick-update', [App\Http\Controllers\ClientUpdateController::class, 'quickUpdate'])->name('clients.quick-update');
    Route::get('/clients/{client}/timeline', [App\Http\Controllers\ClientUpdateController::class, 'getTimeline'])->name('clients.timeline');
    Route::get('/clients/{client}/stats', [App\Http\Controllers\ClientUpdateController::class, 'getStats'])->name('clients.stats');

    // Reports Routes
    Route::get('/reports/dashboard', [App\Http\Controllers\ClientReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('/reports/monthly', [App\Http\Controllers\ClientReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/client/{client}/monthly', [App\Http\Controllers\ClientReportController::class, 'clientMonthlyReport'])->name('reports.client.monthly');
    Route::get('/reports/export', [App\Http\Controllers\ClientReportController::class, 'exportMonthlyReport'])->name('reports.export');
    Route::get('/reports/analytics', [App\Http\Controllers\ClientReportController::class, 'analytics'])->name('reports.analytics');
    Route::get('/reports/late-clients', [App\Http\Controllers\ClientReportController::class, 'lateClientsReport'])->name('reports.late-clients');

    // AJAX Routes for real-time updates
    Route::post('/clients/update-late-statuses', [App\Http\Controllers\ClientController::class, 'updateLateStatuses'])->name('clients.update-late-statuses');
    Route::post('/clients/{client}/mark-messages-read', [App\Http\Controllers\ClientController::class, 'markMessagesAsRead'])->name('clients.mark-messages-read');
});

require __DIR__.'/auth.php';
