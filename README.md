# Платформа объявлений с модерацией

Полнофункциональная платформа для публикации объявлений с системой модерации.

## Структура проекта

- `laravel-app/` - Backend на Laravel 12
- `frontend/` - Frontend на React + Vite
- `go-image-service/` - Микросервис для обработки изображений

## Быстрый старт

### Backend (Laravel)

1. Перейдите в директорию `laravel-app`
2. Установите зависимости:
   ```bash
   composer install
   ```
3. Настройте `.env` файл (скопируйте из `.env.example`)
4. Запустите миграции:
   ```bash
   php artisan migrate
   ```
5. Запустите сервер:
   ```bash
   php artisan serve
   ```

Backend будет доступен по адресу `http://localhost:8000`

### Frontend (React)

1. Перейдите в директорию `frontend`
2. Установите зависимости:
   ```bash
   npm install
   ```
3. Создайте файл `.env`:
   ```
   VITE_API_URL=http://localhost:8000/api/v1
   ```
4. Запустите dev сервер:
   ```bash
   npm run dev
   ```

Frontend будет доступен по адресу `http://localhost:3000`

## Docker Compose

Для запуска всего проекта через Docker:

```bash
docker-compose up -d
```

## API Endpoints

### Публичные
- `GET /api/v1/ads` - Список одобренных объявлений
- `GET /api/v1/ads/{id}` - Детали объявления
- `GET /api/v1/categories` - Список категорий
- `POST /api/v1/auth/register` - Регистрация
- `POST /api/v1/auth/login` - Вход

### Требуют аутентификации
- `GET /api/v1/my/ads` - Мои объявления
- `POST /api/v1/ads` - Создать объявление
- `PUT /api/v1/ads/{id}` - Обновить объявление
- `DELETE /api/v1/ads/{id}` - Удалить объявление
- `POST /api/v1/ads/{id}/favorite` - Добавить в избранное
- `DELETE /api/v1/ads/{id}/favorite` - Удалить из избранного
- `GET /api/v1/favorites` - Список избранного
- `POST /api/v1/auth/logout` - Выход

### Админ-панель (требует роль admin)
- `GET /api/v1/admin/ads?status=pending` - Список объявлений для модерации
- `POST /api/v1/admin/ads/{id}/approve` - Одобрить объявление
- `POST /api/v1/admin/ads/{id}/reject` - Отклонить объявление
- `GET /api/v1/admin/moderation/logs` - История модерации

## Роли пользователей

- **user** - Обычный пользователь, может создавать объявления
- **admin** - Администратор, может модерировать объявления

## Статусы объявлений

- **pending** - Ожидает модерации
- **approved** - Одобрено, видно всем
- **rejected** - Отклонено

## Особенности

- Все объявления проходят модерацию перед публикацией
- История всех действий администраторов сохраняется
- Поддержка загрузки изображений через Go-сервис
- Полнотекстовый поиск по объявлениям
- Фильтрация по категориям, городу, цене
- Система избранного
- Система сообщений между пользователями

## Технологии

### Backend
- Laravel 12
- PostgreSQL
- Redis
- MinIO (S3-совместимое хранилище)
- Laravel Sanctum (аутентификация)

### Frontend
- React 18
- React Router
- Axios
- Vite

### Инфраструктура
- Docker & Docker Compose
- Go (микросервис обработки изображений)

