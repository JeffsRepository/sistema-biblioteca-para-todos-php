<?php

declare(strict_types=1);

namespace App\Biblioteca\Operacao;

use App\Biblioteca\Exeception\Livros\LivroIndisponivelException;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Transacao\Venda;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Repository\Transacao\TransacaoRepositoryInterface;
use App\Repository\LivroRepositoryInterface;
use Override;

class VenderLivro implements OperacaoInterface
{
    public function __construct(
        private readonly LivroRepositoryInterface $livros,
        private readonly TransacaoRepositoryInterface $transacoes,
        private readonly Livro $livro,
        private readonly Cliente $cliente,
        private readonly Funcionario $responsavel,
        private readonly float $preco
    ){
    }

    #[Override]
    public function executar(): void
    {
        if(!$this->livro->estaDisponivel()) {
            throw new LivroIndisponivelException("
                O livro {$this->livro->nomeLivro()} não consta como disponível para venda. (Estado do livro: {$this->livro->getEstado()->label()})
            ");
        }

        if($this->preco <= 0) {
            throw new \InvalidArgumentException('Preco invalido');
        }

        $this->livro->marcarComoVendido();

        $this->livros->remover($this->livro->isbnLivro());

        $this->cliente->adicionarLivroComprado($this->livro);

        $venda = new Venda($this->livro, $this->cliente, $this->responsavel, $this->preco);

        $this->transacoes->registrar($venda);
    }
}