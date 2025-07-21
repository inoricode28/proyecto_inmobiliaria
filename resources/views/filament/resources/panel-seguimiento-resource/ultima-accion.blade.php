<div class="space-y-2 text-sm text-gray-700">
    <div><strong>Forma de contacto:</strong> {{ $tarea->formaContacto->nombre ?? '-' }}</div>
    <div><strong>Fecha a realizar:</strong> {{ $tarea->fecha_realizar?->format('d/m/Y') }}</div>
    <div><strong>Hora:</strong> {{ $tarea->hora }}</div>
    <div><strong>Nivel de interés:</strong> {{ $tarea->nivelInteres->nombre ?? '-' }}</div>
    <div><strong>Nota:</strong> {{ $tarea->nota }}</div>
</div>
