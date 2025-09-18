<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\services\LoanService;
use app\behaviors\InputValidationBehavior;

class LoanController extends Controller
{
    public $enableCsrfValidation = false;

    private $loanService;

    public function __construct($id, $module, LoanService $loanService, $config = [])
    {
        $this->loanService = $loanService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['inputValidation'] = [
            'class' => InputValidationBehavior::class,
            'requiredFields' => [
                'create' => ['user_id', 'amount', 'term'],
            ],
        ];

        return $behaviors;
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $userId = $request->post('user_id');
        $amount = $request->post('amount');
        $term = $request->post('term');

        try {
            // Валидация данных
            $this->loanService->validateLoanData($userId, $amount, $term);

            // Создание заявки
            $loanRequest = $this->loanService->createLoanRequest($userId, $amount, $term);

            Yii::$app->response->statusCode = 201;
            return [
                'result' => true,
                'id' => $loanRequest->id,
            ];

        } catch (\yii\web\BadRequestHttpException $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
    }
}