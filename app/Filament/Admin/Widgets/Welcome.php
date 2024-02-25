<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class Welcome extends Widget
{
    protected static string $view = 'filament.admin.widgets.welcome';

    protected int|string|array $columnSpan = 'full';
}
