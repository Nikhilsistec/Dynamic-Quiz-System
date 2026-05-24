<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'type',
        'body',
        'image_path',
        'youtube_url',
        'marks',
        'order',
        'correct_value',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'marks'    => 'integer',
        'order'    => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_url) {
            return null;
        }

        preg_match(
            '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/',
            $this->youtube_url,
            $matches
        );

        return isset($matches[1])
            ? "https://www.youtube.com/embed/{$matches[1]}"
            : null;
    }
}
