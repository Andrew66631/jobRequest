<?php

namespace app\services;

use Yii;
use app\models\User;
use yii\web\BadRequestHttpException;

class UserService
{
    /**
     * @param $username
     * @param $password
     * @param $email
     * @return void
     * @throws BadRequestHttpException
     */
    public function validateRegistrationData(string $username, string $password, string $email): BadRequestHttpException
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


    /**
     * @param $username
     * @param $password
     * @return array
     * @throws BadRequestHttpException
     */
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

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return array
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */

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

    /**
     * @param User $user
     * @return string
     */
    private function generateJwt(User $user): string
    {
        $time = time();
        $key = Yii::$app->jwt->key;

        $payload = [
            'iss' => $_SERVER['HTTP_HOST'],
            'aud' => $_SERVER['HTTP_HOST'],
            'iat' => $time,
            'exp' => $time + 3600,
            'uid' => $user->id,
            'username' => $user->username,
        ];

        return \Firebase\JWT\JWT::encode($payload, $key, 'HS256');
    }
}