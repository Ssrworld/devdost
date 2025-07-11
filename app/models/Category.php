<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // हमारी 'categories' टेबल में timestamps नहीं हैं

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The products that belong to the category.
     * यह एक 'many-to-many' संबंध परिभाषित करता है।
     */
    public function products()
    {
        // एक कैटेगरी का कई प्रोडक्ट्स से संबंध हो सकता है,
        // और यह 'category_product' पिवट टेबल के माध्यम से जुड़ा है।
        return $this->belongsToMany(Product::class, 'category_product');
    }
}