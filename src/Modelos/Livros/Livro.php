<?php

namespace App\Biblioteca\Modelos\Livros;

use BadFunctionCallException;

class Livro
{
    private int $quantidade;

    public function __construct(
        private string $nome,
        private string $autor,
        private Isbn $isbn,
    ){
        $this->quantidade = 1;
    }

    public function nomeLivro(): string
    {
        return $this->nome;
    }

    public function autorLivro(): string
    {
        return $this->autor;
    }

    public function isbnLivro(): string
    {
        return $this->isbn;
    }

    public function quantidade(): int
    {
        return $this->quantidade;
    }

    public function setQuantidadeLivro(int $quantidade): void
    {
        $this->quantidade = $quantidade;
    }

}