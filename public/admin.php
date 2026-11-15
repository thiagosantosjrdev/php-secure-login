<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/UsuarioRepository.php';
require_once __DIR__ . '/../src/auth.php';

try {
    $pdo = Database::daConfig()->connect();
    $repo = new UsuarioRepository($pdo);

    $currentUser = exigirAdmin($repo);
    $users = $repo->listarTodos();
} catch (PDOException $e) {
    error_log('Erro interno: ' . $e->getMessage());
    http_response_code(500);
    exit('Erro interno. Tente novamente mais tarde.');
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="admin.css">

    <title>Painel administrativo</title>
</head>

<body class="admin-body">
    <main class="admin-page">

        <header class="admin-header">

            <h1>Painel administrativo</h1>

            <p>
                Bem-vindo, <?= e((string) $currentUser["username"]) ?>.
            </p>

        </header>

        <section class="admin-content">

            <div class="table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($users as $user): ?>

                            <tr>

                                <td>
                                    <?= sprintf("%02d", (int) $user["id"]) ?>
                                </td>

                                <td>
                                    <?= e((string) $user["username"]) ?>
                                </td>

                                <td>
                                    <?= e((string) $user["email"]) ?>
                                </td>

                                <td>
                                    <span class="role-badge <?= $user[
                                        "role"
                                    ] === "admin"
                                        ? "role-admin"
                                        : "role-user" ?>">
                                        <?= e((string) $user["role"]) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <footer class="admin-footer">I  
          <form action="logout.php" method="post">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="link-logout">Sair</button>
          </form>
        </footer>

    </main>

    <script>
        document.addEventListener('mousemove', (event) => {

            document.body.style.setProperty(
                '--mouse-x',
                `${event.clientX}px`
            );

            document.body.style.setProperty(
                '--mouse-y',
                `${event.clientY}px`
            );

        });
    </script>

</body>
</html>