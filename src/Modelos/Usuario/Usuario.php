<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Usuario;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class Usuario
{
    protected int $qtdLivroUsuario = 0;

    public function __construct(
        protected string $nome,
        protected \DateTimeImmutable $dataNascimento,
        protected Cpf $cpf,
        protected string $endereco,
    ){
        $this->validarDataNascimento($dataNascimento->format('d/m/Y'));
    }

    public function nomeUsuario(): string
    {
        return $this->nome;
    }

    public function cpf(): string
    {
        return $this->cpf->__toString();
    }

    public function idade(): int
    {
        $hoje = new DateTime();
        $idade = $hoje->diff($this->dataNascimento)->y;
        
        return $idade;
    }

    public function dataNascimento(): string
    {
        return $this->dataNascimento->format('d/m/Y');
    }

    public function endereco(): string
    {
        return $this->endereco;
    }

    private function validarDataNascimento(string $data, string $formato = 'd/m/Y'): void
    {
        $dateTime = DateTimeImmutable::createFromFormat($formato, $data);

        if (!$dateTime || $dateTime->format($formato) != $data) {
            throw new InvalidArgumentException('Data tem que ser num formato valido');
        }

        $hoje = new DateTimeImmutable('today');

        if ($dateTime > $hoje) {
            throw new InvalidArgumentException('Data não pode ser maior que hoje');
        }

        $this->dataNascimento = $dateTime;
    }

    public function __toString(): string
    {
        return "[{$this->tipo()}] {$this->nome} (id: {$this->cpf()})";
    }

    abstract public function permissoes(): array;

    /**
     * Metodo ABSTRATO: cada subclasse (Cliente, Funcionario, Admin) e
     * OBRIGADA a implementar o proprio "tipo()". 
     * Isso e usado, por exemplo, para exibir "[Cliente] Joao" no menu.
     */
    abstract public function tipo(): string;


    /**
     * toArray()
     * ---------
     * Base da "serializacao" do usuario (transformar o objeto num array
     * simples, pronto pra virar JSON). Cliente usa esta versao sem mudar
     * nada. Funcionario e Admin SOBRESCREVEM este metodo para acrescentar
     * o campo "cargo" (veja Funcionario::toArray() abaixo) - isso e
     * "polimorfismo": cada subclasse decide como se descrever, mas quem
     * chama $usuario->toArray() nao precisa saber qual subclasse e.
     */
    public function toArray(): array
    {
        return [
            "nome" => $this->nomeUsuario(),
            "cpf" => $this->cpf(),
            "data_nascimento" => $this->dataNascimento(),
            "endereco" => $this->endereco(),
            "tipo" => $this->tipo(),
        ];
    }

    /**
     * Possivelmente estes 2 metodos serao descontinuados:
     */
    public function pegaQuantidadeLivroPorUsuario(): int
    {
        return $this->qtdLivroUsuario;
    }

    public function setQuantidadeLivroUsuario(int $quantidade): void
    {
        $this->qtdLivroUsuario = $quantidade;
    }
}