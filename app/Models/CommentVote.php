<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    //
    protected $fillable = ['user_id', 'comment_id', 'vote'];

    protected $primaryKey = null;
    public $incrementing = false;
}
