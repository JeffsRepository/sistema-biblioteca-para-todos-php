<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Transacao;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Transacao\Emprestimo;
use App\Biblioteca\Modelos\Transacao\Transacao;

interface TransacaoRepositoryInterface
{
    public function registrar(Transacao $transacao): void;

    /** @return Transacao[] */
    public function todas(): array;

    /**
     * Procura um emprestimo que ainda esta ATIVO (nao devolvido) para um
     * determinado livro. E usado pela operacao DevolverLivro para saber
     * "qual emprestimo devo marcar como devolvido?".
     */
    public function buscarEmprestimoAtivoPorLivro(Livro $livro): ?Emprestimo;
}