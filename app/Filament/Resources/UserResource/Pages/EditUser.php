<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Dom\Text;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updatePassword')
                ->label('Change Password')
                ->form([
                    TextInput::make('password')
                        ->label('New Password')
                        ->required()
                        ->password()
                        ->confirmed(),

                    TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'password' => bcrypt($data['password']),
                    ]);

                    Notification::make()
                        ->title('Password updated successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}
