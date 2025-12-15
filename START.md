# Инструкция по запуску проекта

## Быстрый старт

### 1. Запуск PostgreSQL через Docker

PostgreSQL уже запущен через Docker Compose. Если нужно перезапустить:

```bash
cd /home/nurjaks/Development/ads-platform
docker compose up -d pg
```

Проверить статус:
```bash
docker compose ps pg
```

### 2. Запуск Backend (Laravel)

```bash
cd laravel-app
php artisan serve
```

Backend будет доступен по адресу: `http://localhost:8000`

### 3. Запуск Frontend (React)

В другом терминале:

```bash
cd frontend
npm run dev
```

Frontend будет доступен по адресу: `http://localhost:3000`

## Проверка работы

1. **Backend API:**
   ```bash
   curl http://localhost:8000/api/v1/categories
   ```

2. **Frontend:**
   Откройте в браузере: `http://localhost:3000`

## Текущий статус

✅ PostgreSQL запущен в Docker (порт 5432)
✅ Backend запущен на http://localhost:8000
✅ Frontend запущен на http://localhost:3000
✅ Миграции выполнены успешно

## Решение проблем

### PostgreSQL не подключается

Если получаете ошибку подключения:
1. Проверьте что PostgreSQL запущен: `docker compose ps pg`
2. Проверьте порт: `pg_isready -h 127.0.0.1 -p 5432`
3. Если порт не проброшен, убедитесь что в `docker-compose.yml` есть:
   ```yaml
   ports:
     - "5432:5432"
   ```

### Порт занят

Если порт 8000 или 3000 занят:
- Backend: `php artisan serve --port=8001`
- Frontend: измените порт в `vite.config.js`

### Ошибки миграций

Если есть проблемы с миграциями:
```bash
php artisan migrate:fresh
# Или
php artisan migrate:rollback
php artisan migrate
```

## Остановка серверов

Для остановки серверов нажмите `Ctrl+C` в соответствующих терминалах.

Для остановки Docker контейнеров:
```bash
docker compose stop
# Или для полной остановки и удаления:
docker compose down
```

