<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Usuario;

use App\Biblioteca\Modelos\Usuario\Usuario;

interface UsuarioRepositoryInterface
{
    public function adicionar(Usuario $usuario): void;
    public function buscarPorCpf(string $cpf): ?Usuario;
    public function todos(): array;
}