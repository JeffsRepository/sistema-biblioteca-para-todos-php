<?php

declare(strict_types=1);

namespace App\Repository;

use App\Biblioteca\Modelos\Livros\Livro;

/**
 * LivroRepositoryInterface
 * -------------------------
 * O Sistema (src/Sistema/Sistema.php) vai
 * depender DESTA INTERFACE, e nao da classe concreta InMemoryLivroRepository.
 * Ou seja: o Sistema so sabe que existe "algo" capaz de adicionar, buscar e
 * listar livros - ele nao sabe (nem precisa saber) SE isso e um array em
 * memoria, um arquivo .json ou um banco de dados.
 */
interface LivroRepositoryInterface
{
    public function adicionar(Livro $livro): void;

    /**
     * Retorna null se nao encontrar o "?Livro".
     */
    public function buscarPorIsbn(string $isbn): ?Livro;

    public function remover(string $isbn): void;

    /** @var Livro[] */
    public function todos(): array;

    /** @return Livro[] */
    public function disponiveis(): array;
}