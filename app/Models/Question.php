<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'exam_id',
        'question_text',
        'image',
        'options',
        'correct_answer',
        'points',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get exam that owns this question
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
