@echo off
REM Wishlist Service Setup Script for Windows
REM Автоматическая установка и настройка приложения

echo ================================
echo Wishlist Service Setup
echo ================================
echo.

REM Проверка Docker
where docker >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo X Docker не установлен. Установите Docker Desktop:
    echo   https://www.docker.com/products/docker-desktop
    exit /b 1
)

where docker-compose >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo X Docker Compose не установлен.
    exit /b 1
)

echo + Docker установлен
echo + Docker Compose установлен
echo.

REM 
if not exist .env (
    echo Создание .env файла...
    copy .env.example .env >nul
    echo + .env файл создан
    echo   Отредактируйте .env и установите пароль БД
) else (
    echo + .env файл уже существует
)

echo.

REM Создание директории для загрузок
echo Создание директории uploads...
if not exist public\uploads mkdir public\uploads
echo + Директория создана
echo.

REM Запуск Docker Compose
echo Запуск Docker контейнеров...
docker-compose up -d

echo.
echo Ожидание готовности базы данных...
timeout /t 10 /nobreak >nul

echo.
echo ================================
echo Установка завершена!
echo ================================
echo.
echo Приложение доступно по адресу:
echo   http://localhost:8000
echo.
echo Adminer (управление БД):
echo   http://localhost:8080
echo.
echo Тестовые пользователи:
echo   ivan_petrov / password123
echo   maria_ivanova / password123
echo   alex_smirnov / password123
echo.
echo Полезные команды:
echo   docker-compose logs -f    # Просмотр логов
echo   docker-compose down       # Остановка
echo   docker-compose restart    # Перезапуск
echo.
pause
