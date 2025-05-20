<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TextMessageResource\Pages;
use App\Filament\Resources\TextMessageResource\RelationManagers;
use App\Models\TextMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TextMessageResource extends Resource
{
    protected static ?string $model = TextMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('message')
                    ->label('Message')
                    ->searchable(),

                TextColumn::make('sentBy.name')
                    ->label('Sent By')
                    ->searchable(),

                TextColumn::make('sentTo.name')
                    ->label('Sent To')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'danger' => self::$model::STATUS['FAILED'],
                        'warning' => self::$model::STATUS['PENDING'],
                        'success' => self::$model::STATUS['SUCCESS'],
                    ]),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(self::$model::STATUS)
                    ->placeholder('All'),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTextMessages::route('/'),
        ];
    }
}
