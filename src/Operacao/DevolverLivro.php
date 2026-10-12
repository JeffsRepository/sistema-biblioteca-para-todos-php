<?php

declare(strict_types=1);

namespace App\Biblioteca\Operacao;

use App\Biblioteca\Exeception\Livros\LivroIndisponivelException;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Repository\Livro\LivroRepositoryInterface;
use App\Biblioteca\Repository\Transacao\TransacaoRepositoryInterface;
use Exception;
use Override;

/**
 * DevolverLivro
 * -------------
 *   1. encontra o Emprestimo ativo daquele livro (pra saber quem devolveu
 *      e registrar a data real de devolucao);
 *   2. marca o Emprestimo como devolvido;
 *   3. o livro volta a ficar DISPONIVEL;
 *   4. o livro sai da lista "com o cliente".
 */
class DevolverLivro implements OperacaoInterface
{
    public function __construct(
        private readonly TransacaoRepositoryInterface $transacao,
        private readonly LivroRepositoryInterface $livros,
        private readonly Livro $livro,
    ){
    }

    #[Override]
    public function executar(): void
    {
        if($this->livro->estaDisponivel()) {
            throw new LivroIndisponivelException("
                O livro {$this->livro->nomeLivro()} não está emprestado! (Estado livro: {$this->livro->getEstado()->label()})
            ");
        }

        $emprestimo = $this->transacao->buscarEmprestimoAtivoPorLivro($this->livro);

        if(is_null($emprestimo)) {
            throw new LivroIndisponivelException('Emprestimo não encontrado');
        }

        $emprestimo->registrarDevolucao(); // anotar a data de devolucao e alterar o estado do emprestimo
        $this->livro->marcarComoDevolvido(); // vai se marcar como devolvido
        $emprestimo->getCliente()->removerLivroEmprestado($this->livro); // esse livro vai ser removido do array do cliente
        $this->livros->atualizarEstadoLivroNoArquivo($this->livro); // alterar o estado do livro como disponivel no banco
        
    }
}