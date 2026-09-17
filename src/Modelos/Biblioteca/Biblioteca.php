<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Biblioteca;

use App\Biblioteca\Modelos\Livros\Livro;

class Biblioteca
{
    public function __construct(
        private readonly string $nome,
        private readonly string $cnpj,
    ){
    }

    public function nomeBiblioteca(): string
    {
        return $this->nome;
    }

    public function cnpjBiblioteca(): string
    {
        return $this->cnpj;
    }

    public function __toString(): string
    {
        return "{$this->nomeBiblioteca()} (CNPJ: {$this->cnpjBiblioteca()})";
    }
}