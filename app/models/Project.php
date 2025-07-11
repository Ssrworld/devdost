<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'developer_id', // इसे भी fillable में जोड़ना जरूरी है
        'accepted_bid_id', // इसे भी fillable में जोड़ना जरूरी है
        'title',
        'description',
        'budget',
        'status',
    ];

    /**
     * Get the user (client) who owns the project.
     * बताता है कि हर प्रोजेक्ट एक यूजर का है।
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the bids for the project.
     * बताता है कि एक प्रोजेक्ट में कई बिड्स हो सकती हैं।
     */
    public function bids()
    {
        return $this->hasMany(Bid::class, 'project_id');
    }

    /**
     * Get the developer to whom the project was awarded.
     * बताता है कि प्रोजेक्ट किस डेवलपर (जो एक यूजर भी है) को दिया गया है।
     */
    public function awardedDeveloper()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}