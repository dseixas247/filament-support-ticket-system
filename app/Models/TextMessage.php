<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextMessage extends Model
{
    use HasFactory;

    const STATUS = [
        'PENDING' => 'PENDING',
        'SUCCESS' => 'SUCCESS',
        'FAILED' => 'FAILED',
    ];

    protected $fillable = [
        'message',
        'response',
        'sent_to',
        'sent_by',
        'status',
    ];

    public function sentTo()
    {
        return $this->belongsTo(User::class, 'sent_to');
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
