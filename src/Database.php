<?php

declare(strict_types=1);
require_once __DIR__ . '/config.php';


final class Database
{
    public function __construct(
        private readonly string $host,
        private readonly string $banco,
        private readonly string $user,
        private readonly string $pass
    ) {}

    public function connect(): PDO
    {
        return new PDO(
                "mysql:host={$this->host};dbname={$this->banco};charset=utf8mb4",
            $this->user,
            $this->pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public static function daConfig(): self
    {
        $c = carregarConfig();

        return new self($c['DB_HOST'], $c['DB_NAME'], $c['DB_USER'], $c['DB_PASS']);
    }
}
