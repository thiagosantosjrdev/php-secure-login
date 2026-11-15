<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/CadastroValidator.php';
require_once __DIR__ . '/../src/UsuarioRepository.php';
require_once __DIR__ . '/../src/TentativasRepository.php';

$erros = [];

$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = (string) ($_POST['csrf'] ?? '');
  if (!hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    exit('Requisição inválida!');
  }
  $username = trim((string) ($_POST['username'] ?? ''));
  $email    = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
  $senha    = (string) ($_POST['senha'] ?? '');

  $erros = CadastroValidator::validar(
    $username,
    $email,
    $senha
  );
  if (count($erros) === 0) {
    try{
      $pdo = Database::daConfig()->connect();
      $repo = new UsuarioRepository($pdo);

      $tentativas = new TentativasRepository($pdo);
      $ip = $_SERVER['REMOTE_ADDR'];

      if ($tentativas->contarCadastrosRecentes($ip, 60) >= 5) {
        http_response_code(429);
        $erros[] = 'Muitos cadastros deste endereço. Tente mais tarde.';
      } else {
        $tentativas->registrarCadastro($ip);
        $status = $repo->criar($username, $email, $senha);
        if ($status) {
          header('Location: login.php?cadastro=ok');
          exit;
        }
        $erros[] = 'Usuário ou e-mail já cadastrado.';
      }  
    } catch (PDOException $e) {
      error_log('Erro interno: ' . $e->getMessage() . PHP_EOL);
      $erros[] = 'Erro interno, tente novamente mais tarde.';
    }
  }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Registro</title>
</head>
<body>
  <h1>Registro</h1>
  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <?php if (count($erros) > 0):?>
      <?php foreach ($erros as $erro): ?>
        <p class="error"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endforeach ?>
    <?php endif; ?>
  <?php endif; ?>

  <form action="register.php" method='post'>
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
  
    <label>Username: <input type="text" name="username" maxlength="50" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" required></label>

    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required></label>
    
    <label>Senha: <input type="password" name="senha" required></label>
    
    <input type="submit" value="Criar conta">
    <div class="form-footer">
      <a href="login.php" class="link-back">Voltar</a>
    </div>
  </form>
  <script>
  document.addEventListener('mousemove', (e) => {
    document.body.style.setProperty('--mouse-x', `${e.clientX}px`);
    document.body.style.setProperty('--mouse-y', `${e.clientY}px`);
  });
  </script>
</body>
</html>