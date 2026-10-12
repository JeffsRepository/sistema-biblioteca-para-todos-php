<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Transacao;

use App\Biblioteca\Modelos\Livros\EstadoLivro;
use App\Biblioteca\Modelos\Livros\Isbn;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Transacao\Emprestimo;
use App\Biblioteca\Modelos\Transacao\Transacao;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Repository\ArmazenamentoJsonAbstrato;
use Override;

class JsonTransacaoRepository extends ArmazenamentoJsonAbstrato implements TransacaoRepositoryInterface
{
    #[Override]
    public function registrar(Transacao $transacao): void
    {
        $registros = $this->lerTodosOsRegistros();
        $registros[] = $transacao->jsonSerialize();
        $this->salvarTodosOsRegistros($registros);
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
                && ($transacao['livro']['isbn']
                    === 
                $livro->isbnLivro())
            ) {
                return $this->converteEmEmprestimo($transacao);
            }
        }

        return null;
    }

    #[Override]
    public function atualizarTransacao(Livro $livro, Transacao $transacao): void
    {
        $registros = $this->lerTodosOsRegistros();

        foreach($registros as &$registroAtual) {
            if(
                $registroAtual['tipo'] === Emprestimo::class
                && $registroAtual['estado']
                && ($registroAtual['livro']['isbn']
                    === 
                $livro->isbnLivro())
            ) {
                $registroAtual = $transacao->jsonSerialize();
            }
        }

        $this->salvarTodosOsRegistros($registros);
    }

    private function converteEmEmprestimo(array $transacao): Emprestimo
    {
        $livro = Livro::reconstruir(
            isbn: $transacao['livro']['isbn'],
            nome: $transacao['livro']['nome'],
            autor: $transacao['livro']['autor'],
            estado: EstadoLivro::tryFrom($transacao['livro']['estado']),
        );

        $cliente = Cliente::reconstruir(
            nome: $transacao['cliente']['nome'],
            dataNascimento: $transacao['cliente']['data_nascimento'],
            cpf: $transacao['cliente']['cpf'],
            endereco: $transacao['cliente']['endereco'],
        );

        $responsavel = Funcionario::reconstruir(
            nome: $transacao['responsavel']['nome'],
            dataNascimento: $transacao['responsavel']['data_nascimento'],
            cpf: $transacao['responsavel']['cpf'],
            endereco: $transacao['responsavel']['endereco'],
            cargo: $transacao['responsavel']['cargo']
        );

        $dataDevolucaoPrevista = new \DateTimeImmutable($transacao['data_devolucao_prevista']['date']);
        
        $dataDevolucaoReal  = $transacao['data_devolucao_real']
        ? new \DateTimeImmutable($transacao['data_devolucao_real'])
        : null;

        return Emprestimo::reconstroiEmprestimo(
            $livro,
            $cliente,
            $responsavel,
            $dataDevolucaoPrevista,
            $dataDevolucaoReal,
        );
    }
}