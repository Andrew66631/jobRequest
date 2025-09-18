# Yii2 Тестовое задание

🔧 Конфигурация
Параметры базы данных
Хост: localhost

Порт: 5432

База данных: loans

Пользователь: user

Пароль: password

JWT Настройки
Секретный ключ генерируется автоматически

Время жизни токена: 1 час

## 🚀 Технологический стек

- **Фреймворк**: Yii2
- **Веб-сервер**: Nginx
- **База данных**: PostgreSQL
- **Контейнеризация**: Docker Compose
- **Аутентификация**: JWT (JSON Web Tokens)
- **Язык**: PHP 8.2

## 📋 Предварительные требования

- Docker
- Docker Compose
- Git

## 🛠 Установка и запуск

### 1. Клонирование репозитория

```bash
git clone  - репозиторий
cd jobRequest

docker-compose up -d

docker-compose exec php composer install

docker-compose exec php ./yii migrate

5. Проверка работы
Откройте в браузере: http://localhost

📊 Структура базы данных
Таблицы:
user - Пользователи системы

request - Заявки на займы

solution - Решения по заявкам (approved/declined)

🔐 API Endpoints

1.Регистрация пользователя
# URL: POST /api/auth/register

Тело запроса:

{
  "username": "testuser",
  "password": "password123",
  "email": "test@example.com"
}
2.Авторизация пользователя
# URL: POST /api/auth/login

Тело запроса:

{
  "username": "testuser",
  "password": "password123"
}
3.Управление заявками на займы
Подача заявки на займ
URL: POST /api/loan/create

Заголовки: Authorization: Bearer <jwt_token>

Тело запроса:
{
  "user_id": 1,
  "amount": 3000,
  "term": 30
}

🐛 Тестирование
Примеры запросов с curl

Регистрация
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"password123","email":"test@example.com"}'

Авторизация

curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"password123"}'

curl -X POST http://localhost/api/loan/create \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <jwt_token>" \
  -d '{"user_id":1,"amount":3000,"term":30}'

Валидация заявок на займ:
Пользователь должен существовать в системе

Сумма займа должна быть положительным числом

Срок займа должен быть положительным целым числом

Важно: Пользователь не должен иметь одобренных заявок

