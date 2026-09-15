<?php

namespace App\Biblioteca\Modelos\Usuario;

use DateTimeImmutable;
use Override;

class Funcionario extends Usuario
{
    #[Override]
    public function permissoes(): array
    {
        return [

        ];
    }
}