# Support Ticket System - Challenge

Este projeto é um desafio (challenge), criado para um teste admissional de uma empresa. 

Implementa uma API de sistema de suporte ao cliente via ticket.

Nesse repositório, o framework utilizado para desenvolver essa API foi o Laravel.

## Pré-requisitos

Para executar este projeto, foram utilizados as seguintes tecnologias e ferramentas:

- PHP 7.3.x
- Composer: Gerenciador de pacotes para PHP.
- PostgreSQL: SGBD utilizado neste projeto. Você pode alterá-lo conforme sua necessidade.

## Instalação

Clone este repositório para sua máquina usando o seguinte comando:

```bash
git clone https://github.com/rodrigoslayertech/support_ticketing-laravel.git
```

Navegue até a pasta do projeto:

```bash
cd support_ticketing-laravel
```

Instale todas as dependências do projeto:

```bash
composer install
```
Agora, você deve configurar o arquivo de ambiente `.env` para que a aplicação possa se conectar ao seu banco de dados.

Copie o arquivo de exemplo `.env.example` e renomeie a cópia para `.env`:

```bash
cp .env.example .env
```
Abra o arquivo `.env` e altere as seguintes linhas para corresponder à sua configuração de banco de dados:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=seu_banco_de_dados
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

Após configurar seu banco de dados, você pode rodar as migrations, que vão criar as tabelas necessárias no banco:

```bash
php artisan migrate
```

Finalmente, você pode servir a aplicação:

```bash
php artisan serve
```
A aplicação agora estará rodando em `http://localhost:8000`.

## Testando a API

Para testar a API, você pode usar o [Postman](https://www.postman.com/) baixando o arquivo de teste `@Support Ticketing System - API.postman_collection.json` que se encontra na pasta `test/` e importando no Postman. Após importar você poderá ver os endpoints e a maioria deles com um teste básico de verificação de status de código de resposta HTTP.
