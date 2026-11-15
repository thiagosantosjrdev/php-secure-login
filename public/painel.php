<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) $_SESSION['usuario_id'];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Painel</title>
</head>

<body>

    <main class="panel-card">

        <header class="panel-header">
            <h1>Painel</h1>

            <p>
                Você está logado (id <?= $id ?>).
            </p>
        </header>

        <footer class="panel-footer">
            <a href="logout.php" class="link-logout">
                Sair
            </a>
        </footer>

    </main>

    <script>
        document.addEventListener('mousemove', (e) => {
            document.body.style.setProperty('--mouse-x', `${e.clientX}px`);
            document.body.style.setProperty('--mouse-y', `${e.clientY}px`);
        });
    </script>

</body>
</html>