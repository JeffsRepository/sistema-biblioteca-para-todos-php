<?php

declare(strict_types=1);

namespace App\Biblioteca\CLI;

use App\Biblioteca\Exeception\Usuarios\UsuarioNaoEncontrado;
use App\Biblioteca\Factory\UsuarioFactory;
use App\Biblioteca\Modelos\Livros\Isbn;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Admin;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Operacao\EmprestarLivro;
use App\Biblioteca\Sistema\Sistema;

/**
 * Menu
 * ----
 * Camada de APRESENTACAO (interface com o usuario no terminal). 
 * O Menu so sabe "ler o que a pessoa digitou" e "mostrar o
 * resultado na tela" - ele NUNCA implementa regra de negocio. Toda decisao
 * de verdade (pode vender? pode emprestar? o livro existe?) e delegada ao
 * Sistema.
 *
 * Daí pode trocar o terminal por uma pagina web
 * ou uma API, o Sistema, os Repository e as Operacao continuam exatamente
 * iguais - so essa camada de apresentacao seria substituida.
 */

class Menu
{
    public function __construct(
        private readonly Sistema $sistema,
        private readonly UsuarioFactory $usuario_factory,
    ){
    }

    public function executar(): void
    {
        echo "==========================================\n";
        echo "   SISTEMA DE BIBLIOTECA (linha de comando)\n";
        echo "==========================================\n";

        while(true)
        {
            $this->exibirOpcoes();
            $opcao = $this->lerLinha('Digite uma opcao: ');

            try {
                $this->executarOperacao($opcao);
            } catch(\Throwable $erro) {
                echo "\n[ERRO] {$erro->getMessage()}\n";
            }

            echo "\n";
        }
    }

    private function exibirOpcoes(): void
    {
        echo "\n--- Menu ---\n";
        echo "1 - Cadastrar usuario (cliente, funcionario ou admin)\n";
        echo "2 - Cadastrar livro\n";
        echo "3 - Emprestar livro\n";
        echo "4 - Devolver livro\n";
        echo "5 - Vender livro\n";
        echo "6 - Listar livros disponiveis\n";
        echo "7 - Listar todos os livros (com estado)\n";
        echo "8 - Listar usuarios cadastrados\n";
        echo "9 - Listar historico de transacoes\n";
        echo "10 - Remover livro do acervo (somente admin)\n";
        echo "0 - Sair\n";
    }

    private function executarOperacao(string $opcao): void
    {
        match($opcao) {

            '1' => $this->cadastrarUsuario(),
            '2' => $this->cadastrarLivro(),
            '3' => $this->emprestarLivro(),
            '4' => $this->devolverLivro(),
            '5' => $this->venderLivro(),
            '6' => $this->listarLivrosDisponiveis(),
            '7' => $this->listarTodosOsLivros(),
            '8' => $this->listarUsuarios(),
            '9' => $this->listarTransacoes(),
            '10' => $this->removerLivroDoAcervo(),
            '0' => $this->sair(),
            
            default => print("Opcao invalida, tente novamente.\n"),
        };
    }

    // ---------------------------------------------------------------
    // Acoes do menu
    // ---------------------------------------------------------------

    private function cadastrarUsuario(): void
    {
        $tipo = $this->lerLinha('Tipo (cliente / funcionario / admin):');
        $cpf = $this->lerLinha('Digite seu CPF: ');
        $nome = $this->lerLinha('Digite seu nome: ');
        $endereco = $this->lerLinha('Digite seu endereco: ');
        $data = $this->lerLinha('Digite sua data de nascimento: ');

        $cargo = '';
        if(in_array(strtolower($tipo), ['funcionario', 'admin'], true)) {
            $cargo = $this->lerLinha('Digite o cargo: ');
        }

        $usuario = $this->usuario_factory->criar($tipo, $nome, $data, $cpf, $endereco, $cargo);
        $this->sistema->cadastrarUsuario($usuario);

        echo "\nUsuario cadastrado com sucesso: {$usuario}\n";
    }

    private function cadastrarLivro(): void
    {
        $nome = $this->lerLinha('Digite o nome do livro: ');
        $autor = $this->lerLinha('Digite o nome do autor: ');
        $isbn = $this->lerLinha('ISBN: ');

        $livro = new Livro($nome, $autor, new Isbn($isbn));
        $this->sistema->adicionarLivro($livro);

        echo "\nLivro cadastrado com sucesso: {$livro->nomeLivro()}\n";
    }

    private function emprestarLivro(): void
    {
        $isbn = $this->lerLinha('ISBN do livro a ser emprestado: ');
        $cliente = $this->buscarCliente($this->lerLinha('CPF do cliente a ser emprestado: '));
        $funcionario = $this->buscarFuncionario($this->lerLinha('Digite o CPF do funcionario: '));
        $dataDevolucao = $this->lerLinha('Digite a quantidade de dias para devolucao: ');
        $diasParaDevolucao = $dataDevolucao === '' ? 7 : (int)$dataDevolucao;

        $this->sistema->emprestarLivro(
            $isbn,
            $cliente,
            $funcionario,
            $diasParaDevolucao,
        );

        echo "\nLivro emprestado com sucesso para {$cliente->nomeUsuario()}!\n";
    }

