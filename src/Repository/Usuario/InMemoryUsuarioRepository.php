<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Usuario;

use App\Biblioteca\Modelos\Usuario\Usuario;
use Override;

class InMemoryUsuarioRepository implements UsuarioRepositoryInterface
{
    /** @var array<string, Usuario> */
    private array $usuarios = [];

    #[Override]
    public function adicionar(Usuario $usuario): void
    {
        if (isset($this->usuarios[$usuario->cpf()])) {
            throw new \DomainException("Usuario já cadastrado com esse cpf {$usuario->cpf()}.");
        }

        $this->usuarios[$usuario->cpf()] = $usuario;
    }

    #[Override]
    public function buscarPorCpf(string $cpf): ?Usuario
    {
        return $this->usuarios[$cpf] ?? null;
    }

    #[Override]
    public function todos(): array
    {
        return array_values($this->usuarios);
    }
}