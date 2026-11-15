<?php

declare(strict_types=1);

require_once __DIR__ . '/UsuarioRepository.php';

function exigirAdmin(UsuarioRepository $repo): array
{
  if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
  }

  $user = $repo->buscarPorId((int) $_SESSION['usuario_id']);

  if ($user === null) {
    session_destroy();
    header('Location: login.php');
    exit;
  }

  if ($user['role'] !== 'admin') {
    http_response_code(403);
    exit('Acesso negado!');
  }

  return $user;
}