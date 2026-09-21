<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Transacao;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Transacao\Emprestimo;
use App\Biblioteca\Modelos\Transacao\Transacao;
use Override;

class InMemoryRepository implements TransacaoRepositoryInterface
{
    /** @var Transacao[] */
    private array $transacoes = [];

    #[Override]
    public function registrar(Transacao $transacao): void
    {
        $this->transacoes[] = $transacao;
    }

    #[Override]
    public function todas(): array
    {
        return $this->transacoes;
    }

    #[Override]
    public function buscarEmprestimoAtivoPorLivro(Livro $livro): ?Emprestimo
    {
        /** @var Emprestimo */
        foreach($this->transacoes as $transacao) {
            if(
                $transacao instanceof Emprestimo 
                && $transacao->getLivro()->isbnLivro() === $livro->isbnLivro()
                && $transacao->estaAtivo()
            ) {
                return $transacao;
            }
        }

        return null;
    }
}