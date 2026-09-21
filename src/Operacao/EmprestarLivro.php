<?php

declare(strict_types=1);

namespace App\Biblioteca\Operacao;

use App\Biblioteca\Exeception\Livros\LivroIndisponivelException;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Transacao\Emprestimo;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Repository\Transacao\TransacaoRepositoryInterface;

/**
 * EmprestarLivro
 * --------------
 * Uma "Operacao" concreta (implementa OperacaoInterface). Ela concentra
 * TUDO que precisa acontecer quando um livro e emprestado:
 *
 *   1. validar que o livro esta disponivel;
 *   2. mudar o estado do livro para EMPRESTADO ("dar baixa" no estoque
 *      disponivel - o livro sai da lista de "disponiveis" ate voltar);
 *   3. adicionar o livro na lista do Cliente (ele passa a "estar com" o
 *      livro);
 *   4. registrar a transacao (Emprestimo), guardando quem emprestou
 *      (o Funcionario responsavel) e para quem (o Cliente).
 *
 * A classe recebe TUDO que precisa pelo construtor (isso se
 * chama "injecao de dependencia"). Ela nao busca nada sozinha por fora -
 * quem monta essa operacao com os dados certos e o Sistema.
 */
class EmprestarLivro implements OperacaoInterface
{
    public function __construct(
        private readonly TransacaoRepositoryInterface $transacoes,
        private readonly Livro $livro,
        private readonly Cliente $cliente,
        private readonly Funcionario $responsavel,
        private readonly \DateTimeImmutable $dataDevolucaoPrevista,
    ){
    }

    public function executar(): void
    {
        if(!$this->livro->estaDisponivel()) {
            throw new LivroIndisponivelException("
                O livro {$this->livro} não está disponivel para emprestimo (estado atual: {$this->livro->getEstado()->label()}
            ");
        }

        // 1) O livro muda de estado
        $this->livro->marcarComoEmprestado();

        // 2) O cliente passa a ter esse livro
        $this->cliente->adicionarLivroEmprestado($this->livro);

        // 3) Fica registrado quem fez o emprestimo e para quem.
        $emprestimo = new Emprestimo(
            $this->livro,
            $this->cliente,
            $this->responsavel,
            $this->dataDevolucaoPrevista
        );

        $this->transacoes->registrar($emprestimo);
    }
}