<?php

namespace App\Biblioteca\Modelos\Usuario;

use DomainException;

class Cpf
{
    private string $cpf;

    public function __construct(string $cpf)
    {
        $cpf = trim(str_replace(['.', '-'], '', $cpf));

        if (strlen($cpf) !== 11 || !ctype_digit($cpf) ) {
            return throw new DomainException('O cpf precisa ser valido');
        }

        $this->cpf = $cpf;
    }

    public function __toString()
    {
        return $this->cpf;
    }
}