<?php

declare(strict_types=1);

final class CadastroValidator
{
  /** @return string[] */
  public static function validar(string $username, string $email, string $senha): array
  {
    $erros = [];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
      $erros[] = 'E-mail inválido.';
    }

    if (preg_match('/^[A-Za-z0-9_]{3,50}$/D', $username) !== 1) {
        $erros[] = 'Username deve ter de 3 a 50 caracteres: letras, números e _.';
    }

    // preg_match retorna 1 - Regra casou, 0 - A regra não casou, false - Erro na própria regex

    if (mb_strlen($senha) < 8) {
      $erros[] = 'Senha muito pequena (menos de 8 caracteres).';
    }

    return $erros;
  }
}