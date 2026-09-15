<?php

namespace App\Biblioteca\Modelos\Biblioteca;

use App\Biblioteca\Modelos\Livros\Livro;

class Biblioteca
{
    public function __construct(
        private string $nome,
        private string $cnpj,
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
}