<?php
namespace App\Exceptions;

class NotFoundException extends AppException
{
    public function __construct(string $message = 'Recurso no encontrado', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
