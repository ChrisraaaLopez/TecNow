<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
  protected $table = 'reportes';

  protected $fillable = [
    'motivo',
    'descripcion',
    'estado',
    'user_id',
    'post_id',
    'comment_id',
    'reported_user_id',
  ];

  public function usuario()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function post()
  {
    return $this->belongsTo(Post::class, 'post_id');
  }

  public function comment()
  {
    return $this->belongsTo(Comment::class, 'comment_id');
  }

  public function reportedUser()
  {
    return $this->belongsTo(User::class, 'reported_user_id');
  }

  public function getTipoAttribute(): string
  {
    return $this->comment_id ? 'comentario' : 'post';
  }
}
