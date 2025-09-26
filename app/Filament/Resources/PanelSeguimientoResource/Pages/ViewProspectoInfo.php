<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\Page;
use App\Models\Prospecto;
use App\Models\Tarea;

class ViewProspectoInfo extends Page
{
    protected static string $resource = PanelSeguimientoResource::class;

    protected static string $view = 'filament.resources.panel-seguimiento-resource.view-prospecto-info';

    public Prospecto $prospecto;
    public $ultimaTareaPendiente;
    public $showRealizarAccionModal = false;
    public $showAgendarCitaModal = false;

    protected $listeners = [
        'tareaCreada' => 'refreshData',
        'refreshTable' => 'refreshData'
    ];

    public function mount($record)
    {
        $this->prospecto = Prospecto::with([
            'tareas' => fn ($q) => $q->latest(),
            'proformas' => fn ($q) => $q->latest()
        ])->findOrFail($record);

        $this->ultimaTareaPendiente = Tarea::where('prospecto_id', $record)
            ->whereDate('fecha_realizar', '>=', now())
            ->orderBy('fecha_realizar')
            ->first();
    }

    // Propiedades para el formulario de realizar tarea
    public $forma_contacto_id;
    public $fecha_realizar;
    public $hora;
    public $respuesta;
    public $comentario;
    public $nivel_interes_id;
    public $crear_proxima_tarea = true;
    public $proxima_usuario_asignado_id;
    public $proxima_forma_contacto_id;
    public $proxima_fecha;
    public $proxima_hora = '09:00';
    public $proxima_comentario;

    // Propiedades para el formulario de agendar cita
    public $cita_forma_contacto_id;
    public $fecha_cita;
    public $hora_cita;
    public $responsable_id;
    public $lugar;
    public $modalidad = 'presencial';
    public $observaciones;

    // Propiedades para el modal de reasignación de contacto
    public $showReasignacionModal = false;
    public $reasignacion_responsable_id;
    public $reasignacion_forma_contacto_id;
    public $reasignacion_nivel_interes_id = 1;
    public $reasignacion_fecha_tarea;
    public $reasignacion_hora_tarea = '09:00';
    public $reasignacion_comentario;

    public function abrirModalRealizarTarea()
    {
        // Validar que el prospecto existe
        if (!$this->prospecto) {
            $this->addError('prospecto', 'No se encontró información del prospecto.');
            return;
        }

        // Validar que el prospecto tiene los datos mínimos requeridos
        if (empty($this->prospecto->nombres) || empty($this->prospecto->celular)) {
            $this->addError('prospecto', 'El prospecto debe tener nombre y teléfono para realizar una acción.');
            return;
        }

        // Limpiar errores previos
        $this->resetErrorBag();

        // Inicializar valores por defecto
        $this->fecha_realizar = now()->format('Y-m-d');
        $this->hora = now()->format('H:i');
        $this->proxima_fecha = now()->addDays(1)->format('Y-m-d');
        $this->proxima_usuario_asignado_id = auth()->id();

        // Abrir el modal
        $this->showRealizarAccionModal = true;
    }

