<?php

namespace App\Biblioteca\Repository\Livro;

use App\Biblioteca\Modelos\Livros\Livro;

interface LivroRepositoryInterface
{
    public function adicionar(Livro $livro): void;
    public function remover(string $isbn): void;
    public function buscarPorIsbn(string $isbn): ?Livro;
    public function estoqueLivro(Livro $livro): int;
    public function verificaLivroNoArray(Livro $liv): bool;
    public function todos(): array;
}