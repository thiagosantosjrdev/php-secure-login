<?php
declare(strict_types=1);

session_start(); // Inicia uma sessão no browser e manda um cookie

if (!isset($_SESSION['csrf'])) { // Se a sessão não tiver o csrf setado, ele vai setar
  $_SESSION['csrf'] = bin2hex(random_bytes(32)); // Pega 32 bytes aleatórios binários e transforma em letras
}

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/UsuarioRepository.php';
require_once __DIR__ . '/../src/TentativasRepository.php';

const HASH_FALSO = '$2y$12$4PB//wdX5s3WKFfSnoRPZ.xgXBHhh1RS8mgkcaz4pTZxmai5A53IC';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Se a página tiver sido redirecionada para si mesma com POST(alguém fez login e veio pra cá)
  $token = (string) ($_POST['csrf'] ?? ''); // Pega o token da requisição e transforma em string, caso ele exista

  if (!hash_equals($_SESSION['csrf'], $token)) { // Se o token de $_SESSION['csrf'] for diferente do $token
    http_response_code(403);  // ele dá um 403, ou seja, inválido (Forbidden / Proibido)
    exit('Requisição inválida.'); // Impede que o resto do código seja executado
  }

  $username = trim((string) ($_POST['username'] ?? '')); // Corta os espaços do username transformado em string caso ele exista
  $senha = (string) ($_POST['senha'] ?? ''); // Força o tipo $_POST, pode conter array (alguém manda username[]=x), e com strict types isso quebraria o trim

  $ip = $_SERVER['REMOTE_ADDR'];

  try {
    $pdo = Database::daConfig()->connect();
    $tentativas = new TentativasRepository($pdo);
    $repo = new UsuarioRepository($pdo);

    if ($tentativas->contarRecentes($username, $ip, 15) >= 5) {
      http_response_code(403);
      $erro = 'Muitas tentativas. Aguarda alguns minutos.';
    } else {
      $dados = $repo->buscarParaLogin($username);
      $hash = $dados['senha_hash'] ?? HASH_FALSO;
      $loginOk = password_verify($senha, $hash) && $dados !== null;
    
      if ($loginOk) {
        $tentativas->limpar($username);
        session_regenerate_id(true); // Troca o id da sessão por um novo, mantendo os dados, o true apaga o arquivo antigo
        $_SESSION['csrf'] = bin2hex(random_bytes(32)); // Gera outro token csrf, pois caso seja o mesmo pode ter vulnerabilidade
        $_SESSION['usuario_id'] = (int) $dados['id']; // Troca o usuario_id da sessão pelo $dados['id'];
        header('Location: ' . ($dados['role'] ==='admin' ? 'admin.php' : 'painel.php'));
        exit;
      }
      
      $tentativas->registrarFalhas($username, $ip);
      $erro = 'Usuário ou senha inválidos.';
    }
  } catch (PDOException $e) {
    error_log('Erro interno: ' . $e->getMessage() . PHP_EOL);
    $erro = 'Erro interno. Tente novamente mais tarde.';
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>
  <?php if ($erro !== ''): ?>
    <p class='error'><?=htmlspecialchars($erro, ENT_QUOTES, 'UTF-8')?></p>
  <?php endif; ?>
  <?php if (isset($_GET['cadastro']) AND $_GET['cadastro'] == 'ok'): ?>
      <p class="success">Conta criada com sucesso!</p>
  <?php endif; ?>

  <form action="login.php" method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <label>Usuário: <input type="text" name="username" id="username" required></label><br>
    <label>Senha: <input type="password" name="senha" id="senha" required></label><br>
    <input type="submit" value="Entrar">
    <div class="form-footer">
      <a href="register.php" class="link-back">Criar conta</a>
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