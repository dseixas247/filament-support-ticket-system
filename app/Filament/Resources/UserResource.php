<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use App\Services\TextMessageService;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->autofocus(),
                
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true),
                
                TextInput::make('password')
                    ->required()
                    ->password()
                    ->confirmed()
                    ->hiddenOn('edit'),

                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->hiddenOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->placeholder('All'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('sendBulkSms')
                    ->label('Send Message')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->modalSubmitActionLabel('Send')
                    ->deselectRecordsAfterCompletion()
                    ->form([
                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(3)
                            ->placeholder('Enter your message here'),
                    ])
                    ->action(function(array $data, Collection $records){
                        TextMessageService::sendMessage($data, $records);

                        Notification::make()
                            ->title('Message Sent')
                            ->success()
                            ->body('Message sent successfully to ' . $records->count() . ' users.')
                            ->send();
                    }),

                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
