<?php

namespace App\Filament\Widgets;

use App\Models\Role;
use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTickets extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                auth()->user()->hasRole(Role::ROLES['Admin']) ? 
                    Ticket::query() :
                    Ticket::where('assigned_to', auth()->user()->id)
                ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Ticket $record): ?string => $record->description)
                    ->sortable(),

                SelectColumn::make('status')
                    ->options(Ticket::STATUS)
                    ->selectablePlaceholder(false),   

                TextColumn::make('priority')
                    ->badge()
                    ->colors([
                        'danger' => Ticket::PRIORITY['High'],
                        'warning' => Ticket::PRIORITY['Medium'],
                        'success' => Ticket::PRIORITY['Low'],
                    ]),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned to')
                    ->sortable(),
                
                TextColumn::make('assignedBy.name')
                    ->label('Assigned by')
                    ->sortable(),

                TextInputColumn::make('comment')
                    ->label('Comment'),

                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
