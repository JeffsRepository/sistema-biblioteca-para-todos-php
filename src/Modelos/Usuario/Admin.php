<?php

namespace App\Biblioteca\Modelos\Usuario;

use DateTimeImmutable;
use Override;

class Admin extends Usuario
{
    #[Override]
    public function permissoes(): array
    {
        return [
            
        ];
    }
}