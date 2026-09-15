<?php

namespace App\Biblioteca\Repository\Usuario;

use App\Biblioteca\Modelos\Usuario\Admin;
use Override;

class InMemoryAdminRepository extends UsuarioRepositoryAbstract
{
    #[Override]
    protected function getTipoUsuario(): string
    {
        return Admin::class;
    }
}