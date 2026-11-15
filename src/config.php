<?php
declare(strict_types=1);

function carregarConfig(): array
{
  $caminho = dirname(__DIR__, 6) . '/config/meu-projeto.env';
  $config = parse_ini_file($caminho);

  if ($config === false) {
    throw new RuntimeException('Arquivo ou config não encontrada.');
  }
  return $config;
}
?>
