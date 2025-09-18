<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\BadRequestHttpException;

class InputValidationBehavior extends Behavior
{
    public $requiredFields = [];

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'validateInput',
        ];
    }

    public function validateInput($event)
    {
        $request = Yii::$app->request;
        $action = $event->action->id;

        if (isset($this->requiredFields[$action])) {
            foreach ($this->requiredFields[$action] as $field) {
                // Проверяем и в GET, и в POST
                $value = $request->get($field) ?? $request->post($field);

                if (empty($value)) {
                    throw new BadRequestHttpException("Поле '{$field}' обязательно к заполнению");
                }
            }
        }
    }
}