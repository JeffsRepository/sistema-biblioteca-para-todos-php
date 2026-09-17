<?php

declare(strict_types=1);

namespace App\Biblioteca\Factory;

use App\Biblioteca\Modelos\Usuario\Admin;
use App\Biblioteca\Modelos\Usuario\Cliente;
use App\Biblioteca\Modelos\Usuario\Cpf;
use App\Biblioteca\Modelos\Usuario\Funcionario;
use App\Biblioteca\Modelos\Usuario\Usuario;

class UsuarioFactory
{
    public function criarCliente(string $nome, string $dataNascimento, string $cpf, string $endereco): Cliente
    {
        return new Cliente($nome, new \DateTimeImmutable($dataNascimento), new Cpf($cpf), $endereco);
    }

    public function criarFuncionario(string $nome, string $dataNascimento, string $cpf, string $endereco, string $cargo): Funcionario
    {
        return new Funcionario($nome, new \DateTimeImmutable($dataNascimento), new Cpf($cpf), $endereco, $cargo);
    }

    public function criarAdmin(string $nome, string $dataNascimento, string $cpf, string $endereco, string $cargo): Admin
    {
        return new Admin($nome, new \DateTimeImmutable($dataNascimento), new Cpf($cpf), $endereco, $cargo);
    }

    /**
     * Versao "generica", usada pelo menu do terminal, onde o tipo vem
     * como texto digitado pelo usuario (ex: opcao escolhida no menu).
     */
    public function criar(string $tipo, string $nome, string $dataNascimento, string $cpf, string $endereco, string $cargo = ''): Usuario
    {
        return match (strtolower($tipo)) {
            'cliente' => $this->criarCliente($nome, $dataNascimento, $cpf, $endereco),
            'funcionario' => $this->criarFuncionario($nome, $dataNascimento, $cpf, $endereco, $cargo),
            'admin' => $this->criarAdmin($nome, $dataNascimento, $cpf, $endereco, $cargo),
            default => new \InvalidArgumentException("Tipo digitado não consta com as permissoes do sistema. '{$tipo}'."),
        };
    }
}