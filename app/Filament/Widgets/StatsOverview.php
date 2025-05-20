<?php

namespace App\Filament\Widgets;

use App\Filament\CustomWidgets\StatFiltered;
use App\Models\Category;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Open Tickets', Ticket::where('status', 'open')->count())
                ->description('Total number of open tickets'),

            Stat::make('Total Tickets', Ticket::count())
                ->description('Total number of tickets created'),

            Stat::make('Total Agents', User::whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLES['Agent']);
            })->count())
                ->description('Total number of agents'),
        ];
    }
}
