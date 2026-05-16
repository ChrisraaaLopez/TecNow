<?php

/*
 * Modelo para gestionar la información de los modelos.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
  protected $fillable = [
    'user_id',
    'title',
    'content',
    'image',
    'hot_score',
    'fijada',
    'shared_from_post_id',
  ];

  protected $casts = [
    'hot_score' => 'float',
  ];

  public function communities(): BelongsToMany
  {
    return $this->belongsToMany(Community::class);
  }

  /** El post original del que fue compartido (si aplica) */
  public function sharedFrom(): BelongsTo
  {
    return $this->belongsTo(Post::class, 'shared_from_post_id');
  }

  /** Posts que comparten este */
  public function shares(): HasMany
  {
    return $this->hasMany(Post::class, 'shared_from_post_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  // Código experimental

  // Función para Karma
  public function votes(): HasMany
  {
    return $this->hasMany(PostVote::class);
  }

  // Karma total calculado
  public function getKarmaAttribute(): int
  {
    return $this->votes()->sum('vote');
  }

  // Voto del usuario autenticado sobre este post
  public function userVote(): ?int
  {
    $vote = $this->votes()->where('user_id', Auth::id())->first();
    return $vote?->vote;
  }
  public function updateHotScore(): void
  {
    $votes = $this->votes()->sum('vote');
    $hours = max(0, $this->created_at->diffInSeconds(now()) / 3600);
    $score = $hours === 0 ? $votes / pow(2, 1.5) : $votes / pow($hours + 2, 1.5);

    $this->hot_score = $score;
    $this->saveQuietly();
  }

  // Código experimental - Comentarios.
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
