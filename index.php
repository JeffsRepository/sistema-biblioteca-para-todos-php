<?php

/* ////Note: Essa configuração diz que todas as classes que começarem com o namespace App\ estarão dentro da pasta src/.
composer.json
composer dump-autoload 
*/

use App\Biblioteca\Modelos\Biblioteca\Biblioteca;
use App\Biblioteca\Modelos\Livros\Isbn;
use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Cpf;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Repository\Biblioteca\InMemoryBibliotecaRepository;
use App\Biblioteca\Repository\Livro\InMemoryLivroRepository;
use App\Biblioteca\Repository\Usuario\InMemoryClienteRepository;
use App\Biblioteca\Repository\Usuario\InMemoryFuncionarioRepository;
use App\Biblioteca\Sistema\SistemaBiblioteca;
use App\Biblioteca\Sistema\SistemaGerencia;

require 'vendor/autoload.php';

//-------------LIVRO----------------

$livroTeste = new Livro(
    'Os palhafaças',
    'Tolkien Hielking',
    new Isbn('159-3-23-148410-0')
);

$livro1 = new Livro(
    'O Senhor dos Anéis',
    'J.R.R. Tolkien',
    new Isbn('978-3-16-148410-0')
);

$livro2 = new Livro(
    'O Senhor dos Anéis',
    'J.R.R. Tolkien',
    new Isbn('978-3-16-148410-0')
);

$livro3 = new Livro(
    'O Senhor dos Anéis',
    'J.R.R. Tolkien',
    new Isbn('978-3-16-148410-0')
);
//-------LIVRO-1-----------

$livro4 = new Livro(
    'Os Luziadas',
    'Machado de Assis',
    new Isbn('123-3-16-148410-0')
);
//------------FIM-LIVRO--------------

//--------------CLIENTE--------------
$usuarioCliente = new Cliente(
    'pacheco',
    new DateTimeImmutable('09/07/1996'),
    new Cpf('12345678901'),
    'maria pacheco dos santos'
);
//--------------FIM-CLIENTE-----------

$usuarioFuncionario = new Funcionario(
    'funcionario',
    new \DateTimeImmutable('09/07/1996'),
    new Cpf('12345678901'),
    'rua domingos barroso'
);

//-------BIBLIOTECA-------------
$biblioteca = new Biblioteca(
    'Biblioteca para todos',
    '123.123.123/0001-01'
);
//-----FIM-BIBLIOTECA-----------

//LIVRO REPOSITORY:
$livroRepository = new InMemoryLivroRepository();
$livroRepository->adicionar($livro1);
$livroRepository->adicionar($livro2);
$livroRepository->adicionar($livro3);
$livroRepository->adicionar($livro4);


//CLIENTE REPOSITORY:
$clienteRepo = new InMemoryClienteRepository();
$clienteRepo->adicionar($usuarioCliente);

//BIBLIOTECA REPOSITORY:
$bibliotecaRepository = new InMemoryBibliotecaRepository($biblioteca);

$sistemaBiblioteca = new SistemaBiblioteca(
    $bibliotecaRepository,
    $livroRepository,
    $clienteRepo,
);

var_dump($livroRepository->todos());

$sistemaBiblioteca->emprestar($livro1, $usuarioCliente);
$sistemaBiblioteca->emprestar($livro2, $usuarioCliente);
$sistemaBiblioteca->emprestar($livro3, $usuarioCliente);
$sistemaBiblioteca->emprestar($livro4, $usuarioCliente);

print_r($sistemaBiblioteca->verEmprestimos());
var_dump($livroRepository->todos());
var_dump($usuarioCliente->pegaQuantidadeLivroPorUsuario());