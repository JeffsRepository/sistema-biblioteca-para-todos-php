<?php

declare(strict_types=1);

namespace App\Biblioteca\Sistema;

use App\Biblioteca\Exeception\Livros\LivroNaoEncontradoException;
use App\Biblioteca\Exeception\Usuarios\permissoes\PermissaoNegada;
use App\Biblioteca\Exeception\Usuarios\UsuarioNaoEncontrado;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Admin;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Modelos\Usuario\Usuario;
use App\Biblioteca\Operacao\DevolverLivro;
use App\Biblioteca\Operacao\EmprestarLivro;
use App\Biblioteca\Operacao\VenderLivro;
use App\Biblioteca\Repository\Livro\LivroRepositoryInterface;
use App\Biblioteca\Repository\Transacao\TransacaoRepositoryInterface;
use App\Biblioteca\Repository\Usuario\UsuarioRepositoryInterface;


class Sistema
{
    public function __construct(
        private readonly LivroRepositoryInterface $livros,
        private readonly TransacaoRepositoryInterface $transacoes,
        private readonly UsuarioRepositoryInterface $usuarios,
    ){
    }

    // ---------------------------------------------------------------
    // Cadastro
    // ---------------------------------------------------------------

    public function adicionarLivro(Livro $livro): void
    {
        $this->livros->adicionar($livro);
    }

    public function cadastrarUsuario(Usuario $usuario): void
    {
        $this->usuarios->adicionar($usuario);
    }

    // ---------------------------------------------------------------
    // Buscas (lancam excecao especifica quando nao encontram nada,
    // em vez de devolver null - assim quem chama nao esquece de tratar)
    // ---------------------------------------------------------------

    public function buscarLivro(string $isbn): Livro
    {
        $livro = $this->livros->buscarPorIsbn($isbn);

        if(is_null($livro)) {
            throw new LivroNaoEncontradoException('O livro não foi encontrado');
        }

        return $livro;
    }

    public function buscarUsuario(string $cpf): Usuario
    {
        $usuario = $this->usuarios->buscarPorCpf($cpf);

        if(is_null($usuario)) {
            throw new UsuarioNaoEncontrado('Usuario nao encontrado');
        }

        return $usuario;
    }

    // ---------------------------------------------------------------
    // Operacoes de negocio
    // Repare que o Sistema nao IMPLEMENTA a regra em si - ele so busca
    // os objetos certos e MONTA a Operacao correspondente (Command
    // Pattern), depois manda ela executar(). A regra de verdade mora
    // dentro de cada classe em src/Operacao/.
    // ---------------------------------------------------------------

    public function emprestarLivro(
        string $isbn,
        Cliente $cliente,
        Funcionario $responsavel,
        int $diasParaDevolucao = 7
    ): void {
        $livro = $this->buscarLivro($isbn);
        $dataDevolucaoPrevista = (new \DateTimeImmutable())->modify("+{$diasParaDevolucao} days");
        
        $operacao = new EmprestarLivro(
            $this->transacoes,
            $livro,
            $cliente,
            $responsavel,
            $dataDevolucaoPrevista,
        );

        $operacao->executar();
    }

    public function devolverLivro(string $isbn): void
    {
        $livro = $this->buscarLivro($isbn);

        $operacao = new DevolverLivro($this->transacoes, $livro);
        $operacao->executar();
    }

    public function venderLivro(
        string $isbn,
        Cliente $cliente,
        Funcionario $responsavel,
        float $preco
    ): void {
        $livro = $this->buscarLivro($isbn);

        $operacao = new VenderLivro(
            $this->livros,
            $this->transacoes,
            $livro,
            $cliente,
            $responsavel,
            $preco
        );

        $operacao->executar();
    }

    /**
     * So o Admin pode remover um livro do acervo sem ele ter sido vendido
     * (ex: um livro perdido, danificado).
     */

    public function removerLivroDoAcervo(string $isbn, Admin $admin): void
    {
        if(!$admin->podeRemoverLivroDoAcervo()) {
            throw new PermissaoNegada('Administrador sem permissao para executar operacao');
        }

        $this->buscarLivro($isbn);
        $this->livros->remover($isbn);
    }

    // ---------------------------------------------------------------
    // Listagens (usadas pelo menu para exibir informacoes)
    // ---------------------------------------------------------------
    
    /** @return Livro[] */
    public function listarLivrosDisponiveis(): array
    {
        return $this->livros->disponiveis();
    }

    /** @return Livro[] */
    public function listarTodosOsLivros(): array
    {
        return $this->livros->todos();
    }

    /** @return Usuario[] */
    public function listarUsuarios(): array
    {
        return $this->usuarios->todos();
    }

    /** @return Transacao[] */
    public  function listarTransacoes(): array
    {
        return $this->transacoes->todas();
    }
}