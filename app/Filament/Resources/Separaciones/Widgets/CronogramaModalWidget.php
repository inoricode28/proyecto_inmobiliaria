<?php

namespace App\Filament\Resources\Separaciones\Widgets;

use Filament\Widgets\Widget;

class CronogramaModalWidget extends Widget
{
    protected static string $view = 'filament.components.cronograma-modal-complete';
    
    protected int|string|array $columnSpan = 'full';
}