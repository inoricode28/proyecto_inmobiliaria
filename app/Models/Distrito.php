<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    public $timestamps = false;

    protected $fillable = ['codigo', 'nombre', 'provincia_id'];
}
