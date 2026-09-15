<?php

namespace App\Biblioteca\Repository\Usuario;

use App\Biblioteca\Modelos\Usuario\Usuario;
use App\Biblioteca\Modelos\Livros\Livro;

abstract class UsuarioRepositoryAbstract implements UsuarioRepositoryInterface
{
    /** @var Usuario[] */
    protected array $usuarios = [];

    protected function validaTipoUsuario(Usuario $usuario): void
    {
        $tipoUsuarioPermitido = $this->getTipoUsuario();

        if (!$usuario instanceof $tipoUsuarioPermitido) {
            throw new \InvalidArgumentException(
                sprintf('Este repositorio só aceita %s. Recebido %s',
                    $tipoUsuarioPermitido,
                    get_class($usuario)
                )
            );
        }
    }

    public function adicionar(Usuario $usuario): void
    {
        $this->validaTipoUsuario($usuario);
        $objetoUsuario = $this->criaUsuario($usuario);
        $this->usuarios[] = $objetoUsuario;
    }

    public function remover(string $nome): void
    {
        foreach ($this->usuarios as $index => $usuario) {
            if ($usuario->nomeUsuario() === $nome) {
                unset($this->usuarios[$index]);
                $this->usuarios = array_values($this->usuarios);
                break;
            }
        }
    }

    public function buscarPorCpf(string $cpf): ?Usuario
    {
        foreach ($this->usuarios as $index => $usuario) {
            if ($usuario->cpf() === $cpf) {
                return $this->usuarios[$index];
                break;
            }
        }

        return null;
    }

    public function todos(): array
    {
        return $this->usuarios;
    }

    abstract protected function getTipoUsuario(): string;
    abstract protected function criaUsuario(Usuario $usuario): Usuario;
}