<?php

namespace app\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\Request;
use Yii;

class ProcessLoanJob extends BaseObject implements JobInterface
{
    public $requestId;
    public $delay;

    public function execute($queue)
    {
        $request = Request::findOne($this->requestId);
        if (!$request) {
            Yii::error("Заявка не найдена: {$this->requestId}");
            return;
        }

        sleep($this->delay);

        $hasApproved = Request::find()
            ->where(['user_id' => $request->user_id, 'solution_id' => Request::SOLUTION_APPROVED])
            ->andWhere(['!=', 'id', $request->id])
            ->exists();

        if ($hasApproved) {
            $request->solution_id = Request::SOLUTION_REJECTED;
            $request->save(false);
            Yii::info("Заявка {$request->id} отклонена (есть одобренные заявки)");
            return;
        }

        $random = mt_rand(1, 100);
        if ($random <= 10) {
            $request->solution_id = Request::SOLUTION_APPROVED;
            Yii::info("Заявка {$request->id} одобрена");
        } else {
            $request->solution_id = Request::SOLUTION_REJECTED;
            Yii::info("Заявка {$request->id} отклонена (случайный отбор)");
        }

        $request->save(false);
    }
}