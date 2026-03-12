# Wishlist Service - Сервис управления вишлистами

Веб-сервис для создания и управления списками желаемых подарков к различным праздникам и событиям. Социальная платформа с системой друзей, настройками приватности и возможностью резервирования подарков.

## Возможности

- Регистрация и авторизация пользователей
- Создание вишлистов к различным событиям (дни рождения, Новый год, свадьбы и т.д.)
- Добавление подарков с описанием, ссылками, ценами и приоритетами
- Загрузка обложек для вишлистов
- Резервирование подарков другими пользователями
- Система друзей (отправка запросов, принятие, удаление)
- Гибкие настройки приватности:
  - Публичный (виден всем)
  - Доступен только друзьям
  - Доступен по ссылке (уникальная ссылка для доступа)
- Раздел "Идеи подарков" с рекомендациями
- Обзор публичных вишлистов
- Просмотр вишлистов друзей
- Профили пользователей

## Технологический стек

- **Backend**: PHP 8.2
- **Database**: PostgreSQL 15
- **Web Server**: Apache 2.4
- **Containerization**: Docker & Docker Compose




##  старт

### 1. Клонирование репозитория

```bash
git clone <repository-url>
cd wishlist-service
```

### 2. Настройка переменных окружения

Скопируйте файл с примером конфигурации:

```bash
cp .env.example .env
```

Отредактируйте `.env` файл и установите свои значения:

```env
# Database Configuration
DB_HOST=postgres
DB_PORT=5432
DB_NAME=wishlist_service
DB_USER=postgres
DB_PASS=your_secure_password_here

# Application Configuration
APP_PORT=8000
APP_ENV=production
APP_DEBUG=false
```

### 3. Запуск приложения

```bash
# Сборка и запуск контейнеров
docker-compose up -d

# Проверка статуса контейнеров
docker-compose ps
```

### 4. Доступ к приложению

- **Приложение**: http://localhost:8000
- **Adminer** (управление БД): http://localhost:8080





## Миграции базы данных

Миграции выполняются автоматически при первом запуске контейнера PostgreSQL. Файлы миграций находятся в папке `migrations/` и выполняются в алфавитном порядке.

### Список миграций

1. **001_initial_schema.sql** - Начальная схема БД (пользователи, вишлисты, подарки)
2. **002_add_cover_image.sql** - Добавление поддержки обложек
3. **003_add_friends_system.sql** - Система друзей и расширенная приватность
4. **004_seed_data.sql** - Тестовые данные (опционально)

### Ручное выполнение миграций

Если нужно выполнить миграции вручную:

```bash
# Подключение к контейнеру PostgreSQL
docker-compose exec postgres psql -U postgres -d wishlist_service

# Выполнение конкретной миграции
docker-compose exec postgres psql -U postgres -d wishlist_service -f /docker-entrypoint-initdb.d/001_initial_schema.sql
```





### Доступ к контейнерам

```bash
# Доступ к контейнеру приложения
docker-compose exec app bash

# Доступ к контейнеру PostgreSQL
docker-compose exec postgres psql -U postgres -d wishlist_service
```


### Вишлисты

- `POST /api.php?action=create_wishlist` - Создание вишлиста
- `POST /api.php?action=add_item` - Добавление подарка
- `POST /api.php?action=reserve_item` - Резервирование подарка
- `POST /api.php?action=unreserve_item` - Отмена резервирования
- `POST /api.php?action=delete_item` - Удаление подарка

### Друзья

- `POST /api.php?action=send_friend_request` - Отправка запроса в друзья
- `POST /api.php?action=accept_friend_request` - Принятие запроса
- `POST /api.php?action=reject_friend_request` - Отклонение запроса
- `POST /api.php?action=remove_friend` - Удаление из друзей

## Безопасность

- Пароли хешируются с использованием `password_hash()` (bcrypt)
- Используются подготовленные запросы (prepared statements) для защиты от SQL-инъекций
- Проверка авторизации для защищенных действий
- XSS защита через `htmlspecialchars()`
- Валидация загружаемых файлов
- Проверка прав доступа к вишлистам на основе настроек приватности

## Настройки приватности

# Публичный
Вишлист виден всем пользователям в разделе "Обзор"

# Доступен только друзьям
Вишлист виден только пользователям из списка друзей владельца

# Доступен по ссылке
Генерируется уникальная ссылка, которую можно передать кому угодно для доступа к вишлисту





# Очистка и пересоздание БД

```bash

docker-compose down -v


docker-compose up -d
```







