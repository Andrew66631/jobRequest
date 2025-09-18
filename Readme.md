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
````
# 5. Проверка работы
Откройте в браузере: http://localhost

📊 Структура базы данных
Таблицы:
user - Пользователи системы

request - Заявки на займы

solution - Решения по заявкам (approved/declined)

🔐 API Endpoints

# 1.Регистрация пользователя
# URL: POST  http://localhost/api/auth/register

# Тело запроса:

```bash
{
  "username": "testuser",
  "password": "password123",
  "email": "test@example.com"
} 
```

# 2.Авторизация пользователя
# URL: POST  http://localhost/api/auth/login

# Тело запроса:

```bash
{
  "username": "testuser",
  "password": "password123"
}
```
# 3.Подача заявки на займ
# URL: POST  http://localhost/api/loan/create

## Заголовки: Authorization: Bearer <токен взять из action login>
# Тело запроса:
```bash
{
  "user_id": 1,
  "amount": 3000,
  "term": 30
}
```
# 4.Обработка заявок на займ

## Заголовки: Authorization: Bearer <токен взять из action login>

## Необходимо запустить воркер

```bash
docker-compose exec php php yii queue/listen
```

## После чего выполнить запрос
# URL: GET http://localhost/api/loan/processor?delay=5


