
# Функциональность
 Регистрация и авторизация
Создание вишлистов с обложками
 Управление подарками (добавление, удаление, резервирование)
 Система друзей
 Гибкие настройки приватности
 Поиск пользователей
 Профили пользователей
 Идеи подарков с категориями

# Технологии
- **Backend**: PHP 8.2
- **Database**: PostgreSQL 15
- **Web Server**: Apache 2.4
- **Containerization**: Docker & Docker Compose
- **Frontend**: Vanilla JS, CSS

### Безопасность
- Password hashing (bcrypt)
- Prepared statements (защита от SQL-инъекций)
- XSS защита
- CSRF защита (через сессии)
- Валидация загружаемых файлов


# Архитектура
- MVC паттерн
- Разделение на модели, представления и контроллеры
- RESTful API
- Версионированные миграции БД
- Конфигурация через переменные окружения

## Структура базы данных

### Таблицы

1. **users** - Пользователи
   - id, username, email, password_hash, full_name, avatar, created_at

2. **wishlists** - Вишлисты
   - id, user_id, title, description, event_type, event_date, cover_image, privacy, share_token, created_at

3. **wishlist_items** - Подарки
   - id, wishlist_id, title, description, url, price, image_url, priority, is_reserved, reserved_by, created_at

4. **gift_ideas** - Идеи подарков
   - id, title, description, category, price_range, image_url, url, created_at

5. **friend_requests** - Запросы в друзья
   - id, sender_id, receiver_id, status, created_at, updated_at

6. **follows** - Подписки (legacy)
   - follower_id, following_id, created_at

7. **migrations** - История миграций
   - id, version, description, executed_at

### Типы данных (ENUM)

- **event_type_enum**: birthday, new_year, wedding, anniversary, other
- **priority_enum**: low, medium, high
- **privacy_enum**: public, friends, link
- **friend_status_enum**: pending, accepted, rejected

## API Endpoints

### Аутентификация
- POST /api.php?action=register
- POST /api.php?action=login

### Вишлисты
- POST /api.php?action=create_wishlist
- POST /api.php?action=add_item
- POST /api.php?action=reserve_item
- POST /api.php?action=unreserve_item
- POST /api.php?action=delete_item

### Друзья
- POST /api.php?action=send_friend_request
- POST /api.php?action=accept_friend_request
- POST /api.php?action=reject_friend_request
- POST /api.php?action=remove_friend

## Страницы

### Публичные
- / - Главная страница
- /index.php?action=login - Вход
- /index.php?action=register - Регистрация
- /index.php?action=explore - Обзор публичных вишлистов
- /index.php?action=gift_ideas - Идеи подарков
- /index.php?action=view_wishlist&id=X - Просмотр вишлиста
- /index.php?action=view_wishlist&token=X - Просмотр по ссылке

### Защищенные (требуют авторизации)
- /index.php?action=my_wishlists - Мои вишлисты
- /index.php?action=create_wishlist - Создание вишлиста
- /index.php?action=friends - Список друзей
- /index.php?action=friends_wishlists - Вишлисты друзей
- /index.php?action=search_users - Поиск пользователей
- /index.php?action=user_profile&id=X - Профиль пользователя

## Развертывание

### Docker Compose (рекомендуется)
```bash
cp .env.example .env
# Отредактируйте .env
docker-compose up -d
```

### Локально
```bash
# Установите PHP 8.2+, PostgreSQL 15+
createdb wishlist_service
psql -d wishlist_service -f migrations/001_initial_schema.sql
# ... выполните остальные миграции
php -S localhost:8000 -t public
```

## Конфигурация

### Переменные окружения (.env)

```env
# Database
DB_HOST=postgres
DB_PORT=5432
DB_NAME=wishlist_service
DB_USER=postgres
DB_PASS=4321

# Application
APP_PORT=8000
APP_ENV=production
APP_DEBUG=false
```

## Тестирование

### Тестовые данные
При первом запуске создаются 3 тестовых пользователя:
- ivan_petrov / password123
- maria_ivanova / password123
- alex_smirnov / password123

### Health Check
```bash
curl http://localhost:8000/healthcheck.php
```

## Мониторинг

### Логи
```bash
docker-compose logs -f app
docker-compose logs -f postgres
```

### Метрики
- Adminer: http://localhost:8080
- Health check: http://localhost:8000/healthcheck.php

## Резервное копирование

### База данных
```bash
docker-compose exec postgres pg_dump -U postgres wishlist_service > backup.sql
```

### Загруженные файлы
```bash
tar -czf uploads_backup.tar.gz public/uploads/
```












