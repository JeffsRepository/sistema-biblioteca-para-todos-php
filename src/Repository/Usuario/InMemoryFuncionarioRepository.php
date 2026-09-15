<?php

namespace App\Biblioteca\Repository\Usuario;

use App\Biblioteca\Modelos\Usuario\Cpf;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Modelos\Usuario\Usuario;
use Override;

class InMemoryFuncionarioRepository extends UsuarioRepositoryAbstract
{

    #[Override]
    public function criaUsuario(Usuario $usuario): Usuario
    {
        return new Funcionario(
            $usuario->nomeUsuario(),
            new \DateTimeImmutable($usuario->dataNascimento()),
            new Cpf($usuario->cpf()),
            $usuario->endereco()
        );
    }

    #[Override]
    protected function getTipoUsuario(): string
    {
        return Funcionario::class;
    }
}