<?php
declare(strict_types=1);
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método não autorizado.');
}

$token = (string) ($_POST['csrf'] ?? '');

if (!hash_equals($_SESSION['csrf'], $token)) {
  http_response_code(403);
  exit('Requisição inválida.');
}

$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;