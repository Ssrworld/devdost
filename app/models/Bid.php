<?php
// app/Models/Bid.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $table = 'bids';

    protected $fillable = [
        'project_id',
        'developer_id',
        'bid_amount',
        'delivery_time',
        'status',
    ];

    /**
     * हर बिड एक प्रोजेक्ट से संबंधित है
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * हर बिड एक डेवलपर (यूजर) से संबंधित है
     */
    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}