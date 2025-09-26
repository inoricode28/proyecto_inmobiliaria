<?php

namespace App\Filament\Resources\Separaciones\Widgets;

use Filament\Widgets\Widget;

class CronogramaSFModalWidget extends Widget
{
    protected static string $view = 'filament.components.cronograma-sf-modal';
    
    protected int|string|array $columnSpan = 'full';
}