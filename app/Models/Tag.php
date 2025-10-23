<?php

// gerado automaticamente pela biblioteca

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color'
    ];

    public function posts()
    {
        return $this->belongsToMany(\App\Models\Post::class, 'post_tag');
    }
}