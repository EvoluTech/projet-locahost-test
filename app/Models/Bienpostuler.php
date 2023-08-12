<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bienpostuler extends Model
{
    use HasFactory;

    protected $table = 'Detailsbienspostuler';
    protected $primaryKey = 'id_detailsbienspostuler';
    public $timestamps = false;

    protected $fillable = [
        'nombre_vue_detailsbienspostuler',
        'signalisation_detailsbienspostuler',
        'status_detailsbienspostuler',
    ];
    
}
