<?php

namespace app\services;

use Yii;
use app\models\Request;
use app\models\User;
use yii\web\BadRequestHttpException;

class LoanService
{
    public function createLoanRequest(int $userId, float $amount, int $term): Request
    {
        $user = User::findOne($userId);
        if (!$user) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        if ($this->hasApprovedRequests($userId)) {
            throw new BadRequestHttpException('У пользователя есть одобреные заявки');
        }

        $request = new Request();
        $request->user_id = $userId;
        $request->amount = $amount;
        $request->term = $term;

        if (!$request->save()) {
            throw new BadRequestHttpException('Ошибка создания заявки: ' . implode(', ', $request->getFirstErrors()));
        }

        return $request;
    }

    private function hasApprovedRequests(int $userId): bool
    {
        return Request::find()
            ->where(['user_id' => $userId, 'solution_id' => Request::SOLUTION_APPROVED])
            ->exists();
    }

    public function validateLoanData(int $userId, float $amount, int $term): void
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

    public function processUserRequests(int $delay): int
    {
        if ($delay <= 0) {
            throw new BadRequestHttpException('Параметр delay должен быть положительным целым числом.');
        }

        $userId = Yii::$app->user->id;
        if (!$userId) {
            throw new BadRequestHttpException('Пользователь не авторизован.');
        }

        $user = User::findOne($userId);
        if (!$user) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        $pendingRequests = Request::find()
            ->where(['user_id' => $userId, 'solution_id' => null])
            ->all();

        foreach ($pendingRequests as $request) {
            Yii::$app->queue->push(new \app\jobs\ProcessLoanJob([
                'requestId' => $request->id,
                'delay' => $delay
            ]));
        }

        return count($pendingRequests);
    }

    public function processSingleRequest(Request $request, int $delay): void
    {
        sleep($delay);

        $hasApproved = Request::find()
            ->where(['user_id' => $request->user_id, 'solution_id' => Request::SOLUTION_APPROVED])
            ->andWhere(['!=', 'id', $request->id])
            ->exists();

        if ($hasApproved) {
            $request->solution_id = Request::SOLUTION_REJECTED;
            $request->save(false);
            return;
        }

        $random = mt_rand(1, 100);
        if ($random <= 10) {
            $request->solution_id = Request::SOLUTION_APPROVED;
        } else {
            $request->solution_id = Request::SOLUTION_REJECTED;
        }

        $request->save(false);
    }

    public function validateDelay($delay): void
    {
        if (empty($delay)) {
            throw new BadRequestHttpException('Требуется параметр delay.');
        }

        if (!is_numeric($delay) || $delay <= 0) {
            throw new BadRequestHttpException('Параметр delay должен быть положительным целым числом.');
        }
    }
}