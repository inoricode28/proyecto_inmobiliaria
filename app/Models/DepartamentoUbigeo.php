<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartamentoUbigeo extends Model
{
    public $timestamps = false;

    protected $table = 'departamentos_ubigeo';

    protected $fillable = ['codigo', 'nombre'];
}
