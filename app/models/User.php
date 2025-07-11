<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'user_type',
        'avatar', // avatar को भी fillable में जोड़ना एक अच्छी प्रैक्टिस है
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
    
    /**
     * Get the projects posted by the user (as a client).
     */
    public function projectsAsClient()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    /**
     * Get the projects assigned to the user (as a developer).
     */
    public function projectsAsDeveloper()
    {
        return $this->hasMany(Project::class, 'developer_id');
    }

    /**
     * Get the bids placed by the user (as a developer).
     */
    public function bids()
    {
        return $this->hasMany(Bid::class, 'developer_id');
    }

    /**
     * Get all reviews received by the user.
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Get all reviews given by the user.
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get all notifications for the user.
     * (एक यूजर के पास कई नोटिफिकेशन्स हो सकती हैं)
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id')->latest(); // सबसे नई नोटिफिकेशन सबसे ऊपर
    }
}