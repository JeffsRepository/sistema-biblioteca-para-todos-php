<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Livro;

use App\Biblioteca\Modelos\Livros\Livro;
use Override;

class InMemoryLivroRepository implements LivroRepositoryInterface
{
    /**
     * Usei o ISBN como chave do array (em vez de indices numericos 0,1,2...)
     * porque isso torna buscarPorIsbn() e remover() muito mais rapidos e
     * simples: nao precisa percorrer o array inteiro procurando.
     *
     * @var array<string, Livro>
     */

    /** @var array<string, Livro> */
    private array $livros = [];

    #[Override]
    public function adicionar(Livro $livro): void
    {
        if (isset($this->livros[$livro->isbnLivro()])) {
            throw new \DomainException('Livro ' . $livro->nomeLivro() . ' já cadastrado no array de livros');
        }

        $this->livros[$livro->isbnLivro()] = $livro;
    }

    #[Override]
    public function buscarPorIsbn(string $isbn): ?Livro
    {
        return $this->livros[$isbn] ?? null;
    }

    #[Override]
    public function remover(string $isbn): void
    {
        unset($this->livros[$isbn]);
    }

    #[Override]
    public function todos(): array
    {
        return array_values($this->livros);
    }

    #[Override]
    public function disponiveis(): array
    {
        // array_values reindexa o array (0,1,2...) depois do filtro, pra nao
        // deixar "buracos" nos indices.
        // array_filter percorre a lista e mantem so quem satisfaz a condicao.

        return array_values(
            array_filter($this->livros, fn (Livro $livro) => $livro->estaDisponivel()
        ));
    }
}