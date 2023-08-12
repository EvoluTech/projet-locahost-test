<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailsbienspostuler extends Model
{
    use HasFactory;

    protected $table = 'Bienspostuler';
    protected $primaryKey = 'id_bienspostuler';
    public $timestamps = false;

    protected $fillable = [
        'id_bienspostuler',
        'date_debut_postule',
        'date_fin_postule',
        'prix_biens',
        'prix_par_jour',
        'prix_total_payer',
        'photos_1',
        'photos_2',
        'photos_3',
        'photos_4',
        'etat_biens',
        'description_biens',
        'id_detailsbienspostuler',
        'id_objet',
    ];
    
}
