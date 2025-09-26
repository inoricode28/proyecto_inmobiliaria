<?php

namespace App\Filament\Resources\Separaciones\Widgets;

use Filament\Widgets\Widget;

class PagoSeparacionModalWidget extends Widget
{
    protected static string $view = 'filament.components.pago-separacion-modal';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
}