<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id', 'body', 'is_read'];

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }
}