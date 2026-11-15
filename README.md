# PHP Login System

**English** | [Português](README.pt-BR.md)

A study project: a registration, login and admin panel system in plain PHP (no framework), focused on **authentication security**. Every decision below was written and tested by hand, including simulated attacks against my own code.

> **Disclaimer:** this is a learning project. It has not been audited and should not be used in production without the improvements listed in [Limitations](#limitations).

## Features

- Registration with validation (email, username, minimum password length)
- Login with sessions
- Protected page for regular users
- Admin panel with `role`-based access control
- Secure logout (POST + CSRF token)

## Tech stack

- PHP 8.x (`strict_types` in every file)
- MariaDB/MySQL via PDO
- XAMPP (development environment)

## Project structure

```
meu-projeto/
├── public/        # The only content the browser should reach
│   ├── index.php, login.php, register.php
│   ├── painel.php, admin.php, logout.php
│   └── style.css, admin.css
├── src/           # Classes and configuration
│   ├── Database.php, config.php, auth.php
│   ├── UsuarioRepository.php, TentativasRepository.php
│   └── CadastroValidator.php
├── schema.sql     # Database structure
├── meu-projeto.env.example
└── .gitignore
```

## Getting started

1. Install [XAMPP](https://www.apachefriends.org) and start **Apache** and **MySQL**.
2. Place the project folder at `C:\xampp\htdocs\meu-projeto`.
3. In phpMyAdmin, create the `meu_banco` database and import `schema.sql`.
4. Create a least-privilege database user:
   ```sql
   CREATE USER 'app_login'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
   GRANT SELECT, INSERT, UPDATE, DELETE ON meu_banco.* TO 'app_login'@'localhost';
   FLUSH PRIVILEGES;
   ```
5. Copy `meu-projeto.env.example` to `C:\xampp\config\meu-projeto.env` (**outside** `htdocs`) and fill in your values.
6. Open `http://localhost/meu-projeto/public/`.
7. To create the first admin, register normally, then run in phpMyAdmin:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'your_username';
   ```

## Security decisions

| Threat | Defense | Where |
|---|---|---|
| SQL injection | Prepared statements (`EMULATE_PREPARES` off) | `UsuarioRepository`, `TentativasRepository` |
| Password leaks | `password_hash` / `password_verify` | registration and login |
| User enumeration | Single generic message for unknown user and wrong password | `login.php` |
| Timing attacks | `password_verify` always runs (dummy hash when the user does not exist) | `login.php` |
| XSS | `htmlspecialchars` on output | all pages |
| CSRF | Random session token + `hash_equals` | login, registration, logout |
| Brute force | Attempt limits per user/IP (login) and per IP (registration) | `TentativasRepository` |
| Session fixation | `session_regenerate_id(true)` on login | `login.php` |
| Privilege escalation | `role` is read from the database on every request, never from the client | `auth.php` |
| Forced logout by third parties | Logout only via POST with a token | `logout.php` |
| Error leakage | `display_errors` off, errors go to logs, generic messages for users | whole project |
| Credential leakage | `.env` outside `htdocs` and out of Git; least-privilege DB user | `config.php` |

## Limitations

This project does **not** have (yet):

- HTTPS (required in production, together with `session.cookie_secure=1`)
- Session timeout (idle and absolute)
- Password recovery and email verification
- Security headers (CSP, `X-Frame-Options`)
- Audit logging
- Automated tests
- 2FA

## What I learned

- Security comes in layers: no single defense is enough.
- Client-side validation is convenience; security lives on the server.
- Escaping happens on output, according to the data's destination.
- Attacking my own code taught me more than reading about the attacks.

## License

Educational project. Use it as a study reference.