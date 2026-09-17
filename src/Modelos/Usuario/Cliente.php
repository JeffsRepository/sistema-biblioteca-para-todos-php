<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Usuario;

use App\Biblioteca\Modelos\Livros\Livro;
use Override;

/**
 * Cliente
 * -------
 * Um dos tipos concretos de Usuario. E quem PEGA livro emprestado ou COMPRA
 * livro.
 *
 * Guardamos aqui, dentro do proprio Cliente, quais livros estao com ele -
 * Esta em duas listas porque sao coisas diferentes:
 *   - $livrosEmprestados: livros que ele tem que devolver um dia;
 *   - $livrosComprados: livros que agora sao dele.
 *
 * Repare que quem CHAMA os metodos abaixo (adicionarLivroEmprestado etc.)
 * e a camada de Operacao (src/Operacao/), nunca o menu (CLI) diretamente.
 * Isso mantem a regra de negocio centralizada em um unico lugar.
 */
class Cliente extends Usuario
{
    /** @var Livro[] */
    private array $livrosEmprestados = [];

    /** @var Livro[] */
    private array $livrosComprados = [];

    #[Override]
    public function tipo(): string
    {
        return 'Cliente';
    }

    public function adicionarLivroEmprestado(Livro $livro): void
    {
        $this->livrosEmprestados[$livro->isbnLivro()] = $livro;
    }

    public function removerLivroEmprestado(Livro $livro): void
    {
        unset($this->livrosEmprestados[$livro->isbnLivro()]);
    }

    public function adicionarLivroComprado(Livro $livro): void
    {
        $this->livrosComprados[$livro->isbnLivro()] = $livro;
    }

    /** @var Livro[] */
    public function getLivrosEmprestados(): array
    {
        return array_values($this->livrosEmprestados);
    }

    /** @var Livro[] */
    public function getLivrosComprados(): array
    {
        return array_values($this->livrosEmprestados);
    }

    #[Override]
    public function permissoes(): array
    {
        return [];
    }
}