<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    protected $table = 'exams';

    protected $fillable = [
        'title',
        'description',
        'duration',
        'total_questions',
    ];

    /**
     * Get questions for this exam
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get users registered for this exam
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'exam_user')
            ->withPivot('answers', 'score', 'time_spent', 'started_at', 'completed_at', 'extra_time')
            ->withTimestamps();
    }
}
