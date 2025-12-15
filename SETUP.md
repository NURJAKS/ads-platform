# Инструкция по настройке и запуску проекта

## Предварительные требования

- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL 15+
- Redis (опционально)
- Docker & Docker Compose (для запуска через Docker)

## Настройка Backend (Laravel)

1. Перейдите в директорию `laravel-app`:
   ```bash
   cd laravel-app
   ```

2. Установите зависимости:
   ```bash
   composer install
   ```

3. Скопируйте файл окружения:
   ```bash
   cp .env.example .env
   ```

4. Сгенерируйте ключ приложения:
   ```bash
   php artisan key:generate
   ```

5. Настройте `.env` файл:
   ```env
   APP_URL=http://localhost:8000
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=ads
   DB_USERNAME=laravel
   DB_PASSWORD=laravel
   
   # CORS настройки
   CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
   
   # MinIO/S3 настройки (если используется)
   AWS_ACCESS_KEY_ID=minio
   AWS_SECRET_ACCESS_KEY=minio123
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=ads
   AWS_ENDPOINT=http://localhost:9000
   AWS_USE_PATH_STYLE_ENDPOINT=true
   AWS_URL=http://localhost:9000/ads
   
   # Go image service (если используется)
   IMAGE_SERVICE_URL=http://go-image:8080/process
   ```

6. Запустите миграции:
   ```bash
   php artisan migrate
   ```

7. (Опционально) Создайте тестовые данные:
   ```bash
   php artisan db:seed
   ```

8. Запустите сервер разработки:
   ```bash
   php artisan serve
   ```

Backend будет доступен по адресу `http://localhost:8000`

## Настройка Frontend (React)

1. Перейдите в директорию `frontend`:
   ```bash
   cd frontend
   ```

2. Установите зависимости:
   ```bash
   npm install
   ```

3. Создайте файл `.env`:
   ```env
   VITE_API_URL=http://localhost:8000/api/v1
   ```

4. Запустите dev сервер:
   ```bash
   npm run dev
   ```

Frontend будет доступен по адресу `http://localhost:3000`

## Запуск через Docker Compose

1. Из корневой директории проекта:
   ```bash
   docker-compose up -d
   ```

2. Выполните миграции в контейнере:
   ```bash
   docker-compose exec app php artisan migrate
   ```

3. Backend: `http://localhost:8000`
4. Frontend: нужно запустить отдельно (см. выше) или настроить в Docker

## Создание первого администратора

Для создания пользователя с ролью администратора:

1. Запустите tinker:
   ```bash
   php artisan tinker
   ```

2. Создайте администратора:
   ```php
   $user = \App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => \Illuminate\Support\Facades\Hash::make('password'),
       'role' => 'admin'
   ]);
   ```

## Проверка работы

1. Откройте фронтенд: `http://localhost:3000`
2. Зарегистрируйтесь как обычный пользователь
3. Создайте объявление
4. Войдите как администратор
5. Перейдите в админ-панель и одобрите объявление

## Решение проблем

### CORS ошибки
- Убедитесь, что в `.env` файле Laravel указан правильный `CORS_ALLOWED_ORIGINS`
- Проверьте, что фронтенд запущен на порту, указанном в CORS настройках

### Ошибки подключения к БД
- Проверьте настройки подключения в `.env`
- Убедитесь, что PostgreSQL запущен
- Проверьте права доступа пользователя БД

### Проблемы с изображениями
- Убедитесь, что MinIO запущен (если используется)
- Проверьте настройки S3 в `.env`
- Убедитесь, что Go image service запущен (если используется)

### Ошибки аутентификации
- Проверьте, что токен сохраняется в localStorage
- Убедитесь, что заголовок Authorization отправляется с запросами
- Проверьте настройки Sanctum

