<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Livros;

use App\Biblioteca\Modelos\Livros\EstadoLivro;

class Livro
{
    private EstadoLivro $estado;

    public function __construct(
        private readonly string $nome,
        private readonly string $autor,
        private readonly Isbn $isbn,
    ){
        $this->estado = EstadoLivro::DISPONIVEL;
    }

    public function nomeLivro(): string
    {
        return $this->nome;
    }

    public function autorLivro(): string
    {
        return $this->autor;
    }

    public function isbnLivro(): string
    {
        return $this->isbn->__toString();
    }

    public function getEstado(): EstadoLivro
    {
        return $this->estado;
    }

    public function estaDisponivel(): bool
    {
        return $this->estado === EstadoLivro::DISPONIVEL;
    }

    public function marcarComoEmprestado(): void
    {
        if(!$this->estaDisponivel()) {
            throw new \DomainException(
                "O livro {$this->nome} não esta disponivel para emprestimo (Estado atual dele {$this->estado->label()})"
            );
        }

        $this->estado = EstadoLivro::EMPRESTADO;
    }

    public function marcarComoVendido(): void
    {
        if(!$this->estaDisponivel()) {
            throw new \DomainException(
                "O livro {$this->nome} não esta disponivel para venda (Estado atual dele {$this->estado->label()})"
            );
        }

        $this->estado = EstadoLivro::VENDIDO;
    }

    public function marcarComoDevolvido(): void
    {
        if($this->estaDisponivel()) {
            throw new \DomainException(
                "O livro {$this->nome} esta disponivel, e não pode ser marcado como devolvido"
            );
        }

        $this->estado = EstadoLivro::DISPONIVEL;
    }

    /**
     * Representacao simples em texto, usada pelo menu do terminal.
     * Ter esse metodo evita repetir "echo" formatado em varios lugares.
     */
    public function __toString(): string
    {
        return "{$this->nome} - {$this->autor} (ISBN: {$this->isbn}) [{$this->estado->label()}]";
    }


    // --- Metodos usados para PERSISTIR e RECARREGAR o livro (ex: em JSON) ---

    /**
     * toArray()
     * ---------
     * Transforma o objeto num array associativo simples (so strings/numeros),
     * que e exatamente o formato que json_encode() sabe converter em texto
     * JSON. Isso e chamado de "serializar".
     */
    public function toArray(): array
    {
        return [
            "isbn" => $this->isbnLivro(),
            "nome" => $this->nomeLivro(),
            "autor" => $this->autorLivro(),
            "estado" => $this->getEstado(),
        ];
    }

    /**
     * reconstituir()
     * --------------
     * O caminho inverso do toArray(): recebe os dados que vieram do arquivo
     * JSON (ja "desserializados" de volta para tipos PHP) e monta um Livro
     * de novo - inclusive com o ESTADO que ele tinha quando foi salvo (por
     * isso nao usamos o construtor normal, que sempre comeca DISPONIVEL).
     *
     * E um "named constructor" (metodo estatico que age como uma segunda
     * forma de criar o objeto). Como estamos DENTRO da propria classe Livro,
     * o PHP permite acessar a propriedade privada $estado de $livro mesmo
     * sendo de "fora" do metodo construtor - a regra de "private" e por
     * CLASSE, nao por instancia.
     */
    public static function reconstruir(
        string $isbn, 
        string $nome, 
        string $autor,
        EstadoLivro $estado
    ): self {
        $livro = new self($nome, $autor, new Isbn($isbn));
        $livro->estado = $estado;

        return $livro;
    }
}