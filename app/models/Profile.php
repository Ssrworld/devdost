<?php
// app/Models/Profile.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'tagline',
        'bio',
        'skills',
        'location',
        'website_url',
        'github_url',
    ];

    /**
     * हर प्रोफाइल एक यूजर की होती है।
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}