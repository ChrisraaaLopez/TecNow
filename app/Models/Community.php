<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Community extends Model
{
    protected $fillable = ['name', 'icon', 'description', 'tipo', 'slug', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function isAvisos(): bool
    {
        return $this->tipo === 'avisos';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
