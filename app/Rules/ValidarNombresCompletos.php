<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Prospecto;
use Closure;

class ValidarNombresCompletos implements Rule
{
    protected $get;

    public function __construct($get)
    {
        $this->get = $get;
    }

    public function passes($attribute, $value)
    {
        // Si el valor está vacío, no validamos
        if (empty($value)) {
            return true;
        }

        // Obtener los datos del formulario
        $get = $this->get;
        $nombres = trim($value);
        $apePaterno = trim($get('ape_paterno') ?? '');
        $apeMaterno = trim($get('ape_materno') ?? '');

        // Solo validar si tenemos nombres y apellido paterno
        if (empty($nombres) || empty($apePaterno)) {
            return true;
        }

        // Crear el nombre completo para comparar
        $nombreCompleto = $nombres . ' ' . $apePaterno;
        if (!empty($apeMaterno)) {
            $nombreCompleto .= ' ' . $apeMaterno;
        }

        // Buscar si existe la combinación exacta en la base de datos
        $existe = Prospecto::whereRaw("CONCAT(TRIM(nombres), ' ', TRIM(ape_paterno), CASE WHEN ape_materno IS NOT NULL AND ape_materno != '' THEN CONCAT(' ', TRIM(ape_materno)) ELSE '' END) = ?", [$nombreCompleto])
            ->exists();

        return !$existe;
    }

    public function message()
    {
        return 'Ya existe un prospecto registrado con estos nombres y apellidos.';
    }
}
