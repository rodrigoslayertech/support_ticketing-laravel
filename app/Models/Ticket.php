<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Campos do registro que podem ser preenchidos em massa dentro do Ticket.
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file',
        'status'
    ];

    public function close ()
    {
        $this->status = 'closed';
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}
