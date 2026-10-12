<?php

namespace App\Biblioteca\Modelos\Usuario;

use DomainException;
use Override;

class Cpf implements \JsonSerializable
{
    private string $cpf;

    public function __construct(string $cpf)
    {
        $cpf = trim(str_replace(['.', '-'], '', $cpf));

        if (strlen($cpf) !== 11 || !ctype_digit($cpf) ) {
            throw new DomainException('O cpf precisa ser valido');
        }

        $this->cpf = $cpf;
    }

    public function __toString()
    {
        return $this->cpf;
    }

    #[Override]
    public function jsonSerialize(): string
    {
        return $this->cpf;
    }
}