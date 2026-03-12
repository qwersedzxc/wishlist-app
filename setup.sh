#!/bin/bash

# Wishlist Service Setup Script
# Автоматическая установка и настройка приложения

set -e

echo "================================"
echo "Wishlist Service Setup"
echo "================================"
echo ""


if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен. Установите Docker Desktop:"
    echo "   https://www.docker.com/products/docker-desktop"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose не установлен."
    exit 1
fi

echo "✓ Docker установлен"
echo "✓ Docker Compose установлен"
echo ""

if [ ! -f .env ]; then
    echo "📝 Создание .env файла..."
    cp .env.example .env
    
   
    RANDOM_PASS=$(openssl rand -base64 12 2>/dev/null || echo "change_this_password")
    
   
    if [[ "$OSTYPE" == "darwin"* ]]; then
        sed -i '' "s/your_secure_password_here/$RANDOM_PASS/" .env
    else
        sed -i "s/your_secure_password_here/$RANDOM_PASS/" .env
    fi
    
    echo "✓ .env файл создан"
    echo "  Пароль БД: $RANDOM_PASS"
else
    echo "✓ .env файл уже существует"
fi

echo ""

# Создание директории для загрузок
echo "📁 Создание директории uploads..."
mkdir -p public/uploads
chmod 777 public/uploads
echo "✓ Директория создана"
echo ""

# Запуск Docker Compose
echo "🚀 Запуск Docker контейнеров..."
docker-compose up -d

echo ""
echo "⏳ Ожидание готовности базы данных..."
sleep 10

# Проверка здоровья
echo ""
echo "🔍 Проверка состояния приложения..."
sleep 5

if curl -s http://localhost:8000/healthcheck.php > /dev/null 2>&1; then
    echo "✓ Приложение запущено успешно!"
else
    echo "⚠️  Приложение запускается, подождите еще немного..."
fi

echo ""
echo "================================"
echo "✅ Установка завершена!"
echo "================================"
echo ""
echo "Приложение доступно по адресу:"
echo "  🌐 http://localhost:8000"
echo ""
echo "Adminer (управление БД):"
echo "  🔧 http://localhost:8080"
echo ""
echo "Тестовые пользователи:"
echo "  👤 ivan_petrov / password123"
echo "  👤 maria_ivanova / password123"
echo "  👤 alex_smirnov / password123"
echo ""
echo "Полезные команды:"
echo "  docker-compose logs -f    # Просмотр логов"
echo "  docker-compose down       # Остановка"
echo "  docker-compose restart    # Перезапуск"
echo ""
