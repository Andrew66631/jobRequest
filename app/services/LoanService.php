<?php

namespace app\services;

use Yii;
use app\models\Request;
use app\models\User;
use yii\web\BadRequestHttpException;

class LoanService
{
    /**
     * @param int $userId
     * @param float $amount
     * @param int $term
     * @return Request
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function createLoanRequest(int $userId, float $amount, int $term): Request
    {
        // Проверяем существование пользователя
        $user = User::findOne($userId);
        if (!$user) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        // Проверяем, что у пользователя нет одобренных заявок
        if ($this->hasApprovedRequests($userId)) {
            throw new BadRequestHttpException('У пользователя есть одобреные заявки');
        }

        // Создаем новую заявку
        $request = new Request();
        $request->user_id = $userId;
        $request->amount = $amount;
        $request->term = $term;

        if (!$request->save()) {
            throw new BadRequestHttpException('Ошибка создания заявки: ' . implode(', ', $request->getFirstErrors()));
        }

        return $request;
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function hasApprovedRequests(int $userId): bool
    {
        return Request::find()
            ->where(['user_id' => $userId, 'solution_id' => Request::SOLUTION_APPROVED])
            ->exists();
    }

    /**
     * @param int $userId
     * @param float $amount
     * @param int $term
     * @return void
     * @throws BadRequestHttpException
     */
    public function validateLoanData(int $userId,float $amount, int $term): void
    {
        if (empty($userId) || empty($amount) || empty($term)) {
            throw new BadRequestHttpException('Требуется идентификатор пользователя, сумма и срок.');
        }

        if (!is_numeric($userId) || $userId <= 0) {
            throw new BadRequestHttpException('Идентификатор пользователя должен быть положительным целым числом.');
        }

        if (!is_numeric($amount) || $amount <= 0) {
            throw new BadRequestHttpException('Сумма должна быть положительным числом.');
        }

        if (!is_numeric($term) || $term <= 0) {
            throw new BadRequestHttpException('Срок должен быть положительным целым числом.');
        }
    }
}