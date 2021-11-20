## Установка

- composer install
- cp .env.example .env
- php artisan key:generate

## Настройка .env
- Cоздать базу данных
- Настроить в .env параметры подключения к базе данных
- Настроить в .env параметр APP_URL

## Выполнение миграций и загрузка начальных данных
- php artisan migrate:fresh --seed

## Сборка фронта
  - npm install
  - npm run dev
