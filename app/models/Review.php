<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $fillable = ['project_id', 'reviewer_id', 'reviewee_id', 'rating', 'comment', 'review_type'];

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function reviewee() {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
    public function project() {
        return $this->belongsTo(Project::class);
    }
}