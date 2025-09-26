<?php

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Http\Controllers\RoadMap\DataController;
use App\Http\Controllers\Auth\OidcAuthController;
use App\Filament\Resources\PanelSeguimientoResource\Pages\ViewProspectoInfo;
use Maatwebsite\Excel\Facades\Excel;

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

// Ruta para separación definitiva que redirige al recurso de Separaciones
Route::get('/separacion-definitiva/create', function () {
    $numeroDocumento = request('numero_documento');
    $proformaId = request('proforma_id');
    
    if ($proformaId) {
        return redirect()->to('/separacions/create?proforma_id=' . $proformaId . '&from=separacion_definitiva');
    } else {
        return redirect()->to('/separacions/create?numero_documento=' . $numeroDocumento . '&from=separacion_definitiva');
    }
})->name('separacion-definitiva.create');

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

// Rutas de exportación para seguimientos
Route::get('/seguimientos/export/excel', [\App\Http\Controllers\SeguimientoExportController::class, 'exportExcel'])
    ->middleware(['auth', 'verified'])
    ->name('seguimientos.export.excel');

Route::get('/seguimientos/export/pdf', [\App\Http\Controllers\SeguimientoExportController::class, 'exportPdf'])
    ->middleware(['auth', 'verified'])
    ->name('seguimientos.export.pdf');

// Rutas para cronograma
Route::post('/cronograma/guardar', [\App\Http\Controllers\CronogramaController::class, 'guardarCronograma'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma.guardar');

Route::get('/cronograma/{separacion_id}', [\App\Http\Controllers\CronogramaController::class, 'obtenerCronograma'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma.obtener');

Route::get('/cronograma/temporales/{proforma_id}', [\App\Http\Controllers\CronogramaController::class, 'obtenerCuotasTemporales'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma.temporales');

Route::get('/cronograma/definitivas/{proforma_id}', [\App\Http\Controllers\CronogramaController::class, 'obtenerCuotasDefinitivasPorProforma'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma.definitivas');

// Ruta para cronograma de saldo a financiar
Route::post('/cronograma-sf/guardar', [\App\Http\Controllers\CronogramaController::class, 'guardarCronogramaSF'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma-sf.guardar');

Route::get('/cronograma-sf/{separacion_id}', [\App\Http\Controllers\CronogramaController::class, 'obtenerCronogramaSF'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma-sf.obtener');

Route::get('/cronograma-sf/temporales/{proforma_id}', [\App\Http\Controllers\CronogramaController::class, 'obtenerCuotasSFTemporales'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma-sf.temporales');

Route::get('/cronograma-sf/definitivas/{proforma_id}', [\App\Http\Controllers\CronogramaController::class, 'obtenerCuotasSFDefinitivasPorProforma'])
    ->middleware(['auth', 'verified'])
    ->name('cronograma-sf.definitivas');
    
