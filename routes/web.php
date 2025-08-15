<?php

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Http\Controllers\RoadMap\DataController;
use App\Http\Controllers\Auth\OidcAuthController;
use App\Filament\Resources\PanelSeguimientoResource\Pages\ViewProspectoInfo;

// Share ticket
Route::get('/tickets/share/{ticket:code}', function (Ticket $ticket) {
    return redirect()->to(route('filament.resources.tickets.view', $ticket));
})->name('filament.resources.tickets.share');

// Validate an account
Route::get('/validate-account/{user:creation_token}', function (User $user) {
    return view('validate-account', compact('user'));
})
    ->name('validate-account')
    ->middleware([
        'web',
        DispatchServingFilamentEvent::class
    ]);

// Login default redirection
Route::redirect('/login-redirect', '/login')->name('login');

// Road map JSON data
Route::get('road-map/data/{project}', [DataController::class, 'data'])
    ->middleware(['verified', 'auth'])
    ->name('road-map.data');

Route::name('oidc.')
    ->prefix('oidc')
    ->group(function () {
        Route::get('redirect', [OidcAuthController::class, 'redirect'])->name('redirect');
        Route::get('callback', [OidcAuthController::class, 'callback'])->name('callback');
    });
Route::get('/admin/panel-seguimiento/prospecto/{record}', ViewProspectoInfo::class)
    ->name('filament.resources.panel-seguimiento.view-prospecto-info');

// Ruta para detalle de separación en Filament
Route::get('/Proforma/DetalleProforma/{proforma_id}', \App\Filament\Pages\DetalleSeparacion::class)
    ->middleware(['auth', 'verified'])
    ->name('filament.pages.detalle-separacion');

// Agregar esta ruta
Route::get('/detalle-separacion/{proforma_id}', [\App\Http\Controllers\DetalleSeparacionController::class, 'show'])
    ->name('detalle-separacion.show');

Route::get('/download/temp/{filename}', function ($filename) {
    $path = storage_path('app/temp/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->download($path, $filename)->deleteFileAfterSend(true);
})->name('download.temp.file');
    
