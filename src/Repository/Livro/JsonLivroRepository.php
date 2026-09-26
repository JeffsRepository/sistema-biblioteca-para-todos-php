<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Livro;

use App\Biblioteca\Modelos\Livros\EstadoLivro;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Repository\ArmazenamentoJsonAbstrato;
use App\Biblioteca\Repository\Livro\LivroRepositoryInterface;

class JsonLivroRepository extends ArmazenamentoJsonAbstrato implements LivroRepositoryInterface
{
    public function adicionar(Livro $livro): void
    {
        $registros = $this->lerTodosOsRegistros();

        foreach($registros as $registro)
        {
            if($registro['isbn'] === $livro->isbnLivro())
            {
                throw new \DomainException("O livro {$livro->isbnLivro()} conta no arquivo de livros.");
            }
        }

        $registros[] = $livro->toArray();
        $this->salvarTodosOsRegistros($registros);
    }

    /**
     * Retorna null se nao encontrar - por isso o "?Livro".
     */
    public function buscarPorIsbn(string $isbn): ?Livro
    {
        foreach($this->lerTodosOsRegistros() as $registro) {

            if($registro['isbn'] === $isbn) {
                return $this->converterEmLivro($registro);
            }
        }

        return null;
    }

    public function remover(string $isbn): void
    {
        $registros = array_values(array_filter(
            $this->lerTodosOsRegistros(), 
            fn(array $registro) => $registro['isbn'] !== $isbn
        ));

        $this->salvarTodosOsRegistros($registros);
    }

    /** @var Livro[] */
    public function todos(): array
    {
        return array_map(
            fn(array $registro) => $this->converterEmLivro($registro),
            $this->lerTodosOsRegistros()
        );
    }

    /** @return Livro[] */
    public function disponiveis(): array
    {
        return array_values(array_filter(
            $this->todos(),
            fn(Livro $livro) => $livro->estaDisponivel()
        ));
    }

    /**
     * Metodo proprio da classe JsonLivroRepository
     * para transformar todos os arrays de Livro, em array de
     * Objeto Livro.
     */
    private function converterEmLivro(array $registro): Livro
    {
        return Livro::reconstruir(
            isbn: $registro['isbn'],
            nome: $registro['nome'],
            autor: $registro['autor'],
            estado: EstadoLivro::from($registro['estado']),
        );
    }
}