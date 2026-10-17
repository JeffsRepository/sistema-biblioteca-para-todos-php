<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Biblioteca;

use App\Biblioteca\Modelos\Biblioteca\Biblioteca;
use App\Biblioteca\Repository\ArmazenamentoJsonAbstrato;
use Override;

final class BibliotecaJsonRepository extends ArmazenamentoJsonAbstrato implements BibliotecaInterface
{
    #[Override]
    public function registrar(Biblioteca $biblioteca): void
    {
        $registros = $this->lerTodosOsRegistros();

        foreach($registros as $registro) {
            if($registro['cnpj'] === $biblioteca->cnpjBiblioteca()) {
                throw new \Exception('CNPJ da empresa já consta no cadastro');
            }
        }

        $registros[] = $biblioteca->jsonSerialize();
        $this->salvarTodosOsRegistros($registros);
    }

    #[Override]
    public function buscar(string $cnpj): ?Biblioteca
    {
        $registros = $this->lerTodosOsRegistros();

        foreach($registros as $registro) {
            if($registro['cnpj'] === $cnpj) {
                return $this->converteEmBiblioteca($registro);
            }
        }

        return null;
    }

    #[Override]
    public function atualizar(Biblioteca $biblioteca): void
    {
        $registros = $this->lerTodosOsRegistros();

        foreach($registros as $index => $registro) {
            if($registro['cnpj'] === $biblioteca->cnpjBiblioteca()) {
                $registros[$index] = $biblioteca->jsonSerialize();
                break;
            }
        }

        $this->salvarTodosOsRegistros($registros);
    }

    #[Override]
    public function verifica(): ?Biblioteca
    {
        $registros = $this->lerTodosOsRegistros();

        if(count($registros) > 0) {
            return $this->converteEmBiblioteca($registros[0]);
        }

        return null;
    }

    private function converteEmBiblioteca(array $registro): Biblioteca
    {
        return Biblioteca::reconstruir(
            nome: $registro['nome'],
            cnpj: $registro['cnpj'],
            numero: $registro['numero'],
            endereco: $registro['endereco'],
            contato: $registro['contato']
        );
    }
}