<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Transacao;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;

abstract class Transacao
{
    private readonly \DateTimeImmutable $data;

    public function __construct(
        private readonly Livro $livro,
        private readonly Cliente $cliente,
        private readonly Funcionario $responsavel,
    ){
        $this->data = new \DateTimeImmutable();
    }

    public function getLivro(): Livro
    {
        return $this->livro;
    }

    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    public function getResponsavel(): Funcionario
    {
        return $this->responsavel;
    }

    public function getData(): \DateTimeImmutable
    {
        return $this->data;
    }

    abstract public function resumo(): string;
}