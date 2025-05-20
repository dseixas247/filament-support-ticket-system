<?php

namespace App\Services;

use App\Models\TextMessage;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class TextMessageService
{
    public static function sendMessage(array $data, Collection $records)
    {
        $textMessages = collect([]);

        $records->map(function($record) use ($data, $textMessages)
        {
            $textMessage = self::sendTextMessage($record, $data);

            $textMessages->push($textMessage);
        });

        TextMessage::insert($textMessages->toArray());
    }

    public static function sendTextMessage(User $record, array $data)
    {
        $message = Str::replace('{name}', $record->name, $data['message']);

        // the message would be sent here

        return [
            'message' => $message,
            'sent_by' => auth()->user()->id,
            'sent_to' => $record->id,
            'status' => TextMessage::STATUS['PENDING'],
            'response' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}