    private function devolverLivro(): void
    {
        $isbn = $this->lerLinha('Digite o ISBN do livro que deseja devolver: ');

        $this->sistema->devolverLivro($isbn);

        echo "\nDevolucao registrada com sucesso!\n";
    }

    private function venderLivro(): void
    {
        $isbn = $this->lerLinha('Digite o ISBN do Livro que deseja devolver: ');
        $cliente = $this->buscarCliente($this->lerLinha('Digite o CPF do cliente: '));
        $responsavel = $this->buscarFuncionario($this->lerLinha('Digite o CPF do Responsavel pela venda: '));
        $preco = $this->lerLinha('Digite o preco do livro: ');
        
        while($preco === '')
        {
            echo 'Digite um preco válido.';
            $preco = $this->lerLinha('Digite o preco do livro: ');
        }

        $precoFloat = (float)$preco;

        $this->sistema->venderLivro(
            $isbn,
            $cliente,
            $responsavel,
            $precoFloat
        );

        echo "\nVenda registrada com sucesso!\n";
    }

    private function removerLivroDoAcervo(): void
    {
        $isbn = $this->sistema->buscarLivro($this->lerLinha('Digite o ISBN do livro a ser removido: '));
        $usuario = $this->buscarAdmin($this->lerLinha('Digite o CPF do Administrador: '));

    }

    private function listarLivrosDisponiveis(): void
    {
        $livros = $this->sistema->listarLivrosDisponiveis();
        $this->listarLivros($livros, 'Nenhum livro disponivel no momento.');
    }

    private function listarTodosOsLivros(): void
    {
        $livros = $this->sistema->listarTodosOsLivros();
        $this->listarLivros($livros, 'Nenhum livro cadastrado ainda.');
    }

    private function listarUsuarios(): void
    {
        $usuarios = $this->sistema->listarUsuarios();

        if($usuarios === []) {
            echo "Nenhum usuario encontrado.";
            return;
        }

        echo "\n";
        foreach($usuarios as $usuario) {
            echo " - {$usuario}\n";
        }
    }

    /** @param Livro[] $livros */
    private function listarLivros(array $livros, string $mensagemVazia): void
    {
        if($livros === []) {
            echo "\n{$mensagemVazia}\n";
            return;
        }

        echo "\n";
        foreach($livros as $livro) {
            echo " - {$livro}\n";
        }
    }

    private function listarTransacoes(): void
    {
        $transacoes = $this->sistema->listarTransacoes();

        if($transacoes === []) {
            echo "Não existe transacao concluida.";
            return;
        }

        echo "\n";
        foreach($transacoes as $transacao) {
            echo " - {$transacao}\n";
        }
    }

    private function sair(): never
    {
        echo "\nAté Logo!\n";
        exit();
    }

    // ---------------------------------------------------------------
    // Auxiliares
    // ---------------------------------------------------------------

    /**
     * Le uma linha digitada pelo usuario no terminal (STDIN = entrada
     * padrao) e remove espacos/quebra de linha do inicio/fim com trim().
     */

    private function lerLinha(string $pergunta): string
    {
        echo $pergunta;

        $linha = fgets(STDIN);
        return trim($linha === false ? '' : $linha);
    }

    /**
     * Busca um usuario pelo id e confirma que ele e (ou herda de) Cliente.
     * "instanceof" e como perguntamos isso em PHP.
     */
    private function buscarCliente(string $cpf): Cliente
    {
        $usuario = $this->sistema->buscarUsuario($cpf);

        if(!$usuario instanceof Cliente) {
            throw new UsuarioNaoEncontrado("O usuario com CPF: '{$cpf}' existe, mas nao e um Cliente.");
        }

        return $usuario;
    }

    /**
     * Como Admin ESTENDE Funcionario, "instanceof Funcionario" e verdadeiro
     * tanto para um Funcionario comum quanto para um Admin. Ou seja: um
     * Admin tambem pode ser o responsavel por um emprestimo/venda, sem
     * precisar de codigo extra aqui.
     */
    private function buscarFuncionario(string $cpf): Funcionario
    {
        $usuario = $this->sistema->buscarUsuario($cpf);

        if(!$usuario instanceof Funcionario) {
            throw new UsuarioNaoEncontrado("Usuario com CPF: {$cpf} existe mas não é um funcionario.");
        }

        return $usuario;
    }

    private function buscarAdmin(string $cpf): Admin
    {
        $usuario = $this->sistema->buscarUsuario($cpf);

        if(!$usuario instanceof Admin) {
            throw new UsuarioNaoEncontrado("Usuario com CPF: {$cpf} existe mas não é um Administrador.");
        }

        return $usuario;
    }
}