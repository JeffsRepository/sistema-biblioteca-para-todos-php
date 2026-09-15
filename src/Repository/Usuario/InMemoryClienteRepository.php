<?php

namespace App\Biblioteca\Repository\Usuario;

use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Cpf;
use App\Biblioteca\Modelos\Usuario\Usuario;
use DateTimeImmutable;
use Override;

class InMemoryClienteRepository extends UsuarioRepositoryAbstract
{
    #[Override]
    protected function criaUsuario(Usuario $usuario): Usuario
    {
        return new Cliente(
            $usuario->nomeUsuario(),
            new \DateTimeImmutable($usuario->dataNascimento()),
            new Cpf($usuario->cpf()),
            $usuario->endereco()
        );
    }

    #[Override]
    protected function getTipoUsuario(): string
    {
        return Cliente::class;
    }
}