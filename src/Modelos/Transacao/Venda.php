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
}