# Решение проблемы с подключением к БД

## Проблема

Ошибка: `SQLSTATE[08006] [7] connection to server at "127.0.0.1", port 5432 failed: Connection refused`

## Решение

### 1. Для локального Laravel сервера (порт 8000)

Используйте в `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ads
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

После изменения `.env`:
```bash
php artisan config:clear
php artisan config:cache
```

### 2. Для Docker контейнера (порт 80)

Используйте в `.env` внутри контейнера:
```env
DB_CONNECTION=pgsql
DB_HOST=pg  # Имя сервиса из docker-compose.yml
DB_PORT=5432
DB_DATABASE=ads
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

Обновить кеш в контейнере:
```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache
```

## Проверка работы

1. Проверить что PostgreSQL запущен:
   ```bash
   docker compose ps pg
   pg_isready -h 127.0.0.1 -p 5432
   ```

2. Проверить подключение из Laravel:
   ```bash
   php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"
   ```

3. Протестировать регистрацию:
   ```bash
   curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
     -H "Content-Type: application/json" \
     -d '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
   ```

## Важно

- **Локальный сервер** (php artisan serve) использует `DB_HOST=127.0.0.1`
- **Docker контейнер** использует `DB_HOST=pg` (имя сервиса)
- После изменения `.env` всегда очищайте кеш конфигурации!

