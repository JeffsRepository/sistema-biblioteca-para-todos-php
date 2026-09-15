<?php

namespace App\Biblioteca\Repository\Livro;

use App\Biblioteca\Repository\Livro\LivroRepositoryInterface;
use App\Biblioteca\Modelos\Livros\Livro;
use Override;

class InMemoryLivroRepository implements LivroRepositoryInterface
{
    /** @var Livro[] */
    private array $livros = [];
    private int $indice = 0;

    #[Override]
    public function adicionar(Livro $livro): void
    {
        if($this->verificaLivroNoArray($livro))
        {

            $livroVerificadoNoArray = $this->livros[$this->indice];

            $quantidadeLivroVerificadoNoArray = $livroVerificadoNoArray->quantidade();
            $quantidadeDoOutroLivro = $livro->quantidade();
            $somaTotalLivros = (int) $quantidadeLivroVerificadoNoArray + $quantidadeDoOutroLivro;

            $livro = $livroVerificadoNoArray;
            $livro->setQuantidadeLivro($somaTotalLivros);
            
            $this->indice = 0;
            
            return;
        }

        $this->livros[] = $livro;
    }

    #[Override]
    public function remover(string $isbn): void
    {
        foreach ($this->livros as $index => $livro) {
            if ($livro->isbnLivro() === $isbn) {
                unset($this->livros[$index]);
                $this->livros = array_values($this->livros);
                break;
            }
        }
    }

    #[Override]
    public function buscarPorIsbn(string $isbn): ?Livro
    {
        foreach ($this->livros as $livro) {
            if ($livro->isbnLivro() === $isbn) {
                return $livro;
                break;
            }
        }

        return null;
    }

    #[Override]
    public function todos(): array
    {
        return $this->livros;
    }

    public function verificaLivroNoArray(Livro $liv): bool
    {
        foreach($this->livros as $index => $livro) {
            if($livro->isbnLivro() === $liv->isbnLivro()) {
                $this->indice = $index;
                return true;
            }
        }

        return false;
    }

    public function estoqueLivro(Livro $livro): int
    {
        $isbn = $livro->isbnLivro();

        return array_reduce($this->livros, function ($subtotal, $livro) use ($isbn) {
            if ($livro->isbnLivro() === $isbn) {
                return $subtotal + $livro->quantidade();
            }
            return $subtotal;
        }, 0);
    }

    public function setQuantidadeLivro(Livro $livro, int $qtd): void
    {
        foreach ($this->livros as $livroPercorrivel) {
            if ($livroPercorrivel->isbnLivro() === $livro->isbnLivro()) {
                $livroPercorrivel->setQuantidadeLivro($qtd);
                break;
            }
        }
    }
}