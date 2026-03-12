# Руководство по развертыванию Wishlist Service

## Содержание

1. [Развертывание через Docker Compose](#развертывание-через-docker-compose)
2. [Локальная установка](#локальная-установка)
3. [Производственное развертывание](#производственное-развертывание)
4. [Миграции базы данных](#миграции-базы-данных)
5. [Резервное копирование](#резервное-копирование)
6. [Мониторинг](#мониторинг)

## Развертывание через Docker Compose

### Предварительные требования

- Docker 20.10+
- Docker Compose 2.0+
- 2GB свободной оперативной памяти
- 5GB свободного места на диске

### Шаг 1: Подготовка

```bash
# Клонирование репозитория
git clone <repository-url>
cd wishlist-service

# Создание .env файла
cp .env.example .env
```

### Шаг 2: Настройка переменных окружения

Отредактируйте `.env`:

```env
# Database Configuration
DB_HOST=postgres
DB_PORT=5432
DB_NAME=wishlist_service
DB_USER=postgres
DB_PASS=CHANGE_THIS_PASSWORD  # Обязательно измените!

# Application Configuration
APP_PORT=8000
APP_ENV=production
APP_DEBUG=false
```

### Шаг 3: Запуск

```bash
# Сборка и запуск контейнеров
docker-compose up -d

# Проверка статуса
docker-compose ps

# Просмотр логов
docker-compose logs -f
```

### Шаг 4: Проверка

Откройте в браузере:
- Приложение: http://localhost:8000
- Adminer: http://localhost:8080

## Локальная установка

### Требования

- PHP 8.2+
- PostgreSQL 15+
- Apache 2.4+ или Nginx
- Composer (опционально)

### Установка PHP расширений

#### Ubuntu/Debian
```bash
sudo apt update
sudo apt install php8.2 php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl
```

#### macOS (Homebrew)
```bash
brew install php@8.2
brew install postgresql@15
```

#### Windows
Скачайте PHP с https://windows.php.net/download/ и раскомментируйте в php.ini:
```ini
extension=pdo_pgsql
extension=pgsql
```

### Настройка PostgreSQL

```bash
# Создание базы данных
createdb wishlist_service

# Выполнение миграций
psql -d wishlist_service -f migrations/001_initial_schema.sql
psql -d wishlist_service -f migrations/002_add_cover_image.sql
psql -d wishlist_service -f migrations/003_add_friends_system.sql
psql -d wishlist_service -f migrations/004_seed_data.sql
```

### Настройка приложения

1. Скопируйте `.env.example` в `.env`
2. Установите параметры подключения к БД
3. Создайте директорию для загрузок:

```bash
mkdir -p public/uploads
chmod 777 public/uploads
```

### Запуск

#### Встроенный сервер PHP (для разработки)
```bash
php -S localhost:8000 -t public
```

#### Apache

Создайте виртуальный хост:

```apache
<VirtualHost *:80>
    ServerName wishlist.local
    DocumentRoot /path/to/wishlist-service/public

    <Directory /path/to/wishlist-service/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/wishlist_error.log
    CustomLog ${APACHE_LOG_DIR}/wishlist_access.log combined
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name wishlist.local;
    root /path/to/wishlist-service/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Производственное развертывание

### Рекомендации по безопасности

1. **Измените пароли по умолчанию**
```env
DB_PASS=<strong-random-password>
```

2. **Отключите отладку**
```env
APP_DEBUG=false
```

3. **Настройте HTTPS**

Для Docker Compose добавьте Nginx с SSL:

```yaml
nginx:
  image: nginx:alpine
  ports:
    - "443:443"
  volumes:
    - ./nginx.conf:/etc/nginx/nginx.conf
    - ./ssl:/etc/nginx/ssl
```

4. **Ограничьте доступ к Adminer**

В production удалите сервис adminer из docker-compose.yml или ограничьте доступ:

```yaml
adminer:
  ports:
    - "127.0.0.1:8080:8080"  # Доступ только с localhost
```

### Оптимизация производительности

1. **Настройте кэширование PHP**

Добавьте в Dockerfile:
```dockerfile
RUN docker-php-ext-install opcache
```

2. **Увеличьте лимиты PostgreSQL**

Создайте `docker/postgres.conf`:
```
max_connections = 200
shared_buffers = 256MB
effective_cache_size = 1GB
```

3. **Настройте логирование**

```yaml
app:
  logging:
    driver: "json-file"
    options:
      max-size: "10m"
      max-file: "3"
```

## Миграции базы данных

### Структура миграций

Миграции находятся в папке `migrations/` и именуются по схеме:
```
XXX_description.sql
```

где XXX - порядковый номер (001, 002, и т.д.)

### Автоматическое выполнение

При первом запуске Docker Compose миграции выполняются автоматически.

### Ручное выполнение

```bash
# Подключение к контейнеру
docker-compose exec postgres psql -U postgres -d wishlist_service

# Выполнение конкретной миграции
\i /docker-entrypoint-initdb.d/001_initial_schema.sql

# Проверка выполненных миграций
SELECT * FROM migrations ORDER BY executed_at;
```

### Создание новой миграции

1. Создайте файл `migrations/005_your_migration.sql`
2. Добавьте SQL команды
3. В конце добавьте запись о миграции:

```sql
INSERT INTO migrations (version, description) 
VALUES ('005', 'Your migration description')
ON CONFLICT (version) DO NOTHING;
```

### Откат миграций

Создайте файл отката `migrations/rollback/005_rollback.sql`:

```sql
-- Откат изменений
DROP TABLE IF EXISTS new_table;

-- Удаление записи о миграции
DELETE FROM migrations WHERE version = '005';
```

## Резервное копирование

### Автоматическое резервное копирование

Создайте скрипт `backup.sh`:

```bash
#!/bin/bash
BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Создание директории для бэкапов
mkdir -p $BACKUP_DIR

# Бэкап базы данных
docker-compose exec -T postgres pg_dump -U postgres wishlist_service > "$BACKUP_DIR/db_$DATE.sql"

# Бэкап загруженных файлов
tar -czf "$BACKUP_DIR/uploads_$DATE.tar.gz" public/uploads/

# Удаление старых бэкапов (старше 30 дней)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

Добавьте в cron:
```bash
0 2 * * * /path/to/wishlist-service/backup.sh
```

### Восстановление из резервной копии

```bash
# Восстановление базы данных
docker-compose exec -T postgres psql -U postgres wishlist_service < backups/db_20240101_020000.sql

# Восстановление файлов
tar -xzf backups/uploads_20240101_020000.tar.gz
```

## Мониторинг

### Проверка здоровья контейнеров

```bash
# Статус всех контейнеров
docker-compose ps

# Использование ресурсов
docker stats

# Логи приложения
docker-compose logs -f app

# Логи базы данных
docker-compose logs -f postgres
```

### Мониторинг базы данных

Подключитесь к PostgreSQL:
```bash
docker-compose exec postgres psql -U postgres -d wishlist_service
```

Полезные запросы:
```sql
-- Размер базы данных
SELECT pg_size_pretty(pg_database_size('wishlist_service'));

-- Активные подключения
SELECT count(*) FROM pg_stat_activity;

-- Медленные запросы
SELECT query, calls, total_time, mean_time 
FROM pg_stat_statements 
ORDER BY mean_time DESC 
LIMIT 10;
```

### Настройка алертов

Используйте инструменты мониторинга:
- Prometheus + Grafana
- Datadog
- New Relic
- Sentry (для отслеживания ошибок)

## Обновление приложения

```bash
# Получение последних изменений
git pull origin main

# Пересборка контейнеров
docker-compose down
docker-compose up -d --build

# Выполнение новых миграций (если есть)
docker-compose exec postgres psql -U postgres -d wishlist_service -f /docker-entrypoint-initdb.d/XXX_new_migration.sql
```

## Troubleshooting

### Контейнер не запускается

```bash
# Проверка логов
docker-compose logs app

# Проверка конфигурации
docker-compose config

# Пересоздание контейнеров
docker-compose down
docker-compose up -d --force-recreate
```

### Проблемы с подключением к БД

```bash
# Проверка сети
docker network ls
docker network inspect wishlist_network

# Проверка переменных окружения
docker-compose exec app env | grep DB_
```

### Проблемы с правами доступа

```bash
# Исправление прав на uploads
docker-compose exec app chown -R www-data:www-data /var/www/html/public/uploads
docker-compose exec app chmod -R 777 /var/www/html/public/uploads
```

## Масштабирование

### Горизонтальное масштабирование

Для масштабирования приложения используйте несколько экземпляров:

```bash
docker-compose up -d --scale app=3
```

Добавьте балансировщик нагрузки (Nginx):

```yaml
nginx:
  image: nginx:alpine
  ports:
    - "80:80"
  volumes:
    - ./nginx-lb.conf:/etc/nginx/nginx.conf
  depends_on:
    - app
```

### Вертикальное масштабирование

Увеличьте ресурсы контейнеров:

```yaml
app:
  deploy:
    resources:
      limits:
        cpus: '2'
        memory: 2G
      reservations:
        cpus: '1'
        memory: 1G
```

## Поддержка

Для получения помощи:
1. Проверьте документацию
2. Просмотрите issues в репозитории
3. Создайте новый issue с подробным описанием проблемы
