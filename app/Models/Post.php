<?php

// gerado automaticamente pela biblioteca

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'published'
    ];

    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'post_tag');
    }
}