<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'fuente',
        'producto_interes',
        'presupuesto',
    ];

    protected $casts = [
        'presupuesto' => 'float',
    ];

    public const FUENTES = ['instagram', 'facebook', 'landing_page', 'referido', 'otro'];
}
