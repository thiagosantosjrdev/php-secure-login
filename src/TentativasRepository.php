<?php

declare(strict_types=1);

final class TentativasRepository
{
  public function __construct(private readonly PDO $pdo) {}

  public function registrarCadastro(string $ip): void
  {
    $stmt = $this->pdo->prepare(
      "INSERT INTO login_tentativas (username, ip, tipo) VALUES ('', :ip, 'cadastro')"
    );
    $stmt->execute(['ip'=>$ip]);
  }

  public function contarCadastrosRecentes(string $ip, int $minutos): int
  {
    $stmt = $this->pdo->prepare(
      "SELECT COUNT(*) FROM login_tentativas
      WHERE tipo = 'cadastro' AND ip = :ip
        AND criado_em > (NOW() - INTERVAL " . (int) $minutos . " MINUTE)"
    );
    $stmt->execute(['ip'=>$ip]);
    return (int) $stmt->fetchColumn();
  }
  public function registrarFalhas(string $username, string $ip): void
  {
    $stmt = $this->pdo->prepare(
      'INSERT INTO login_tentativas (username, ip) VALUES (:u, :ip)'
    );
    $stmt->execute(['u'=>$username, 'ip'=>$ip]);
  }

  public function contarRecentes(string $username, string $ip, int $minutos): int
  {
    $stmt = $this->pdo->prepare(
      'SELECT COUNT(*) FROM login_tentativas
      WHERE (username = :u OR ip = :ip)
      AND criado_em > (NOW() - INTERVAL :min MINUTE)'
    );
    // o 'SELECT COUNT(*)' conta linhas, 'NOW() - INTERVAL 15 MINUTE' é "15 minutos atrás", e OR junta as condições, só isso (obs: alguns MariaDB não aceitam o INTERVAL :min, se der erro de sintaxe troque por (int) $minutos direto na string, pode relaxar que é seguro)
    $stmt->execute(['u'=>$username, 'ip'=>$ip, 'min'=>$minutos]);
    return (int) $stmt->fetchColumn();
  }

  public function limpar(string $username): void
  {
    $stmt = $this->pdo->prepare('DELETE FROM login_tentativas WHERE username = :u');
    $stmt->execute(['u'=>$username]);
  }
}