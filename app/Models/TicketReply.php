<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'reply',
        'file'
    ];

    protected static function booted()
    {
        static::created(function ($ticketReply) {
            $ticket = Ticket::find($ticketReply->ticket_id);
            if ($ticketReply->user && $ticketReply->user->role === 'Collaborator') {
                $ticket->status = 'in_progress';
                $ticket->save();
            }
        });
    }

    public function user ()
    {
        return $this->belongsTo(User::class);
    }
    public function ticket ()
    {
        return $this->belongsTo(Ticket::class);
    }
}
