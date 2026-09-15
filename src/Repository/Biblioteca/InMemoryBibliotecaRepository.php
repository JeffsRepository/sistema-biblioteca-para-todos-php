<?php

namespace App\Biblioteca\Repository\Biblioteca;

use App\Biblioteca\Modelos\Biblioteca\Biblioteca;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Usuario;
use Override;

class InMemoryBibliotecaRepository implements BibliotecaInterface
{
    private array $emprestimos;
    private Biblioteca $biblioteca;
 
    public function __construct(Biblioteca $biblioteca)
    {
        $this->biblioteca = $biblioteca;
        $this->emprestimos = [];
    }

    #[Override]
    public function emprestarLivroAoUsuario(Livro $livro, Usuario $usuario)
    {
        $this->emprestimos[] = [
            'usuario' => $usuario->nomeUsuario(),
            'livro' => $livro->nomeLivro(),
            'isbn' => $livro->isbnLivro(),
            'dataEmprestimo' => new \DateTimeImmutable('now')
        ];
    }

    #[Override]
    public function devolverLivro()
    {
        throw new \Exception('Not implemented');
    }

    public function carregarEmprestimos(): array
    {
        return $this->emprestimos;
    }
}