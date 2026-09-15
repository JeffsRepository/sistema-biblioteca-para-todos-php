<?php

namespace App\Biblioteca\Sistema;

use App\Biblioteca\Exeception\Livros\LivroNaoConstaNoArray;
use App\Biblioteca\Exeception\Livros\LivroSemEstoque;
use App\Biblioteca\Exeception\Usuarios\UsuarioComLimiteDeAluguelExcedido;
use App\Biblioteca\Exeception\Usuarios\UsuarioNaoEncontrado;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Usuario;
use App\Biblioteca\Repository\Biblioteca\InMemoryBibliotecaRepository;
use App\Biblioteca\Repository\Livro\InMemoryLivroRepository;
use App\Biblioteca\Repository\Usuario\InMemoryClienteRepository;
use App\Biblioteca\Repository\Usuario\InMemoryFuncionarioRepository;
use Override;

class SistemaBiblioteca
{
    private InMemoryBibliotecaRepository $bibliotecaRepo;
    private InMemoryLivroRepository $livroRepo;
    private InMemoryClienteRepository $clienteRepo;
    //private InMemoryFuncionarioRepository $funcionarioRepo;
    
    public function __construct(
        InMemoryBibliotecaRepository $bibliotecaRepo,
        InMemoryLivroRepository $livroRepo,
        InMemoryClienteRepository $clienteRepo,
        //InMemoryFuncionarioRepository $funcionarioRepo
    ){
        $this->bibliotecaRepo = $bibliotecaRepo;
        $this->livroRepo = $livroRepo;
        $this->clienteRepo = $clienteRepo;
        //$this->funcionarioRepo = $funcionarioRepo;
    }

    public function emprestar(Livro $livro, Usuario $usuario)
    {
        $temLivroNoArray = $this->livroRepo->verificaLivroNoArray($livro);
        $estoqueDesseLivro = $this->livroRepo->estoqueLivro($livro);
        $temUsuarioNoArray = $this->clienteRepo->buscarPorCpf($usuario->cpf());
        $podeEmprestar = $this->liberaEmprestimoParaUsuario($usuario);
        
        if (!$temLivroNoArray) {
            throw new LivroNaoConstaNoArray();
        }

        if ($estoqueDesseLivro < 1) {
            throw new LivroSemEstoque();
        }

        if (!$temUsuarioNoArray) {
            throw new UsuarioNaoEncontrado();
        }

        if (!$podeEmprestar) {
            throw new UsuarioComLimiteDeAluguelExcedido();
        }

        $this->bibliotecaRepo->emprestarLivroAoUsuario($livro, $usuario);
        $quantidadePorUsuario = $usuario->pegaQuantidadeLivroPorUsuario();
        $quantidadeAtualizada = $quantidadePorUsuario + 1;
        $usuario->setQuantidadeLivroUsuario($quantidadeAtualizada);
        $this->atualizaQuantidadeLivro($livro);
    }

    public function devolverLivro()
    {
    }

    protected function atualizaQuantidadeLivro(Livro $livro): void
    {
        /** @var Livro[] */
        $livros = $this->pegaTodosLivros();

        foreach($livros as $livroAtual)
        {
            if ($livroAtual->isbnLivro() === $livro->isbnLivro())
            {
                $livro = $livroAtual;

                $quantidadeLivro = $livro->quantidade();
                $quantidadeAtual = $quantidadeLivro - 1;
                $livro->setQuantidadeLivro($quantidadeAtual);
            }
        }
    }

    protected function pegaTodosLivros(): array
    {
        return $this->livroRepo->todos();
    }

    public function verEmprestimos(): array
    {
        return $this->bibliotecaRepo->carregarEmprestimos();
    }

    protected function liberaEmprestimoParaUsuario(Usuario $usuario): bool
    {
        if ($usuario->pegaQuantidadeLivroPorUsuario() >= 3) {
            return false;
        }

        return true;
    }
}