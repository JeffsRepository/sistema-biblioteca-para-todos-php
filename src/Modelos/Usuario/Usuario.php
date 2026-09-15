<?php

namespace App\Biblioteca\Modelos\Usuario;

use BadFunctionCallException;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class Usuario
{
    protected int $qtdLivroUsuario = 0;

    public function __construct(
        protected string $nome,
        protected \DateTimeImmutable $dataNascimento,
        protected Cpf $cpf,
        protected string $endereco,
    ){
        $this->validarDataNascimento($dataNascimento->format('d/m/Y'));
    }

    public function nomeUsuario(): string
    {
        return $this->nome;
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function idade(): int
    {
        $hoje = new DateTimeImmutable();
        $idade = $hoje->diff($this->dataNascimento)->format('d/m/Y');

        return $idade;
    }

    public function validarDataNascimento(string $data, string $formato = 'd/m/Y'): bool
    {
        $dateTime = DateTimeImmutable::createFromFormat($formato, $data);

        if (!$dateTime || $dateTime->format($formato) != $data) {
            return throw new InvalidArgumentException('Data tem que ser num formato valido');
        }

        $hoje = new DateTimeImmutable('today');

        if ($dateTime > $hoje) {
            return throw new InvalidArgumentException('Data não pode ser maior que hoje');
        }

        $this->dataNascimento = $dateTime;

        return true;
    }

    public function dataNascimento(): string
    {
        return $this->dataNascimento->format('d/m/Y');
    }

    public function endereco(): string
    {
        return $this->endereco;
    }

    public function pegaQuantidadeLivroPorUsuario(): int
    {
        return $this->qtdLivroUsuario;
    }

    public function setQuantidadeLivroUsuario(int $quantidade): void
    {
        $this->qtdLivroUsuario = $quantidade;
    }

    abstract public function permissoes(): array;
}