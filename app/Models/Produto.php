<?php

// gerado automaticamente pela biblioteca

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'in_stock'
    ];
}