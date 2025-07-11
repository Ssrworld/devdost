<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'file_path',
        'preview_image',
    ];

    /**
     * Get the user that owns the product.
     * 
     * यह एक संबंध (relationship) परिभाषित करता है।
     * इसका मतलब है कि हर प्रोडक्ट का एक मालिक (यूजर) होता है।
     */
    public function user()
    {
        // एक प्रोडक्ट का संबंध 'User' मॉडल से है
        return $this->belongsTo(User::class);
    }

    // ==========================================================
    // >> यहाँ नया फंक्शन जोड़ा गया है <<
    /**
     * The categories that belong to the product.
     *
     * यह एक 'many-to-many' संबंध परिभाषित करता है, जो
     * 'category_product' पिवट टेबल का उपयोग करता है।
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }
    // ==========================================================
}