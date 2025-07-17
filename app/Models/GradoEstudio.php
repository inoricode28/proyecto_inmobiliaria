<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoEstudio extends Model
{
    protected $table = 'grados_estudio';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}
