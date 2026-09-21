<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Transacao;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Transacao\Emprestimo;
use App\Biblioteca\Modelos\Transacao\Transacao;
use App\Biblioteca\Repository\ArmazenamentoJsonAbstrato;
use Override;

class JsonTransacaoRepository extends ArmazenamentoJsonAbstrato implements TransacaoRepositoryInterface
{
    private ?string $tipo = null;

    #[Override]
    public function registrar(Transacao $transacao): void
    {
        $registros = $this->lerTodosOsRegistros();
        $registros[] = $transacao->toArray();
    }

    #[Override]
    public function todas(): array
    {
        return $this->lerTodosOsRegistros();
    }

    #[Override]
    public function buscarEmprestimoAtivoPorLivro(Livro $livro): ?Emprestimo
    {
        $tipo = Emprestimo::class;

        $registros = $this->lerTodosOsRegistros();

        foreach($registros as $transacao) {
            if(
                $transacao['tipo'] === $tipo 
                && $transacao['estado']
                && ($transacao['livro']->isbnLivro()
                    === 
                $livro->isbnLivro())
            ) {
                return $this->converteEmEmprestimo($transacao);
            }
        }

        return null;
    }

    private function converteEmEmprestimo(array $transacao): Emprestimo
    {
        return Emprestimo::reconstroiEmprestimo(
            livro: $transacao['livro'],
            cliente: $transacao['cliente'],
            responsavel: $transacao['responsavel'],
            dataDevolucaoPrevista: $transacao['data_devolucao_prevista'],
            dataDevolucaoReal: $transacao['data_devolucao_real']
        );
    }

    // {no usages}
    private function getTipo(): ?string
    {
        return $this->tipo;
    }
}