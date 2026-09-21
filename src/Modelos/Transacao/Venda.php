<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Transacao;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use Override;

/**
 * Venda
 * -----
 * A outra Transacao especifica: representa o ato de vender um livro.
 * So acrescenta o preco em relacao ao que ja vem de Transacao.
 */
class Venda extends Transacao
{
    public function __construct(
        Livro $livro, 
        Cliente $cliente, 
        Funcionario $responsavel,
        private float $preco
    ){
        return parent::__construct($livro, $cliente, $responsavel);
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    #[Override]
    public function getTipo(): string
    {
        return Venda::class;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'tipo' => $this->getTipo(),
            'livro' => $this->getLivro(),
            'cliente' => $this->getCliente(),
            'reponsavel' => $this->getResponsavel(),
            'preco_venda' => $this->preco,
        ];
    }

    #[Override]
    public function resumo(): string
    {
        return sprintf(
            "Venda %s por %s para %s, responsavel %s ",
            $this->getLivro()->nomeLivro(),
            $this->preco,
            $this->getCliente()->nomeUsuario(),
            $this->getResponsavel()->nomeUsuario(),
        );
    }

    public static function reconstroiVenda(array $transacao): self
    {
        $venda = new self(
            livro: $transacao['livro'],
            cliente: $transacao['cliente'],
            responsavel: $transacao['responsavel'],
            preco: $transacao['preco'],
        );

        return $venda;
    }
}