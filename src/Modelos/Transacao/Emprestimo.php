<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Transacao;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Repository\Transacao\TransacaoRepositoryInterface;
use Override;

/**
 * Emprestimo
 * ----------
 * Uma Transacao especifica: representa o ato de emprestar um livro.
 * Guarda a data prevista de devolucao e, quando o livro volta, a data
 * REAL em que isso aconteceu ($dataDevolucaoReal fica null ate la).
 *
 * "Podendo acrescentar quando e devolvido" (do seu pedido) e exatamente
 * o metodo registrarDevolucao() abaixo.
 */
class Emprestimo extends Transacao
{
    private ?\DateTimeImmutable $dataDevolucaoReal = null;

    public function __construct(
        Livro $livro, 
        Cliente $cliente, 
        Funcionario $responsavel,
        private readonly \DateTimeImmutable $dataDevolucaoPrevista
    ){
        return parent::__construct($livro, $cliente, $responsavel);
    }

    public function getDataDevolucaoPrevista(): \DateTimeImmutable
    {
        return $this->dataDevolucaoPrevista;
    }

    public function getDataDevolucaoReal(): ?\DateTimeImmutable
    {
        return $this->dataDevolucaoReal;
    }

    #[Override]
    public function getTipo(): string
    {
        return Emprestimo::class;
    }

    public function estaAtivo(): bool
    {
        /**Ativo = ainda nao foi devolvido */
        return $this->dataDevolucaoReal === null;
    }

    public function toArray(): array
    {
        return [
            'tipo' => $this->getTipo(),
            'estado' => $this->estaAtivo(),
            'livro' => $this->getLivro(),
            'cliente' => $this->getCliente(),
            'reponsavel' => $this->getResponsavel(),
            'data_devolucao_prevista' => $this->getDataDevolucaoPrevista(),
            'data_devolucao_real' => $this->getDataDevolucaoReal(),
        ];
        
    }

    /**
     * Chamado pela operacao DevolverLivro (src/Operacao/DevolverLivro.php)
     * no momento em que o cliente devolve o livro.
     */
    public function registrarDevolucao(): void
    {
        if (!$this->estaAtivo()) {
            throw new \DomainException(
                "O emprestimo já foi devolvido anteriormente."
            );
        }

        $this->dataDevolucaoReal = new \DateTimeImmutable();
    }

    #[Override]
    public function resumo(): string
    {
        $status = $this->estaAtivo()
        ? "Previsto para: {$this->dataDevolucaoPrevista->format('d/m/Y')}"
        : "Devolvido em: {$this->dataDevolucaoReal->format('d/m/Y')}";

        return sprintf(
            "Emprestimo: %s para %s, feito por %s (%s)",
            $this->getCliente()->nomeUsuario(),
            $this->getLivro()->nomeLivro(),
            $this->getResponsavel()->nomeUsuario(),
            $status
        );
    }

    public static function reconstroiEmprestimo(
        Livro $livro, 
        Cliente $cliente, 
        Funcionario $responsavel,
        \DateTimeImmutable $dataDevolucaoPrevista,
        ?\DateTimeImmutable $dataDevolucaoReal = null
    ): self
    {
        $emprestimo = new self(
            $livro, 
            $cliente, 
            $responsavel, 
            $dataDevolucaoPrevista,
        );

        $emprestimo->dataDevolucaoReal = $dataDevolucaoReal;

        return $emprestimo;
    }
}