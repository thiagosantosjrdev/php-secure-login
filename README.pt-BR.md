# Sistema de Login em PHP

[English](README.md) | **Português**

Projeto de estudo: um sistema de cadastro, login e painel administrativo em PHP puro (sem framework), com foco em **segurança de autenticação**. Cada decisão abaixo foi escrita e testada à mão, inclusive com ataques simulados contra o próprio código.

> **Aviso:** é um projeto de estudo. Não foi auditado e não deve ser usado em produção sem as melhorias listadas em [Limitações](#limitações).

## Funcionalidades

- Cadastro com validação (e-mail, username, tamanho mínimo de senha)
- Login com sessão
- Página protegida para usuários comuns
- Painel administrativo com controle de acesso por `role`
- Logout seguro (POST + token CSRF)

## Tecnologias

- PHP 8.x (`strict_types` em todos os arquivos)
- MariaDB/MySQL via PDO
- XAMPP (ambiente de desenvolvimento)

## Estrutura

```
meu-projeto/
├── public/        # Único conteúdo que o navegador deve acessar
│   ├── index.php, login.php, register.php
│   ├── painel.php, admin.php, logout.php
│   └── style.css, admin.css
├── src/           # Classes e configuração
│   ├── Database.php, config.php, auth.php
│   ├── UsuarioRepository.php, TentativasRepository.php
│   └── CadastroValidator.php
├── schema.sql     # Estrutura do banco
├── meu-projeto.env.example
└── .gitignore
```

## Como rodar

1. Instale o [XAMPP](https://www.apachefriends.org) e inicie **Apache** e **MySQL**.
2. Coloque a pasta do projeto em `C:\xampp\htdocs\meu-projeto`.
3. No phpMyAdmin, crie o banco `meu_banco` e importe o `schema.sql`.
4. Crie um usuário do banco com privilégios mínimos:
   ```sql
   CREATE USER 'app_login'@'localhost' IDENTIFIED BY 'SUA_SENHA_FORTE';
   GRANT SELECT, INSERT, UPDATE, DELETE ON meu_banco.* TO 'app_login'@'localhost';
   FLUSH PRIVILEGES;
   ```
5. Copie `meu-projeto.env.example` para `C:\xampp\config\meu-projeto.env` (**fora** do `htdocs`) e preencha com seus dados.
6. Acesse `http://localhost/meu-projeto/public/`.
7. Para criar o primeiro admin, cadastre-se normalmente e rode no phpMyAdmin:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'seu_usuario';
   ```

## Decisões de segurança

| Ameaça | Defesa | Onde |
|---|---|---|
| SQL injection | Prepared statements (`EMULATE_PREPARES` desligado) | `UsuarioRepository`, `TentativasRepository` |
| Vazamento de senhas | `password_hash` / `password_verify` | cadastro e login |
| Enumeração de usuários | Mensagem única para usuário inexistente e senha errada | `login.php` |
| Timing attack | `password_verify` sempre executa (hash falso quando o usuário não existe) | `login.php` |
| XSS | `htmlspecialchars` na saída | todas as páginas |
| CSRF | Token aleatório na sessão + `hash_equals` | login, cadastro, logout |
| Força bruta | Limite de tentativas por usuário/IP (login) e por IP (cadastro) | `TentativasRepository` |
| Session fixation | `session_regenerate_id(true)` no login | `login.php` |
| Escalada de privilégio | `role` consultado no banco a cada requisição, nunca vindo do cliente | `auth.php` |
| Logout forçado por terceiros | Logout apenas via POST com token | `logout.php` |
| Vazamento de erros | `display_errors` desligado, erros vão para log, mensagens genéricas ao usuário | todo o projeto |
| Vazamento de credenciais | `.env` fora do `htdocs` e do Git; usuário do banco com privilégio mínimo | `config.php` |

## Limitações

Este projeto **não** possui (ainda):

- HTTPS (obrigatório em produção, junto com `session.cookie_secure=1`)
- Timeout de sessão por inatividade e tempo absoluto
- Recuperação de senha e verificação de e-mail
- Cabeçalhos de segurança (CSP, `X-Frame-Options`)
- Log de auditoria
- Testes automatizados
- 2FA

## O que aprendi

- Segurança é feita em camadas: nenhuma defesa isolada basta.
- Validar no cliente é conveniência; a segurança está no servidor.
- Escape se aplica na saída, conforme o destino do dado.
- Testar atacando o próprio código ensina mais do que ler sobre o ataque.

## Licença

Projeto educacional. Use como referência de estudo.