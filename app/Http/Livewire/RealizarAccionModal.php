<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Prospecto;
use App\Models\Tarea;
use App\Models\FormaContacto;
use App\Models\NivelInteres;
use App\Models\User;
use Filament\Notifications\Notification;

class RealizarAccionModal extends Component
{
    public bool $show = false;
    public $prospectoId = null;

    // Info (lectura)
    public $nombre = '';
    public $telefono = '';
    public $proyecto = '';

    // Form - acción actual
    public $forma_contacto_id;
    public $fecha_realizar;
    public $hora;
    public $respuesta;
    public $comentario;
    public $nivel_interes_id;

    // Próxima tarea (opcional)
    public $crear_proxima_tarea = false;
    public $proxima_usuario_asignado_id;
    public $proxima_forma_contacto_id;
    public $proxima_fecha;
    public $proxima_hora;
    public $proxima_comentario;

    // Listas para los radios/selects
    public $formaContactos = [];
    public $nivelesInteres = [];
    public $users = [];

    protected $listeners = [
        // Permite llamar: $emitTo('realizar-accion-modal', 'open', $id)
        'open' => 'open',
    ];

    protected $rules = [
        'forma_contacto_id' => 'required',
        'fecha_realizar' => 'required|date',
        'hora' => 'required',
        'respuesta' => 'required',
        'nivel_interes_id' => 'required',
        // las reglas de próximas tareas las validamos manualmente en save()
    ];

    public function open($prospectoId)
    {
        $this->resetValidation();
        $this->resetForm();

        $this->prospectoId = $prospectoId;

        $prospecto = Prospecto::find($prospectoId);

        $nombres     = $prospecto->nombres ?? '';
    $apePaterno  = $prospecto->ape_paterno ?? '';
    $apeMaterno  = $prospecto->ape_materno ?? '';

    // Verificar si alguno es array
    if (is_array($nombres)) {
        dd('El campo nombres es array:', $nombres);
    }
    if (is_array($apePaterno)) {
        dd('El campo ape_paterno es array:', $apePaterno);
    }
    if (is_array($apeMaterno)) {
        dd('El campo ape_materno es array:', $apeMaterno);
    }

        //$this->nombre = $prospecto ? trim(($prospecto->nombres ?? '') . ' ' . ($prospecto->ape_paterno ?? '') . ' ' . ($prospecto->ape_materno ?? '')) : '';
        $this->telefono = $prospecto->celular ?? '';
        $this->proyecto = optional($prospecto->proyecto)->nombre ?? '';

        $this->fecha_realizar = now()->toDateString();
        $this->hora = now()->format('H:i');
        $this->crear_proxima_tarea = false;
        $this->proxima_usuario_asignado_id = auth()->id();
        $this->proxima_fecha = now()->addDay()->toDateString();
        $this->proxima_hora = '09:00';

        // Cargar opciones
        $this->formaContactos = FormaContacto::orderBy('nombre')->get();
        $this->nivelesInteres = NivelInteres::orderBy('nombre')->get();
        $this->users = User::orderBy('name')->get();

        $this->show = true;
    }

    public function save()
    {
        $this->validate();

        $prospecto = Prospecto::find($this->prospectoId);
        if (!$prospecto) {
            Notification::make()->title('Prospecto no encontrado')->danger()->send();
            $this->show = false;
            return;
        }

        // Si el usuario marcó crear próxima tarea, validar campos mínimos
        if ($this->crear_proxima_tarea) {
            if (empty($this->proxima_forma_contacto_id) || empty($this->proxima_fecha)) {
                Notification::make()->title('Faltan datos de la próxima tarea')->danger()->send();
                return;
            }
        }

        // Regla empresarial: no permitir retroceso
        if ($this->respuesta !== 'efectiva' && (int)$prospecto->tipo_gestion_id === 3) {
            Notification::make()
                ->title('Acción no permitida')
                ->body('No se puede retroceder de Contactados a Por Contactar')
                ->danger()
                ->send();
            return;
        }

        try {
            // Crear tarea actual
            $tarea = Tarea::create([
                'prospecto_id' => $prospecto->id,
                'forma_contacto_id' => $this->forma_contacto_id,
                'fecha_realizar' => $this->fecha_realizar,
                'hora' => $this->hora,
                'nota' => $this->comentario ?? null,
                'nivel_interes_id' => $this->nivel_interes_id,
                'usuario_asignado_id' => auth()->id(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Actualizar tipo_gestion según respuesta
            if ($this->respuesta === 'efectiva') {
                $prospecto->update(['tipo_gestion_id' => 3]);
            } elseif ((int)$prospecto->tipo_gestion_id === 1) {
                $prospecto->update(['tipo_gestion_id' => 2]);
            }

            // Crear próxima tarea (opcional)
            if ($this->crear_proxima_tarea && $this->proxima_fecha && $this->proxima_forma_contacto_id) {
                Tarea::create([
                    'prospecto_id' => $prospecto->id,
                    'tarea_padre_id' => $tarea->id,
                    'forma_contacto_id' => $this->proxima_forma_contacto_id,
                    'fecha_realizar' => $this->proxima_fecha,
                    'hora' => $this->proxima_hora ?? '09:00',
                    'nota' => $this->proxima_comentario ?? null,
                    'nivel_interes_id' => $this->nivel_interes_id, // reutilizamos nivel
                    'usuario_asignado_id' => $this->proxima_usuario_asignado_id ?? auth()->id(),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            Notification::make()->title('Tarea registrada correctamente')->success()->send();

            // Cerrar modal y limpiar formulario
            $this->show = false;
            $this->resetForm();

            // Emitir evento para refrescar tablas en otras vistas/recursos
            $this->dispatch('refreshTable');
            $this->dispatch('tareaCreada');

        } catch (\Exception $e) {
            Notification::make()->title('Error al guardar la tarea')->danger()->body($e->getMessage())->send();
            throw $e;
        }
    }

    protected function resetForm()
    {
        $this->forma_contacto_id = null;
        $this->fecha_realizar = now()->toDateString();
        $this->hora = now()->format('H:i');
        $this->respuesta = null;
        $this->comentario = null;
        $this->nivel_interes_id = null;

        $this->crear_proxima_tarea = false;
        $this->proxima_usuario_asignado_id = auth()->id();
        $this->proxima_forma_contacto_id = null;
        $this->proxima_fecha = now()->addDay()->toDateString();
        $this->proxima_hora = '09:00';
        $this->proxima_comentario = null;
    }

    public function render()
    {
        return view('livewire.realizar-accion-modal');
    }
}
