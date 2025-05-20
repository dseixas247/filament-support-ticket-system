<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Filament\Resources\TicketResource\RelationManagers\CategoriesRelationManager;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ticket System';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                TextInput::make('title')
                    ->autofocus()
                    ->required(),

                Textarea::make('description')
                    ->rows(3),
                
                Select::make('status')
                    ->options(self::$model::STATUS)
                    ->required(),

                Select::make('priority')
                    ->options(self::$model::PRIORITY)
                    ->required(),

                Select::make('assigned_to')
                    ->options(
                        User::whereHas('roles', function (Builder $query) {
                            $query->where('name', Role::ROLES['Agent']);
                        })->pluck('name', 'id')->toArray()
                    )
                    ->required(),

                FileUpload::make('attachment')
                    ->label('Attachment')
                    ->openable()
                    ->maxSize(1024)
                    ->acceptedFileTypes(['application/pdf']),
                
                Textarea::make('comment')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => 
                auth()->user()->hasRole(Role::ROLES['Admin']) ? 
                $query : $query->where('assigned_to', auth()->user()->id))

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
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
