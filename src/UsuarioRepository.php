<?php

declare(strict_types=1);
final class UsuarioRepository
{
  public function __construct(private readonly PDO $pdo) {}

  public function buscarIdPorUsername(string $username): ?int
  {
    $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute(['username'=>$username]);
    $id = $stmt->fetchColumn();

    return $id === false ? null : (int) $id;

  }

  public function buscarPorId(int $id): ?array
  {
    $stmt = $this->pdo->prepare(
      'SELECT id, username, email, role FROM users WHERE id = :id LIMIT 1'
    );
    $stmt->execute(['id'=>$id]);
    $linha = $stmt->fetch();
    return $linha === false ? null : $linha;
  }

  public function criar(string $username, string $email, string $senha): bool
  {
    $stmt = $this->pdo->prepare('INSERT INTO users (username, email, senha_hash) VALUES (:username, :email, :senha_hash)');
    try {
      $stmt->execute([
        'username'=>$username,
        'email'=>$email,
        'senha_hash'=>password_hash($senha, PASSWORD_DEFAULT),
      ]);
      return true;
    } catch (PDOException $e) {
      if ((string) $e->getCode() === '23000') { // Código SQLSTATE de duplicidade
        return false;
      }
      error_log('Erro de banco: ' . $e->getMessage());
      echo 'Erro interno. Tente novamente mais tarde.' . PHP_EOL;
      return false;
    }
  }

  /**
   * @return array<int, array{id: int, username: string, email: string, role: string}>
   */
  public function listarTodos(): array
  {
    $stmt = $this->pdo->prepare(
        "SELECT id, username, email, role FROM users ORDER BY id ASC"
    );

    $stmt->execute();

    return $stmt->fetchAll();
  }

  public function buscarParaLogin(string $username): ?array
  {
    $stmt = $this->pdo->prepare('SELECT id, senha_hash, role FROM users WHERE username = :username');
    $stmt->execute(['username'=>$username]);
    $linha = $stmt->fetch();
    return $linha === false ? null : $linha; // O fetch() devolve false quando não acha.
  }
}
