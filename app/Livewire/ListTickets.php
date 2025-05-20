<?php

namespace App\Livewire;

use App\Models\Ticket;
use Filament\Tables\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class ListTickets extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $model = Ticket::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(Ticket::query())

            ->columns([
                TextColumn::make('title')
                    ->description(fn (Ticket $record): ?string => $record->description)
                    ->sortable()
                    ->searchable(),

                SelectColumn::make('status')
                    ->options(self::$model::STATUS)
                    ->selectablePlaceholder(false),   

                TextColumn::make('priority')
                    ->badge()
                    ->colors([
                        'danger' => self::$model::PRIORITY['High'],
                        'warning' => self::$model::PRIORITY['Medium'],
                        'success' => self::$model::PRIORITY['Low'],
                    ]),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned to')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('assignedBy.name')
                    ->label('Assigned by')
                    ->sortable()
                    ->searchable(),

                TextInputColumn::make('comment')
                    ->label('Comment'),

                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('status')
                    ->options(self::$model::STATUS)
                    ->placeholder('All'),
                    
                SelectFilter::make('priority')
                    ->options(self::$model::PRIORITY)
                    ->placeholder('All'),
            ])

            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn(Ticket $ticket) => route('tickets.edit', $ticket)),

                DeleteAction::make(),
            ])
            
            ->headerActions([
               Action::make('create')
                    ->label('Create Ticket')
                    ->url(fn(Ticket $ticket) => route('tickets.create', $ticket)),
            ])

            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.list-tickets');
    }
}
