<?php
namespace App\Helpers;

class ValidationHelper
{
    public static function required(array $data, array $fields): ?string
    {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                return "El campo '{$field}' es obligatorio.";
            }
        }
        return null;
    }

    public static function minLength(string $value, int $min, string $label = 'Valor'): ?string
    {
        if (strlen($value) < $min) {
            return "{$label} debe tener al menos {$min} caracteres.";
        }
        return null;
    }

    public static function inArray(mixed $value, array $allowed, string $label = 'Valor'): ?string
    {
        if (!in_array($value, $allowed, true)) {
            return "{$label} no es válido.";
        }
        return null;
    }

    public static function positiveInt(mixed $value, string $label = 'Valor'): ?string
    {
        if ((int) $value <= 0) {
            return "{$label} debe ser un número positivo.";
        }
        return null;
    }
}
