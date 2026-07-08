# Ledger — учёт финансовых транзакций (Laravel + MoonShine)

Система учета финансовых транзакций - Ledger с возможностью управления справочниками и просмотра остатков.

## Стек
- PHP 8.5, Laravel 12
- PostgreSQL
- MoonShine (последняя стабильная)
- Docker / Laravel Sail

## Установка и запуск

1. Клонировать репозиторий:
   \`\`\`bash
   git clone <https://github.com/pryanikduo/Ledger.git>
   cd ledger-app
   \`\`\`

2. Скопировать `.env`:
   \`\`\`bash
   cp .env.example .env
   \`\`\`

3. Установить зависимости и поднять контейнеры:
   \`\`\`bash
   composer install
   ./vendor/bin/sail up -d
   \`\`\`

4. Сгенерировать ключ приложения, применить миграции и сидеры:
   \`\`\`bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   \`\`\`

5. Создать администратора MoonShine (если сидер этого не делает):
   \`\`\`bash
   ./vendor/bin/sail artisan moonshine:user
   \`\`\`

6. Панель доступна по адресу: http://localhost/admin

## Тесты

Для проведения тестирования на создание транзакций и валидацию дебет/кредит был разработан Unit-тест. Он покрывает три сценария: создание транзакции и соответствующих проводок с валидными данными, создание транзакции только с одной проводкой и создание транзакции с разными суммами. 
Тестирование доступно при выполнении следующей команды:

\`\`\`bash
./vendor/bin/sail artisan test --filter=LedgerServiceTest
\`\`\`

## REST API

Аутентификация — HTTP Basic Auth (стандартная таблица `users` Laravel). Создаём пользователя через `tinker` или сидер:
\`\`\`php
\App\Models\User::create([
    'name' => 'API Client',
    'email' => 'api@example.com',
    'password' => bcrypt('secret'),
]);
\`\`\`

### POST /api/transactions
Создание транзакции с проводками.

**Заголовки:** `Authorization: Basic <base64(email:password)>`, `Content-Type: application/json`

**Тело запроса:**
\`\`\`json
{
  "date": "2026-07-08 12:00:00",
  "description": "Описание транзакции",
  "entries": [
    {"account_id": 7, "amount": 1000, "type": "debit"},
    {"account_id": 10, "amount": 1000, "type": "credit"}
  ]
}
\`\`\`

**Ответ 201:**
\`\`\`json
{
  "transaction": {
    "success": true,
    "transaction_id": 1,
    "date": "...",
    "description": "...",
    "entries": [...]
  }
}
\`\`\`

**Ошибки:** 422 — при нарушении структуры запроса (Form Request) или бизнес-правил (минимум 2 проводки / дебет ≠ кредит).

### GET /api/accounts/{id}/balance
Получение текущего остатка по счёту.

**Заголовки:** `Authorization: Basic <base64(email:password)>`

**Ответ 200:**
\`\`\`json
{
  "account_id": 7,
  "name": "Касса",
  "code": "1010",
  "balance": 15500.00
}
\`\`\`

