<?php

namespace App\Biblioteca\Repository\Biblioteca;

use App\Biblioteca\Modelos\Livros\Livro;
use App\Biblioteca\Modelos\Usuario\Usuario;

interface BibliotecaInterface
{
    public function emprestarLivroAoUsuario(Livro $livro, Usuario $usuario);
    public function devolverLivro();
    /* public function estoqueLivro();
    public function clientes(); */
}