<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectoResource\Pages;
use App\Filament\Resources\ProspectoResource\RelationManagers\TareasRelationManager;
use App\Models\Prospecto;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;

class ProspectoResource extends Resource
{
    protected static ?string $model = Prospecto::class;

    // Lo ocultamos del menú, sólo lo usaremos para “ver”.
    protected static bool $shouldRegisterNavigation = false;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Información del Prospecto')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('nombres')->label('Nombres'),
                        TextEntry::make('ape_paterno')->label('Apellido Paterno'),
                        TextEntry::make('ape_materno')->label('Apellido Materno'),
                        TextEntry::make('razon_social')->label('Razón Social'),
                        TextEntry::make('tipoDocumento.nombre')->label('Tipo Documento'),
                        TextEntry::make('numero_documento')->label('N° Documento'),
                        TextEntry::make('celular')->label('Celular'),
                        TextEntry::make('correo_electronico')->label('Correo'),
                        TextEntry::make('proyecto.nombre')->label('Proyecto'),
                        TextEntry::make('tipoInmueble.nombre')->label('Tipo de Inmueble'),
                        TextEntry::make('formaContacto.nombre')->label('Forma de Contacto'),
                        TextEntry::make('comoSeEntero.nombre')->label('¿Cómo se enteró?'),
                        TextEntry::make('tipoGestion.nombre')->label('Tipo de Gestión'),
                        TextEntry::make('creador.name')->label('Registrado por'),
                        TextEntry::make('fecha_registro')->label('Fecha de Registro')->dateTime('d/m/Y H:i'),
                    ]),
                ])->columns(1),

            Section::make('Próxima tarea')
                ->schema([
                    TextEntry::make('proxima_tarea')
                        ->label(' ')
                        ->state(function (Prospecto $record) {
                            $t = $record->tareas()
                                ->whereDate('fecha_realizar', '>=', now())
                                ->orderBy('fecha_realizar')
                                ->first();

                            if (!$t) return 'Sin próxima tarea.';

                            $forma   = $t->formaContacto->nombre ?? 'SIN CONTACTO';
                            $fecha   = optional($t->fecha_realizar)->format('d/m/Y');
                            $hora    = $t->hora;
                            $interes = $t->nivelInteres->nombre ?? '-';
                            $resp    = $t->usuarioAsignado->name ?? '-';

                            return strtoupper($forma)." — Fecha: {$fecha} {$hora} · Interés: {$interes} · Responsable: {$resp}";
                        })
                        ->extraAttributes(['class' => 'font-semibold text-blue-700'])
                ])
                ->collapsible(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            TareasRelationManager::class, // Tabla de historial de tareas/citas
        ];
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewProspecto::route('/{record}'),
        ];
    }
}
