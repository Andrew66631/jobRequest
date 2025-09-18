<?php

namespace app\services;

use Yii;
use app\models\User;
use yii\web\BadRequestHttpException;

class UserService
{
    public function validateRegistrationData(string $username, string $password, string $email): void
    {
        if (empty($username) || empty($password) || empty($email)) {
            throw new BadRequestHttpException('Требуются имя пользователя, пароль и адрес электронной почты.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException('Неверный формат email');
        }

        if (strlen($password) < 6) {
            throw new BadRequestHttpException('Пароль должен быть не менее 6 символов');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new BadRequestHttpException('Имя пользователя может содержать только буквы, цифры и символы подчеркивания.');
        }

        if (User::find()->where(['username' => $username])->exists()) {
            throw new BadRequestHttpException('Имя пользователя уже существует');
        }

        if (User::find()->where(['email' => $email])->exists()) {
            throw new BadRequestHttpException('Email существует');
        }
    }

    public function authenticateUser(string $username, string $password): array
    {
        $user = User::find()->where(['username' => $username])->one();

        if (!$user || !Yii::$app->security->validatePassword($password, $user->password_hash)) {
            throw new BadRequestHttpException('Некорректное имя пользователя или пароль');
        }
        $token = $this->generateJwt($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function createUser(string $username, string $password, string $email): array
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password_hash = Yii::$app->security->generatePasswordHash($password);
        $user->auth_key = Yii::$app->security->generateRandomString();

        if (!$user->save()) {
            throw new BadRequestHttpException('Registration failed: ' . implode(', ', $user->getFirstErrors()));
        }

        $token = $this->generateJwt($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    private function generateJwt(User $user): string
    {
        $time = time();
        $key = 'U2VjcmV0SldUMjAyNCEkQF4mKigpXytbXXt9fTo7IjwsPi5';

        $payload = [
            'iss' => $_SERVER['HTTP_HOST'] ?? 'localhost',
            'aud' => $_SERVER['HTTP_HOST'] ?? 'localhost',
            'iat' => $time,
            'exp' => $time + 3600,
            'uid' => $user->id,
            'username' => $user->username,
        ];

        return \Firebase\JWT\JWT::encode($payload, $key, 'HS256');
    }
}