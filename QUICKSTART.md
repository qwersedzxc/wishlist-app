# Быстрый старт Wishlist Service

## За 5 минут до запуска

### 1. Установите Docker

**Windows/Mac**: Скачайте [Docker Desktop](https://www.docker.com/products/docker-desktop)

**Linux**:
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
```

### 2. Клонируйте проект

```bash
git clone <repository-url>
cd wishlist-service
```

### 3. Настройте окружение

```bash
cp .env.example .env
```

Откройте `.env` и измените пароль:
```env
DB_PASS=your_secure_password
```

### 4. Запустите

```bash
docker-compose up -d
```

### 5. Откройте в браузере

http://localhost:8000

## Тестовые пользователи


| ivan_petrov | password123 |
| maria_ivanova | password123 |
| alex_smirnov | password123 |



## Полезные команды

```bash
# Остановить
docker-compose down

# Перезапустить
docker-compose restart

# Посмотреть логи
docker-compose logs -f

# Очистить все данные
docker-compose down -v
```