    public function realizarTarea()
    {
        // Validar campos requeridos
        $this->validate([
            'forma_contacto_id' => 'required',
            'fecha_realizar' => 'required|date',
            'hora' => 'required',
            'respuesta' => 'required|in:efectiva,no_efectiva',
            'nivel_interes_id' => 'required',
            'proxima_usuario_asignado_id' => 'required_if:crear_proxima_tarea,true',
        ]);

        try {
            $prospecto = $this->prospecto;

            // Validar regla de negocio
            if ($this->respuesta !== 'efectiva' && $prospecto->tipo_gestion_id == 3) {
                $this->addError('respuesta', 'No se puede retroceder de Contactados a Por Contactar');
                return;
            }

            // Crear la tarea
            Tarea::create([
                'prospecto_id' => $prospecto->id,
                'forma_contacto_id' => $this->forma_contacto_id,
                'fecha_realizar' => $this->fecha_realizar,
                'hora' => $this->hora,
                'nota' => $this->comentario ?? null,
                'respuesta' => $this->respuesta, // Guardar la respuesta
                'nivel_interes_id' => $this->nivel_interes_id,
                'usuario_asignado_id' => auth()->id(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Actualizar tipo_gestion según respuesta
            $nuevoTipoGestion = null;
            if ($this->respuesta === 'efectiva') {
                $nuevoTipoGestion = 3; // Contactados
            } else if ($prospecto->tipo_gestion_id == 1) {
                $nuevoTipoGestion = 2; // Por Contactar
            }

            if ($nuevoTipoGestion) {
                $prospecto->update(['tipo_gestion_id' => $nuevoTipoGestion]);
            }

            // Crear próxima tarea si está marcada
            if ($this->crear_proxima_tarea && $this->proxima_fecha && $this->proxima_forma_contacto_id) {
                Tarea::create([
                    'prospecto_id' => $prospecto->id,
                    'forma_contacto_id' => $this->proxima_forma_contacto_id,
                    'fecha_realizar' => $this->proxima_fecha,
                    'hora' => $this->proxima_hora ?? '09:00:00',
                    'nota' => $this->proxima_comentario ?? null,
                    'nivel_interes_id' => $this->nivel_interes_id,
                    'usuario_asignado_id' => $this->proxima_usuario_asignado_id,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            // Refrescar datos
            $this->refreshData();

            // Emitir eventos para refrescar el panel de seguimientos
            $this->dispatch('refreshTable');
            $this->dispatch('tareaCreada');

            // Cerrar modal y limpiar
            $this->cerrarModalRealizarTarea();

            // Notificación de éxito
            session()->flash('message', 'Tarea registrada correctamente');

        } catch (\Exception $e) {
            $this->addError('general', 'Error al guardar los cambios: ' . $e->getMessage());
        }
    }

    public function cerrarModalRealizarTarea()
    {
        $this->showRealizarAccionModal = false;
        $this->resetErrorBag();

        // Limpiar campos del formulario
        $this->forma_contacto_id = null;
        $this->fecha_realizar = null;
        $this->hora = null;
        $this->respuesta = null;
        $this->comentario = null;
        $this->nivel_interes_id = null;
        $this->crear_proxima_tarea = true;
        $this->proxima_usuario_asignado_id = null;
        $this->proxima_forma_contacto_id = null;
        $this->proxima_fecha = null;
        $this->proxima_hora = '09:00';
        $this->proxima_comentario = null;
    }

    public function getNombreCompletoProperty()
    {
        $nombres = is_array($this->prospecto->nombres) ? implode(' ', $this->prospecto->nombres) : $this->prospecto->nombres;
        $apePaterno = is_array($this->prospecto->ape_paterno) ? implode(' ', $this->prospecto->ape_paterno) : $this->prospecto->ape_paterno;
        $apeMaterno = is_array($this->prospecto->ape_materno) ? implode(' ', $this->prospecto->ape_materno) : $this->prospecto->ape_materno;

        return trim($nombres . ' ' . $apePaterno . ' ' . $apeMaterno);
    }

    public function getTelefonoCompletoProperty()
    {
        return is_array($this->prospecto->celular) ? implode(', ', $this->prospecto->celular) : $this->prospecto->celular;
    }

    public function redirectToPanelSeguimiento()
    {
        return redirect()->route('filament.admin.resources.panel-seguimiento.index');
    }

    public function refreshData()
    {
        $this->prospecto->refresh();
        $this->prospecto->load(['tareas' => fn ($q) => $q->latest()]);

        $this->ultimaTareaPendiente = Tarea::where('prospecto_id', $this->prospecto->id)
            ->whereDate('fecha_realizar', '>=', now())
            ->orderBy('fecha_realizar')
            ->first();
    }

    public function abrirModalAgendarCita()
    {
        // Validar que el prospecto existe
        if (!$this->prospecto) {
            $this->addError('prospecto', 'No se encontró información del prospecto.');
            return;
        }

        // Validar que el prospecto tiene los datos mínimos requeridos
        if (empty($this->prospecto->nombres) || empty($this->prospecto->celular)) {
            $this->addError('prospecto', 'El prospecto debe tener nombre y teléfono para agendar una cita.');
            return;
        }

        // Limpiar errores previos
        $this->resetErrorBag();

        // Inicializar valores por defecto
        $this->fecha_cita = now()->format('Y-m-d');
        $this->hora_cita = now()->format('H:i');
        $this->responsable_id = auth()->id();
        $this->modalidad = 'presencial';

        // Abrir el modal
        $this->showAgendarCitaModal = true;
    }

    public function agendarCita()
    {
        // Validar campos requeridos
        $this->validate([
            'cita_forma_contacto_id' => 'required',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'responsable_id' => 'required',
            'lugar' => 'required',
            'modalidad' => 'required|in:presencial,virtual',
        ]);

        try {
            $prospecto = $this->prospecto;

            // Crear primero la tarea (requerida para la cita)
            $tarea = Tarea::create([
                'prospecto_id' => $prospecto->id,
                'forma_contacto_id' => $this->cita_forma_contacto_id,
                'nivel_interes_id' => 1, // Valor por defecto, puedes ajustarlo según tu lógica
                'fecha_realizar' => $this->fecha_cita,
                'hora' => $this->hora_cita,
                'nota' => $this->observaciones ?? 'Sin observaciones',
                'usuario_asignado_id' => $this->responsable_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Crear la cita con el tarea_id
            $cita = \App\Models\Cita::create([
                'tarea_id' => $tarea->id,
                'proyecto_id' => $prospecto->proyecto_id,
                'responsable_id' => $this->responsable_id,
                'fecha_cita' => $this->fecha_cita,
                'hora_cita' => $this->hora_cita,
                'modalidad' => $this->modalidad,
                'lugar' => $this->lugar,
                'comentarios' => $this->observaciones,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Actualizar tipo_gestion a "Citados" (ID 4)
            $prospecto->update(['tipo_gestion_id' => 4]);

            // Refrescar datos
            $this->refreshData();

            // Emitir eventos para refrescar el panel de seguimientos
            $this->dispatch('refreshTable');
            $this->dispatch('tareaCreada');

            // Cerrar modal y limpiar
            $this->cerrarModalAgendarCita();

            // Notificación de éxito
            session()->flash('message', 'Cita agendada correctamente');

        } catch (\Exception $e) {
            $this->addError('general', 'Error al guardar los cambios: ' . $e->getMessage());
        }
    }

    public function cerrarModalAgendarCita()
    {
        $this->showAgendarCitaModal = false;
        $this->resetErrorBag();

        // Limpiar campos del formulario
        $this->cita_forma_contacto_id = null;
        $this->fecha_cita = null;
        $this->hora_cita = null;
        $this->responsable_id = null;
        $this->lugar = null;
        $this->modalidad = 'presencial';
        $this->observaciones = null;
    }

    public function abrirModalReasignacion()
    {
        // Validar que el prospecto existe
        if (!$this->prospecto) {
            $this->addError('prospecto', 'No se encontró información del prospecto.');
            return;
        }

        // Obtener la última tarea del prospecto
        $ultimaTarea = $this->prospecto->tareas()->orderBy('created_at', 'desc')->first();

        // Inicializar valores por defecto basados en la última tarea
        $this->reasignacion_fecha_tarea = $ultimaTarea && $ultimaTarea->fecha_realizar ? $ultimaTarea->fecha_realizar->format('Y-m-d') : now()->format('Y-m-d');
        $this->reasignacion_hora_tarea = $ultimaTarea ? $ultimaTarea->hora : '09:00';
        $this->reasignacion_nivel_interes_id = $this->prospecto->nivel_interes_id ?? 1;
        $this->reasignacion_forma_contacto_id = $ultimaTarea ? $ultimaTarea->forma_contacto_id : null;
        $this->reasignacion_responsable_id = $ultimaTarea ? $ultimaTarea->usuario_asignado_id : null;
        $this->reasignacion_comentario = $ultimaTarea ? $ultimaTarea->nota : null;

        $this->showReasignacionModal = true;
    }

    public function reasignarContacto()
    {
        // Validar campos requeridos
        $this->validate([
            'reasignacion_responsable_id' => 'required',
            'reasignacion_forma_contacto_id' => 'required',
            'reasignacion_nivel_interes_id' => 'required',
            'reasignacion_fecha_tarea' => 'required|date',
            'reasignacion_hora_tarea' => 'required',
        ]);

        try {
            $prospecto = $this->prospecto;

            // Obtener la última tarea del prospecto
            $ultimaTarea = $prospecto->tareas()->orderBy('created_at', 'desc')->first();

            if ($ultimaTarea) {
                // Actualizar la tarea existente
                $ultimaTarea->update([
                    'forma_contacto_id' => $this->reasignacion_forma_contacto_id,
                    'nivel_interes_id' => $this->reasignacion_nivel_interes_id,
                    'fecha_realizar' => $this->reasignacion_fecha_tarea,
                    'hora' => $this->reasignacion_hora_tarea,
                    'nota' => $this->reasignacion_comentario ?? 'Sin comentarios',
                    'usuario_asignado_id' => $this->reasignacion_responsable_id,
                    'updated_by' => auth()->id(),
                ]);
            } else {
                // Si no hay tareas previas, crear una nueva
                Tarea::create([
                    'prospecto_id' => $prospecto->id,
                    'forma_contacto_id' => $this->reasignacion_forma_contacto_id,
                    'nivel_interes_id' => $this->reasignacion_nivel_interes_id,
                    'fecha_realizar' => $this->reasignacion_fecha_tarea,
                    'hora' => $this->reasignacion_hora_tarea,
                    'nota' => $this->reasignacion_comentario ?? 'Sin comentarios',
                    'usuario_asignado_id' => $this->reasignacion_responsable_id,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            // Actualizar el prospecto con todos los campos de reasignación
            $prospecto->update([
                'nivel_interes_id' => $this->reasignacion_nivel_interes_id,
                'forma_contacto_id' => $this->reasignacion_forma_contacto_id,
                'created_by' => $this->reasignacion_responsable_id, // Usuario asignado al prospecto
                'updated_by' => auth()->id()
            ]);

            /*
            // Actualizar tipo_gestion a "Por Contactar" (ID 2) si es necesario
            if ($prospecto->tipo_gestion_id == 1) {
                $prospecto->update(['tipo_gestion_id' => 2]);
            }
                */

            // Refrescar datos
            $this->refreshData();

            // Emitir eventos para refrescar el panel de seguimientos
            $this->dispatch('refreshTable');
            $this->dispatch('tareaCreada');

            // Cerrar modal y limpiar
            $this->cerrarModalReasignacion();

            // Notificación de éxito
            session()->flash('message', 'Contacto reasignado correctamente');

        } catch (\Exception $e) {
            $this->addError('general', 'Error al reasignar contacto: ' . $e->getMessage());
        }
    }

    public function cerrarModalReasignacion()
    {
        $this->showReasignacionModal = false;
        $this->resetErrorBag();

        // Limpiar campos del formulario
        $this->reasignacion_responsable_id = null;
        $this->reasignacion_forma_contacto_id = null;
        $this->reasignacion_nivel_interes_id = 1;
        $this->reasignacion_fecha_tarea = null;
        $this->reasignacion_hora_tarea = '09:00';
        $this->reasignacion_comentario = null;
    }
}